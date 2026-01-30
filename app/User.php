<?php

namespace App;

use App\Model\Order;

use App\Model\ProductCompare;
use App\Model\ShippingAddress;
use App\Model\ServiceProviderFirm;
use App\Model\ServiceProviderPlan;
use App\Model\ProviderReviews;
use App\Model\Wishlist;
use App\Model\ProviderGallery;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $fillable = [

        'username',
        'business_name',
        'name',
        'f_name',
        'l_name',
        'gender',
        'father_name',
        'phone',
        'email',
        'password',

        'image',
        'banner_image',
        'adhaar_number',
        'adhaar_front_image',
        'adhaar_back_image',

        'dob',
        'email_verified_at',

        'current_address',
        'permanent_address',
        'street_address',
        'house_no',
        'apartment_no',
        'country',
        'state',
        'distric',
        'city',
        'zip',
        'latitude',
        'longitude',

        'role',
        'role_name',
        'work_role',
        'type_of_work',
        'tools',
        'working_location',
        'radius_of_working_area_in_mm',
        'serviceTypeId',

        'working_since',
        'team_strength',
        'total_project_done',
        'description',
        'achievments',
        'featured',

        'refrence_1',
        'refrence_2',

        'whatsapp_number',
        'website',
        'insta_link',
        'facebook_link',
        'youtube_link',

        'login_medium',
        'social_id',
        'remember_token',
        'temporary_token',
        'is_phone_verified',
        'is_email_verified',
        'login_hit_count',
        'is_temp_blocked',
        'temp_block_time',
        'cm_firebase_token',
        'gst',
        'pan',
        'user_balance',
        'wallet_balance',
        'loyalty_point',
        'payout_ways',
        'payout_amount',
        'payment_card_last_four',
        'payment_card_brand',
        'payment_card_fawry_token',
        'razorpay_contact_id',
        'razorpay_fund_account_id',

        'referral_code',
        'reffered_by',

        'is_active',
    ];

    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'integer',
        'is_phone_verified'=>'integer',
        'is_email_verified' => 'integer',
        'wallet_balance'=>'float',
        'loyalty_point'=>'float'
    ];

    public function wish_list()
    {
        return $this->hasMany(Wishlist::class, 'customer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
     
    public function firm()
    {
        return $this->hasOne(ServiceProviderFirm::class, 'provider_id');
    } 
     
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function shipping()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address');
    }
    public function compare_list()
    {
        return $this->hasMany(ProductCompare::class, 'user_id');
    }
    
    // In User.php model
    public function reviews()
    {
        return $this->hasMany(ProviderReviews::class, 'provider_id'); // Assuming 'provider_id' is the foreign key
    }
    
     public function provider_plan()
    {
        return $this->hasMany(ServiceProviderPlan::class, 'provider_id')->orderBy('amount');; // Assuming 'provider_id' is the foreign key
    }
    
     public function provider_gallery()
    {
        return $this->hasMany(ProviderGallery::class, 'provider_id'); // Assuming 'provider_id' is the foreign key
    }

}