@extends('layouts.operator')

@section('content')

<h1 class="text-xl font-bold mb-4">
    Esegui Controllo
</h1>

<div class="bg-white shadow rounded-lg p-4 mb-4">

    <div class="text-sm text-gray-500">
        {{ $entitySecurityTask->entity->nome }}
    </div>

    <div class="font-semibold text-lg">
        {{ $entitySecurityTask->securityTask->titolo }}
    </div>

    @if($entitySecurityTask->latestCheck)
        <div class="text-sm text-gray-600 mt-2">
            Ultimo controllo:
            {{ $entitySecurityTask->latestCheck->checked_at->format('d/m/Y H:i') }}
            ({{ strtoupper($entitySecurityTask->latestCheck->esito) }})
        </div>
    @else
        <div class="text-sm text-gray-600 mt-2">
            Nessun controllo precedente.
        </div>
    @endif

</div>

<form method="POST"
      action="{{ route('operator.check.store', $entitySecurityTask->id) }}"
      class="bg-white shadow rounded-lg p-4">

    @csrf

    <div class="mb-4">
        <label class="block font-semibold mb-2">Esito</label>

        <div class="space-y-2">

            <label class="flex items-center space-x-2">
                <input type="radio" name="esito" value="ok" required>
                <span>OK</span>
            </label>

            <label class="flex items-center space-x-2">
                <input type="radio" name="esito" value="ko">
                <span>KO</span>
            </label>

            <label class="flex items-center space-x-2">
                <input type="radio" name="esito" value="na">
                <span>NA</span>
            </label>

        </div>

        @error('esito')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-2">Note (facoltative)</label>
        <textarea name="note"
                  rows="4"
                  class="w-full border rounded p-2"></textarea>
    </div>

    <button type="submit"
            class="w-full bg-blue-600 text-white py-2 rounded font-semibold">
        Salva controllo
    </button>

</form>

@endsection
