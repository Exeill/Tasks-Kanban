<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;
use Spatie\Activitylog\Traits\LogsActivity;

class Task extends Model implements Sortable
{
    use HasFactory, SortableTrait, LogsActivity;

    protected $fillable = [
        'user_id',
            'user',
            'title',
            'description',
            'urgent',
            'project',
            'due_date',
            'progress',
            'status',
            'order_column',
            'is_done',
            'team',
            'text_color',
            'bg_color',
    ];

    protected $casts = [
        'users' => AsArrayObject::class, // casting the JSON database column
        'teams' => 'collection',
        'updated_at' => 'datetime:m-d-Y h:i A',
        'created_at' => 'datetime:m-d-Y h:i A',

    ];
    public function user_tasks()
    {
        return $this->belongsToMany(User::class, 'task_user')->withTimestamps();
    }

    protected static $recordEvents = ['created','updated','deleted'];
    
        
    public function getActivitylogOptions(): LogOptions
    {
        
        return LogOptions::defaults()
        

        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => "Tasks has been {$eventName}")
            ->logOnly([
                'user_id',
                'title',
                'progress',
                'status',
                'created_at',
                'updated_at',
            ])
                
            ->logOnlyDirty()
        ->dontLogIfAttributesChangedOnly([
            'user',
            'title',
            'description',
            'updated_at',
            'due_date',
            'order_column',
            'urgent',
            'team',
            'user',
            'is_done',
            'text_color',
            'bg_color'])
    
        
        ;
        
            
    }

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function getTrim() 
    {
        return Str::limit(strip_tags($this->description), 100);
    }

    // public function scopeOrdered($query)
    // {
    //     return $query->orderBy('due_date', 'desc');
    // }

    

    
    
}
