<div class="space-y-6">

    <div>
        <div class="text-sm text-gray-500">
            {{ $record->entity->nome }}
        </div>
    </div>

    @if($record->securityTask->descrizione)
        <div>
            <div class="font-semibold text-gray-600 mb-1">
                Descrizione
            </div>
            <div class="text-gray-700 whitespace-pre-line">
                {{ $record->securityTask->descrizione }}
            </div>
        </div>
    @endif

    @if($record->descrizione_specifica)
        <div>
            <div class="font-semibold text-gray-600 mb-1">
                Note specifiche ente
            </div>
            <div class="text-gray-700 whitespace-pre-line">
                {{ $record->descrizione_specifica }}
            </div>
        </div>
    @endif

    @if($record->securityTask->documents->count())
        <div>
            <div class="font-semibold text-gray-600 mb-2">
                Documenti allegati
            </div>

            <div class="space-y-3">
                @foreach($record->securityTask->documents as $doc)
                    <div class="p-3 border rounded-lg bg-gray-50">
                        <a href="{{ Storage::url($doc->file_path) }}"
                           target="_blank"
                           class="text-primary-600 underline font-medium">
                            {{ $doc->name }}
                        </a>

                        @if(Str::endsWith($doc->file_path, '.pdf'))
                            <iframe
                                src="{{ Storage::url($doc->file_path) }}"
                                class="w-full h-80 mt-3 border rounded">
                            </iframe>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($record->securityTask->documentation_url)
        <div>
            <div class="font-semibold text-gray-600 mb-1">
                Documentazione esterna
            </div>
            <a href="{{ $record->securityTask->documentation_url }}"
               target="_blank"
               class="text-primary-600 underline">
                Apri link
            </a>
        </div>
    @endif

</div>