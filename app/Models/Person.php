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
    ];


    protected $casts = [
        'last_updated' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function hasInfos(): bool
    {
        return !empty($this->name) && !empty($this->role) && !empty($this->avatar) && !empty($this->linkedin_url);
    }
}
