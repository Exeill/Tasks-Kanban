<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'start',
        'end',
    ];

    protected $casts = [
        'updated_at' => 'datetime:m-d-Y h:i A',
        'created_at' => 'datetime:m-d-Y h:i A',
        'start' => 'datetime:m-d-Y h:i A',
        'end' => 'datetime:m-d-Y h:i A',

        
    ];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

}
