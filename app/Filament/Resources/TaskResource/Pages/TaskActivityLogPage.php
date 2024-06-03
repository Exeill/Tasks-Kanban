<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Resources\Pages\Page;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class TaskActivityLogPage extends ListActivities
{
    protected static string $resource = TaskResource::class;

    // protected static string $view = 'filament.resources.task-resource.pages.task-activity-log-page';
}
