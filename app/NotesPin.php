<?php

namespace App;

use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum NotesPin: string
{
    use IsKanbanStatus;

    case Pinned = 'pinned';
    case Note = 'note';
    case Delete = 'deleted';

    public static function kanbanCases(): array
    {
        return [
            static::Pinned,
            static::Note,
        ];
    }

    public function getTitle(): string
    {
        return $this->name;
    }
}