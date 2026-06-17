<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Trilha: ') }} {{ $trail->name }}
            </h2>
            <a href="{{ route('manager.trails.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6 sm:p-8 space-y-6">
                
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Editar Trilha de Conhecimento</h3>
                    <p class="text-xs text-gray-500 mt-1">Atualize as informações cadastrais e altere a composição ou a ordem dos cursos da trilha.</p>
                </div>

                <form action="{{ route('manager.trails.update', $trail) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nome da Trilha</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $trail->name) }}" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm text-sm">
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descrição -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Descrição / Objetivos da Trilha</label>
                        <textarea name="description" id="description" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm text-sm">{{ old('description', $trail->description) }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Seleção de Cursos e Posição -->
                    <div class="border-t border-gray-100 pt-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Selecionar e Ordenar Cursos</label>
                        <p class="text-xs text-gray-400 mb-4">Marque os cursos que devem pertencer a esta trilha e defina a ordem de execução.</p>
                        
                        @if($courses->isEmpty())
                            <p class="text-xs text-amber-600 font-semibold">⚠️ Não há cursos cadastrados disponíveis para esta trilha.</p>
                        @else
                            <!-- Filtro de Cursos -->
                            <div class="mb-4">
                                <input type="text" id="course-filter-input" placeholder="🔍 Filtrar cursos..." onkeyup="filterTrailCourses(this.value)" class="w-full px-3 py-1.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs shadow-sm">
                            </div>
                            <p id="courses-empty-msg" class="text-xs text-gray-450 italic pb-2 text-center" style="display: none;">Nenhum curso correspondente encontrado.</p>

                            <div class="space-y-3 p-4 border border-gray-150 rounded-lg max-h-80 overflow-y-auto">
                                @foreach($courses as $course)
                                    @php
                                        $isChecked = in_array($course->id, $selectedCourseIds);
                                        $posValue = isset($coursePositions[$course->id]) ? $coursePositions[$course->id] : 1;
                                    @endphp
                                    <div class="course-row flex items-center justify-between p-2 hover:bg-gray-50 rounded transition" data-title="{{ $course->title }}">
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5 mt-0.5">
                                                <input id="course_{{ $course->id }}" name="courses[]" type="checkbox" value="{{ $course->id }}" {{ $isChecked ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded transition">
                                            </div>
                                            <div class="ml-3 text-xs">
                                                <label for="course_{{ $course->id }}" class="font-bold text-gray-800 cursor-pointer">{{ $course->title }}</label>
                                                <div class="flex items-center space-x-1.5 mt-0.5">
                                                    @if($course->entity_id !== auth()->user()->entity_id)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-100">
                                                            Compartilhado ({{ $course->entity?->name ?? 'Independente' }})
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-teal-50 text-teal-850 border border-teal-100">
                                                            Sua Entidade
                                                        </span>
                                                    @endif
                                                    @if($course->requires_quiz)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-rose-50 text-rose-800 border border-rose-100">
                                                            Requer Prova
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Posição da Trilha -->
                                        <div class="flex items-center space-x-1.5">
                                            <label for="pos_{{ $course->id }}" class="text-[10px] text-gray-400 font-bold">Posição:</label>
                                            <input type="number" name="positions[{{ $course->id }}]" id="pos_{{ $course->id }}" value="{{ old('positions.' . $course->id, $posValue) }}" min="1" class="w-12 text-xs py-1 px-1.5 border border-gray-300 rounded text-center focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('courses')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('manager.trails.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-xs rounded-lg uppercase tracking-wider hover:bg-gray-50 transition shadow-sm">
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
    <!-- Script de Filtro Instantâneo para Edição de Trilha -->
    <script>
        function filterTrailCourses(query) {
            const rows = document.querySelectorAll('.course-row');
            const cleanQuery = query.toLowerCase().trim();
            let visibleCount = 0;

            rows.forEach(row => {
                const title = row.getAttribute('data-title').toLowerCase();
                if (title.includes(cleanQuery)) {
                    row.style.setProperty('display', 'flex', 'important');
                    visibleCount++;
                } else {
                    row.style.setProperty('display', 'none', 'important');
                }
            });

            const emptyElement = document.getElementById('courses-empty-msg');
            if (emptyElement) {
                emptyElement.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }
    </script>
</x-app-layout>
