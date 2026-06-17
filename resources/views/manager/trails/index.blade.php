<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gerenciamento de Trilhas de Conhecimento') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('manager.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                    Voltar ao Painel
                </a>
                <a href="{{ route('manager.trails.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none transition ease-in-out duration-150 shadow-md">
                    Nova Trilha
                </a>
            </div>
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
                <h3 class="text-lg font-bold text-gray-900 mb-6">Trilhas Cadastradas</h3>

                @if($trails->isEmpty())
                    <p class="text-sm text-gray-500 py-8 text-center">Nenhuma trilha de conhecimento cadastrada.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Trilha</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Descrição</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Cursos</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-sm">
                                @foreach($trails as $trail)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <!-- Nome -->
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
                                            {{ $trail->name }}
                                        </td>
                                        
                                        <!-- Descrição -->
                                        <td class="px-6 py-4 text-gray-500 font-medium max-w-xs truncate">
                                            {{ $trail->description ?? 'Sem descrição.' }}
                                        </td>

                                        <!-- Cursos -->
                                        <td class="px-6 py-4 whitespace-nowrap text-indigo-650 font-bold">
                                            {{ $trail->courses_count }} cursos
                                        </td>

                                        <!-- Ações -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold space-x-3">
                                            <a href="{{ route('manager.trails.assign.show', $trail) }}" class="text-emerald-600 hover:text-emerald-900 transition">
                                                Atribuir Trilha
                                            </a>
                                            <a href="{{ route('manager.trails.edit', $trail) }}" class="text-indigo-600 hover:text-indigo-900 transition">
                                                Editar Trilha
                                            </a>
                                            <form action="{{ route('manager.trails.destroy', $trail) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir esta trilha? As atribuições dos colaboradores serão perdidas.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-900 transition">
                                                    Excluir
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Links de Paginação -->
                    <div class="mt-6">
                        {{ $trails->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
