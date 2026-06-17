<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Convidar Membro para:') }} {{ $entity->name }}
            </h2>
            <a href="{{ route('manager.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6 sm:p-8 space-y-6">
                
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Novo Cadastro na Entidade</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Preencha os dados do novo membro. Ele será automaticamente associado a esta organização e poderá acessar a plataforma de acordo com o perfil selecionado.
                    </p>
                </div>

                <form action="{{ route('manager.invite.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nome Completo</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Ex: Rodrigo Silva" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm text-sm">
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">E-mail</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="Ex: rodrigo@empresa.com" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm text-sm">
                        @error('email')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>



                    <!-- Perfil / Papel -->
                    <div>
                        <label for="role" class="block text-sm font-semibold text-gray-700 mb-1">Perfil de Acesso</label>
                        <select name="role" id="role" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm text-sm">
                            <option value="collaborator" {{ old('role') == 'collaborator' ? 'selected' : '' }}>Colaborador (Estudante)</option>
                            <option value="instructor" {{ old('role') == 'instructor' ? 'selected' : '' }}>Instrutor (Criador de Cursos)</option>
                        </select>
                        @error('role')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('manager.dashboard') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-xs rounded-lg uppercase tracking-wider hover:bg-gray-50 transition shadow-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-lg transition shadow-md">
                            Cadastrar Membro
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
