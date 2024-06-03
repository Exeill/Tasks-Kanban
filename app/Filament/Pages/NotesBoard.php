<?php

namespace App\Filament\Pages;

use App\Models\Note;
use App\NotesPin;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;

class NotesBoard extends KanbanBoard
{

    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';
    protected ?string $subheading = 'Have Fun!';
    protected static string $recordStatusAttribute = 'pin';

    protected static string $model = Note::class;

    protected static string $statusEnum = NotesPin::class;

    protected static ?int $navigationSort = 1;

    // protected static ?string $navigationGroup = 'Board';
    protected static ?string $title = 'My Notes';

    protected static string $view = 'mynotes-kanban.kanban-board';

    protected static string $headerView = 'mynotes-kanban.kanban-header';

    protected static string $recordView = 'mynotes-kanban.kanban-record';

    protected static string $statusView = 'mynotes-kanban.kanban-status';


    protected function records(): Collection
    {

        return Note::ordered()->where('user_id', auth()->id())->get();
    }

    public function onStatusChanged(int $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {

        Note::find($recordId)->update(['pin' => $status]);
        Note::setNewOrder($toOrderedIds);
    }

    public function onSortChanged(int $recordId, string $status, array $orderedIds): void
    {
        Note::setNewOrder($orderedIds);
    }

    protected function getEditModalFormSchema(null|int $recordId): array
    {
        return [

            Grid::make([
                'default' => 1,
                'sm' => 2,
            ])->schema([

                Section::make('My Note')
                    ->description(' ')
                    ->schema([
                        TextInput::make('title')
                        ->autofocus()
                            ->label('Task Name')
                            ->autocapitalize('words')
                            ->required(),
                        Textarea::make('description')
                            ->rows('3'),
                        Hidden::make('user_id')
                            ->default(auth()->id())
                            // ->disabled()
                            // ->relationship('user', 'name')
                            ->required(),
                        // Radio::make('pin')->inline()
                        //     ->label('Pin to Top')
                        //     ->default('note')
                        //     ->options([
                        //         'pinned' => 'Yes',
                        //         'note' => 'No',
                        //     ]),
                            ToggleButtons::make('pin')
                            ->label('Pin to Top')
                            ->inline()
                            ->default('note')
                            ->grouped()
                            ->options([
                                'pinned' => 'Yes',
                                'note' => 'No',
                                'deleted' => 'Delete',
                            ])
                            ->colors([
                                'pinned' => 'success',
                                'note' => 'warning',
                                'deleted' => 'danger',
                            ])



                    ])->columnSpan(1),
                Section::make('Customization')
                    ->description(' ')
                    ->schema([
                        TextInput::make('status')
                        ->maxLength(24)
                            ->label('Note Tag'),

                        Select::make('text_color')
                            ->default('white')
                            ->required()
                            ->options([
                                'text-white' => 'white',
                                'text-black' => 'black',
                                'text-yellow-400' => 'yellow',
                                'text-red-600' => 'red',
                                'text-sky-600' => 'blue',
                                'text-lime-600' => 'green',
                            ])
                            ->label(__('Text Color')),

                        Select::make('bg_color')
                            ->default('blue')
                            ->required()
                            ->options([
                                'bg-white' => 'white',
                                'bg-black' => 'black',
                                'bg-sky-400' => 'blue',
                                'bg-sky-800' => 'dark blue',
                                'bg-red-400' => 'red',
                                'bg-orange-400' => 'orange',
                                'bg-yellow-400' => 'yellow',
                                'bg-lime-400' => 'lime',
                                'bg-green-400' => 'green',
                                'bg-teal-400' => 'teal',
                                'bg-cyan-400' => 'cyan',
                                'bg-violet-400' => 'violet',
                                'bg-fuchsia-400' => 'fucshia',
                                'bg-pink-400' => 'pink',
                                'bg-rose-400' => 'rose',
                            ])
                            
                            ->label(__('Background Color')),

                           
                            
                        

                    ])->columnSpan(1),

            ])->columns(2),
        ];
    }

    protected function getEditModalRecordData(int $recordId, array $data): array
    {
        return Note::find($recordId)->toArray();
    }

    protected function editRecord($recordId, array $data, array $state): void
    {


        Note::find($recordId)->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'],
            // 'user_id' => $data['user_id'],
            'text_color' => $data['text_color'],
            'bg_color' => $data['bg_color'],
            'pin' => $data['pin'],


        ]);
    }

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()
                ->model(Note::class)
                ->form([

                    Grid::make([
                        'default' => 1,
                        'sm' => 2,
                    ])->schema([

                        Section::make('My Note')
                            ->description(' ')
                            ->schema([
                                TextInput::make('title')
                                    ->autofocus()
                                    ->label('Note Title')
                                    ->autocapitalize('words')
                                    ->required(),
                                Textarea::make('description')
                                ->label('Description')
                                    ->rows('3'),
                                Hidden::make('user_id')
                                    ->default(auth()->id())
                                    // ->disabled()
                                    // ->relationship('user', 'name')
                                    ->required(),
                                Radio::make('pin')->inline()
                                    ->label('Pin to Top')
                                    ->default('note')
                                    ->options([
                                        'pinned' => 'Yes',
                                        'note' => 'No',
                                    ])



                            ])->columnSpan(1),
                        Section::make('Customization')
                            ->description(' ')
                            ->schema([
                                TextInput::make('status')
                                ->maxLength(24)
                                    ->label('Note Tag'),

                                Select::make('text_color')
                                    ->default('text-white')
                                    ->required()
                                    ->options([
                                        'text-white' => 'white',
                                        'text-black' => 'black',
                                        'text-yellow-400' => 'yellow',
                                        'text-red-600' => 'red',
                                        'text-sky-600' => 'blue',
                                        'text-lime-600' => 'green',
                                    ])
                                    ->label(__('Text Color')),

                                Select::make('bg_color')
                                    ->default('bg-sky-400')
                                    ->required()
                                    ->options([
                                        'bg-white' => 'white',
                                        'bg-black' => 'black',
                                        'bg-sky-400' => 'blue',
                                        'bg-sky-800' => 'dark blue',
                                        'bg-red-400' => 'red',
                                        'bg-orange-400' => 'orange',
                                        'bg-yellow-400' => 'yellow',
                                        'bg-lime-400' => 'lime',
                                        'bg-green-400' => 'green',
                                        'bg-teal-400' => 'teal',
                                        'bg-cyan-400' => 'cyan',
                                        'bg-violet-400' => 'violet',
                                        'bg-fuchsia-400' => 'fucshia',
                                        'bg-pink-400' => 'pink',
                                        'bg-rose-400' => 'rose',
                                    ])
                                    
                                    ->label(__('Background Color')),
                                

                            ])->columnSpan(1),

                    ])->columns(2),


                ]),
        ];
    }

    protected function additionalRecordData(Model $record): Collection
    {

        return collect([
            'pin' => $record->pin,
            'status' => $record->status,
            'description' => $record->description,
            'text_color' => $record->text_color,
            'bg_color' => $record->bg_color,

        ]);
    }
}
