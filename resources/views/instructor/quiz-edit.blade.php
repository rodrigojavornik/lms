<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Configurar Prova (Quiz): ') }} {{ $course->title }}
            </h2>
            <a href="{{ route('instructor.courses.edit', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                Voltar ao Curso
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('instructor.quiz.update', $course) }}" method="POST">
                @csrf
                
                <div class="space-y-8">
                    
                    <!-- Configurações Gerais da Prova -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Configurações Gerais</h3>
                        
                        <div class="w-full md:w-1/3">
                            <label for="min_score" class="block text-sm font-semibold text-gray-700 mb-1">Média de Acertos Mínima (%)</label>
                            <div class="flex items-center space-x-2">
                                <input type="number" name="min_score" id="min_score" value="{{ $quiz->min_score ?? 70 }}" min="0" max="100" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                                <span class="text-gray-500 font-bold">%</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1.5">Mínimo necessário para o aluno ser aprovado (ex: 70%).</p>
                        </div>
                    </div>

                    <!-- Bloco de Questões -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Perguntas da Prova</h3>
                            <button type="button" id="btn-add-question" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 transition shadow-sm">
                                + Adicionar Pergunta
                            </button>
                        </div>

                        <!-- Container de Questões -->
                        <div id="questions-container" class="space-y-6">
                            @php $qIndex = 0; @endphp
                            @forelse($quiz->questions as $question)
                                <div class="question-card p-6 bg-gray-50 rounded-xl border border-gray-200 relative" data-index="{{ $qIndex }}">
                                    <button type="button" class="btn-remove-question absolute top-4 right-4 text-xs font-bold text-rose-600 hover:text-rose-900">
                                        Remover Questão
                                    </button>
                                    
                                    <!-- Enunciado -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Enunciado da Questão #{{ $qIndex + 1 }}</label>
                                        <input type="text" name="questions[{{ $qIndex }}][text]" value="{{ $question->question_text }}" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                                    </div>

                                    <!-- Alternativas -->
                                    <div class="space-y-3">
                                        <label class="block text-xs font-bold text-gray-600">Alternativas (Selecione a correta no botão lateral)</label>
                                        
                                        @php $oIndex = 0; @endphp
                                        @foreach($question->options as $option)
                                            <div class="flex items-center space-x-3">
                                                <input type="radio" name="questions[{{ $qIndex }}][correct_index]" value="{{ $oIndex }}" {{ $option->is_correct ? 'checked' : '' }} required class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                                <input type="text" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][text]" value="{{ $option->option_text }}" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white text-sm" placeholder="Texto da alternativa">
                                            </div>
                                            @php $oIndex++; @endphp
                                        @endforeach

                                        <!-- Caso existam menos de 3 alternativas, renderizamos o restante para completar 3 -->
                                        @for($o = $oIndex; $o < 3; $o++)
                                            <div class="flex items-center space-x-3">
                                                <input type="radio" name="questions[{{ $qIndex }}][correct_index]" value="{{ $o }}" required class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                                <input type="text" name="questions[{{ $qIndex }}][options][{{ $o }}][text]" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white text-sm" placeholder="Texto da alternativa">
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                @php $qIndex++; @endphp
                            @empty
                                <div id="no-questions-placeholder" class="text-center py-8 text-gray-500 text-sm">
                                    Nenhuma pergunta cadastrada. Clique em "+ Adicionar Pergunta" para começar.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Botão de Envio -->
                    <div class="flex justify-end p-6 bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 active:bg-indigo-900 transition shadow-md">
                            Salvar Configurações da Prova
                        </button>
                    </div>

                </div>

            </form>
        </div>
    </div>

    <!-- Script para gerenciar dinamicamente as questões -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('questions-container');
            const btnAdd = document.getElementById('btn-add-question');
            const placeholder = document.getElementById('no-questions-placeholder');
            
            let questionIndex = {{ $qIndex }};

            // Remover Questão
            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-question')) {
                    const card = e.target.closest('.question-card');
                    card.remove();
                    reindexQuestions();
                    
                    if (container.children.length === 0 && placeholder) {
                        placeholder.style.display = 'block';
                    }
                }
            });

            // Adicionar Questão
            btnAdd.addEventListener('click', function() {
                if (placeholder) {
                    placeholder.style.display = 'none';
                }

                const cardHtml = `
                    <div class="question-card p-6 bg-gray-50 rounded-xl border border-gray-200 relative" data-index="${questionIndex}">
                        <button type="button" class="btn-remove-question absolute top-4 right-4 text-xs font-bold text-rose-600 hover:text-rose-900">
                            Remover Questão
                        </button>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Enunciado da Questão #${questionIndex + 1}</label>
                            <input type="text" name="questions[${questionIndex}][text]" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-bold text-gray-600">Alternativas (Selecione a correta no botão lateral)</label>
                            
                            <div class="flex items-center space-x-3">
                                <input type="radio" name="questions[${questionIndex}][correct_index]" value="0" required class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <input type="text" name="questions[${questionIndex}][options][0][text]" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white text-sm" placeholder="Alternativa A">
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <input type="radio" name="questions[${questionIndex}][correct_index]" value="1" required class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <input type="text" name="questions[${questionIndex}][options][1][text]" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white text-sm" placeholder="Alternativa B">
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <input type="radio" name="questions[${questionIndex}][correct_index]" value="2" required class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <input type="text" name="questions[${questionIndex}][options][2][text]" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white text-sm" placeholder="Alternativa C">
                            </div>
                        </div>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', cardHtml);
                questionIndex++;
            });

            // Reindexar questões ao remover
            function reindexQuestions() {
                const cards = container.querySelectorAll('.question-card');
                questionIndex = 0;
                
                cards.forEach(function(card, idx) {
                    card.setAttribute('data-index', idx);
                    
                    const label = card.querySelector('label');
                    if (label) label.textContent = `Enunciado da Questão #${idx + 1}`;
                    
                    const textInput = card.querySelector('input[type="text"]');
                    if (textInput) textInput.setAttribute('name', `questions[${idx}][text]`);
                    
                    const radios = card.querySelectorAll('input[type="radio"]');
                    radios.forEach(function(radio, rIdx) {
                        radio.setAttribute('name', `questions[${idx}][correct_index]`);
                        radio.setAttribute('value', rIdx);
                    });

                    const optInputs = card.querySelectorAll('.space-y-3 input[type="text"]');
                    optInputs.forEach(function(optInput, oIdx) {
                        optInput.setAttribute('name', `questions[${idx}][options][${oIdx}][text]`);
                    });
                    
                    questionIndex++;
                });
            }
        });
    </script>
</x-app-layout>
