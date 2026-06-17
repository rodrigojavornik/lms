<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Certificate;
use App\Models\Trail;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $entityId = $user->entity_id;
        
        // Cursos que o aluno já está matriculado
        $myCourses = $user->courses()->withCount('lessons')->get();
        
        // Cursos disponíveis para se matricular (pertencentes à mesma entidade do aluno, sem entidade, ou compartilhados especificamente com ela)
        $availableCourses = Course::whereNotIn('id', $myCourses->pluck('id'))
            ->where(function ($query) use ($entityId) {
                $query->whereNull('entity_id');
                if ($entityId) {
                    $query->orWhere('entity_id', $entityId)
                          ->orWhereHas('sharedEntities', function ($q) use ($entityId) {
                              $q->where('entities.id', $entityId);
                          });
                }
            })
            ->withCount('lessons')
            ->get();

        // Trilhas atribuídas ao estudante (diretamente ou através de grupos)
        $directTrails = $user->trails()->with('courses')->get();
        $groupTrails = Trail::whereHas('groups', function ($q) use ($user) {
            $q->whereIn('groups.id', $user->groups->pluck('id'));
        })->with('courses')->get();

        $myTrails = $directTrails->merge($groupTrails)->unique('id');
            
        return view('student.dashboard', compact('myCourses', 'availableCourses', 'myTrails'));
    }

    public function enroll(Course $course)
    {
        $user = auth()->user();
        
        if (!$user->isEnrolled($course->id)) {
            $user->courses()->attach($course->id);
        }
        
        return redirect()->route('dashboard')->with('success', "Você se matriculou no curso: {$course->title}!");
    }

    public function lessonPlayer(Course $course, Lesson $lesson)
    {
        $user = auth()->user();
        
        // Garante que o aluno está matriculado para assistir às aulas
        if (!$user->isEnrolled($course->id)) {
            return redirect()->route('dashboard')->with('error', 'Você precisa se matricular no curso primeiro.');
        }

        // Garante que a aula pertence ao curso
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $course->load('lessons');
        $progress = $user->courseProgress($course);
        $isCompleted = $user->hasCompletedLesson($lesson->id);

        // Verifica se todas as aulas foram concluídas
        $completedLessonsCount = $user->lessons()
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->count();
        $allLessonsCompleted = ($completedLessonsCount === $course->lessons->count());

        // Verifica se o curso já está marcado como concluído
        $courseCompleted = $user->courses()
            ->where('course_id', $course->id)
            ->whereNotNull('completed_at')
            ->exists();

        // Achar a próxima aula se houver
        $nextLesson = $course->lessons->where('order', '>', $lesson->order)->first();

        return view('student.course-player', compact('course', 'lesson', 'progress', 'isCompleted', 'allLessonsCompleted', 'nextLesson', 'courseCompleted'));
    }

    public function completeLesson(Request $request, Lesson $lesson)
    {
        $user = auth()->user();
        $courseId = $lesson->course_id;

        // Associa a aula ao usuário na pivot lesson_user
        $user->lessons()->syncWithoutDetaching([
            $lesson->id => ['completed_at' => now()]
        ]);

        $course = Course::with('lessons')->findOrFail($courseId);
        
        // Verifica se concluiu tudo
        $completedLessonsCount = $user->lessons()
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->count();
            
        $allCompleted = ($completedLessonsCount === $course->lessons->count());

        // Se concluiu todas as aulas e o curso NÃO exige prova, marca o curso como concluído
        if ($allCompleted && !$course->requires_quiz) {
            $user->courses()->updateExistingPivot($courseId, [
                'completed_at' => now()
            ]);
            $this->generateCertificate($user, $course);
        }

        // Acha a próxima aula para avançar automaticamente
        $nextLesson = $course->lessons->where('order', '>', $lesson->order)->first();

        if ($nextLesson) {
            return redirect()->route('courses.player', [$courseId, $nextLesson->id])
                ->with('success', 'Aula concluída! Avançando...');
        }

        return redirect()->route('courses.player', [$courseId, $lesson->id])
            ->with('success', 'Aula concluída!');
    }

    public function quiz(Course $course)
    {
        $user = auth()->user();

        if (!$user->isEnrolled($course->id)) {
            return redirect()->route('dashboard');
        }

        // Verifica se todas as aulas foram concluídas antes de permitir a prova
        $totalLessons = $course->lessons()->count();
        $completedLessons = $user->lessons()
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->count();

        if ($completedLessons < $totalLessons) {
            return redirect()->route('courses.player', [$course->id, $course->lessons()->first()->id])
                ->with('error', 'Você precisa concluir todas as aulas antes de fazer a prova.');
        }

        $quiz = Quiz::where('course_id', $course->id)->with('questions.options')->firstOrFail();

        return view('student.quiz', compact('course', 'quiz'));
    }

    public function submitQuiz(Request $request, Course $course)
    {
        $user = auth()->user();
        $quiz = Quiz::where('course_id', $course->id)->with('questions.options')->firstOrFail();
        
        $answers = $request->input('answers', []); // format: [question_id => option_id]
        
        $totalQuestions = $quiz->questions->count();
        if ($totalQuestions === 0) {
            return redirect()->route('dashboard');
        }

        $correctCount = 0;

        foreach ($quiz->questions as $question) {
            $selectedOptionId = $answers[$question->id] ?? null;
            if ($selectedOptionId) {
                $correctOption = $question->options->where('is_correct', true)->first();
                if ($correctOption && $correctOption->id == $selectedOptionId) {
                    $correctCount++;
                }
            }
        }

        $score = round(($correctCount / $totalQuestions) * 100);
        $passed = ($score >= $quiz->min_score);

        // Salva a tentativa
        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'score' => $score,
            'passed' => $passed,
        ]);

        if ($passed) {
            // Marca o curso como concluído na pivot course_user
            $user->courses()->updateExistingPivot($course->id, [
                'completed_at' => now()
            ]);
            $this->generateCertificate($user, $course);
        }

        return redirect()->route('courses.quiz.result', $course->id);
    }

    public function quizResult(Course $course)
    {
        $user = auth()->user();
        $quiz = Quiz::where('course_id', $course->id)->firstOrFail();
        
        // Pega a última tentativa
        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->latest()
            ->firstOrFail();

        return view('student.quiz-result', compact('course', 'attempt', 'quiz'));
    }

    public function showCertificate(Course $course)
    {
        $user = auth()->user();

        // Garante que o aluno está matriculado e concluiu o curso
        $enrollment = $user->courses()->where('course_id', $course->id)->first();
        
        if (!$enrollment || is_null($enrollment->pivot->completed_at)) {
            return redirect()->route('dashboard')->with('error', 'Você precisa concluir este curso primeiro para obter o certificado.');
        }

        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        $verificationUrl = route('certificates.verify', $certificate->verification_code);
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($verificationUrl);

        return view('student.certificate', compact('course', 'certificate', 'qrCodeUrl'));
    }

    private function generateCertificate($user, $course)
    {
        $exists = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if (!$exists) {
            $code = 'CERT-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
            
            while (Certificate::where('verification_code', $code)->exists()) {
                $code = 'CERT-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
            }

            Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'verification_code' => $code,
                'issued_at' => now(),
            ]);
        }
    }
}
