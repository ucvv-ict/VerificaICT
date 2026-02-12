@extends('layouts.operator')

@section('content')

<h1 class="text-xl font-bold mb-6">
    Assegnazione Massiva Task
</h1>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('admin.bulk.store') }}" class="space-y-6">
    @csrf

    <div>
        <h2 class="font-semibold mb-2">Seleziona Enti</h2>
        <div class="grid grid-cols-2 gap-2">
            @foreach($entities as $entity)
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="entities[]" value="{{ $entity->id }}">
                    <span>{{ $entity->nome }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <h2 class="font-semibold mb-2">Seleziona Tag</h2>
        <div class="grid grid-cols-2 gap-2">
            @foreach($tags as $tag)
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}">
                    <span>{{ $tag->nome }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <button type="submit"
            class="bg-blue-600 text-white px-6 py-2 rounded font-semibold">
        Applica assegnazione
    </button>

</form>

@endsection
