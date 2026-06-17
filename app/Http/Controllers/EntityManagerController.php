<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Trail;
use App\Models\Course;

class EntityManagerController extends Controller
{
    /**
     * Dashboard do Gestor.
     * Lista colaboradores, grupos, trilhas e o progresso de cada um.
     */
    public function dashboard()
    {
        $manager = auth()->user();
        $entityId = $manager->entity_id;

        if (!$entityId) {
            return redirect()->route('dashboard')->with('error', 'Você não está vinculado a nenhuma entidade.');
        }

        // Estatísticas
        $stats = [
            'total_users' => User::where('entity_id', $entityId)->where('is_admin', false)->count(),
            'total_groups' => Group::where('entity_id', $entityId)->count(),
            'total_trails' => Trail::where('entity_id', $entityId)->count(),
        ];

        // Listar todos os colaboradores da entidade (exceto o próprio gestor e admins)
        $collaborators = User::where('entity_id', $entityId)
            ->where('is_admin', false)
            ->where('id', '!=', $manager->id)
            ->with(['courses', 'groups', 'trails'])
            ->get();

        // Encontrar as trilhas de cada colaborador (diretas + via grupos)
        foreach ($collaborators as $collab) {
            $groupTrailIds = Trail::whereHas('groups', function ($q) use ($collab) {
                $q->whereIn('groups.id', $collab->groups->pluck('id'));
            })->pluck('id');

            $allTrailIds = $collab->trails->pluck('id')->merge($groupTrailIds)->unique();
            $collab->assigned_trails = Trail::whereIn('id', $allTrailIds)->with('courses')->get();
        }

        return view('manager.dashboard', compact('stats', 'collaborators'));
    }

    // --- CRUD de Grupos ---

    public function groupsIndex()
    {
        $entityId = auth()->user()->entity_id;
        $groups = Group::where('entity_id', $entityId)->withCount('users')->paginate(10);

        return view('manager.groups.index', compact('groups'));
    }

    public function groupsCreate()
    {
        $entityId = auth()->user()->entity_id;
        $users = User::where('entity_id', $entityId)
            ->where('is_admin', false)
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();

        return view('manager.groups.create', compact('users'));
    }

    public function groupsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $entityId = auth()->user()->entity_id;

        $group = Group::create([
            'name' => $request->name,
            'entity_id' => $entityId,
        ]);

        if ($request->has('users')) {
            $group->users()->sync($request->users);
        }

        return redirect()->route('manager.groups.index')->with('success', 'Grupo criado com sucesso!');
    }

    public function groupsEdit(Group $group)
    {
        // Segurança: verificar se o grupo pertence à mesma entidade
        if ($group->entity_id !== auth()->user()->entity_id) {
            abort(403, 'Acesso negado.');
        }

        $entityId = auth()->user()->entity_id;
        $users = User::where('entity_id', $entityId)
            ->where('is_admin', false)
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();

        $groupUserIds = $group->users->pluck('id')->toArray();

        return view('manager.groups.edit', compact('group', 'users', 'groupUserIds'));
    }

    public function groupsUpdate(Request $request, Group $group)
    {
        if ($group->entity_id !== auth()->user()->entity_id) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $group->update([
            'name' => $request->name,
        ]);

        $group->users()->sync($request->input('users', []));

        return redirect()->route('manager.groups.index')->with('success', 'Grupo atualizado com sucesso!');
    }

    public function groupsDestroy(Group $group)
    {
        if ($group->entity_id !== auth()->user()->entity_id) {
            abort(403, 'Acesso negado.');
        }

        $group->delete();

        return redirect()->route('manager.groups.index')->with('success', 'Grupo excluído com sucesso!');
    }

    // --- CRUD de Trilhas ---

    public function trailsIndex()
    {
        $entityId = auth()->user()->entity_id;
        $trails = Trail::where('entity_id', $entityId)->withCount('courses')->paginate(10);

        return view('manager.trails.index', compact('trails'));
    }

    public function trailsCreate()
    {
        $entityId = auth()->user()->entity_id;

        // Cursos da própria entidade + Cursos globais + Cursos compartilhados especificamente com ela
        $courses = Course::whereNull('entity_id')
            ->orWhere('entity_id', $entityId)
            ->orWhereHas('sharedEntities', function ($q) use ($entityId) {
                $q->where('entities.id', $entityId);
            })
            ->orderBy('title')
            ->get();

        return view('manager.trails.create', compact('courses'));
    }

    public function trailsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
            'positions' => 'nullable|array',
        ]);

        $entityId = auth()->user()->entity_id;

        $trail = Trail::create([
            'name' => $request->name,
            'description' => $request->description,
            'entity_id' => $entityId,
        ]);

        if ($request->has('courses')) {
            $coursesData = [];
            $positions = $request->input('positions', []);
            foreach ($request->courses as $courseId) {
                $coursesData[$courseId] = [
                    'position' => isset($positions[$courseId]) ? (int)$positions[$courseId] : 1
                ];
            }
            $trail->courses()->sync($coursesData);
        }

        return redirect()->route('manager.trails.index')->with('success', 'Trilha criada com sucesso!');
    }

    public function trailsEdit(Trail $trail)
    {
        if ($trail->entity_id !== auth()->user()->entity_id) {
            abort(403, 'Acesso negado.');
        }

        $entityId = auth()->user()->entity_id;

        $courses = Course::whereNull('entity_id')
            ->orWhere('entity_id', $entityId)
            ->orWhereHas('sharedEntities', function ($q) use ($entityId) {
                $q->where('entities.id', $entityId);
            })
            ->orderBy('title')
            ->get();

        $trailCourses = $trail->courses()->withPivot('position')->get();
        $selectedCourseIds = $trailCourses->pluck('id')->toArray();
        $coursePositions = $trailCourses->pluck('pivot.position', 'id')->toArray();

        return view('manager.trails.edit', compact('trail', 'courses', 'selectedCourseIds', 'coursePositions'));
    }

    public function trailsUpdate(Request $request, Trail $trail)
    {
        if ($trail->entity_id !== auth()->user()->entity_id) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
            'positions' => 'nullable|array',
        ]);

        $trail->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $coursesData = [];
        if ($request->has('courses')) {
            $positions = $request->input('positions', []);
            foreach ($request->courses as $courseId) {
                $coursesData[$courseId] = [
                    'position' => isset($positions[$courseId]) ? (int)$positions[$courseId] : 1
                ];
            }
        }
        $trail->courses()->sync($coursesData);

        return redirect()->route('manager.trails.index')->with('success', 'Trilha atualizada com sucesso!');
    }

    public function trailsDestroy(Trail $trail)
    {
        if ($trail->entity_id !== auth()->user()->entity_id) {
            abort(403, 'Acesso negado.');
        }

        $trail->delete();

        return redirect()->route('manager.trails.index')->with('success', 'Trilha excluída com sucesso!');
    }

    // --- Atribuição de Trilhas ---

    public function trailsAssignShow(Trail $trail)
    {
        if ($trail->entity_id !== auth()->user()->entity_id) {
            abort(403, 'Acesso negado.');
        }

        $entityId = auth()->user()->entity_id;

        // Usuários da entidade (exceto gestor/admin)
        $users = User::where('entity_id', $entityId)
            ->where('is_admin', false)
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();

        // Grupos da entidade
        $groups = Group::where('entity_id', $entityId)->orderBy('name')->get();

        $assignedUserIds = $trail->users->pluck('id')->toArray();
        $assignedGroupIds = $trail->groups->pluck('id')->toArray();

        return view('manager.trails.assign', compact('trail', 'users', 'groups', 'assignedUserIds', 'assignedGroupIds'));
    }

    public function trailsAssignStore(Request $request, Trail $trail)
    {
        if ($trail->entity_id !== auth()->user()->entity_id) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'groups' => 'nullable|array',
            'groups.*' => 'exists:groups,id',
        ]);

        // Sincroniza usuários
        $trail->users()->sync($request->input('users', []));

        // Sincroniza grupos
        $trail->groups()->sync($request->input('groups', []));

        return redirect()->route('manager.trails.index')->with('success', 'Atribuições de trilha atualizadas com sucesso!');
    }

    /**
     * Exibe a tela de convite/cadastro de membro.
     */
    public function inviteShow()
    {
        $manager = auth()->user();
        $entity = $manager->entity;

        if (!$manager->entity_id) {
            return redirect()->route('dashboard')->with('error', 'Você não está vinculado a nenhuma entidade.');
        }

        return view('manager.invite', compact('entity'));
    }

    /**
     * Salva o membro convidado/cadastrado.
     */
    public function inviteStore(Request $request)
    {
        $manager = auth()->user();
        $entityId = $manager->entity_id;

        if (!$entityId) {
            return redirect()->route('dashboard')->with('error', 'Você não está vinculado a nenhuma entidade.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => 'required|in:collaborator,instructor',
        ]);

        $isInstructor = $request->role === 'instructor';
        $token = \Illuminate\Support\Str::random(40);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
            'entity_id' => $entityId,
            'is_instructor' => $isInstructor,
            'is_entity_manager' => false,
            'is_admin' => false,
            'invitation_token' => $token,
        ]);

        $invitationLink = route('accept-invitation', ['token' => $token]);

        return redirect()->route('manager.dashboard')->with('success', "Membro '{$request->name}' cadastrado com sucesso!")
            ->with('invitation_link', $invitationLink)
            ->with('invited_name', $request->name);
    }

    /**
     * Exibe a tela de aceitação de convite e definição de senha.
     */
    public function acceptInvitationShow($token)
    {
        $user = User::where('invitation_token', $token)->firstOrFail();
        return view('auth.accept-invitation', compact('user', 'token'));
    }

    /**
     * Salva a senha definida pelo usuário convidado.
     */
    public function acceptInvitationStore(Request $request, $token)
    {
        $user = User::where('invitation_token', $token)->firstOrFail();

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'invitation_token' => null,
        ]);

        // Autentica o usuário no sistema
        auth()->login($user);

        return redirect()->route('dashboard')->with('success', 'Sua senha foi definida com sucesso! Bem-vindo ao LMS.');
    }
}
