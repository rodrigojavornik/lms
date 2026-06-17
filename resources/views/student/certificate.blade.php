<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado - {{ $course->title }}</title>
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;800&family=Montserrat:wght@400;500;600;700&family=Pinyon+Script&display=swap');
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f3f4f6;
        }

        .serif-title {
            font-family: 'Cinzel', serif;
        }

        .cursive-signature {
            font-family: 'Pinyon Script', cursive;
        }

        /* Configuração de Impressão */
        @media print {
            body {
                background-color: #ffffff;
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                background-color: #ffffff !important;
            }
            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-4 sm:p-8">

    <!-- Barra de Controle Superior (Não imprimível) -->
    <div class="no-print w-full max-w-5xl flex justify-between items-center mb-6 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-900 transition">
            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Voltar ao painel
        </a>
        <button onclick="window.print()" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-sm transition">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimir ou Salvar PDF
        </button>
    </div>

    <!-- Container do Certificado (Estrutura Paisagem A4) -->
    <div class="print-container w-full max-w-5xl aspect-[1.414/1] bg-white border-[16px] border-double border-indigo-950 p-8 sm:p-12 shadow-2xl relative flex flex-col justify-between overflow-hidden" style="background-image: radial-gradient(circle, rgba(243, 244, 246, 0.3) 0%, rgba(255, 255, 255, 0.9) 100%);">
        
        <!-- Detalhes de Cantos Ornamentados (Fronteira Clássica) -->
        <div class="absolute top-2 left-2 w-12 h-12 border-t-4 border-l-4 border-indigo-950"></div>
        <div class="absolute top-2 right-2 w-12 h-12 border-t-4 border-r-4 border-indigo-950"></div>
        <div class="absolute bottom-2 left-2 w-12 h-12 border-b-4 border-l-4 border-indigo-950"></div>
        <div class="absolute bottom-2 right-2 w-12 h-12 border-b-4 border-r-4 border-indigo-950"></div>

        <!-- Marca D'água Decorativa de Fundo -->
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
            <svg class="w-[500px] h-[500px] text-indigo-950" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L2 22h20L12 2zm0 3.99L19.53 19H4.47L12 5.99zM12 9c-.55 0-1 .45-1 1v4c0 .55.45 1 1 1s1-.45 1-1v-4c0-.55-.45-1-1-1zm0 8c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1z"/>
            </svg>
        </div>

        <!-- Cabeçalho do Certificado -->
        <div class="text-center space-y-2 z-10">
            <div class="flex items-center justify-center mb-1">
                <!-- Logo LMS Simplificado -->
                <div class="bg-indigo-950 text-white p-2 rounded-lg font-bold text-lg tracking-wider">LMS</div>
            </div>
            <span class="text-xs font-bold text-indigo-900 uppercase tracking-widest block">Certificado de Conclusão</span>
            <div class="w-16 h-[2px] bg-amber-500 mx-auto mt-1"></div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="text-center space-y-6 z-10 my-4">
            <p class="text-gray-500 text-sm italic font-medium">Certificamos para os devidos fins que</p>
            
            <h1 class="serif-title text-3xl sm:text-4xl font-extrabold text-indigo-950 tracking-wide border-b border-gray-100 pb-2 inline-block px-12">
                {{ auth()->user()->name }}
            </h1>
            
            <p class="text-gray-600 text-sm max-w-2xl mx-auto leading-relaxed">
                concluiu com aproveitamento e êxito todos os requisitos teóricos e avaliativos do curso livre de aperfeiçoamento profissional em
                <span class="block text-xl sm:text-2xl font-bold text-indigo-950 mt-2 serif-title tracking-tight">{{ $course->title }}</span>
            </p>
        </div>

        <!-- Rodapé do Certificado (Assinaturas, QR Code e Verificação) -->
        <div class="grid grid-cols-3 items-end gap-6 z-10 pt-4 border-t border-gray-100">
            
            <!-- Assinatura Diretor -->
            <div class="text-center pb-2">
                <span class="cursive-signature text-3xl text-indigo-900 block leading-none">LMS Plataforma</span>
                <div class="w-32 h-[1px] bg-gray-300 mx-auto mt-2"></div>
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mt-1">Diretor Acadêmico</span>
            </div>

            <!-- Selo de Verificação Central -->
            <div class="text-center pb-2 flex flex-col items-center justify-center">
                <div class="h-14 w-14 rounded-full border-2 border-amber-500 flex items-center justify-center bg-amber-50 shadow-inner">
                    <svg class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span class="text-[9px] font-bold text-amber-700 uppercase tracking-widest mt-2 block">Autenticidade Garantida</span>
            </div>

            <!-- QR Code e Código de Verificação -->
            <div class="flex items-center space-x-3 justify-end">
                <div class="text-right">
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block">Código do Certificado</span>
                    <span class="text-xs font-bold text-indigo-950 block font-mono">{{ $certificate->verification_code }}</span>
                    <span class="text-[9px] font-medium text-gray-400 block mt-0.5">Emitido em: {{ $certificate->issued_at->format('d/m/Y') }}</span>
                </div>
                <div class="border p-1.5 bg-white rounded shadow-sm">
                    <img src="{{ $qrCodeUrl }}" alt="QR Code de Verificação" class="h-16 w-16">
                </div>
            </div>

        </div>

    </div>

</body>
</html>
