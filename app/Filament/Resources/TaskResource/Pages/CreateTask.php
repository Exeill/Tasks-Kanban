<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;

class CreateTask extends CreateRecord
{
    use LogCreateRecord;
    protected static string $resource = TaskResource::class;
}
