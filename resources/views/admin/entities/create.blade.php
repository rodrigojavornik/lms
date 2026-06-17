<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Cadastrar Nova Entidade') }}
            </h2>
            <a href="{{ route('admin.entities.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6 sm:p-8 space-y-6">
                
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Nova Entidade / Organização</h3>
                    <p class="text-xs text-gray-500 mt-1">Crie uma nova organização isolada para conter colaboradores, instrutores e cursos específicos.</p>
                </div>

                <form action="{{ route('admin.entities.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nome da Entidade</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Ex: Secretaria de Educação" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm text-sm">
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descrição -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Descrição / Informações Adicionais</label>
                        <textarea name="description" id="description" rows="4" placeholder="Breve resumo sobre a entidade..." class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm text-sm">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.entities.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-xs rounded-lg uppercase tracking-wider hover:bg-gray-50 transition shadow-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-lg transition shadow-md">
                            Cadastrar Entidade
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
