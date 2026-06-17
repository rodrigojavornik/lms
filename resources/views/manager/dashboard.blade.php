<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Painel do Gestor de Entidade') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('manager.invite.show') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150 shadow-md" style="background-color: #2563eb; color: #ffffff;">
                    Convidar Membro
                </a>
                <a href="{{ route('manager.groups.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none transition ease-in-out duration-150 shadow-md">
                    Gerenciar Grupos
                </a>
                <a href="{{ route('manager.trails.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none transition ease-in-out duration-150 shadow-md">
                    Gerenciar Trilhas
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                    <span class="block sm:inline font-semibold">{{ session('success') }}</span>
                    @if(session('invitation_link'))
                        <div class="mt-3 p-3 bg-white rounded-lg border border-emerald-100 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-inner">
                            <div class="text-xs text-gray-700 flex-1">
                                Compartilhe este link de convite com <strong>{{ session('invited_name') }}</strong> para que ele(a) crie sua própria senha:
                                <input type="text" readonly id="inv-link" value="{{ session('invitation_link') }}" class="w-full mt-1.5 px-3 py-1 rounded bg-gray-50 border border-gray-200 text-indigo-700 font-mono text-xs select-all">
                            </div>
                            <button onclick="copyInvitationLink()" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs font-bold shadow transition shrink-0">
                                Copiar Link
                            </button>
                        </div>
                        <script>
                            function copyInvitationLink() {
                                const copyText = document.getElementById("inv-link");
                                copyText.select();
                                copyText.setSelectionRange(0, 99999);
                                navigator.clipboard.writeText(copyText.value);
                            }
                        </script>
                    @endif
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Banner de Boas-Vindas Gestor -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-teal-850 via-teal-900 to-indigo-950 p-8 shadow-lg text-white" style="background: linear-gradient(to right, #0f766e, #115e59, #312e81);">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-teal-500/10 blur-xl"></div>
                <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-indigo-500/10 blur-xl"></div>
                <div class="relative z-10 space-y-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm">
                        {{ auth()->user()->entity?->name ?? 'Sem Entidade' }}
                    </span>
                    <h2 class="text-3xl font-extrabold tracking-tight">Olá, {{ auth()->user()->name }}! 🏢</h2>
                    <p class="text-teal-100 text-sm max-w-xl">Gerencie os colaboradores da sua entidade, organize-os em grupos de trabalho e acompanhe o progresso individual nas trilhas de conhecimento atribuídas.</p>
                </div>
            </div>

            <!-- Métricas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Colaboradores -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Colaboradores</span>
                    <span class="text-3xl font-extrabold text-gray-900 mt-2 block">{{ $stats['total_users'] }}</span>
                </div>
                <!-- Grupos -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Grupos</span>
                    <span class="text-3xl font-extrabold text-indigo-600 mt-2 block">{{ $stats['total_groups'] }}</span>
                </div>
                <!-- Trilhas -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Trilhas de Conhecimento</span>
                    <span class="text-3xl font-extrabold text-emerald-600 mt-2 block">{{ $stats['total_trails'] }}</span>
                </div>
            </div>

            <!-- Listagem de Colaboradores e Progresso -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Acompanhamento de Colaboradores</h3>

                @if($collaborators->isEmpty())
                    <p class="text-sm text-gray-500 py-8 text-center">Nenhum colaborador cadastrado nesta entidade ainda.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Colaborador</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Grupos</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Progresso das Trilhas</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-sm">
                                @foreach($collaborators as $collab)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <!-- Nome/Email -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-bold text-gray-900">{{ $collab->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $collab->email }}</div>
                                        </td>
                                        
                                        <!-- Grupos -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($collab->groups->isEmpty())
                                                <span class="text-xs text-gray-450 italic">Sem grupo</span>
                                            @else
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($collab->groups as $group)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                            {{ $group->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Progresso Trilhas -->
                                        <td class="px-6 py-4">
                                            @if($collab->assigned_trails->isEmpty())
                                                <span class="text-xs text-gray-400 italic">Nenhuma trilha atribuída</span>
                                            @else
                                                <div class="space-y-3 max-w-xs">
                                                    @foreach($collab->assigned_trails as $trail)
                                                        @php
                                                            $progress = $collab->trailProgress($trail);
                                                        @endphp
                                                        <div class="space-y-1">
                                                            <div class="flex justify-between text-xs font-bold">
                                                                <span class="text-gray-700 truncate mr-2">{{ $trail->name }}</span>
                                                                <span class="{{ $progress === 100 ? 'text-emerald-600' : 'text-indigo-600' }}">{{ $progress }}%</span>
                                                            </div>
                                                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                                                <div class="h-full rounded-full {{ $progress === 100 ? 'bg-emerald-500' : 'bg-indigo-600' }}" style="width: {{ $progress }}%"></div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
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
