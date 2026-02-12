@extends('layouts.operator')

@section('content')

<h1 class="text-xl font-bold mb-6">
    Dashboard Globale Enti
</h1>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="text-left p-3">Ente</th>
                <th class="p-3 text-center">Conformità</th>
                <th class="p-3 text-center">🔴 Critici</th>
                <th class="p-3 text-center">🟡 Warning</th>
                <th class="p-3 text-center">🟢 OK</th>
                <th class="p-3 text-center">Totale</th>
            </tr>
        </thead>
        <tbody>
        @foreach($data as $row)
            <tr class="border-t">
                <td class="p-3 font-semibold">
                    <a href="{{ route('operator.dashboard') }}?entity={{ $row['entity']->id }}"
                    class="hover:underline">
                        {{ $row['entity']->nome }}
                    </a>

                    @if($row['critici'] > 0)
                        <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-700 rounded">
                            Criticità
                        </span>
                    @elseif($row['warning'] > 0)
                        <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded">
                            Attenzione
                        </span>
                    @else
                        <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-700 rounded">
                            OK
                        </span>
                    @endif
                </td>

                <td class="p-3 text-center">
                    <div class="w-full bg-gray-200 rounded h-3">
                        <div class="h-3 rounded
                            {{ $row['percentuale'] >= 80 ? 'bg-green-500' :
                            ($row['percentuale'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                            style="width: {{ $row['percentuale'] }}%">
                        </div>
                    </div>
                    <div class="text-xs mt-1">
                        {{ $row['percentuale'] }}%
                    </div>
                </td>

                <td class="p-3 text-center">
                    <span class="font-bold text-red-600">
                        {{ $row['critici'] }}
                    </span>
                </td>

                <td class="p-3 text-center">
                    <span class="font-bold text-yellow-600">
                        {{ $row['warning'] }}
                    </span>
                </td>

                <td class="p-3 text-center">
                    <span class="font-bold text-green-600">
                        {{ $row['ok'] }}
                    </span>
                </td>

                <td class="p-3 text-center">
                    {{ $row['totale'] }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection
