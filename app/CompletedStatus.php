<?php

namespace App;

use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;
use Filament\Support\Contracts\HasLabel;
use JaOcero\RadioDeck\Contracts\HasDescriptions;
use JaOcero\RadioDeck\Contracts\HasIcons;

enum CompletedStatus: string implements HasLabel, HasDescriptions, HasIcons
{
    use IsKanbanStatus;

    case PendingReview = 'pending';
    case Done = 'done';
    case Undone = 'undone';
    

    public function getTitle(): string
    {
        return $this->name;
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Done => 'Done',
            self::PendingReview => 'Pending',
            self::Undone => 'Undone',
            
        };
    }

    public static function kanbanCases(): array
    {
        return [
            static::PendingReview,
            static::Done,
            static::Undone,
        ];
    }

    public function getDescriptions(): ?string
    {
        return match ($this) {
            self::Done => 'Yeah!',
            self::PendingReview => 'Pending...',
            self::Undone => 'Oh No!',
            
        };
    }

    public function getIcons(): ?string
    {
        return match ($this) {
            self::Done => 'heroicon-s-check-circle',
            self::PendingReview => 'heroicon-s-exclamation-circle',
            self::Undone => 'heroicon-s-x-circle',
            
        };
    }

}