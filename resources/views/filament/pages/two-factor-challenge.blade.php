<x-filament::page>

    <div class="min-h-screen flex items-center justify-center">

        <div class="w-full max-w-md bg-white dark:bg-gray-900 p-8 rounded-xl shadow-lg space-y-6">

            {{-- Logo --}}
            <div class="flex justify-center">
                <img src="{{ asset('images/logo-verificaict.png') }}"
                     alt="VerificaICT"
                     class="h-10">
            </div>

            <div class="text-center space-y-2">
                <h1 class="text-2xl font-semibold">
                    Verifica Autenticazione
                </h1>
                <p class="text-sm text-gray-500">
                    Inserisci il codice OTP generato dalla tua app.
                </p>
            </div>

            <x-filament::input
                wire:model.defer="otp"
                type="text"
                maxlength="6"
                placeholder="Codice OTP"
            />

            <x-filament::button
                wire:click="verifyOtp"
                class="w-full"
            >
                Verifica
            </x-filament::button>

            <form method="POST" action="{{ filament()->getLogoutUrl() }}">
                @csrf
                <button type="submit"
                    class="mt-4 w-full text-sm text-gray-500 hover:text-gray-700 underline">
                    Cambia utente
                </button>
            </form>

        </div>

    </div>

</x-filament::page>
