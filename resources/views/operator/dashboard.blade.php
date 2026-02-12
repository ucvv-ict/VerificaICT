@extends('layouts.operator')

@section('content')
<div class="max-w-4xl mx-auto p-4">

    <h1 class="text-xl font-bold mb-4">
        Dashboard Operatore
    </h1>

    @forelse($tasks as $task)
        <div class="bg-white shadow rounded-lg p-4 mb-3 border">

            <div class="text-sm text-gray-500">
                {{ $task->entity->nome }}
            </div>

            <div class="font-semibold">
                {{ $task->securityTask->titolo }}
            </div>

            <div class="text-sm mt-2">
                Stato:
                @php
                    $color = match($task->current_status) {
                        'verde' => 'bg-green-100 text-green-800',
                        'arancione' => 'bg-yellow-100 text-yellow-800',
                        default => 'bg-red-100 text-red-800',
                    };
                @endphp

                <span class="px-2 py-1 rounded text-xs {{ $color }}">
                    {{ strtoupper($task->current_status) }}
                </span>
            </div>

            @if($task->latestCheck)
                <div class="text-sm text-gray-600">
                    Ultimo controllo:
                    {{ $task->latestCheck->checked_at->format('d/m/Y') }}
                    ({{ strtoupper($task->latestCheck->esito) }})
                </div>
            @else
                <div class="text-sm text-gray-600">
                    Mai eseguito
                </div>
            @endif

            <div class="mt-3">
                <a href="{{ route('operator.check.show', $task->id) }}"
                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded text-sm">
                    Esegui controllo
                </a>
            </div>

        </div>
    @empty
        <p>Nessun controllo assegnato.</p>
    @endforelse

</div>
@endsection
