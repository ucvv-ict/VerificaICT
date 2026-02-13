<div class="space-y-2 text-sm">
    <div><strong>Task coinvolti:</strong> {{ $result['tasks_count'] }}</div>
    <div><strong>Enti selezionati:</strong> {{ $result['entities_count'] }}</div>
    <div class="text-green-600">
        <strong>Nuove assegnazioni:</strong> {{ $result['new'] }}
    </div>
    <div class="text-gray-600">
        <strong>Già esistenti:</strong> {{ $result['existing'] }}
    </div>
</div>
