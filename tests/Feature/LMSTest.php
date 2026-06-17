<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Course;
use Tests\TestCase;

class LMSTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_student_can_access_dashboard_and_see_courses(): void
    {
        $student = User::factory()->create([
            'is_instructor' => false,
        ]);

        $course = Course::create([
            'title' => 'Curso Teste Laravel',
            'description' => 'Aprenda testes no Laravel.',
            'requires_quiz' => false,
        ]);

        $response = $this->actingAs($student)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Curso Teste Laravel');
        $response->assertSee('Matricular-se Gratuitamente');
    }

    public function test_student_can_enroll_in_a_course(): void
    {
        $student = User::factory()->create([
            'is_instructor' => false,
        ]);

        $course = Course::create([
            'title' => 'Curso Teste Laravel',
            'description' => 'Aprenda testes no Laravel.',
            'requires_quiz' => false,
        ]);

        $response = $this->actingAs($student)->post(route('courses.enroll', $course));

        $response->assertRedirect('/dashboard');
        $this->assertTrue($student->isEnrolled($course->id));
    }

    public function test_non_instructor_cannot_access_instructor_panel(): void
    {
        $student = User::factory()->create([
            'is_instructor' => false,
        ]);

        $response = $this->actingAs($student)->get('/instructor/dashboard');

        $response->assertRedirect('/dashboard');
    }

    public function test_instructor_can_access_instructor_panel(): void
    {
        $instructor = User::factory()->create([
            'is_instructor' => true,
        ]);

        $response = $this->actingAs($instructor)->get('/instructor/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Painel do Instrutor');
    }

    public function test_instructor_can_view_students_report(): void
    {
        $instructor = User::factory()->create([
            'is_instructor' => true,
        ]);

        $studentA = User::factory()->create([
            'name' => 'Aluno Concluido',
            'is_instructor' => false,
        ]);

        $studentB = User::factory()->create([
            'name' => 'Aluno Em Andamento',
            'is_instructor' => false,
        ]);

        $course = Course::create([
            'title' => 'Curso Teste de Relatorio',
            'description' => 'Aprenda relatórios.',
            'requires_quiz' => false,
        ]);

        // Aluno A matricula e conclui
        $studentA->courses()->attach($course->id, ['completed_at' => now()]);

        // Aluno B apenas matricula
        $studentB->courses()->attach($course->id);

        $response = $this->actingAs($instructor)->get(route('instructor.courses.students', $course));

        $response->assertStatus(200);
        $response->assertSee('Aluno Concluido');
        $response->assertSee('Concluído em');
        $response->assertSee('Aluno Em Andamento');
        $response->assertSee('Em Andamento');
        $response->assertSee('Desempenho dos Estudantes');
    }

    public function test_non_instructor_cannot_view_students_report(): void
    {
        $student = User::factory()->create([
            'is_instructor' => false,
        ]);

        $course = Course::create([
            'title' => 'Curso Teste de Relatorio',
            'description' => 'Aprenda relatórios.',
            'requires_quiz' => false,
        ]);

        $response = $this->actingAs($student)->get(route('instructor.courses.students', $course));

        $response->assertRedirect('/dashboard');
    }

    public function test_certificate_is_generated_on_course_completion(): void
    {
        $student = User::factory()->create([
            'is_instructor' => false,
        ]);

        $course = Course::create([
            'title' => 'Curso de Certificados',
            'description' => 'Aprenda a emitir certificados.',
            'requires_quiz' => false,
        ]);

        $lesson = $course->lessons()->create([
            'title' => 'Aula Unica',
            'content' => 'Conteudo',
            'order' => 1,
        ]);

        // Matricula
        $student->courses()->attach($course->id);

        // Completa a aula (isso deve gerar o certificado pois o curso nao requer prova)
        $response = $this->actingAs($student)->post(route('lessons.complete', $lesson));

        $response->assertRedirect();
        
        // Verifica banco
        $this->assertDatabaseHas('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        // Tenta ver o certificado
        $certResponse = $this->actingAs($student)->get(route('courses.certificate', $course));
        $certResponse->assertStatus(200);
        $certResponse->assertSee('Certificado de Conclusão');
        $certResponse->assertSee('Curso de Certificados');
    }

    public function test_cannot_view_other_users_certificate(): void
    {
        $studentA = User::factory()->create(['is_instructor' => false]);
        $studentB = User::factory()->create(['is_instructor' => false]);

        $course = Course::create([
            'title' => 'Curso de Certificados',
            'description' => 'Aprenda a emitir certificados.',
            'requires_quiz' => false,
        ]);

        $studentA->courses()->attach($course->id);
        
        // Aluno B tenta ver o certificado sem sequer estar matriculado ou ter concluido
        $response = $this->actingAs($studentB)->get(route('courses.certificate', $course));
        $response->assertRedirect('/dashboard');
    }

    public function test_public_verification_endpoint(): void
    {
        $student = User::factory()->create([
            'name' => 'Aluno do Certificado Publico',
            'is_instructor' => false,
        ]);

        $course = Course::create([
            'title' => 'Curso de Certificados Publico',
            'description' => 'Aprenda a emitir certificados.',
            'requires_quiz' => false,
        ]);

        $certificate = \App\Models\Certificate::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'verification_code' => 'CERT-TEST-1234',
            'issued_at' => now(),
        ]);

        // Acessa sem logar (publicamente)
        $response = $this->get(route('certificates.verify', 'CERT-TEST-1234'));
        $response->assertStatus(200);
        $response->assertSee('Certificado Autêntico');
        $response->assertSee('Aluno do Certificado Publico');

        // Código inválido
        $invalidResponse = $this->get(route('certificates.verify', 'CERT-INVALID-999'));
        $invalidResponse->assertStatus(200);
        $invalidResponse->assertSee('Certificado Não Encontrado');
    }

    public function test_player_shows_certificate_button_when_course_completed(): void
    {
        $student = User::factory()->create(['is_instructor' => false]);
        
        $course = Course::create([
            'title' => 'Curso de Teste Concluido',
            'description' => 'Aprenda e conclua.',
            'requires_quiz' => true,
        ]);

        $lesson = $course->lessons()->create([
            'title' => 'Aula 1',
            'content' => 'Conteudo',
            'order' => 1,
        ]);

        // Matricula o aluno e marca o curso como concluído
        $student->courses()->attach($course->id, ['completed_at' => now()]);
        
        // Completa a aula
        $student->lessons()->attach($lesson->id, ['completed_at' => now()]);

        // Acessa o player
        $response = $this->actingAs($student)->get(route('courses.player', [$course->id, $lesson->id]));

        $response->assertStatus(200);
        
        // Deve ver a conclusão e o certificado
        $response->assertSee('Curso Concluído!');
        $response->assertSee('Visualizar Certificado');
        
        // Não deve ver a chamada para a prova
        $response->assertDontSee('Fazer a Prova do Curso');
        $response->assertDontSee('Para concluir este curso, você precisa realizar e passar na prova avaliativa.');
    }

    public function test_instructor_can_access_create_lesson_page(): void
    {
        $instructor = User::factory()->create(['is_instructor' => true]);
        
        $course = Course::create([
            'title' => 'Curso de Teste Criar Aula',
            'description' => 'Descricao',
            'requires_quiz' => false,
        ]);

        $response = $this->actingAs($instructor)->get(route('instructor.lessons.create', $course));

        $response->assertStatus(200);
        $response->assertSee('Registrar Aula');
        $response->assertSee('Curso de Teste Criar Aula');
    }

    public function test_instructor_can_upload_lesson_image(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $instructor = User::factory()->create(['is_instructor' => true]);

        $file = \Illuminate\Http\UploadedFile::fake()->create('lesson_image.png', 100, 'image/png');

        $response = $this->actingAs($instructor)->post(route('instructor.lessons.upload-image'), [
            'image' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['url']);

        // Verifica se o arquivo foi armazenado no disco public
        $json = $response->json();
        $path = str_replace(asset('storage/'), '', $json['url']);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($path);
    }

    public function test_non_admin_cannot_access_admin_panel(): void
    {
        $student = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($student)->get(route('admin.dashboard'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Painel Administrativo');
        $response->assertSee('Visão Geral do Administrador');
    }

    public function test_admin_can_promote_user_to_instructor_and_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $student = User::factory()->create(['is_instructor' => false, 'is_admin' => false]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $student), [
            'name' => 'Aluno Promovido',
            'email' => $student->email,
            'is_instructor' => '1',
            'is_admin' => '1',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        
        $student->refresh();
        $this->assertTrue($student->is_instructor);
        $this->assertTrue($student->is_admin);
        $this->assertEquals('Aluno Promovido', $student->name);
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'is_instructor' => '1',
            // is_admin omitido
        ]);

        $response->assertSessionHas('error');
        $admin->refresh();
        $this->assertTrue($admin->is_admin);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_manage_entities(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        // Ver index
        $response = $this->actingAs($admin)->get(route('admin.entities.index'));
        $response->assertStatus(200);
        $response->assertSee('Entidades Cadastradas');

        // Criar entidade
        $response = $this->actingAs($admin)->post(route('admin.entities.store'), [
            'name' => 'Entidade de Teste Nova',
            'description' => 'Descricao de teste',
        ]);
        $response->assertRedirect(route('admin.entities.index'));
        $this->assertDatabaseHas('entities', ['name' => 'Entidade de Teste Nova']);
    }

    public function test_non_admin_cannot_manage_entities(): void
    {
        $student = User::factory()->create(['is_admin' => false, 'is_entity_manager' => true]);
        
        $response = $this->actingAs($student)->get(route('admin.entities.index'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_manager_can_access_manager_dashboard(): void
    {
        $entity = \App\Models\Entity::create(['name' => 'Entidade do Gestor']);
        $manager = User::factory()->create([
            'is_admin' => false,
            'is_entity_manager' => true,
            'entity_id' => $entity->id,
        ]);

        $response = $this->actingAs($manager)->get(route('manager.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Painel do Gestor de Entidade');
    }

    public function test_non_manager_cannot_access_manager_dashboard(): void
    {
        $student = User::factory()->create(['is_admin' => false, 'is_entity_manager' => false]);
        
        $response = $this->actingAs($student)->get(route('manager.dashboard'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_manager_can_manage_groups_and_trails(): void
    {
        $entity = \App\Models\Entity::create(['name' => 'Entidade Central']);
        $manager = User::factory()->create([
            'is_admin' => false,
            'is_entity_manager' => true,
            'entity_id' => $entity->id,
        ]);
        $collab = User::factory()->create([
            'is_admin' => false,
            'entity_id' => $entity->id,
        ]);
        $course = Course::create([
            'title' => 'Curso da Trilha',
            'entity_id' => $entity->id,
        ]);

        // Criar grupo
        $response = $this->actingAs($manager)->post(route('manager.groups.store'), [
            'name' => 'Grupo Foco',
            'users' => [$collab->id],
        ]);
        $response->assertRedirect(route('manager.groups.index'));
        $this->assertDatabaseHas('groups', ['name' => 'Grupo Foco']);
        $group = \App\Models\Group::where('name', 'Grupo Foco')->first();
        $this->assertTrue($group->users->contains($collab->id));

        // Criar trilha
        $response = $this->actingAs($manager)->post(route('manager.trails.store'), [
            'name' => 'Trilha Foco',
            'description' => 'Trilha de onboarding',
            'courses' => [$course->id],
            'positions' => [$course->id => 1],
        ]);
        $response->assertRedirect(route('manager.trails.index'));
        $this->assertDatabaseHas('trails', ['name' => 'Trilha Foco']);
        $trail = \App\Models\Trail::where('name', 'Trilha Foco')->first();

        // Atribuir trilha ao grupo e colab
        $response = $this->actingAs($manager)->post(route('manager.trails.assign.store', $trail), [
            'users' => [$collab->id],
            'groups' => [$group->id],
        ]);
        $response->assertRedirect(route('manager.trails.index'));
        $trail->refresh();
        $this->assertTrue($trail->users->contains($collab->id));
        $this->assertTrue($trail->groups->contains($group->id));
    }

    public function test_student_sees_only_own_entity_or_shared_courses(): void
    {
        $entityA = \App\Models\Entity::create(['name' => 'Entidade A']);
        $entityB = \App\Models\Entity::create(['name' => 'Entidade B']);

        $student = User::factory()->create(['entity_id' => $entityA->id, 'is_instructor' => false]);

        $courseOwn = Course::create(['title' => 'Curso Proprio', 'entity_id' => $entityA->id]);
        $courseShared = Course::create(['title' => 'Curso Compartilhado', 'entity_id' => $entityB->id]);
        $courseShared->sharedEntities()->attach($entityA->id);
        $coursePrivate = Course::create(['title' => 'Curso Privado Alheio', 'entity_id' => $entityB->id]);

        $response = $this->actingAs($student)->get(route('dashboard'));
        $response->assertStatus(200);

        // Deve ver o próprio e o compartilhado
        $response->assertSee('Curso Proprio');
        $response->assertSee('Curso Compartilhado');

        // Não deve ver o privado da outra entidade
        $response->assertDontSee('Curso Privado Alheio');
    }

    public function test_instructor_courses_restricted_by_entity(): void
    {
        $entityA = \App\Models\Entity::create(['name' => 'Entidade A']);
        $entityB = \App\Models\Entity::create(['name' => 'Entidade B']);

        $instructor = User::factory()->create(['entity_id' => $entityA->id, 'is_instructor' => true]);

        // Curso da própria entidade
        $courseA = Course::create(['title' => 'Curso de A', 'entity_id' => $entityA->id]);
        // Curso de outra entidade
        $courseB = Course::create(['title' => 'Curso de B', 'entity_id' => $entityB->id]);

        $response = $this->actingAs($instructor)->get(route('instructor.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Curso de A');
        $response->assertDontSee('Curso de B');

        // Criar novo curso
        $response = $this->actingAs($instructor)->post(route('instructor.courses.store'), [
            'title' => 'Novo Curso de A',
            'description' => 'Desc',
        ]);
        $response->assertRedirect(route('instructor.dashboard'));

        $this->assertDatabaseHas('courses', [
            'title' => 'Novo Curso de A',
            'entity_id' => $entityA->id
        ]);
    }

    public function test_manager_can_invite_members_to_their_entity(): void
    {
        $entity = \App\Models\Entity::create(['name' => 'Entidade do Gestor Teste']);
        $manager = User::factory()->create([
            'is_admin' => false,
            'is_entity_manager' => true,
            'entity_id' => $entity->id,
        ]);

        // Ver tela de convite
        $response = $this->actingAs($manager)->get(route('manager.invite.show'));
        $response->assertStatus(200);
        $response->assertSee('Convidar Membro para: Entidade do Gestor Teste');

        // Postar convite (sem senha)
        $postResponse = $this->actingAs($manager)->post(route('manager.invite.store'), [
            'name' => 'Novo Colaborador Convidado',
            'email' => 'convidado@teste.com',
            'role' => 'collaborator',
        ]);

        $postResponse->assertRedirect(route('manager.dashboard'));
        $postResponse->assertSessionHasNoErrors();
        $postResponse->assertSessionHas('invitation_link');

        // Verificar banco de dados
        $user = User::where('email', 'convidado@teste.com')->firstOrFail();
        $this->assertEquals($entity->id, $user->entity_id);
        $this->assertFalse($user->is_instructor);
        $this->assertNotNull($user->invitation_token);

        // Acessar a tela pública de definição de senha
        $invitationLink = session('invitation_link');
        $acceptResponse = $this->get($invitationLink);
        $acceptResponse->assertStatus(200);
        $acceptResponse->assertSee('Defina sua Senha Inicial');
        $acceptResponse->assertSee('Novo Colaborador Convidado');

        // Enviar a nova senha
        $passwordResponse = $this->post(route('accept-invitation.store', $user->invitation_token), [
            'password' => 'novasenha123',
            'password_confirmation' => 'novasenha123',
        ]);

        $passwordResponse->assertRedirect(route('dashboard'));
        
        $user->refresh();
        $this->assertNull($user->invitation_token);
        $this->assertTrue(auth()->check());
        $this->assertEquals($user->id, auth()->id());
    }

    public function test_non_manager_cannot_invite_members(): void
    {
        $student = User::factory()->create([
            'is_admin' => false,
            'is_entity_manager' => false,
        ]);

        $response = $this->actingAs($student)->get(route('manager.invite.show'));
        $response->assertRedirect(route('dashboard'));

        $postResponse = $this->actingAs($student)->post(route('manager.invite.store'), [
            'name' => 'Invasor',
            'email' => 'invasor@teste.com',
            'role' => 'collaborator',
        ]);
        $postResponse->assertRedirect(route('dashboard'));
    }
}
