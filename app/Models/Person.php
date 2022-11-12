<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function hasInfos(): bool
    {
        return !empty($this->name) && !empty($this->role) && !empty($this->avatar) && !empty($this->linkedin_url);
    }
}
