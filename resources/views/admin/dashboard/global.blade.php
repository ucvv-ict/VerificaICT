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
                    {{ $row['entity']->nome }}
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
