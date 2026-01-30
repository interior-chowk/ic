<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ServiceProviderSocialMedia extends Model
{
    protected $casts = [
        'status'        => 'integer',
        'active_status' => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];
    protected $table = 'service_provider_social_medias';
}
