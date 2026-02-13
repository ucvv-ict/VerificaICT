<x-filament::page>

    {{-- ================= KPI ================= --}}
    <div class="fi-section grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6">

        @foreach ($this->getStats() as $stat)
            <x-filament::card>
                <div class="text-sm text-gray-500">
                    {{ $stat->getLabel() }}
                </div>

                <div class="text-3xl font-bold
                    @if($stat->getColor() === 'danger') text-danger-600
                    @elseif($stat->getColor() === 'warning') text-warning-600
                    @else text-gray-900
                    @endif">
                    {{ $stat->getValue() }}
                </div>
            </x-filament::card>
        @endforeach

    </div>


    {{-- ================= ALERT ================= --}}
    @php
        $hasCritical = collect($this->getEntitiesSummary())->sum('critici') > 0;
    @endphp

    @if($hasCritical)
        <x-filament::section class="mt-6">
            <x-filament::card class="border-danger-200 bg-danger-50">
                <div class="text-danger-700 font-semibold">
                    Sono presenti criticità nel sistema.
                </div>
            </x-filament::card>
        </x-filament::section>
    @endif


    {{-- ================= RIEPILOGO ENTI ================= --}}
    <x-filament::section class="mt-8" heading="Riepilogo Enti">

        <x-filament::card>
            <div class="overflow-x-auto">
                <table class="fi-table min-w-full text-sm">
                    <thead>
                        <tr>
                            <th class="text-left p-3">Ente</th>
                            <th class="text-center p-3">Conformità</th>
                            <th class="text-center p-3 text-danger-600">●</th>
                            <th class="text-center p-3 text-warning-600">●</th>
                            <th class="text-center p-3 text-success-600">●</th>
                            <th class="text-center p-3">Totale</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($this->getEntitiesSummary() as $row)
                            <tr class="border-t">
                                <td class="p-3 font-medium">
                                    {{ $row['entity']->nome }}
                                </td>

                                <td class="p-3 text-center font-semibold">
                                    {{ $row['percentuale'] }}%
                                </td>

                                <td class="p-3 text-center text-danger-600">
                                    {{ $row['critici'] }}
                                </td>

                                <td class="p-3 text-center text-warning-600">
                                    {{ $row['warning'] }}
                                </td>

                                <td class="p-3 text-center text-success-600">
                                    {{ $row['ok'] }}
                                </td>

                                <td class="p-3 text-center">
                                    {{ $row['totale'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::card>

    </x-filament::section>


    {{-- ================= ATTIVITÀ ================= --}}
    <x-filament::section class="mt-8" heading="Attività recente">

        <x-filament::card>
            <div class="space-y-3 text-sm">
                @forelse($this->getRecentLogs() as $log)
                    <div class="border-b pb-2 last:border-0">
                        <div class="text-gray-500 text-xs">
                            {{ $log->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            {{ $log->descrizione ?? $log->azione }}
                        </div>
                    </div>
                @empty
                    <div class="text-gray-500">
                        Nessuna attività recente.
                    </div>
                @endforelse
            </div>
        </x-filament::card>

    </x-filament::section>

</x-filament::page>
