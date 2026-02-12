@extends('layouts.operator')

@section('content')

<h1 class="text-xl font-bold mb-4">
    Dashboard Operatore
</h1>

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
@if($grouped['rosso']->isNotEmpty())
    <h2 class="text-lg font-bold text-red-700 mb-3">
        🔴 Controlli Critici ({{ $counts['rosso'] }})
    </h2>

    @foreach($grouped['rosso'] as $task)
        @include('operator.partials.card', ['task' => $task])
    @endforeach
@endif

{{-- BLOCCO ARANCIONE --}}
@if($grouped['arancione']->isNotEmpty())
    <h2 class="text-lg font-bold text-yellow-700 mt-6 mb-3">
        🟡 In Scadenza ({{ $counts['arancione'] }})
    </h2>

    @foreach($grouped['arancione'] as $task)
        @include('operator.partials.card', ['task' => $task])
    @endforeach
@endif

{{-- BLOCCO VERDE --}}
@if($grouped['verde']->isNotEmpty())
    <h2 class="text-lg font-bold text-green-700 mt-6 mb-3">
        🟢 Regolari ({{ $counts['verde'] }})
    </h2>

    @foreach($grouped['verde'] as $task)
        @include('operator.partials.card', ['task' => $task])
    @endforeach
@endif

@endsection
