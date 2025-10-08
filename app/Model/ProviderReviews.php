<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Model\ServiceProviderFirm;
class ProviderReviews extends Model
{

    protected $casts = [
        'provider_id'  => 'integer',
        'customer_id' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function serviceProvider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
    
    public function firm()
    {
        return $this->belongsTo(ServiceProviderFirm::class, 'provider_id', 'provider_id');
    }
    
    
     public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

  
}
