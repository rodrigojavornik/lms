<x-guest-layout>
    <div class="mb-4 text-sm text-gray-650 text-center">
        <h2 class="text-xl font-bold text-gray-900 mb-2">Defina sua Senha Inicial</h2>
        <p class="text-xs text-gray-500">
            Olá, <strong>{{ $user->name }}</strong>! Você foi convidado para a entidade <strong>{{ $user->entity?->name }}</strong>. 
            Crie sua senha de acesso abaixo para ativar sua conta.
        </p>
    </div>

    <form method="POST" action="{{ route('accept-invitation.store', $token) }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Nova Senha')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirme a Senha')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('Definir Senha e Entrar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
