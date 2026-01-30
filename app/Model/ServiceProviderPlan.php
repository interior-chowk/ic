<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;

class ServiceProviderPlan extends Model
{
   
  protected $casts = [
        'provider_id ' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
  
    protected $fillable = ['provider_id', 'membership_id', 'amount', 'transaction_id'];
    
     public function provider()
    {
       return $this->belongsTo(User::class, 'provider_id')->whereIn('role', [2, 3, 4, 5]);
    } 
    
     public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    } 
}
