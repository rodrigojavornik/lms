<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Painel Administrativo') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.entities.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none transition ease-in-out duration-150 shadow-md">
                    Gerenciar Entidades
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none transition ease-in-out duration-150 shadow-md">
                    Gerenciar Usuários
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Banner de Boas-Vindas Admin -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-800 via-gray-900 to-indigo-950 p-8 shadow-lg text-white" style="background: linear-gradient(to right, #1f2937, #111827, #312e81);">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-indigo-500/10 blur-xl"></div>
                <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-gray-500/10 blur-xl"></div>
                <div class="relative z-10 space-y-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm">
                        Visão Geral do Administrador
                    </span>
                    <h2 class="text-3xl font-extrabold tracking-tight">Olá, {{ auth()->user()->name }}! 🛡️</h2>
                    <p class="text-gray-300 text-sm max-w-xl">Aqui você tem acesso e controle total sobre a plataforma LMS: controle de usuários, moderação de cursos e métricas gerais do sistema.</p>
                </div>
            </div>

            <!-- Métricas Consolidadas (Widgets) -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 sm:gap-6">
                <!-- Total Usuários -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Usuários</span>
                    <span class="text-3xl font-extrabold text-gray-900 mt-2 block">{{ $stats['total_users'] }}</span>
                </div>
                <!-- Alunos -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Estudantes</span>
                    <span class="text-3xl font-extrabold text-gray-900 mt-2 block">{{ $stats['total_users'] - $stats['total_instructors'] }}</span>
                </div>
                <!-- Instrutores -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Instrutores</span>
                    <span class="text-3xl font-extrabold text-indigo-600 mt-2 block">{{ $stats['total_instructors'] }}</span>
                </div>
                <!-- Admins -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Admins</span>
                    <span class="text-3xl font-extrabold text-rose-600 mt-2 block">{{ $stats['total_admins'] }}</span>
                </div>
                <!-- Entidades -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Entidades</span>
                    <span class="text-3xl font-extrabold text-emerald-600 mt-2 block">{{ $stats['total_entities'] }}</span>
                </div>
                <!-- Cursos -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Cursos</span>
                    <span class="text-3xl font-extrabold text-gray-900 mt-2 block">{{ $stats['total_courses'] }}</span>
                </div>
                <!-- Certificados -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Certificados</span>
                    <span class="text-3xl font-extrabold text-amber-500 mt-2 block">{{ $stats['total_certificates'] }}</span>
                </div>
            </div>

            <!-- Seção de Cursos da Plataforma -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-gray-50 pb-4">
                    <h3 class="text-lg font-bold text-gray-900">Todos os Cursos da Plataforma ({{ $courses->count() }})</h3>
                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                        @if(!$courses->isEmpty())
                            <input type="text" placeholder="🔍 Filtrar cursos..." onkeyup="filterCourses(this.value)" class="px-3 py-1.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs w-full sm:w-64">
                        @endif
                        <a href="{{ route('instructor.courses.create') }}" class="inline-flex justify-center items-center px-4 py-2 bg-gray-950 hover:bg-gray-800 text-white font-bold text-xs uppercase tracking-widest rounded-lg transition shadow-md whitespace-nowrap">
                            Criar Novo Curso
                        </a>
                    </div>
                </div>

                @if($courses->isEmpty())
                    <p class="text-sm text-gray-500 py-8 text-center">Nenhum curso cadastrado no sistema.</p>
                @else
                    <p id="admin-courses-empty" class="text-sm text-gray-500 py-6 text-center" style="display: none;">Nenhum curso corresponde à sua pesquisa.</p>
                    <div id="admin-courses-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($courses as $course)
                            <div class="course-card flex flex-col bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-lg transition duration-300" data-title="{{ $course->title }}">
                                <div class="h-28 bg-gradient-to-br from-indigo-700 to-purple-800 flex items-center justify-center p-4 text-white text-center relative">
                                    <span class="font-bold text-base tracking-tight line-clamp-2">{{ $course->title }}</span>
                                    @if($course->requires_quiz)
                                        <span class="absolute top-2 right-2 bg-amber-400 text-amber-950 text-[10px] px-2 py-0.5 rounded-full font-bold shadow-sm">
                                            Requer Prova
                                        </span>
                                    @endif
                                </div>
                                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                                    <div class="space-y-2">
                                        <div class="flex flex-col text-xs text-gray-400 font-medium">
                                            <span>Entidade: <strong class="text-gray-700">{{ $course->entity?->name ?? 'Independente' }}</strong></span>
                                            <span>Aulas: <strong class="text-gray-700">{{ $course->lessons_count }}</strong></span>
                                        </div>
                                        <p class="text-xs text-gray-500 line-clamp-2">{{ $course->description ?? 'Sem descrição.' }}</p>
                                        
                                        <!-- Controle de Compartilhamento -->
                                        <div class="pt-2 space-y-1.5">
                                            <div class="text-[10px] text-gray-450 font-bold uppercase tracking-wider">Compartilhamento:</div>
                                            @if(is_null($course->entity_id))
                                                <div class="text-xs text-emerald-600 font-bold flex items-center">
                                                    <span>🌐 Global (Liberado para todos)</span>
                                                </div>
                                            @else
                                                @if($course->sharedEntities->isEmpty())
                                                    <div class="text-xs text-gray-500 italic">🔒 Restrito à entidade</div>
                                                @else
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($course->sharedEntities as $sharedEntity)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                                🤝 {{ $sharedEntity->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <a href="{{ route('admin.courses.share.show', $course) }}" class="w-full inline-flex justify-center items-center px-3 py-1.5 rounded-lg text-xs font-bold transition duration-150 border bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100 mt-2">
                                                    ⚙️ Configurar Liberações
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex space-x-2 pt-2 border-t border-gray-50">
                                        <!-- Editar (Leva ao painel do instrutor) -->
                                        <a href="{{ route('instructor.courses.edit', $course) }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-indigo-55 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-lg transition shadow-sm">
                                            Editar Curso
                                        </a>
                                        <!-- Excluir (Como admin, vamos prover o botão de exclusão direto) -->
                                        <form action="{{ route('instructor.courses.destroy', $course) }}" method="POST" class="flex-1" onsubmit="return confirm('Tem certeza que deseja excluir permanentemente este curso e todas as suas aulas?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full inline-flex justify-center items-center px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-lg transition shadow-sm">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            </div>

        </div>
    </div>

    <!-- Script de Filtro Instantâneo -->
    <script>
        function filterCourses(query) {
            const grid = document.getElementById('admin-courses-grid');
            if (!grid) return;
            
            const cards = grid.getElementsByClassName('course-card');
            const cleanQuery = query.toLowerCase().trim();
            let visibleCount = 0;

            for (let card of cards) {
                const title = card.getAttribute('data-title').toLowerCase();
                if (title.includes(cleanQuery)) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            }

            const emptyElement = document.getElementById('admin-courses-empty');
            if (emptyElement) {
                emptyElement.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }
    </script>
</x-app-layout>
