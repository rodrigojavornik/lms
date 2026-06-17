<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Compartilhar Curso: ') }} {{ $course->title }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6 sm:p-8 space-y-6">
                
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Compartilhamento Granular por Entidade</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Este curso pertence à entidade: <strong class="text-indigo-600">{{ $course->entity?->name ?? 'Independente (Sistema)' }}</strong>.
                        Selecione abaixo quais outras entidades podem disponibilizar este curso aos seus colaboradores.
                    </p>
                </div>

                <form action="{{ route('admin.courses.share.update', $course) }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Seleção de Entidades -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-750">Selecione as Entidades Liberadas</label>
                        
                        @if($entities->isEmpty())
                            <p class="text-xs text-gray-400 italic">Nenhuma outra entidade cadastrada no sistema.</p>
                        @else
                            <div class="space-y-3 p-4 border border-gray-150 rounded-lg max-h-72 overflow-y-auto">
                                @foreach($entities as $entity)
                                    @php
                                        $isShared = in_array($entity->id, $sharedEntityIds);
                                    @endphp
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="entity_{{ $entity->id }}" name="entities[]" type="checkbox" value="{{ $entity->id }}" {{ $isShared ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded transition">
                                        </div>
                                        <div class="ml-3 text-xs">
                                            <label for="entity_{{ $entity->id }}" class="font-bold text-gray-750 cursor-pointer flex items-center">
                                                {{ $entity->name }}
                                                @if($isShared)
                                                    <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                        Liberado
                                                    </span>
                                                @endif
                                            </label>
                                            <span class="text-gray-400 block mt-0.5">{{ $entity->description ?? 'Sem descrição.' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('entities')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-xs rounded-lg uppercase tracking-wider hover:bg-gray-50 transition shadow-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-lg transition shadow-md">
                            Salvar Liberações
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
