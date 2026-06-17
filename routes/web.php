<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EntityManagerController;
use App\Http\Controllers\PublicCertificateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/accept-invitation/{token}', [EntityManagerController::class, 'acceptInvitationShow'])->name('accept-invitation');
Route::post('/accept-invitation/{token}', [EntityManagerController::class, 'acceptInvitationStore'])->name('accept-invitation.store');

Route::middleware(['auth'])->group(function () {
    // Área do Aluno
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::post('/courses/{course}/enroll', [StudentController::class, 'enroll'])->name('courses.enroll');
    Route::get('/courses/{course}/lessons/{lesson}', [StudentController::class, 'lessonPlayer'])->name('courses.player');
    Route::post('/lessons/{lesson}/complete', [StudentController::class, 'completeLesson'])->name('lessons.complete');
    
    // Provas
    Route::get('/courses/{course}/quiz', [StudentController::class, 'quiz'])->name('courses.quiz');
    Route::post('/courses/{course}/quiz/submit', [StudentController::class, 'submitQuiz'])->name('courses.quiz.submit');
    Route::get('/courses/{course}/quiz/result', [StudentController::class, 'quizResult'])->name('courses.quiz.result');
    Route::get('/courses/{course}/certificate', [StudentController::class, 'showCertificate'])->name('courses.certificate');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Área do Instrutor (Protegido por auth e pelo middleware customizado 'instructor')
Route::middleware(['auth', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses/create', [InstructorController::class, 'createCourse'])->name('courses.create');
    Route::post('/courses', [InstructorController::class, 'storeCourse'])->name('courses.store');
    Route::get('/courses/{course}/edit', [InstructorController::class, 'editCourse'])->name('courses.edit');
    Route::put('/courses/{course}', [InstructorController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{course}', [InstructorController::class, 'destroyCourse'])->name('courses.destroy');
    Route::get('/courses/{course}/students', [InstructorController::class, 'studentsReport'])->name('courses.students');
    
    // Aulas
    Route::get('/courses/{course}/lessons/create', [InstructorController::class, 'createLesson'])->name('lessons.create');
    Route::post('/courses/{course}/lessons', [InstructorController::class, 'storeLesson'])->name('lessons.store');
    Route::post('/lessons/upload-image', [InstructorController::class, 'uploadLessonImage'])->name('lessons.upload-image');
    Route::delete('/lessons/{lesson}', [InstructorController::class, 'destroyLesson'])->name('lessons.destroy');
    
    // Prova
    Route::get('/courses/{course}/quiz', [InstructorController::class, 'editQuiz'])->name('quiz.edit');
    Route::post('/courses/{course}/quiz', [InstructorController::class, 'updateQuiz'])->name('quiz.update');
});

// Área do Administrador (Protegido por auth e pelo middleware 'admin')
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');
    Route::get('/users/{user}/edit', [AdminController::class, 'usersEdit'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'usersUpdate'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'usersDestroy'])->name('users.destroy');

    // Entidades (CRUD)
    Route::get('/entities', [AdminController::class, 'entitiesIndex'])->name('entities.index');
    Route::get('/entities/create', [AdminController::class, 'entitiesCreate'])->name('entities.create');
    Route::post('/entities', [AdminController::class, 'entitiesStore'])->name('entities.store');
    Route::get('/entities/{entity}/edit', [AdminController::class, 'entitiesEdit'])->name('entities.edit');
    Route::put('/entities/{entity}', [AdminController::class, 'entitiesUpdate'])->name('entities.update');
    Route::delete('/entities/{entity}', [AdminController::class, 'entitiesDestroy'])->name('entities.destroy');

    // Compartilhamento de Cursos
    Route::get('/courses/{course}/share', [AdminController::class, 'coursesShareShow'])->name('courses.share.show');
    Route::post('/courses/{course}/share', [AdminController::class, 'coursesShareUpdate'])->name('courses.share.update');
});

// Área do Gestor de Entidade (Protegido por auth e pelo middleware 'entity_manager')
Route::middleware(['auth', 'entity_manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [EntityManagerController::class, 'dashboard'])->name('dashboard');
    Route::get('/invite', [EntityManagerController::class, 'inviteShow'])->name('invite.show');
    Route::post('/invite', [EntityManagerController::class, 'inviteStore'])->name('invite.store');

    // Grupos (CRUD)
    Route::get('/groups', [EntityManagerController::class, 'groupsIndex'])->name('groups.index');
    Route::get('/groups/create', [EntityManagerController::class, 'groupsCreate'])->name('groups.create');
    Route::post('/groups', [EntityManagerController::class, 'groupsStore'])->name('groups.store');
    Route::get('/groups/{group}/edit', [EntityManagerController::class, 'groupsEdit'])->name('groups.edit');
    Route::put('/groups/{group}', [EntityManagerController::class, 'groupsUpdate'])->name('groups.update');
    Route::delete('/groups/{group}', [EntityManagerController::class, 'groupsDestroy'])->name('groups.destroy');

    // Trilhas (CRUD)
    Route::get('/trails', [EntityManagerController::class, 'trailsIndex'])->name('trails.index');
    Route::get('/trails/create', [EntityManagerController::class, 'trailsCreate'])->name('trails.create');
    Route::post('/trails', [EntityManagerController::class, 'trailsStore'])->name('trails.store');
    Route::get('/trails/{trail}/edit', [EntityManagerController::class, 'trailsEdit'])->name('trails.edit');
    Route::put('/trails/{trail}', [EntityManagerController::class, 'trailsUpdate'])->name('trails.update');
    Route::delete('/trails/{trail}', [EntityManagerController::class, 'trailsDestroy'])->name('trails.destroy');

    // Atribuição de Trilhas
    Route::get('/trails/{trail}/assign', [EntityManagerController::class, 'trailsAssignShow'])->name('trails.assign.show');
    Route::post('/trails/{trail}/assign', [EntityManagerController::class, 'trailsAssignStore'])->name('trails.assign.store');
});

Route::get('/certificates/verify/{code}', [PublicCertificateController::class, 'verify'])->name('certificates.verify');

require __DIR__.'/auth.php';
