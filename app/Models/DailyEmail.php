<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parameters',
        'sent_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
