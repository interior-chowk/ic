<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProviderHelpTopic extends Model
{
    protected $table = 'provider_help_topics';
    protected $casts = [

        'ranking'    => 'integer',
        'status'     => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $fillable = [
        'question',
        'answer',
        'status',
        'ranking',
    ];

    public function scopeStatus($query)
    {
        return $query->where('status', 1);
    }
}
