<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\EventResource;
use \Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use \Saade\FilamentFullCalendar\Data\EventData;
use App\Models\Event;
use Carbon\Carbon;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Clusters\Cluster;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Saade\FilamentFullCalendar\Widgets\Concerns\InteractsWithEvents;

class CalendarWidget extends FullCalendarWidget

{
    use InteractsWithEvents;

    public Model | string | null $model = Event::class;

    public function fetchEvents(array $fetchInfo): array
    {
        return Event::where('start', '>=', $fetchInfo['start'])
            ->where('end', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Event $event) {
                return [
                    'id'    => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start' => $event->start,
                    'end'   => $event->end,
                ];
            })
            ->toArray();
    
        }
    
        public static function canView(): bool
    {
        return true;
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                        ->label('Event Name')
                        ->required()
                        ->columnSpan(1),
                    Textarea::make('description')
                        ->rows('3')
                        ->columnSpan(1),
                        
                    DateTimePicker::make('start')
                    ->seconds(false)
                    ->closeOnDateSelection()
                    ->required()
                    ->columnSpan(1),
                    DateTimePicker::make('end')
                    ->seconds(false)
                    ->closeOnDateSelection()
                    ->required()
                    ->columnSpan(1),
                    Hidden::make('user_id')
                            ->default(auth()->id())
                            ->required()
        ];
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
            ->model(Event::class)
            ->form([
                Section::make('Event Details')
                ->description(' ')
                ->schema([
                    TextInput::make('title')
                        ->label('Event Name')
                        ->required()
                        ->columnSpan(1),
                    Textarea::make('description')
                        ->rows('3')
                        ->columnSpan(1),
                        
                        DateTimePicker::make('start')
                        ->seconds(false)
                        ->closeOnDateSelection()
                        ->required()
                        ->columnSpan(1),
                        DateTimePicker::make('end')
                        ->seconds(false)
                        ->closeOnDateSelection()
                        ->required()
                        ->columnSpan(1),
                    Hidden::make('user_id')
                            ->default(auth()->id())
                            ->required()
            ])->columns(2)
            
            ,
           
                ])
                   ,
        ];
    }

    protected function modalActions(): array
{
    return [
        EditAction::make()
            ->modalHeading('Custom Heading for Edit'),
        DeleteAction::make()
            ->modalHeading('Custom Heading for Delete'),
    ];
}


    public function config(): array
    {
        return [
            'firstDay' => 0,
            'headerToolbar' => [
                'left' => 'prev,today,next',
                'center' => 'title',
                'right' => 'dayGridYear,dayGridMonth,dayGridWeek,dayGridDay',
            ],
            'titleFormat' => [
                // 'weekday' => 'long',
                'month' => 'long',
                'day' => 'numeric',
                'year' => 'numeric'
            ],
            'height' => 700,
            // 'stickyHeaderDates' => true,
            // 'selectMirror' => true,
            // 'selectOverlap' => false,
            'eventMinHeight' => 50,
            // 'slotLabelFormat' => [
            //     'hour' => '2-digit',
            //     'minute' => '2-digit'
            // ],
        ];
    }

}
   
    

