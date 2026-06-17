<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Avaliação do Curso: ') }} {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-8">
                
                <!-- Informações e Instruções da Prova -->
                <div class="mb-8 p-4 bg-indigo-50 border border-indigo-100 text-indigo-900 rounded-lg">
                    <h3 class="font-bold text-sm mb-1">Instruções para a Prova:</h3>
                    <ul class="list-disc pl-5 text-xs space-y-1">
                        <li>Esta prova contém <strong>{{ $quiz->questions->count() }}</strong> perguntas de múltipla escolha.</li>
                        <li>Você precisa acertar pelo menos <strong>{{ $quiz->min_score }}%</strong> das questões para ser aprovado e concluir o curso.</li>
                        <li>Não há limite de tempo para responder, mas você deve responder todas as perguntas antes de enviar.</li>
                    </ul>
                </div>

                <form action="{{ route('courses.quiz.submit', $course->id) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-8">
                        @php $qIdx = 1; @endphp
                        @foreach($quiz->questions as $question)
                            <div class="p-6 bg-gray-50 rounded-xl border border-gray-100" x-data="{ selectedAnswer: null }">
                                <h4 class="font-bold text-gray-900 text-base mb-4">
                                    <span class="text-indigo-600 mr-1">Questão #{{ $qIdx }}</span> 
                                    {{ $question->question_text }}
                                </h4>

                                <!-- Alternativas -->
                                <div class="space-y-3">
                                    @foreach($question->options as $option)
                                        <label :class="selectedAnswer == {{ $option->id }} ? 'border-indigo-600 bg-indigo-50/40 ring-2 ring-indigo-100' : 'border-gray-200 bg-white hover:bg-gray-50'" class="flex items-center p-3.5 border rounded-lg cursor-pointer transition shadow-sm">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required @change="selectedAnswer = {{ $option->id }}" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-indigo-300 mr-3">
                                            <span class="text-sm font-medium text-gray-700">{{ $option->option_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @php $qIdx++; @endphp
                        @endforeach
                    </div>

                    <!-- Enviar -->
                    <div class="flex justify-between items-center pt-8 mt-8 border-t border-gray-100">
                        <a href="{{ route('courses.player', [$course->id, $course->lessons->first()->id]) }}" class="text-sm font-semibold text-gray-500 hover:text-gray-950 transition">
                            Voltar para o curso
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 active:bg-indigo-900 transition shadow-md">
                            Finalizar e Enviar Prova
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
