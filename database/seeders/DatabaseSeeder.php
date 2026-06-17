<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entity;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\Group;
use App\Models\Trail;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Criar Entidades
        $entidadeA = Entity::create([
            'name' => 'Entidade A (Tecnologia)',
            'description' => 'Empresa focada em desenvolvimento de software e inovação tecnológica.',
        ]);

        $entidadeB = Entity::create([
            'name' => 'Entidade B (Design)',
            'description' => 'Estúdio criativo de UX/UI, branding e design moderno.',
        ]);

        // 2. Criar Usuários
        // Admin Geral
        $admin = User::create([
            'name' => 'Admin Geral',
            'email' => 'admin@lms.com',
            'password' => Hash::make('password'),
            'is_instructor' => true,
            'is_admin' => true,
            'is_entity_manager' => false,
            'entity_id' => null,
        ]);

        // Gestor da Entidade A
        $gestorA = User::create([
            'name' => 'Gestor Tecnologia',
            'email' => 'gestor_a@lms.com',
            'password' => Hash::make('password'),
            'is_instructor' => false,
            'is_admin' => false,
            'is_entity_manager' => true,
            'entity_id' => $entidadeA->id,
        ]);

        // Gestor da Entidade B
        $gestorB = User::create([
            'name' => 'Gestor Design',
            'email' => 'gestor_b@lms.com',
            'password' => Hash::make('password'),
            'is_instructor' => false,
            'is_admin' => false,
            'is_entity_manager' => true,
            'entity_id' => $entidadeB->id,
        ]);

        // Instrutor da Entidade A
        $instrutorA = User::create([
            'name' => 'Instrutor Tech A',
            'email' => 'instrutor_a@lms.com',
            'password' => Hash::make('password'),
            'is_instructor' => true,
            'is_admin' => false,
            'is_entity_manager' => false,
            'entity_id' => $entidadeA->id,
        ]);

        // Instrutor da Entidade B
        $instrutorB = User::create([
            'name' => 'Instrutor Design B',
            'email' => 'instrutor_b@lms.com',
            'password' => Hash::make('password'),
            'is_instructor' => true,
            'is_admin' => false,
            'is_entity_manager' => false,
            'entity_id' => $entidadeB->id,
        ]);

        // Aluno da Entidade A
        $alunoA = User::create([
            'name' => 'Colaborador Tech 1',
            'email' => 'aluno_a@lms.com',
            'password' => Hash::make('password'),
            'is_instructor' => false,
            'is_admin' => false,
            'is_entity_manager' => false,
            'entity_id' => $entidadeA->id,
        ]);

        // Aluno da Entidade B
        $alunoB = User::create([
            'name' => 'Colaborador Design 1',
            'email' => 'aluno_b@lms.com',
            'password' => Hash::make('password'),
            'is_instructor' => false,
            'is_admin' => false,
            'is_entity_manager' => false,
            'entity_id' => $entidadeB->id,
        ]);

        // 3. Criar Cursos vinculados a Entidades
        // Curso 1 (Entidade A)
        $curso1 = Course::create([
            'title' => 'Introdução ao Laravel 10',
            'description' => 'Aprenda os conceitos básicos do framework PHP mais popular do mercado. Cobriremos rotas, controllers e views.',
            'requires_quiz' => false,
            'entity_id' => $entidadeA->id,
            'is_approved_for_sharing' => false, // Somente para Entidade A inicialmente
        ]);

        Lesson::create([
            'course_id' => $curso1->id,
            'title' => 'Instalação e Setup do Laravel',
            'content' => 'Nesta aula você aprenderá a configurar seu ambiente de desenvolvimento PHP e a instalar o Laravel usando o Composer.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'order' => 1,
        ]);

        Lesson::create([
            'course_id' => $curso1->id,
            'title' => 'Trabalhando com Rotas e Controllers',
            'content' => 'Descubra como mapear as URLs da sua aplicação para métodos específicos em seus controladores.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'order' => 2,
        ]);

        // Curso 2 (Entidade B)
        $curso2 = Course::create([
            'title' => 'Design Premium com Tailwind CSS',
            'description' => 'Aprenda a construir interfaces ricas, modernas e responsivas utilizando a biblioteca utilitária Tailwind CSS.',
            'requires_quiz' => true,
            'entity_id' => $entidadeB->id,
            'is_approved_for_sharing' => true, // Compartilhado com outras entidades pelo admin!
        ]);

        Lesson::create([
            'course_id' => $curso2->id,
            'title' => 'Introdução ao Utility-First',
            'content' => 'Entenda a filosofia de classes utilitárias e por que ela agiliza o processo de desenvolvimento web.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'order' => 1,
        ]);

        Lesson::create([
            'course_id' => $curso2->id,
            'title' => 'Layouts Flexbox e Grid no Tailwind',
            'content' => 'Aprenda a alinhar elementos de forma profissional usando as classes utilitárias de Grid e Flex do Tailwind.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'order' => 2,
        ]);

        Lesson::create([
            'course_id' => $curso2->id,
            'title' => 'Customização e Temas',
            'content' => 'Aprenda a configurar o arquivo tailwind.config.js para estender paletas de cores, fontes e espaçamentos.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'order' => 3,
        ]);

        // Prova do Curso 2
        $quiz = Quiz::create([
            'course_id' => $curso2->id,
            'min_score' => 70,
        ]);

        $q1 = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'Qual arquivo é utilizado para customizar as configurações de cores e temas no Tailwind CSS?',
        ]);

        Option::create([
            'question_id' => $q1->id,
            'option_text' => 'tailwind.config.js',
            'is_correct' => true,
        ]);
        Option::create([
            'question_id' => $q1->id,
            'option_text' => 'app.css',
            'is_correct' => false,
        ]);

        // 4. Criar Grupos de Colaboradores
        $grupoA = Group::create([
            'name' => 'Equipe de Desenvolvimento',
            'entity_id' => $entidadeA->id,
        ]);
        $grupoA->users()->attach($alunoA->id);

        // 5. Criar Trilhas de Conhecimento
        $trilhaA = Trail::create([
            'name' => 'Trilha Backend Iniciante',
            'description' => 'Passo a passo para aprender arquitetura moderna de backend.',
            'entity_id' => $entidadeA->id,
        ]);

        // Associa curso 1 à trilha
        $trilhaA->courses()->attach($curso1->id, ['position' => 1]);

        // Atribui trilha ao alunoA diretamente
        $trilhaA->users()->attach($alunoA->id);
    }
}
