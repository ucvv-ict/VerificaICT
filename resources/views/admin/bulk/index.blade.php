@extends('layouts.operator')

@section('content')

<h1 class="text-xl font-bold mb-6">
    Assegnazione Massiva Task
</h1>

<div class="mb-6">
    <label class="font-semibold block mb-2">Modalità Assegnazione</label>

    <select id="mode-select" name="mode" class="border rounded p-2 w-full">
        <option value="tasks">Task specifici</option>
        <option value="tags">Per Tag</option>
        <option value="package">Pacchetto Base</option>
    </select>
</div>

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

    <div id="tags-section" class="mb-6">
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

    <div id="tasks-section" class="mb-6">
        <h2 class="font-semibold mb-2">Seleziona Task</h2>
        <div class="grid grid-cols-2 gap-2">
            @foreach(\App\Models\SecurityTask::orderBy('titolo')->get() as $task)
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="tasks[]" value="{{ $task->id }}">
                    <span>{{ $task->titolo }}</span>
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
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modeSelect = document.getElementById('mode-select');
    const tasksSection = document.getElementById('tasks-section');
    const tagsSection = document.getElementById('tags-section');

    function updateVisibility() {
        const mode = modeSelect.value;

        if (mode === 'tasks') {
            tasksSection.style.display = 'block';
            tagsSection.style.display = 'none';
        }

        if (mode === 'tags') {
            tasksSection.style.display = 'none';
            tagsSection.style.display = 'block';
        }

        if (mode === 'package') {
            tasksSection.style.display = 'none';
            tagsSection.style.display = 'none';
        }
    }

    modeSelect.addEventListener('change', updateVisibility);

    // Inizializzazione al load
    updateVisibility();
});
</script>
