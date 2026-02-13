<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use App\Models\Entity;
use App\Models\Tag;
use App\Models\SecurityTask;
use App\Services\BulkAssignmentService;
use Filament\Forms\Components\Toggle;

class BulkAssignment extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bars4;
    protected static ?string $navigationLabel = 'Assegnazioni Massive';

    public static function getNavigationGroup(): ?string
    {
        return 'Operatività';
    }

    protected string $view = 'filament.pages.bulk-assignment';

    public array $data = [
        'mode' => 'tasks',
        'entities' => [],
        'tasks' => [],
        'tags' => [],
    ];

    public function getResolvedTasksCount(): int
    {
        $mode = $this->data['mode'] ?? 'tasks';
        $tasks = $this->data['tasks'] ?? [];
        $tags = $this->data['tags'] ?? [];

        if ($mode === 'tasks') {
            return \App\Models\SecurityTask::whereIn('id', $tasks)
                ->where('attiva', true)
                ->count();
        }

        if ($mode === 'tags') {
            return \App\Models\SecurityTask::whereHas('tags', function ($q) use ($tags) {
                    $q->whereIn('tags.id', $tags);
                })
                ->where('attiva', true)
                ->count();
        }

        if ($mode === 'package') {
            return \App\Models\SecurityTask::whereHas('tags', function ($q) {
                    $q->where('nome', 'base');
                })
                ->where('attiva', true)
                ->count();
        }

        return 0;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                Section::make('Modalità')
                    ->schema([
                        Radio::make('mode')
                            ->options([
                                'tasks' => 'Task specifici',
                                'tags' => 'Per Tag',
                                'package' => 'Pacchetto Base',
                            ])
                            ->default('tasks')
                            ->reactive(),

                        Toggle::make('sync')
                            ->label('Modalità sincronizzazione (rimuove task non inclusi)')
                            ->helperText('Se attivo, verranno rimossi i task non presenti nel pacchetto selezionato.')
                            ->default(false),
                    ]),

                Section::make('Enti')
                    ->schema([
                        CheckboxList::make('entities')
                            ->options(
                                Entity::where('attivo', true)
                                    ->pluck('nome', 'id')
                                    ->toArray()
                            )
                            ->columns(2)
                            ->required(),
                    ]),

                Section::make('Tag')
                    ->visible(fn ($get) => $get('mode') === 'tags')
                    ->schema([
                        CheckboxList::make('tags')
                            ->options(Tag::pluck('nome', 'id')->toArray())
                            ->columns(2),
                    ]),

                Section::make('Task')
                    ->visible(fn ($get) => $get('mode') === 'tasks')
                    ->schema([
                        CheckboxList::make('tasks')
                            ->options(
                                SecurityTask::orderBy('titolo')
                                    ->pluck('titolo', 'id')
                                    ->toArray()
                            )
                            ->columns(2),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [

            Action::make('preview')
                ->label('Preview')
                ->color('gray')
                ->modalHeading('Anteprima Assegnazione')
                ->modalSubmitAction(false)
                ->modalContent(function () {

                    $service = app(BulkAssignmentService::class);

                    $result = $service->preview(
                        $this->data['mode'],
                        $this->data['entities'] ?? [],
                        $this->data['tasks'] ?? [],
                        $this->data['tags'] ?? [],
                    );

                    return view('filament.pages.bulk-preview', [
                        'result' => $result,
                    ]);
                }),

            Action::make('apply')
                ->label('Applica assegnazione')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function () {
                    if (empty($this->data['entities'])) {

                        Notification::make()
                            ->title('Seleziona almeno un ente')
                            ->danger()
                            ->send();

                        return;
                    } 

                    $service = app(BulkAssignmentService::class);

                    $result = $service->assign(
                        $this->data['mode'],
                        $this->data['entities'] ?? [],
                        $this->data['tasks'] ?? [],
                        $this->data['tags'] ?? [],
                        $this->data['sync'] ?? false,
                    );

                    Notification::make()
                        ->title("Nuove: {$result['created']} — Esistenti: {$result['existing']}")
                        ->success()
                        ->send();
                }),
        ];
    }
}
