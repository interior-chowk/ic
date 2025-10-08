<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $fillable = [
        'plan_name',
        'plan_description',
        'price',
        'logo',
        'trusted_partner_tag',
        'profile_image',
        'contact_no_show',
        'free_2d_design',
        'free_3d_design',
        'rewards_on_self_purchase',
        'rewards_on_client_purchase',
        'reward_value',
        'listing_view',
        'advertisement',
        'scheme_participation',
        'discount_on_delivery',
        'discount_on_yearly_plan',
        
    ];

   
  
}
