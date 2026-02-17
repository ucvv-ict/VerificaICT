<x-filament::page>

    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-950">

        <div class="w-full max-w-md space-y-6">

            {{-- Logo --}}
            <div class="flex justify-center">
                <img src="{{ asset('images/logo-verificaict.png') }}"
                     alt="VerificaICT"
                     class="h-10">
            </div>

            {{-- Card --}}
            <div class="bg-white dark:bg-gray-900 p-8 rounded-xl shadow-xl space-y-6">

                <div class="text-center space-y-2">
                    <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full
                        bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                        Configurazione sicurezza
                    </span>

                    <h1 class="text-2xl font-semibold">
                        Attiva Autenticazione 2FA
                    </h1>

                    <p class="text-sm text-gray-500">
                        Scansiona il QR code con la tua app di autenticazione.
                    </p>
                </div>

                @if($qrCodeSvg)
                    <div class="flex justify-center">
                        <div class="p-4 bg-white rounded-lg shadow">
                            {!! $qrCodeSvg !!}
                        </div>
                    </div>
                @endif

                <x-filament::input
                    wire:model.defer="otp"
                    type="text"
                    maxlength="6"
                    placeholder="Inserisci codice OTP"
                />

                <x-filament::button
                    wire:click="confirmTwoFactorSetup"
                    class="w-full"
                >
                    Conferma attivazione
                </x-filament::button>

            </div>

        </div>

    </div>

</x-filament::page>
