<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Minhas Aulas') }}
            </h2>
            @if(auth()->user()->is_instructor)
                <a href="{{ route('instructor.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none transition ease-in-out duration-150 shadow-md">
                    Painel do Instrutor
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Banner de Boas-Vindas Premium -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-800 p-8 shadow-lg text-white" style="background: linear-gradient(to right, #4f46e5, #4338ca, #6b21a8);">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-indigo-500/20 blur-xl"></div>
                <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-purple-500/20 blur-xl"></div>
                <div class="relative z-10 space-y-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm">
                        Portal de Aprendizado
                    </span>
                    <h2 class="text-3xl font-extrabold tracking-tight">Olá, {{ auth()->user()->name }}! 👋</h2>
                    <p class="text-indigo-100 text-sm max-w-xl">Bem-vindo à sua plataforma LMS. Acompanhe seus cursos em andamento ou descubra novos conhecimentos no nosso catálogo abaixo.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Seção 0: Minhas Trilhas de Conhecimento -->
            @if(!$myTrails->isEmpty())
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="h-6 w-6 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        Minhas Trilhas de Aprendizado
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($myTrails as $trail)
                            @php
                                $trailProgress = auth()->user()->trailProgress($trail);
                            @endphp
                            <div class="flex flex-col bg-white rounded-xl border border-gray-150 overflow-hidden shadow-sm p-6 hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="text-base font-bold text-gray-900">{{ $trail->name }}</h4>
                                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $trailProgress === 100 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                                        {{ $trailProgress }}% Concluído
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ $trail->description ?? 'Sem descrição da trilha.' }}</p>
                                
                                <div class="w-full bg-gray-100 rounded-full h-1.5 mb-6 overflow-hidden">
                                    <div class="h-full rounded-full {{ $trailProgress === 100 ? 'bg-emerald-500' : 'bg-indigo-600' }}" style="width: {{ $trailProgress }}%"></div>
                                </div>

                                <div class="space-y-3">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Cursos nesta trilha:</span>
                                    <div class="space-y-2">
                                        @foreach($trail->courses as $tCourse)
                                            @php
                                                $tCourseCompleted = auth()->user()->courses()->where('courses.id', $tCourse->id)->whereNotNull('completed_at')->exists();
                                                $isEnrolled = auth()->user()->isEnrolled($tCourse->id);
                                            @endphp
                                            <div class="flex items-center justify-between text-xs p-2 bg-gray-50 rounded border border-gray-100">
                                                <div class="flex items-center space-x-2">
                                                    @if($tCourseCompleted)
                                                        <span class="text-emerald-500 font-bold">✓</span>
                                                    @else
                                                        <span class="text-gray-300 font-bold">•</span>
                                                    @endif
                                                    <span class="font-medium {{ $tCourseCompleted ? 'text-gray-400 line-through' : 'text-gray-700' }}">{{ $tCourse->title }}</span>
                                                </div>
                                                
                                                <div>
                                                    @if($tCourseCompleted)
                                                        <span class="text-[10px] text-emerald-600 font-bold">Concluído</span>
                                                    @elseif($isEnrolled)
                                                        @if($tCourse->lessons()->count() > 0)
                                                            <a href="{{ route('courses.player', [$tCourse->id, $tCourse->lessons->first()->id]) }}" class="text-[10px] text-indigo-600 hover:underline font-bold">Estudar</a>
                                                        @else
                                                            <span class="text-[10px] text-gray-450">Sem aulas</span>
                                                        @endif
                                                    @else
                                                        <form action="{{ route('courses.enroll', $tCourse) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-[10px] text-gray-600 hover:text-indigo-600 hover:underline font-bold">Iniciar</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Seção 1: Meus Cursos (Cursos Matriculados) -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-gray-50 pb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <svg class="h-6 w-6 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Meus Cursos em Andamento
                    </h3>
                    @if(!$myCourses->isEmpty())
                        <input type="text" placeholder="🔍 Filtrar meus cursos..." onkeyup="filterCourses(this.value, 'my-courses-grid', 'my-courses-empty')" class="px-3 py-1.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs w-full sm:w-64">
                    @endif
                </div>

                @if($myCourses->isEmpty())
                    <p class="text-sm text-gray-500 py-6 text-center">Você não está matriculado em nenhum curso no momento. Escolha um curso no catálogo abaixo para começar!</p>
                @else
                    <p id="my-courses-empty" class="text-sm text-gray-500 py-6 text-center" style="display: none;">Nenhum curso corresponde à sua pesquisa.</p>
                    <div id="my-courses-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($myCourses as $course)
                            @php 
                                $progress = auth()->user()->courseProgress($course);
                                $completed = !is_null($course->pivot->completed_at);
                            @endphp
                            <div class="course-card flex flex-col bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transform transition duration-300" data-title="{{ $course->title }}">
                                <div class="h-32 bg-gradient-to-br from-indigo-600 to-purple-700 flex items-center justify-center p-6 text-white text-center relative">
                                    <span class="font-bold text-lg tracking-tight line-clamp-2">{{ $course->title }}</span>
                                    @if($completed)
                                        <span class="absolute top-3 right-3 bg-emerald-400 text-emerald-950 text-xs px-2.5 py-1 rounded-full font-bold shadow-sm flex items-center">
                                            Concluído
                                        </span>
                                    @endif
                                </div>
                                <div class="p-6 flex-1 flex flex-col justify-between">
                                    <div>
                                        <!-- Barra de Progresso -->
                                        <div class="mb-4">
                                            <div class="flex justify-between items-center text-xs mb-1">
                                                <span class="text-gray-400 font-medium">Seu progresso</span>
                                                <span class="text-indigo-600 font-bold">{{ $progress }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-2">
                                                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                                            </div>
                                        </div>

                                        <p class="text-xs text-gray-500 line-clamp-2 mb-6">{{ $course->description }}</p>
                                    </div>

                                    @if($course->lessons_count > 0)
                                        @if($completed)
                                            <div class="flex space-x-2">
                                                <a href="{{ route('courses.player', [$course->id, $course->lessons->first()->id]) }}" class="flex-1 inline-flex justify-center items-center px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-lg uppercase tracking-wider transition shadow-sm">
                                                    Aulas
                                                </a>
                                                <a href="{{ route('courses.certificate', $course->id) }}" target="_blank" class="flex-1 inline-flex justify-center items-center px-3 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-lg uppercase tracking-wider transition shadow-sm" style="background-color: #f59e0b; color: #ffffff;">
                                                    Certificado
                                                </a>
                                            </div>
                                        @else
                                            <a href="{{ route('courses.player', [$course->id, $course->lessons->first()->id]) }}" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg uppercase tracking-wider transition shadow-sm hover:shadow-md">
                                                Continuar Estudando
                                            </a>
                                        @endif
                                    @else
                                        <button disabled class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gray-100 text-gray-400 font-semibold text-xs rounded-lg uppercase tracking-wider cursor-not-allowed">
                                            Sem Aulas Disponíveis
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Seção 2: Catálogo de Cursos (Cursos Disponíveis) -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-gray-50 pb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <svg class="h-6 w-6 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Catálogo de Cursos Disponíveis
                    </h3>
                    @if(!$availableCourses->isEmpty())
                        <input type="text" placeholder="🔍 Filtrar catálogo..." onkeyup="filterCourses(this.value, 'available-courses-grid', 'available-courses-empty')" class="px-3 py-1.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs w-full sm:w-64">
                    @endif
                </div>

                @if($availableCourses->isEmpty())
                    <p class="text-sm text-gray-500 py-6 text-center">Nenhum curso novo disponível no catálogo neste momento.</p>
                @else
                    <p id="available-courses-empty" class="text-sm text-gray-500 py-6 text-center" style="display: none;">Nenhum curso corresponde à sua pesquisa.</p>
                    <div id="available-courses-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($availableCourses as $course)
                            <div class="course-card flex flex-col bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transform transition duration-300" data-title="{{ $course->title }}">
                                <div class="h-32 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center p-6 text-white text-center relative">
                                    <span class="font-bold text-lg tracking-tight line-clamp-2">{{ $course->title }}</span>
                                    @if($course->requires_quiz)
                                        <span class="absolute top-3 right-3 bg-amber-400 text-amber-950 text-xs px-2.5 py-1 rounded-full font-bold shadow-sm">
                                            Requer Prova
                                        </span>
                                    @endif
                                </div>
                                <div class="p-6 flex-1 flex flex-col justify-between">
                                    <div>
                                        <p class="text-xs text-gray-500 line-clamp-3 mb-4">{{ $course->description ?? 'Sem descrição.' }}</p>
                                        
                                        <div class="flex items-center space-x-2 text-xs text-gray-400 mb-6">
                                            <span class="bg-gray-50 text-gray-600 px-2 py-0.5 rounded font-medium border border-gray-100">
                                                {{ $course->lessons_count }} {{ Str::plural('Aula', $course->lessons_count) }}
                                            </span>
                                        </div>
                                    </div>

                                    <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-semibold text-xs rounded-lg uppercase tracking-wider transition shadow-sm hover:shadow-md">
                                            Matricular-se Gratuitamente
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Script de Filtro Instantâneo -->
    <script>
        function filterCourses(query, gridId, emptyId) {
            const grid = document.getElementById(gridId);
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

            const emptyElement = document.getElementById(emptyId);
            if (emptyElement) {
                emptyElement.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }
    </script>
</x-app-layout>
