<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-8">
                
                @if($attempt->passed)
                    <!-- Caso de Aprovação -->
                    <div class="text-center space-y-6">
                        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-emerald-100 text-emerald-600 shadow-inner">
                            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>

                        <div class="space-y-2">
                            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Parabéns, você passou!</h2>
                            <p class="text-sm text-gray-500">Você concluiu com sucesso a avaliação do curso e obteve a certificação interna.</p>
                        </div>

                        <!-- Nota obtida -->
                        <div class="p-6 bg-emerald-50 rounded-2xl border border-emerald-100 inline-block w-full max-w-sm">
                            <span class="block text-xs font-bold text-emerald-800 uppercase tracking-wider mb-1">Sua Nota Final</span>
                            <span class="text-5xl font-extrabold text-emerald-600">{{ $attempt->score }}%</span>
                            <span class="block text-xs text-emerald-700 mt-2 font-medium">Pontuação mínima exigida: {{ $quiz->min_score }}%</span>
                        </div>

                        <div class="pt-6 flex flex-col sm:flex-row justify-center gap-4">
                            <a href="{{ route('dashboard') }}" class="inline-flex justify-center items-center px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-md transition">
                                Voltar ao Painel Geral
                            </a>
                            <a href="{{ route('courses.player', [$course->id, $course->lessons->first()->id]) }}" class="inline-flex justify-center items-center px-6 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-lg transition border border-gray-200">
                                Rever Aulas
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Caso de Reprovação -->
                    <div class="text-center space-y-6">
                        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-100 text-rose-600 shadow-inner">
                            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>

                        <div class="space-y-2">
                            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Não foi dessa vez...</h2>
                            <p class="text-sm text-gray-500">Você não atingiu a pontuação mínima necessária de {{ $quiz->min_score }}% nesta tentativa.</p>
                        </div>

                        <!-- Nota obtida -->
                        <div class="p-6 bg-rose-50 rounded-2xl border border-rose-100 inline-block w-full max-w-sm">
                            <span class="block text-xs font-bold text-rose-800 uppercase tracking-wider mb-1">Sua Nota Final</span>
                            <span class="text-5xl font-extrabold text-rose-600">{{ $attempt->score }}%</span>
                            <span class="block text-xs text-rose-700 mt-2 font-medium">Pontuação mínima exigida: {{ $quiz->min_score }}%</span>
                        </div>

                        <p class="text-xs text-gray-400">Recomendamos que você revise as aulas complementares antes de tentar responder a prova novamente.</p>

                        <div class="pt-6 flex flex-col sm:flex-row justify-center gap-4">
                            <a href="{{ route('courses.quiz', $course->id) }}" class="inline-flex justify-center items-center px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-md transition">
                                Tentar Novamente
                            </a>
                            <a href="{{ route('courses.player', [$course->id, $course->lessons->first()->id]) }}" class="inline-flex justify-center items-center px-6 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-lg transition border border-gray-200">
                                Revisar Aulas
                            </a>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
