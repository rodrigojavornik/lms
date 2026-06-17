<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Curso & Aulas') }}
            </h2>
            <a href="{{ route('instructor.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                Voltar ao Painel
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Coluna 1: Informações do Curso -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Informações do Curso</h3>
                        
                        <form action="{{ route('instructor.courses.update', $course) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Título -->
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Título</label>
                                <input type="text" name="title" id="title" value="{{ $course->title }}" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                            </div>

                            <!-- Descrição -->
                            <div class="mb-4">
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Descrição</label>
                                <textarea name="description" id="description" rows="4" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">{{ $course->description }}</textarea>
                            </div>

                            <!-- Requer Prova? -->
                            <div class="mb-6 flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="requires_quiz" name="requires_quiz" type="checkbox" value="1" {{ $course->requires_quiz ? 'checked' : '' }} class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded transition">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="requires_quiz" class="font-semibold text-gray-700">Exigir Prova para Conclusão</label>
                                </div>
                            </div>

                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-sm">
                                Atualizar Informações
                            </button>
                        </form>

                        @if($course->requires_quiz)
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <h4 class="text-sm font-bold text-gray-900 mb-2">Avaliação / Prova</h4>
                                <p class="text-xs text-gray-500 mb-4">Este curso exige a realização de uma prova. Configure as perguntas e as alternativas corretas.</p>
                                <a href="{{ route('instructor.quiz.edit', $course) }}" class="w-full inline-flex justify-center items-center px-4 py-3 bg-amber-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 transition shadow-sm" style="background-color: #f59e0b; color: #ffffff;">
                                    Gerenciar Prova (Quiz)
                                </a>
                            </div>
                        @endif

                        <!-- Relatório de Alunos -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h4 class="text-sm font-bold text-gray-900 mb-2">Relatório de Alunos</h4>
                            <p class="text-xs text-gray-500 mb-4">Acompanhe a lista de alunos matriculados, o progresso individual nas aulas e o histórico de tentativas de provas.</p>
                            <a href="{{ route('instructor.courses.students', $course) }}" class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-sm" style="background-color: #4f46e5; color: #ffffff;">
                                Ver Alunos Inscritos
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Coluna 2: Lista de Aulas e Adicionar Aulas -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Grade de Aulas Existentes -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Grade de Aulas</h3>
                            <a href="{{ route('instructor.lessons.create', $course) }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-widest rounded-lg transition shadow-md">
                                <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                Nova Aula
                            </a>
                        </div>

                        @if($course->lessons->isEmpty())
                            <p class="text-sm text-gray-500 text-center py-8">Nenhuma aula cadastrada ainda. Clique no botão acima para adicionar uma nova aula.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($course->lessons as $lesson)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-indigo-100 text-indigo-800 h-8 w-8 rounded-full flex items-center justify-center font-bold text-sm">
                                                {{ $lesson->order }}
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-800 text-sm">{{ $lesson->title }}</h4>
                                                <div class="flex space-x-3 text-xs text-gray-400 mt-0.5">
                                                    @if($lesson->video_url)
                                                        <span class="flex items-center">
                                                            <svg class="h-3.5 w-3.5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                            Vídeo vinculado
                                                        </span>
                                                    @endif
                                                    @if($lesson->content)
                                                        <span class="flex items-center">
                                                            <svg class="h-3.5 w-3.5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            Material textual
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <form action="{{ route('instructor.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta aula?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900 transition p-1 text-sm font-semibold">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
