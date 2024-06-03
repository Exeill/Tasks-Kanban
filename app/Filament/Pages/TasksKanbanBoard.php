<?php

namespace App\Filament\Pages;

use App\CompletedStatus;
use App\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\ViewField;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Guava\FilamentClusters\Forms\Cluster;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;
use Noxo\FilamentActivityLog\Extensions\LogEditRecord;
use Spatie\Activitylog\Contracts\Activity;

class TasksKanbanBoard extends KanbanBoard
{
    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document';

    protected static string $view = 'mytasks-kanban.kanban-board';

    protected static string $headerView = 'mytasks-kanban.kanban-header';

    protected static string $recordView = 'mytasks-kanban.kanban-record';

    protected static string $statusView = 'mytasks-kanban.kanban-status';

    protected static string $model = Task::class;

    protected static string $statusEnum = TaskStatus::class;

    protected ?string $subheading = 'Mark as Done or Delete to remove from board.';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Board';
    protected static ?string $title = 'My Tasks';

    // protected function records(): Collection
    // {

    //     return Task::ordered()
    //         ->whereHas(
    //             'team',
    //             function ($query) {
    //                 return $query->where('user_id', auth()->id());
    //             }
    //         )
    //         ->orWhere('user_id', auth()->id())
    //         ->get();
    // }

    use LogCreateRecord;
    use LogEditRecord;

    protected function records(): Collection
    {
        // Get current date and the weekday number (0 for Sunday, 1 for Monday, etc.)
        $currentDate = Carbon::now();
        $currentWeekday = $currentDate->dayOfWeek;

        // Determine the start and end of the current week (Monday to Friday)
        $startOfWeek = $currentDate->copy()->startOfWeek(Carbon::MONDAY)->subDays(30);
        $endOfWeek = $currentDate->copy()->startOfWeek(Carbon::MONDAY)->addDays(6);

        // Retrieve tasks created from Monday to Friday with status not equal to 'done'
        // and either belong to a team with the current authenticated user or are directly assigned to the current authenticated user
        return Task::ordered()
        ->where(function ($query) use ($startOfWeek, $endOfWeek) {
            $query->where('is_done', '!=', 'done')
                  ->orWhereBetween('created_at', [$startOfWeek, $endOfWeek]);
        })
        
                    // ->where('is_done', '!=', 'done')
                    // ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    
                    ->where(function ($query) {
                        $query->whereHas('team', function ($query) {
                            $query->where('user_id', auth()->id());
                        })
                        ->orWhere('user_id', auth()->id());
                    })
                    ->get();
    }

    public function onStatusChanged(int $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {


        Task::find($recordId)->update(['status' => $status]);
        Task::setNewOrder($toOrderedIds);
        // Log::info($message);
    }

    public function onSortChanged(int $recordId, string $status, array $orderedIds): void
    {
        Task::setNewOrder($orderedIds);
    }


    protected function getEditModalFormSchema(null|int $recordId): array
    {
        return [
            RadioDeck::make('is_done')
            ->label('Status')
                        ->options(CompletedStatus::class)
                        ->descriptions(CompletedStatus::class)
                        ->icons(CompletedStatus::class)
                        ->required()
                        ->iconSize(IconSize::Small)
                        ->iconPosition(IconPosition::Before)
                        ->alignment(Alignment::Center)
                        ->extraCardsAttributes([
                            'class' => 'rounded-md'
                        ])
                        ->extraOptionsAttributes([
                            'class' => 'text-sm leading-none w-full flex flex-col items-center justify-center p-1'
                        ])
                        ->extraDescriptionsAttributes([ 
                            'class' => 'text-xs font-light text-center'
                        ])
                        ->color('primary')
                        ->padding('px-3 px-3') 
                        ->columns(3),
            Section::make('Task Details')
                ->description(' ')
                ->schema([
                    Toggle::make('urgent')
                        ->required()
                        ->columnSpan(1),

                    TextInput::make('progress')
                        ->label('')
                        ->prefix('Progress')
                        ->numeric()
                        ->maxValue(100)
                        ->minValue(0)
                        ->suffix('%')
                        ->columnSpan(2),

                    Cluster::make([
                        TextInput::make('title')
                            ->label('Task Name')
                            ->autocapitalize('words')
                            ->required()
                            ->columnSpan(2),
                        Textarea::make('description')
                            ->rows('3')
                            ->columnSpan(2),
                    ])
                        ->label('Task Name')
                        ->hint('')
                        ->helperText('*Description can be Blank')->columnSpan(3),

                    Cluster::make([


                        TextInput::make('project')
                            ->label('Project')
                            ->nullable()
                            ->columnSpan(1),
                        DatePicker::make('due_date')
                            ->label('Due Date')
                            ->date('D - M d, Y')
                            ->nullable()->columnSpan(1),
                    ])
                        ->label('Project')
                        ->hint('Due Date')
                        ->columnSpan(3),

                    Cluster::make([
                        Select::make('user_id')
                            ->default(auth()->id())
                            ->relationship('user', 'name')
                            ->required()
                            ->columnSpan(2),
                        Select::make('team')
                            ->label('Assigned User')
                            ->relationship('team', 'name')
                            ->multiple()
                            ->nullable()
                            ->searchable()
                            ->preload()
                            ->columnSpan(2),
                    ])
                        ->label('User')
                        ->hint('Assigned User/s')
                        ->helperText(' ')->columnSpan(3),
                        Cluster::make([
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
                                    ->label(__('Text Color'))
                                    ->columnSpan(1),

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
                                    
                                    ->label(__('Background Color'))
                                    ->columnSpan(1),
                                    
                        ])
                            ->label('Customization - Text Color | BG Color')
                            ->hint('Default is White Text & Blue Background')
                            ->helperText(' ')->columnSpan(3),
                            ToggleButtons::make('status')
                            ->label('Set')->inline()->grouped()
                            ->options([
                                'todo' => 'Back to Todo',
                                'ongoing' => 'On-Going',
                                'review' => 'For Review',
                                'deleted' => 'Delete',
                            ])
                            ->colors([
                                'todo' => 'info',
                                'ongoing' => 'warning',
                                'review' => 'success',
                                'deleted' => 'danger',
                            ])

                ])->columns(3),
        ];
    }

    protected function getEditModalRecordData(int $recordId, array $data): array
    {
        return Task::find($recordId)->toArray();
    }

    

    protected function editRecord($recordId, array $data, array $state): void
    {


        Task::find($recordId)->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'urgent' => $data['urgent'],
            'project' => $data['project'],
            'due_date' => $data['due_date'],
            'progress' => $data['progress'],
            'user_id' => $data['user_id'],
            'is_done' => $data['is_done'],
            'text_color' => $data['text_color'],
            'bg_color' => $data['bg_color'],
            'status' => $data['status'],

        ]);

        // $this->logRecordAfter($this->recordId);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(Task::class)
                ->form([
                    Toggle::make('urgent')
                        ->required(),
                    Cluster::make([
                        TextInput::make('title')
                            ->label('Task Name')
                            ->required()
                            ->columnSpan(1),
                        Textarea::make('description')
                            ->required()
                            ->rows('3')
                            ->columnSpan(2),
                    ])
                        ->label('Task Name')
                        ->hint('')
                        ->helperText('*Description can be Blank')->columns(1),

                    Cluster::make([


                        TextInput::make('project')
                            ->label('Project')
                            ->nullable(),
                        DatePicker::make('due_date')
                            ->label('Due Date')
                            ->date('D - M d, Y')
                            ->nullable(),

                    ])
                        ->label('Project')
                        ->hint('Due Date')
                        ->columns(2),

                    Cluster::make([
                        Select::make('user_id')
                            ->default(auth()->id())
                            ->relationship('user', 'name')
                            ->required()
                            ->columnSpan(1),
                        Select::make('team')
                            ->label('Assigned User')
                            ->relationship('team', 'name')
                            ->multiple()
                            ->nullable()
                            ->searchable()
                            ->preload()
                            ->columnSpan(2),
                    ])
                        ->label('User')
                        ->hint('Assigned User/s')
                        ->helperText(' ')->columns(3),
                        Cluster::make([
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
                        ])
                            ->label('Customization - Text Color | BG Color')
                            ->hint('Default is White Text & Blue Background')
                            ->helperText(' ')->columns(2),


                ]),
        ];
    }

    protected function additionalRecordData(Model $record): Collection
    {

        return collect([
            'urgent' => $record->urgent,
            'progress' => $record->progress,
            // 'owner' => $record->user->name,
            'description' => $record->description,
            'text_color' => $record->text_color,
            'bg_color' => $record->bg_color,

        ]);
    }
}
