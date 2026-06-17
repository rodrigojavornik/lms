<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Grupo: ') }} {{ $group->name }}
            </h2>
            <a href="{{ route('manager.groups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6 sm:p-8 space-y-6">
                
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Editar Grupo de Colaboradores</h3>
                    <p class="text-xs text-gray-500 mt-1">Atualize o nome do grupo e gerencie a lista de membros associados.</p>
                </div>

                <form action="{{ route('manager.groups.update', $group) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nome do Grupo</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm text-sm">
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Seleção de Usuários -->
                    <div class="border-t border-gray-100 pt-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Membros do Grupo</label>
                        <p class="text-xs text-gray-400 mb-4">Marque os colaboradores que devem fazer parte deste grupo e desmarque os que devem ser removidos.</p>
                        
                        @if($users->isEmpty())
                            <p class="text-xs text-amber-600 font-semibold">⚠️ Não há colaboradores cadastrados nesta entidade.</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-60 overflow-y-auto p-4 border border-gray-150 rounded-lg">
                                @foreach($users as $user)
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="user_{{ $user->id }}" name="users[]" type="checkbox" value="{{ $user->id }}" {{ in_array($user->id, $groupUserIds) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded transition">
                                        </div>
                                        <div class="ml-3 text-xs">
                                            <label for="user_{{ $user->id }}" class="font-bold text-gray-750 block cursor-pointer">{{ $user->name }}</label>
                                            <span class="text-gray-400 block">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('users')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('manager.groups.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-xs rounded-lg uppercase tracking-wider hover:bg-gray-50 transition shadow-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-lg transition shadow-md">
                            Salvar Alterações
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
