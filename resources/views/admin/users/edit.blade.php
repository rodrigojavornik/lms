<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Usuário: ') }} {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6 sm:p-8 space-y-6">
                
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Editar Cadastro e Privilégios</h3>
                    <p class="text-xs text-gray-500 mt-1">Atualize as informações cadastrais básicas e selecione os papéis do usuário no sistema.</p>
                </div>

                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nome Completo</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm text-sm">
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- E-mail -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Endereço de E-mail</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm text-sm">
                        @error('email')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border-t border-gray-100 pt-6 space-y-4">
                        <h4 class="text-sm font-bold text-gray-900">Funções & Permissões</h4>
                        
                        <!-- Checkbox Instrutor -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_instructor" name="is_instructor" type="checkbox" value="1" {{ old('is_instructor', $user->is_instructor) ? 'checked' : '' }} class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded transition">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_instructor" class="font-semibold text-gray-700">Função: Instrutor</label>
                                <p class="text-xs text-gray-400">Permite criar cursos, gerenciar provas, adicionar aulas e visualizar relatórios de desempenho dos alunos.</p>
                            </div>
                        </div>

                        <!-- Checkbox Administrador -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                @if($user->id === auth()->id())
                                    <!-- Trava visual para o próprio usuário -->
                                    <input type="hidden" name="is_admin" value="1">
                                    <input id="is_admin" type="checkbox" checked disabled class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded opacity-50 cursor-not-allowed">
                                @else
                                    <input id="is_admin" name="is_admin" type="checkbox" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }} class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded transition">
                                @endif
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_admin" class="font-semibold text-gray-700">Função: Administrador</label>
                                <p class="text-xs text-gray-400">Permite acesso total à plataforma, incluindo painel administrativo geral, gerenciamento de usuários (esta tela) e estatísticas globais.</p>
                                @if($user->id === auth()->id())
                                    <p class="text-xs text-amber-600 font-semibold mt-1">⚠️ Você não pode remover sua própria permissão de administrador para evitar perder o acesso.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Checkbox Gestor de Entidade -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_entity_manager" name="is_entity_manager" type="checkbox" value="1" {{ old('is_entity_manager', $user->is_entity_manager) ? 'checked' : '' }} class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded transition">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_entity_manager" class="font-semibold text-gray-700">Função: Gestor de Entidade</label>
                                <p class="text-xs text-gray-400">Permite gerenciar colaboradores em grupos e criar trilhas de conhecimento para sua entidade.</p>
                            </div>
                        </div>

                        <!-- Seleção de Entidade -->
                        <div class="mt-4">
                            <label for="entity_id" class="block text-sm font-semibold text-gray-700 mb-1">Entidade / Organização</label>
                            <select name="entity_id" id="entity_id" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm text-sm">
                                <option value="">Nenhuma (Independente / Sem Entidade)</option>
                                @foreach($entities as $entity)
                                    <option value="{{ $entity->id }}" {{ old('entity_id', $user->entity_id) == $entity->id ? 'selected' : '' }}>
                                        {{ $entity->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Vincule o usuário a uma entidade específica. Instrutores criam cursos específicos desta entidade.</p>
                            @error('entity_id')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-xs rounded-lg uppercase tracking-wider hover:bg-gray-50 transition shadow-sm">
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
