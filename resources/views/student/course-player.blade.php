<x-app-layout>
    <div class="py-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Link para voltar ao Dashboard -->
            <div class="mb-4">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-900 transition">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Voltar aos meus cursos
                </a>
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

            <!-- Grid do Player -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- Coluna Principal (Player de Vídeo e Conteúdo) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                        
                        <!-- Vídeo (YouTube Embed) -->
                        @if($lesson->video_url)
                            <div class="aspect-w-16 aspect-h-9 bg-black rounded-lg overflow-hidden shadow-md mb-6 relative" style="padding-bottom: 56.25%">
                                <iframe class="absolute top-0 left-0 w-full h-full" src="{{ $lesson->video_url }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        @else
                            <div class="p-8 bg-indigo-50 rounded-lg text-center mb-6 border border-indigo-100">
                                <svg class="mx-auto h-12 w-12 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <h4 class="mt-2 font-bold text-indigo-900">Material de Leitura</h4>
                                <p class="text-xs text-indigo-700 mt-1">Esta aula não contém vídeo. Leia o material de apoio abaixo.</p>
                            </div>
                        @endif

                        <!-- Título e Descrição da Aula -->
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $lesson->title }}</h2>
                        
                        <div class="prose max-w-none text-gray-700 text-sm leading-relaxed mb-8">
                            {!! $lesson->content !!}
                        </div>

                        <!-- Botão Concluir Aula / Avançar -->
                        <div class="flex justify-between items-center pt-6 border-t border-gray-100">
                            @if(!$isCompleted)
                                <form action="{{ route('lessons.complete', $lesson->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 transition shadow-md">
                                        Concluir Aula
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex items-center text-sm font-semibold text-emerald-600 bg-emerald-50 px-4 py-2 rounded-lg border border-emerald-200">
                                    <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Aula Concluída!
                                </span>
                            @endif

                            @if($nextLesson)
                                <a href="{{ route('courses.player', [$course->id, $nextLesson->id]) }}" class="inline-flex items-center text-sm font-bold text-gray-700 hover:text-gray-900 transition">
                                    Próxima Aula
                                    <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @endif
                        </div>

                    </div>
                </div>

                <!-- Coluna Lateral (Grade de Aulas) -->
                <div class="space-y-6">
                    
                    <!-- Box de Progresso -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Seu Progresso no Curso</h3>
                        <h2 class="text-lg font-bold text-gray-800 mb-4">{{ $course->title }}</h2>

                        <div class="w-full bg-gray-100 rounded-full h-3 mb-2">
                            <div class="bg-indigo-600 h-3 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-indigo-600">{{ $progress }}% completo</span>
                    </div>

                    <!-- Box da Prova / Conclusão -->
                    @if($allLessonsCompleted)
                        @if($courseCompleted)
                            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 overflow-hidden shadow-xl sm:rounded-xl p-6 text-white" style="background: linear-gradient(to bottom right, #10b981, #0d9488);">
                                <div class="flex items-center space-x-2 mb-2">
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <h3 class="text-lg font-bold">Curso Concluído!</h3>
                                </div>
                                <p class="text-xs mb-4">Parabéns! Você concluiu com sucesso todas as etapas e avaliações deste curso.</p>
                                <a href="{{ route('courses.certificate', $course->id) }}" target="_blank" class="w-full inline-flex justify-center items-center px-4 py-3 bg-white hover:bg-gray-100 text-teal-950 font-bold text-xs uppercase tracking-wider rounded-lg transition shadow-md">
                                    Visualizar Certificado
                                </a>
                            </div>
                        @else
                            <div class="bg-gradient-to-br from-amber-400 to-amber-500 overflow-hidden shadow-xl sm:rounded-xl p-6 text-amber-950">
                                @if($course->requires_quiz)
                                    <h3 class="text-lg font-bold mb-1">Todas as aulas concluídas!</h3>
                                    <p class="text-xs mb-4">Para concluir este curso, você precisa realizar e passar na prova avaliativa.</p>
                                    <a href="{{ route('courses.quiz', $course->id) }}" class="w-full inline-flex justify-center items-center px-4 py-3 bg-amber-950 hover:bg-amber-900 text-white font-bold text-xs uppercase tracking-wider rounded-lg transition shadow-md">
                                        Fazer a Prova do Curso
                                    </a>
                                @else
                                    <div class="flex items-center space-x-2">
                                        <svg class="h-6 w-6 text-amber-950" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <h3 class="text-lg font-bold">Curso Concluído!</h3>
                                    </div>
                                    <p class="text-xs mt-1">Parabéns! Você concluiu com sucesso todas as etapas deste curso.</p>
                                @endif
                            </div>
                        @endif
                    @endif

                    <!-- Lista de Aulas -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                        <h3 class="text-sm font-bold text-gray-900 mb-4">Conteúdo do Curso</h3>
                        
                        <div class="space-y-3">
                            @foreach($course->lessons as $cLesson)
                                @php 
                                    $isCurrent = ($cLesson->id === $lesson->id);
                                    $completed = auth()->user()->hasCompletedLesson($cLesson->id);
                                @endphp
                                <a href="{{ route('courses.player', [$course->id, $cLesson->id]) }}" class="flex items-center justify-between p-3 rounded-lg border transition {{ $isCurrent ? 'bg-indigo-50 border-indigo-200' : 'bg-gray-50 border-gray-100 hover:bg-gray-100' }}">
                                    <div class="flex items-center space-x-3">
                                        @if($completed)
                                            <span class="text-emerald-600">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                            </span>
                                        @else
                                            <span class="text-gray-400 font-bold text-xs w-5 text-center">
                                                {{ $cLesson->order }}
                                            </span>
                                        @endif
                                        <span class="text-sm font-medium {{ $isCurrent ? 'text-indigo-950 font-semibold' : 'text-gray-700' }} line-clamp-1">
                                            {{ $cLesson->title }}
                                        </span>
                                    </div>
                                    @if($isCurrent)
                                        <span class="bg-indigo-600 h-2 w-2 rounded-full"></span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
