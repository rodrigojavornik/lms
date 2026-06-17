<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nova Aula: ') }} {{ $course->title }}
            </h2>
            <a href="{{ route('instructor.courses.edit', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                Cancelar
            </a>
        </div>
    </x-slot>

    <!-- Quill Editor Styles -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />

    <style>
        .ql-editor {
            min-height: 250px;
            font-size: 0.875rem; /* text-sm equivalent */
            line-height: 1.625;
            font-family: inherit;
        }
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            border-color: #d1d5db;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            border-color: #d1d5db;
        }
    </style>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100 p-6 sm:p-8 space-y-6">
                
                <div class="border-b border-gray-100 pb-4">
                    <h3 class="text-lg font-bold text-gray-900">Registrar Aula</h3>
                    <p class="text-xs text-gray-500 mt-1">Preencha o título, a URL do vídeo do YouTube opcional, e use o editor de texto rico para o conteúdo complementar.</p>
                </div>

                <form action="{{ route('instructor.lessons.store', $course) }}" method="POST" id="lessonForm">
                    @csrf

                    <!-- Grid: Título e Vídeo -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Título da Aula -->
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Título da Aula <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" id="title" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm" placeholder="Ex: Introdução ao Eloquent">
                            @error('title')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Link do Vídeo (YouTube) -->
                        <div>
                            <label for="video_url" class="block text-sm font-semibold text-gray-700 mb-1">URL do Vídeo (YouTube - Opcional)</label>
                            <input type="url" name="video_url" id="video_url" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm" placeholder="Ex: https://www.youtube.com/watch?v=...">
                            @error('video_url')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Conteúdo Textual (Rich Text Editor) -->
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Conteúdo Textual (Rich Text)</label>
                        
                        <!-- Quill editor container -->
                        <div id="editor-wrapper" class="rounded-lg shadow-sm">
                            <div id="editor"></div>
                        </div>

                        <!-- Hidden Input to store the HTML content -->
                        <input type="hidden" name="content" id="content">
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('instructor.courses.edit', $course) }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-xs rounded-lg uppercase tracking-wider hover:bg-gray-50 transition shadow-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-lg transition shadow-md">
                            Salvar Aula
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Quill Editor Library -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa o Quill
            const quill = new Quill('#editor', {
                theme: 'snow',
                placeholder: 'Escreva e formate o conteúdo e material da aula aqui...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        ['link', 'image'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                }
            });

            const form = document.getElementById('lessonForm');
            const contentInput = document.getElementById('content');

            // Intercepta submit para copiar HTML do editor para a input oculta
            form.addEventListener('submit', function(e) {
                // Pega o conteúdo semântico HTML gerado pelo Quill
                const html = quill.getSemanticHTML();
                
                // Se o editor estiver vazio (contendo apenas tag vazia), define como nulo
                if (quill.getText().trim() === '') {
                    contentInput.value = '';
                } else {
                    contentInput.value = html;
                }
            });

            // Sobrescreve o handler de imagem padrão para fazer upload AJAX
            const toolbar = quill.getModule('toolbar');
            toolbar.addHandler('image', function() {
                const fileInput = document.createElement('input');
                fileInput.setAttribute('type', 'file');
                fileInput.setAttribute('accept', 'image/*');
                fileInput.click();

                fileInput.onchange = async () => {
                    const file = fileInput.files[0];
                    if (file) {
                        // Validação básica de tamanho (ex: 2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            alert('A imagem não pode ter mais do que 2MB.');
                            return;
                        }

                        const formData = new FormData();
                        formData.append('image', file);

                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        // Indicador visual de progresso
                        const range = quill.getSelection(true);
                        quill.insertText(range.index, '[Enviando imagem...]', { italic: true, color: '#999' });

                        try {
                            const response = await fetch('{{ route("instructor.lessons.upload-image") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: formData
                            });

                            // Deleta o texto de progresso (comprimento do texto de progresso = 20)
                            quill.deleteText(range.index, 20);

                            if (response.ok) {
                                const data = await response.json();
                                // Insere a imagem
                                quill.insertEmbed(range.index, 'image', data.url);
                                // Move o cursor para depois da imagem
                                quill.setSelection(range.index + 1);
                            } else {
                                const errData = await response.json();
                                alert(errData.message || 'Erro ao realizar upload da imagem.');
                            }
                        } catch (error) {
                            console.error('Error uploading image:', error);
                            quill.deleteText(range.index, 20);
                            alert('Erro na conexão com o servidor.');
                        }
                    }
                };
            });
        });
    </script>
</x-app-layout>
