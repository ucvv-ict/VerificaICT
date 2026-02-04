<x-filament-panels::page>
    <div class="space-y-6">
        <div class="text-sm text-gray-600">
            Scansiona il QR code con Google Authenticator (o app compatibile), poi inserisci il codice OTP a 6 cifre.
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <div class="w-56 max-w-full overflow-hidden">
            @if (str_starts_with($this->qrCodeSvg, 'data:image'))
                <img
                    src="{{ $this->qrCodeSvg }}"
                    alt="QR code 2FA"
                    class="h-56 w-56 object-contain"
                />
            @else
                {!! $this->qrCodeSvg !!}
            @endif
            </div>
        </div>

        <form wire:submit="confirmTwoFactorSetup" class="space-y-4">
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

            <x-filament::button type="submit" color="success">
                Ho configurato il 2FA
            </x-filament::button>
        </form>
    </div>
</x-filament-panels::page>
