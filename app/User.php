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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'f_name', 'l_name','gender', 'name', 'email', 'password', 'phone', 'image', 'login_medium','is_active','social_id','is_phone_verified','temporary_token','referral_code'
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
