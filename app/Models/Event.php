<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'calendar_api_id',
        'title',
        'start_at',
        'end_at',
        'sent_at',
        'last_updated'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'sent_at' => 'datetime',
        'last_updated' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }
}
