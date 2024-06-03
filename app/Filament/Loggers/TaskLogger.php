<?php

namespace App\Filament\Loggers;

use App\Models\Task;
use App\Filament\Resources\TaskResource;
use App\TaskStatus;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;
use Spatie\Activitylog\Models\Activity;

class TaskLogger extends Logger
{
    public static ?string $model = Task::class;

    // public static function getLabel(): string|Htmlable|null
    // {
    //     return TaskResource::getModelLabel();
    // }

    public function getSubjectRoute(Activity $activity): ?string
    {
        return TaskResource::getUrl('edit', ['record' => $activity->subject_id]);
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                // Here you have to define all of your fields to be logged
                Field::make('title')
                    ->label(__('Task Title')),
                Field::make('user.name')
                    ->hasOne('user')
                    ->label('User'),
                Field::make('description'),
                Field::make('progress'),
                Field::make('status')
                    ->label(__('Status'))
                    ->enum(TaskStatus::class),
                Field::make('team.name')

                    ->hasMany('team')
                    ->label(__('Co-Assignee'))
                    ->badge(),

            ])
            ->relationManagers([
                //
            ]);
    }
}
