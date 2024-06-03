<?php

namespace App;

use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum TaskStatus: string
{
    use IsKanbanStatus;

    case Todo = 'todo';
    case OnGoing = 'ongoing';
    case ForReview = 'review';
    case Delete = 'deleted';

    public static function kanbanCases(): array
    {
        return [
            static::Todo,
            static::OnGoing,
            static::ForReview,
        ];
    }

    public function getTitle(): string
    {
        return $this->name;
    }
}