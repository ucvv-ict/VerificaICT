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
    <div class="mb-6">
        <label class="font-semibold block mb-2">Modalità Assegnazione</label>
        <select id="mode-select" name="mode" class="border rounded p-2 w-full">
            <option value="tasks">Task specifici</option>
            <option value="tags">Per Tag</option>
            <option value="package">Pacchetto Base</option>
        </select>
    </div>

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

    <div id="tags-section" class="mb-6 hidden">
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

    <div id="tasks-section" class="mb-6 hidden">
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

    <button type="button"
            id="preview-btn"
            class="bg-gray-700 text-white px-6 py-2 rounded font-semibold">
        Preview
    </button>
</form>

<div id="preview-result" class="mt-4 hidden bg-gray-100 p-4 rounded"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const modeSelect = document.getElementById('mode-select');
    const tasksSection = document.getElementById('tasks-section');
    const tagsSection = document.getElementById('tags-section');

    const taskInputs = tasksSection.querySelectorAll('input[type="checkbox"]');
    const tagInputs = tagsSection.querySelectorAll('input[type="checkbox"]');

    function disableInputs(inputs, disabled = true) {
        inputs.forEach(input => {
            input.disabled = disabled;
            if (disabled) input.checked = false;
        });
    }

    function updateVisibility() {
        const mode = modeSelect.value;

        tasksSection.classList.add('hidden');
        tagsSection.classList.add('hidden');

        disableInputs(taskInputs, true);
        disableInputs(tagInputs, true);

        if (mode === 'tasks') {
            tasksSection.classList.remove('hidden');
            disableInputs(taskInputs, false);
        }

        if (mode === 'tags') {
            tagsSection.classList.remove('hidden');
            disableInputs(tagInputs, false);
        }

        // package: entrambe nascoste e disabilitate
    }

    modeSelect.addEventListener('change', updateVisibility);

    updateVisibility();
});
</script>
<script>
document.getElementById('preview-btn').addEventListener('click', function () {

    const form = document.querySelector('form');
    const formData = new FormData(form);

    fetch("{{ route('admin.bulk.preview') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        const box = document.getElementById('preview-result');

        if (data.error) {
            box.innerHTML = `<div class="text-red-600">${data.error}</div>`;
        } else {
            box.innerHTML = `
                <strong>Preview Assegnazione</strong><br>
                Task coinvolti: ${data.tasks_count}<br>
                Enti selezionati: ${data.entities_count}<br>
                Nuove assegnazioni: ${data.new}<br>
                Già esistenti: ${data.existing}
            `;
        }

        box.classList.remove('hidden');
    });
});
</script>
@endsection
