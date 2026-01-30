<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProviderGallery extends Model
{
    protected $casts = [
        'provider_id'     => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

  
}
