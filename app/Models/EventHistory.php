<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventHistory extends Model
{
    protected $fillable = ['event_id', 'version', 'snapshot', 'valid_from', 'valid_to'];

    protected $casts = [
        'snapshot' => 'array',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
