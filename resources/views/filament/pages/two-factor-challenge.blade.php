<x-filament-panels::page>
    <div class="space-y-6">
        <div class="text-sm text-gray-600">
            Inserisci il codice OTP generato dalla tua app di autenticazione per completare l'accesso.
        </div>

        <form wire:submit="verifyOtp" class="space-y-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Codice OTP</label>
                <input
                    type="text"
                    inputmode="numeric"
                    maxlength="6"
                    wire:model.defer="otp"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    placeholder="123456"
                />
                @error('otp')
                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <x-filament::button type="submit">
                Verifica codice
            </x-filament::button>
        </form>

        <form action="{{ filament()->getLogoutUrl() }}" method="post">
            @csrf

            <x-filament::button type="submit" color="gray">
                Esci
            </x-filament::button>
        </form>
    </div>
</x-filament-panels::page>
