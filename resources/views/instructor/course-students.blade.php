<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Alunos Inscritos: ') }} {{ $course->title }}
            </h2>
            <a href="{{ route('instructor.courses.edit', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                Voltar ao Curso
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Banner de Acompanhamento Premium -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-800 via-purple-700 to-indigo-800 p-8 shadow-lg text-white mb-2" style="background: linear-gradient(to right, #581c87, #7e22ce, #3730a3);">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-purple-500/20 blur-xl"></div>
                <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-indigo-500/20 blur-xl"></div>
                <div class="relative z-10 space-y-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm">
                        Relatório do Curso
                    </span>
                    <h2 class="text-3xl font-extrabold tracking-tight">Desempenho dos Estudantes</h2>
                    <p class="text-purple-100 text-sm max-w-xl">Acompanhe a lista de inscritos, o engajamento através do progresso de leitura e as tentativas e notas nas provas de avaliação.</p>
                </div>
            </div>

            <!-- Tabela de Alunos -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Lista de Alunos Matriculados ({{ $students->count() }})</h3>

                @if($students->isEmpty())
                    <p class="text-sm text-gray-500 py-8 text-center">Nenhum aluno matriculado neste curso até o momento.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aluno</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Matrícula</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Progresso de Aulas</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status Geral</th>
                                    @if($course->requires_quiz)
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Notas de Avaliação</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-sm">
                                @foreach($students as $student)
                                    @php 
                                        $progress = $student->courseProgress($course);
                                        $completed = !is_null($student->pivot->completed_at);
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <!-- Aluno / E-mail -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-bold text-gray-900">{{ $student->name }}</div>
                                            <div class="text-xs text-gray-400 font-medium">{{ $student->email }}</div>
                                        </td>
                                        
                                        <!-- Data de Inscrição -->
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-xs font-medium">
                                            {{ $student->pivot->created_at ? $student->pivot->created_at->format('d/m/Y H:i') : 'N/A' }}
                                        </td>

                                        <!-- Progresso de Aulas -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-24 bg-gray-100 rounded-full h-2">
                                                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                                                </div>
                                                <span class="text-xs font-bold text-gray-700">{{ $progress }}%</span>
                                            </div>
                                        </td>

                                        <!-- Status de Conclusão -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($completed)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Concluído em {{ \Carbon\Carbon::parse($student->pivot->completed_at)->format('d/m/Y') }}
                                                </span>
                                            @elseif($progress === 100 && $course->requires_quiz)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                                    Aguardando Prova
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                    Em Andamento
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Tentativas de Prova (se aplicável) -->
                                        @if($course->requires_quiz)
                                            <td class="px-6 py-4">
                                                @if($student->quizAttempts->isEmpty())
                                                    <span class="text-xs text-gray-400 font-medium">Nenhuma tentativa</span>
                                                @else
                                                    <div class="space-y-1.5 max-w-xs">
                                                        @foreach($student->quizAttempts as $attempt)
                                                            <div class="flex justify-between items-center text-xs p-1.5 rounded border {{ $attempt->passed ? 'bg-emerald-50 border-emerald-100 text-emerald-800' : 'bg-rose-50 border-rose-100 text-rose-800' }}">
                                                                <span class="font-bold">{{ $attempt->score }}%</span>
                                                                <span class="font-medium text-[10px] uppercase tracking-wider">{{ $attempt->passed ? 'Aprovado' : 'Reprovado' }}</span>
                                                                <span class="text-[9px] text-gray-400 font-normal">{{ $attempt->created_at->format('d/m H:i') }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
