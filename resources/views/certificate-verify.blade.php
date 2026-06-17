<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de Autenticidade de Certificado</title>
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Montserrat:wght@400;500;600;700;800&display=swap');
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .serif-title {
            font-family: 'Cinzel', serif;
        }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4 sm:p-6">

    <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        
        <!-- Faixa Superior Decorativa -->
        <div class="h-3 bg-gradient-to-r {{ $certificate ? 'from-emerald-500 to-teal-600' : 'from-rose-500 to-orange-600' }}"></div>

        <div class="p-8 text-center space-y-6">

            @if($certificate)
                <!-- CASO VÁLIDO -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-inner">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>

                <div class="space-y-1">
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Certificado Autêntico</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Documento Válido
                    </span>
                </div>

                <div class="w-full h-[1px] bg-gray-100 my-4"></div>

                <!-- Detalhes do Aluno e Curso -->
                <div class="space-y-4 text-left">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 space-y-3">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Estudante</span>
                            <span class="text-base font-bold text-gray-900 block">{{ $certificate->user->name }}</span>
                        </div>
                        
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Curso Concluído</span>
                            <span class="text-sm font-semibold text-indigo-950 block serif-title">{{ $certificate->course->title }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-1">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Emissão</span>
                                <span class="text-xs font-bold text-gray-700 block">{{ $certificate->issued_at->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Código de Registro</span>
                                <span class="text-xs font-bold text-indigo-950 block font-mono">{{ $certificate->verification_code }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-gray-500 leading-relaxed max-w-sm mx-auto">
                    Este documento digital atesta que o aluno concluiu com sucesso todas as etapas, aulas e avaliações exigidas pela plataforma de ensino.
                </p>

            @else
                <!-- CASO INVÁLIDO -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 text-rose-600 border border-rose-100 shadow-inner">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <div class="space-y-1">
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Certificado Não Encontrado</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                        Código Inválido
                    </span>
                </div>

                <div class="w-full h-[1px] bg-gray-100 my-4"></div>

                <div class="p-4 bg-rose-50/50 rounded-xl border border-rose-100 text-center">
                    <p class="text-xs text-rose-800 font-medium">O código de autenticidade <strong class="font-mono text-sm block mt-1.5 text-rose-950">{{ $code }}</strong> não corresponde a nenhum certificado gerado ou registrado em nosso sistema.</p>
                </div>

                <p class="text-xs text-gray-500 leading-relaxed max-w-sm mx-auto">
                    Verifique se o código foi digitado corretamente ou se o QR Code lido é válido. Em caso de dúvidas, entre em contato com o suporte acadêmico.
                </p>
            @endif

            <div class="pt-4 border-t border-gray-100 flex justify-center">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center">
                    <!-- Logo LMS -->
                    <span class="bg-gray-800 text-white px-2 py-0.5 rounded mr-1">LMS</span> Plataforma de Ensino
                </div>
            </div>

        </div>

    </div>

</body>
</html>
