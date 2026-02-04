<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="changePassword" class="space-y-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Nuova password</label>
                <input
                    type="password"
                    wire:model.defer="password"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                />
                @error('password')
                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Conferma nuova password</label>
                <input
                    type="password"
                    wire:model.defer="password_confirmation"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                />
            </div>

            <x-filament::button type="submit">
                Aggiorna password
            </x-filament::button>
        </form>
    </div>
</x-filament-panels::page>
