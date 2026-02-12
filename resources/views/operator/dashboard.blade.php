@extends('layouts.operator')

@section('content')

<h1 class="text-xl font-bold mb-4">
    Dashboard Operatore
</h1>
<form method="GET" class="bg-white p-4 rounded shadow mb-6 flex flex-wrap gap-4 items-end">

    {{-- Ente --}}
    <div>
        <label class="block text-xs font-semibold mb-1">Ente</label>
        <select name="entity" class="border rounded p-2 text-sm">
            <option value="">Tutti</option>
            @foreach($entities as $entity)
                <option value="{{ $entity->id }}"
                    {{ request('entity') == $entity->id ? 'selected' : '' }}>
                    {{ $entity->nome }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Tag --}}
    <div>
        <label class="block text-xs font-semibold mb-1">Tag</label>
        <select name="tag" class="border rounded p-2 text-sm">
            <option value="">Tutti</option>
            @foreach($tags as $tag)
                <option value="{{ $tag->id }}"
                    {{ request('tag') == $tag->id ? 'selected' : '' }}>
                    {{ $tag->nome }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Stato --}}
    <div>
        <label class="block text-xs font-semibold mb-1">Stato</label>
        <select name="status" class="border rounded p-2 text-sm">
            <option value="">Tutti</option>
            <option value="rosso" {{ request('status') == 'rosso' ? 'selected' : '' }}>Critici</option>
            <option value="arancione" {{ request('status') == 'arancione' ? 'selected' : '' }}>In scadenza</option>
            <option value="verde" {{ request('status') == 'verde' ? 'selected' : '' }}>Regolari</option>
        </select>
    </div>

    <div>
        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded text-sm">
            Filtra
        </button>
    </div>

    <div>
        <a href="{{ route('operator.dashboard') }}"
           class="text-sm text-gray-600 underline">
            Reset
        </a>
    </div>

</form>

{{-- CONTATORI --}}
<div class="grid grid-cols-3 gap-3 mb-6">

    <div class="bg-red-100 text-red-800 p-3 rounded text-center">
        <div class="text-xl font-bold">{{ $counts['rosso'] }}</div>
        <div class="text-xs uppercase">Critici</div>
    </div>

    <div class="bg-yellow-100 text-yellow-800 p-3 rounded text-center">
        <div class="text-xl font-bold">{{ $counts['arancione'] }}</div>
        <div class="text-xs uppercase">In scadenza</div>
    </div>

    <div class="bg-green-100 text-green-800 p-3 rounded text-center">
        <div class="text-xl font-bold">{{ $counts['verde'] }}</div>
        <div class="text-xs uppercase">Regolari</div>
    </div>

</div>

{{-- BLOCCO ROSSO --}}
@if($grouped->get('rosso', collect())->isNotEmpty())
    <details open class="mb-4">
        <summary class="cursor-pointer text-lg font-bold text-red-700">
            🔴 Controlli Critici ({{ $counts['rosso'] }})
        </summary>

        <div class="mt-3">
            @foreach($grouped->get('rosso', collect()) as $task)
                @include('operator.partials.card', ['task' => $task])
            @endforeach
        </div>
    </details>
@endif

{{-- BLOCCO ARANCIONE --}}
@if($grouped->get('arancione', collect())->isNotEmpty())
    <details class="mb-4">
        <summary class="cursor-pointer text-lg font-bold text-yellow-700">
            🟡 In Scadenza ({{ $counts['arancione'] }})
        </summary>

        <div class="mt-3">
            @foreach($grouped->get('arancione', collect()) as $task)
                @include('operator.partials.card', ['task' => $task])
            @endforeach
        </div>
    </details>
@endif

{{-- BLOCCO VERDE --}}
@if($grouped->get('verde', collect())->isNotEmpty())
    <details class="mb-4">
        <summary class="cursor-pointer text-lg font-bold text-green-700">
            🟢 Regolari ({{ $counts['verde'] }})
        </summary>

        <div class="mt-3">
            @foreach($grouped->get('verde', collect()) as $task)
                @include('operator.partials.card', ['task' => $task])
            @endforeach
        </div>
    </details>
@endif

@endsection
