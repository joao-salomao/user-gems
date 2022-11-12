<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function internalParticipants()
    {
        return $this->participants()
            ->join('people', 'people.id', '=', 'event_participants.person_id')
            ->where('people.is_internal', true);
    }

    public function externalParticipants()
    {
        return $this->participants()
            ->join('people', 'people.id', '=', 'event_participants.person_id')
            ->where('people.is_internal', false);
    }

    public function scopeAtDate($query, Carbon $date)
    {
        return $query->where('start_at', '>=', $date->startOfDay()->toDateTimeString())
            ->where('start_at', '<=', $date->endOfDay()->toDateTimeString());
    }
}
