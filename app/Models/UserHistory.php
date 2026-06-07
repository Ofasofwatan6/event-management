<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHistory extends Model
{
    protected $fillable = ['user_id', 'version', 'snapshot', 'valid_from', 'valid_to'];

    protected $casts = [
        'snapshot' => 'array',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
