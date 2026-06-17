<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gerenciamento de Usuários') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                Voltar ao Painel
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                <!-- Barra de Pesquisa e Filtros -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Usuários Cadastrados</h3>
                    
                    <form action="{{ route('admin.users.index') }}" method="GET" class="flex items-center w-full md:max-w-md gap-2">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Pesquisar por nome ou e-mail..." class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm text-sm">
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-semibold text-xs uppercase tracking-widest rounded-lg transition shadow-sm">
                            Pesquisar
                        </button>
                        @if($search)
                            <a href="{{ route('admin.users.index') }}" class="text-xs text-rose-600 hover:underline font-bold whitespace-nowrap px-1">
                                Limpar
                            </a>
                        @endif
                    </form>
                </div>

                @if($users->isEmpty())
                    <p class="text-sm text-gray-500 py-8 text-center">Nenhum usuário encontrado.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">E-mail</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Entidade</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Funções</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-sm">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <!-- Nome -->
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span class="text-[10px] bg-gray-100 text-gray-600 font-bold px-2 py-0.5 rounded-full ml-1.5">Você</span>
                                            @endif
                                        </td>
                                        
                                        <!-- E-mail -->
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">
                                            {{ $user->email }}
                                        </td>

                                        <!-- Entidade -->
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">
                                            {{ $user->entity?->name ?? 'Independente' }}
                                        </td>

                                        <!-- Funções (Roles) -->
                                        <td class="px-6 py-4 whitespace-nowrap space-x-1">
                                            @if($user->is_admin)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200">
                                                    Administrador
                                                </span>
                                            @endif
                                            @if($user->is_entity_manager)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Gestor de Entidade
                                                </span>
                                            @endif
                                            @if($user->is_instructor)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                                    Instrutor
                                                </span>
                                            @endif
                                            @if(!$user->is_admin && !$user->is_instructor && !$user->is_entity_manager)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                    Estudante
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Ações -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold space-x-2">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 transition">
                                                Editar
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este usuário permanentemente? Esta ação excluirá todas as suas matrículas, progresso e tentativas de prova.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-600 hover:text-rose-900 transition">
                                                        Excluir
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-300 cursor-not-allowed">Excluir</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Links de Paginação -->
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
