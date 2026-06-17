<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Atribuir Trilha: ') }} {{ $trail->name }}
            </h2>
            <a href="{{ route('manager.trails.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6 sm:p-8 space-y-6">
                
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Configurar Atribuições de Aprendizado</h3>
                    <p class="text-xs text-gray-500 mt-1">Defina quais grupos ou colaboradores individuais devem cursar os módulos contidos nesta trilha de conhecimento.</p>
                </div>

                <form action="{{ route('manager.trails.assign.store', $trail) }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Atribuição a Grupos -->
                        <div class="space-y-3">
                            <h4 class="text-sm font-bold text-gray-900 border-b border-gray-100 pb-2">Atribuir a Grupos</h4>
                            <p class="text-xs text-gray-400">Todos os membros atuais e futuros destes grupos terão acesso à trilha.</p>

                            @if($groups->isEmpty())
                                <p class="text-xs text-gray-550 italic">Nenhum grupo cadastrado.</p>
                            @else
                                <div class="space-y-2 max-h-60 overflow-y-auto p-4 border border-gray-150 rounded-lg">
                                    @foreach($groups as $group)
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="group_{{ $group->id }}" name="groups[]" type="checkbox" value="{{ $group->id }}" {{ in_array($group->id, $assignedGroupIds) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded transition">
                                            </div>
                                            <div class="ml-3 text-xs">
                                                <label for="group_{{ $group->id }}" class="font-bold text-gray-750 cursor-pointer">{{ $group->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Atribuição a Colaboradores Individuais -->
                        <div class="space-y-3">
                            <h4 class="text-sm font-bold text-gray-900 border-b border-gray-100 pb-2">Atribuir Individualmente</h4>
                            <p class="text-xs text-gray-400">Escolha colaboradores avulsos que também devem cursar esta trilha.</p>

                            @if($users->isEmpty())
                                <p class="text-xs text-gray-550 italic">Nenhum colaborador cadastrado.</p>
                            @else
                                <div class="space-y-2 max-h-60 overflow-y-auto p-4 border border-gray-150 rounded-lg">
                                    @foreach($users as $user)
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="user_{{ $user->id }}" name="users[]" type="checkbox" value="{{ $user->id }}" {{ in_array($user->id, $assignedUserIds) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded transition">
                                            </div>
                                            <div class="ml-3 text-xs">
                                                <label for="user_{{ $user->id }}" class="font-bold text-gray-750 block cursor-pointer">{{ $user->name }}</label>
                                                <span class="text-gray-400 block">{{ $user->email }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('manager.trails.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-xs rounded-lg uppercase tracking-wider hover:bg-gray-50 transition shadow-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-lg transition shadow-md">
                            Salvar Atribuições
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
