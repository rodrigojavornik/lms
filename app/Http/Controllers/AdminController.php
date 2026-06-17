<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Certificate;
use App\Models\Entity;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_instructors' => User::where('is_instructor', true)->count(),
            'total_admins' => User::where('is_admin', true)->count(),
            'total_courses' => Course::count(),
            'total_lessons' => Lesson::count(),
            'total_certificates' => Certificate::count(),
            'total_entities' => Entity::count(),
        ];

        $courses = Course::with(['entity', 'sharedEntities'])->withCount('lessons')->get();

        return view('admin.dashboard', compact('stats', 'courses'));
    }

    public function usersIndex(Request $request)
    {
        $search = $request->input('search');

        $usersQuery = User::with('entity');

        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function usersEdit(User $user)
    {
        $entities = Entity::all();
        return view('admin.users.edit', compact('user', 'entities'));
    }

    public function usersUpdate(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'is_instructor' => 'nullable|boolean',
            'is_admin' => 'nullable|boolean',
            'is_entity_manager' => 'nullable|boolean',
            'entity_id' => 'nullable|exists:entities,id',
        ]);

        $isInstructor = $request->has('is_instructor');
        $isAdmin = $request->has('is_admin');
        $isEntityManager = $request->has('is_entity_manager');

        // Trava de segurança: não permitir que o admin logado remova seu próprio acesso admin
        if ($user->id === auth()->id() && !$isAdmin) {
            return redirect()->back()->with('error', 'Você não pode revogar seus próprios privilégios de administrador.');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_instructor' => $isInstructor,
            'is_admin' => $isAdmin,
            'is_entity_manager' => $isEntityManager,
            'entity_id' => $request->entity_id ?: null,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function usersDestroy(User $user)
    {
        // Trava de segurança: não permitir a autoexclusão
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Você não pode excluir a sua própria conta.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuário excluído com sucesso!');
    }

    // --- CRUD de Entidades ---

    public function entitiesIndex(Request $request)
    {
        $search = $request->input('search');

        $entitiesQuery = Entity::query();

        if ($search) {
            $entitiesQuery->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
        }

        $entities = $entitiesQuery->paginate(10)->withQueryString();

        return view('admin.entities.index', compact('entities', 'search'));
    }

    public function entitiesCreate()
    {
        return view('admin.entities.create');
    }

    public function entitiesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:entities,name',
            'description' => 'nullable|string',
        ]);

        Entity::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.entities.index')->with('success', 'Entidade cadastrada com sucesso!');
    }

    public function entitiesEdit(Entity $entity)
    {
        return view('admin.entities.edit', compact('entity'));
    }

    public function entitiesUpdate(Request $request, Entity $entity)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:entities,name,' . $entity->id,
            'description' => 'nullable|string',
        ]);

        $entity->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.entities.index')->with('success', 'Entidade atualizada com sucesso!');
    }

    public function entitiesDestroy(Entity $entity)
    {
        // Se a entidade for deletada, o BD tratará conforme as regras de migration.
        $entity->delete();

        return redirect()->route('admin.entities.index')->with('success', 'Entidade excluída com sucesso!');
    }

    // --- Compartilhamento de Cursos ---

    public function coursesShareShow(Course $course)
    {
        $entities = Entity::where('id', '!=', $course->entity_id)->get();
        $sharedEntityIds = $course->sharedEntities->pluck('id')->toArray();

        return view('admin.courses.share', compact('course', 'entities', 'sharedEntityIds'));
    }

    public function coursesShareUpdate(Request $request, Course $course)
    {
        $request->validate([
            'entities' => 'nullable|array',
            'entities.*' => 'exists:entities,id',
        ]);

        $course->sharedEntities()->sync($request->input('entities', []));

        return redirect()->route('admin.dashboard')->with('success', "Compartilhamento do curso '{$course->title}' atualizado com sucesso!");
    }
}
