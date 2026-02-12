@extends('layouts.operator')

@section('content')

<h1 class="text-xl font-bold mb-6">
    Audit Log Amministrativo
</h1>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3 text-left">Data</th>
                <th class="p-3 text-left">Utente</th>
                <th class="p-3 text-left">Azione</th>
                <th class="p-3 text-left">Dettagli</th>
            </tr>
        </thead>
        <tbody>
        @foreach($logs as $log)
            <tr class="border-t">
                <td class="p-3">
                    {{ $log->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="p-3">
                    {{ $log->user->name }}
                </td>
                <td class="p-3 font-semibold">
                    {{ $log->action }}
                </td>
                <td class="p-3 text-xs">
                    <pre>{{ json_encode($log->metadata, JSON_PRETTY_PRINT) }}</pre>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $logs->links() }}
</div>

@endsection
