<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'role',
        'avatar',
        'linkedin_url',
        'last_updated',
        'calendar_api_token',
        'is_internal'
    ];


    protected $casts = [
        'last_updated' => 'datetime',
        'is_internal' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function events()
    {
        return $this->hasManyThrough(Event::class, EventParticipant::class, 'person_id', 'id', 'id', 'event_id');
    }

    public function scopeWithCalendarApiToken($query)
    {
        return $query->whereNotNull('calendar_api_token');
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    public function scopeWithEventsAtDate($query, Carbon $date)
    {
        return $query->whereHas('events', function ($query) use ($date) {
            $query->where('events.start_at', '>=', $date->startOfDay()->toDateTimeString())
                ->where('events.start_at', '<=', $date->endOfDay()->toDateTimeString());
        });
    }

    public function getMeetingsCountAttribute()
    {
        return $this->events()->count();
    }

    public function getMeetingsCountByInternalPeople(int $onlyPersonId = null, int $personIdToExclude = null)
    {
        $query = DB::table('people')
            ->select('people.id', 'people.name', DB::raw('count(*) as meetings_count'))
            ->join('event_participants', 'event_participants.person_id', '=', 'people.id')
            ->join('events', 'events.id', '=', 'event_participants.event_id')
            ->where('event_participants.status', 'accepted')
            ->where('people.is_internal', true)
            ->where('people.id', '!=', $this->id)
            ->whereIn('events.id', function ($query) {
                $query->select('event_id')
                    ->from('event_participants')
                    ->where('status', 'accepted')
                    ->where('person_id', $this->id);
            });

        if ($personIdToExclude != null) {
            $query = $query->where('people.id', '!=', $personIdToExclude);
        }

        if ($onlyPersonId != null) {
            $query = $query->where('people.id', $onlyPersonId);
        }

        return $query
            ->groupBy('people.id')
            ->orderByDesc('meetings_count')
            ->get();
    }
}
