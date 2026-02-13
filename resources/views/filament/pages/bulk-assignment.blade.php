<x-filament::page>

    <div class="mb-2 text-sm text-gray-600">
        Enti selezionati:
        <span class="font-semibold">
            {{ count($this->data['entities'] ?? []) }}
        </span>
    </div>

    <div class="mb-4 text-sm text-gray-600">
        Task coinvolti:
        <span class="font-semibold">
            {{ $this->getResolvedTasksCount() }}
        </span>
    </div>

    {{ $this->form }}

</x-filament::page>
