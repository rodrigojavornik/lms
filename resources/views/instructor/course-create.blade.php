<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Criar Novo Curso') }}
            </h2>
            <a href="{{ route('instructor.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                Voltar ao Painel
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100">
                <div class="p-6 bg-white">
                    <form action="{{ route('instructor.courses.store') }}" method="POST">
                        @csrf
                        
                        <!-- Título do Curso -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Título do Curso</label>
                            <input type="text" name="title" id="title" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm" placeholder="Ex: Programação Funcional com PHP">
                            @error('title')
                                <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descrição -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Descrição do Curso</label>
                            <textarea name="description" id="description" rows="5" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm" placeholder="Forneça uma descrição detalhada sobre o que os alunos aprenderão neste curso."></textarea>
                            @error('description')
                                <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Exige Prova? -->
                        <div class="mb-8 p-4 bg-indigo-50 rounded-lg border border-indigo-100 flex items-start">
                            <div class="flex items-center h-5">
                                <input id="requires_quiz" name="requires_quiz" type="checkbox" value="1" class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded transition">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="requires_quiz" class="font-semibold text-indigo-900">Exigir Prova para Conclusão</label>
                                <p class="text-indigo-700">Se marcado, o aluno precisará realizar e ser aprovado em um questionário avaliativo após assistir a todas as aulas para concluir o curso.</p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-100">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none transition shadow-md">
                                Salvar e Continuar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
