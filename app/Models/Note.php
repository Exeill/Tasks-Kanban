<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Support\Str;

class Note extends Model
{
    use HasFactory, SortableTrait;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'pin',
        'status',
        'order_column',
        'text_color',
        'bg_color',
    ];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function getTrim() 
    {
        return Str::limit(strip_tags($this->description), 100);
    }
}
