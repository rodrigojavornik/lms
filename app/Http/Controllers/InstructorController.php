<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;

class InstructorController extends Controller
{
    private function checkCourseOwnership(Course $course)
    {
        $user = auth()->user();
        if (!$user->is_admin && $course->entity_id !== $user->entity_id) {
            abort(403, 'Você não tem permissão para acessar ou gerenciar este curso.');
        }
    }

    public function dashboard()
    {
        $user = auth()->user();
        
        // Admins sem entidade cadastrada podem ver tudo, caso contrário filtra por entidade do instrutor
        if ($user->is_admin && !$user->entity_id) {
            $courses = Course::withCount('lessons')->get();
        } else {
            $courses = Course::where('entity_id', $user->entity_id)->withCount('lessons')->get();
        }

        return view('instructor.dashboard', compact('courses'));
    }

    public function createCourse()
    {
        return view('instructor.course-create');
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requires_quiz' => 'nullable|boolean',
        ]);

        Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'requires_quiz' => $request->has('requires_quiz'),
            'entity_id' => auth()->user()->entity_id,
        ]);

        return redirect()->route('instructor.dashboard')->with('success', 'Curso criado com sucesso!');
    }

    public function editCourse(Course $course)
    {
        $this->checkCourseOwnership($course);
        $course->load('lessons');
        return view('instructor.course-edit', compact('course'));
    }

    public function updateCourse(Request $request, Course $course)
    {
        $this->checkCourseOwnership($course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requires_quiz' => 'nullable|boolean',
        ]);

        $course->update([
            'title' => $request->title,
            'description' => $request->description,
            'requires_quiz' => $request->has('requires_quiz'),
        ]);

        return redirect()->route('instructor.dashboard')->with('success', 'Curso atualizado com sucesso!');
    }

    public function destroyCourse(Course $course)
    {
        $this->checkCourseOwnership($course);
        $course->delete();
        return redirect()->route('instructor.dashboard')->with('success', 'Curso excluído com sucesso!');
    }

    public function storeLesson(Request $request, Course $course)
    {
        $this->checkCourseOwnership($course);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|string|url',
        ]);

        // Determina a ordem
        $order = $course->lessons()->count() + 1;

        // Limpa/Normaliza URL do YouTube de "watch?v=" para "embed/"
        $videoUrl = $request->video_url;
        if ($videoUrl && str_contains($videoUrl, 'youtube.com/watch?v=')) {
            $parts = parse_url($videoUrl);
            parse_str($parts['query'], $query);
            if (isset($query['v'])) {
                $videoUrl = "https://www.youtube.com/embed/" . $query['v'];
            }
        } elseif ($videoUrl && str_contains($videoUrl, 'youtu.be/')) {
            $parts = explode('youtu.be/', $videoUrl);
            $id = explode('?', $parts[1])[0];
            $videoUrl = "https://www.youtube.com/embed/" . $id;
        }

        $course->lessons()->create([
            'title' => $request->title,
            'content' => $request->content,
            'video_url' => $videoUrl,
            'order' => $order,
        ]);

        return redirect()->route('instructor.courses.edit', $course)->with('success', 'Aula adicionada com sucesso!');
    }

    public function createLesson(Course $course)
    {
        $this->checkCourseOwnership($course);
        return view('instructor.lesson-create', compact('course'));
    }

    public function uploadLessonImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('lessons', 'public');
            return response()->json([
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'Upload falhou.'], 400);
    }

    public function destroyLesson(Lesson $lesson)
    {
        $course = $lesson->course;
        $this->checkCourseOwnership($course);

        $courseId = $lesson->course_id;
        $lesson->delete();
        return redirect()->route('instructor.courses.edit', $courseId)->with('success', 'Aula excluída com sucesso!');
    }

    public function editQuiz(Course $course)
    {
        $this->checkCourseOwnership($course);

        $quiz = Quiz::firstOrCreate(
            ['course_id' => $course->id],
            ['min_score' => 70]
        );

        $quiz->load('questions.options');

        return view('instructor.quiz-edit', compact('course', 'quiz'));
    }

    public function updateQuiz(Request $request, Course $course)
    {
        $this->checkCourseOwnership($course);

        $request->validate([
            'min_score' => 'required|integer|min:0|max:100',
            'questions' => 'nullable|array',
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|string',
            'questions.*.correct_index' => 'required|integer',
        ]);

        $quiz = Quiz::updateOrCreate(
            ['course_id' => $course->id],
            ['min_score' => $request->min_score]
        );

        // Remove perguntas antigas para recriar
        $quiz->questions()->delete();

        if ($request->questions) {
            foreach ($request->questions as $qData) {
                $question = $quiz->questions()->create([
                    'question_text' => $qData['text'],
                ]);

                foreach ($qData['options'] as $oIdx => $oData) {
                    $question->options()->create([
                        'option_text' => $oData['text'],
                        'is_correct' => ($oIdx == $qData['correct_index']),
                    ]);
                }
            }
        }

        return redirect()->route('instructor.courses.edit', $course)->with('success', 'Prova configurada com sucesso!');
    }

    public function studentsReport(Course $course)
    {
        $this->checkCourseOwnership($course);

        // Tenta encontrar o quiz caso o curso o exija
        $quizId = $course->requires_quiz && $course->quiz ? $course->quiz->id : null;

        $course->load(['users.quizAttempts' => function ($query) use ($quizId) {
            if ($quizId) {
                $query->where('quiz_id', $quizId)->latest();
            }
        }]);

        $students = $course->users;

        return view('instructor.course-students', compact('course', 'students'));
    }
}
