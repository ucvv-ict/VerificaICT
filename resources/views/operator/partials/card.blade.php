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

    {{-- TAG --}}
    @if($task->securityTask->tags->isNotEmpty())
        <div class="mt-2 flex flex-wrap gap-1">
            @foreach($task->securityTask->tags as $tag)
                <span class="bg-gray-200 text-gray-700 text-xs px-2 py-0.5 rounded">
                    {{ $tag->nome }}
                </span>
            @endforeach
        </div>
    @endif

    {{-- RESPONSABILE --}}
    <div class="text-xs text-gray-500 mt-1">
        Responsabile:
        {{ $task->responsabile->name ?? 'Non assegnato' }}
    </div>

    {{-- Ultimo controllo --}}
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

    {{-- Pulsanti --}}
    <div class="mt-3 flex flex-wrap gap-2">

        <form method="POST"
              action="{{ route('operator.quick-check', $task->id) }}">
            @csrf
            <input type="hidden" name="esito" value="ok">
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                OK
            </button>
        </form>

        <form method="POST"
              action="{{ route('operator.quick-check', $task->id) }}">
            @csrf
            <input type="hidden" name="esito" value="ko">
            <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                KO
            </button>
        </form>

        <form method="POST"
              action="{{ route('operator.quick-check', $task->id) }}">
            @csrf
            <input type="hidden" name="esito" value="na">
            <button type="submit"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-xs">
                NA
            </button>
        </form>

        <a href="{{ route('operator.check.show', $task->id) }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs">
            Dettaglio
        </a>

    </div>

</div>
