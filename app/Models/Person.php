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
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
