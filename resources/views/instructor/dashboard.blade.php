<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Painel do Instrutor') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-md font-semibold text-xs text-indigo-700 uppercase tracking-widest hover:bg-indigo-100 active:bg-indigo-200 focus:outline-none focus:border-indigo-300 focus:ring ring-indigo-200 transition ease-in-out duration-150">
                    Ir para Área do Aluno
                </a>
                <a href="{{ route('instructor.courses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md" style="background-color: #4f46e5; color: #ffffff;">
                    Criar Novo Curso
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Banner Administrativo Premium -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-800 via-purple-700 to-indigo-800 p-8 shadow-lg text-white mb-2" style="background: linear-gradient(to right, #581c87, #7e22ce, #3730a3);">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-purple-500/20 blur-xl"></div>
                <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-indigo-500/20 blur-xl"></div>
                <div class="relative z-10 space-y-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm">
                        Painel de Controle
                    </span>
                    <h2 class="text-3xl font-extrabold tracking-tight">Gestão Acadêmica</h2>
                    <p class="text-purple-100 text-sm max-w-xl">Crie e edite cursos, gerencie as aulas e configure questionários e provas avaliativas automáticas para os seus alunos.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-gray-50 pb-4">
                        <h3 class="text-lg font-medium text-gray-900">Seus Cursos Cadastrados</h3>
                        @if(!$courses->isEmpty())
                            <input type="text" placeholder="🔍 Filtrar meus cursos..." onkeyup="filterCourses(this.value)" class="px-3 py-1.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs w-full sm:w-64">
                        @endif
                    </div>

                    @if($courses->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum curso</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece criando um novo curso para os alunos.</p>
                            <div class="mt-6">
                                <a href="{{ route('instructor.courses.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Criar Novo Curso
                                </a>
                            </div>
                        </div>
                    @else
                        <p id="courses-empty" class="text-sm text-gray-500 py-6 text-center" style="display: none;">Nenhum curso corresponde à sua pesquisa.</p>
                        <div id="courses-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($courses as $course)
                                <div class="course-card flex flex-col bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transform transition duration-300" data-title="{{ $course->title }}">
                                    <div class="h-40 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center p-6 text-white text-center relative">
                                        <span class="font-bold text-xl tracking-tight line-clamp-2">{{ $course->title }}</span>
                                        @if($course->requires_quiz)
                                            <span class="absolute top-3 right-3 bg-amber-400 text-amber-900 text-xs px-2.5 py-1 rounded-full font-bold shadow-sm">
                                                Requer Prova
                                            </span>
                                        @endif
                                    </div>
                                    <div class="p-6 flex-1 flex flex-col justify-between">
                                        <div>
                                            <p class="text-sm text-gray-500 line-clamp-3 mb-4">{{ $course->description ?? 'Sem descrição fornecida.' }}</p>
                                            <div class="flex items-center space-x-2 text-xs text-gray-400 mb-6">
                                                <span class="bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded font-medium">
                                                    {{ $course->lessons_count }} {{ Str::plural('Aula', $course->lessons_count) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex justify-between items-center pt-4 border-t border-gray-50">
                                            <a href="{{ route('instructor.courses.edit', $course) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-900 transition">
                                                Editar Aulas & Curso
                                            </a>
                                            <form action="{{ route('instructor.courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este curso e todas as suas aulas?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-semibold text-rose-600 hover:text-rose-900 transition">
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
            const grid = document.getElementById('courses-grid');
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

            const emptyElement = document.getElementById('courses-empty');
            if (emptyElement) {
                emptyElement.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }
    </script>
</x-app-layout>
