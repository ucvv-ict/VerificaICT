@php
    $color = match($task->current_status) {
        'verde' => 'border-green-400',
        'arancione' => 'border-yellow-400',
        default => 'border-red-400',
    };
@endphp

<div class="bg-white shadow rounded-lg p-4 mb-3 border-l-4 {{ $color }}">

    <div class="text-sm text-gray-500">
        {{ $task->entity->nome }}
    </div>

    <div class="font-semibold">
        {{ $task->securityTask->titolo }}
    </div>

    @if($task->latestCheck)
        <div class="text-sm text-gray-600 mt-2">
            Ultimo controllo:
            {{ $task->latestCheck->checked_at->format('d/m/Y H:i') }}
            —
            {{ strtoupper($task->latestCheck->esito) }}
            —
            {{ $task->latestCheck->user->name ?? 'N/D' }}
        </div>
    @else
        <div class="text-sm text-gray-600 mt-2">
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
