<?php

namespace App\Model;

use App\CPU\Helpers;
use App\Model\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'brand_id',
        'category_id',
        'images',
        'min_qty',
        'published',
        'tax',
        'unit_price',
        'status',
        'discount',
        'current_stock',
        'free_shipping',
        'featured_status',
        'refundable',
        'featured',
        'flash_deal',
        'seller_id',
        'purchase_price',
        'created_at',
        'updated_at',
        'shipping_cost',
        'multiply_qty',
        'temp_shipping_cost',
        'is_shipping_cost_updated'
    ];
    protected $casts = [
        'user_id' => 'integer',
        'brand_id' => 'integer',
        'min_qty' => 'integer',
        'published' => 'integer',
        'tax' => 'float',
        'unit_price' => 'float',
        'status' => 'integer',
        'discount' => 'float',
        'current_stock' => 'integer',
        'free_shipping' => 'integer',
        'featured_status' => 'integer',
        'refundable' => 'integer',
        'featured' => 'integer',
        'flash_deal' => 'integer',
        'seller_id' => 'integer',
        'purchase_price' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'shipping_cost' => 'float',
        'multiply_qty' => 'integer',
        'temp_shipping_cost' => 'float',
        'is_shipping_cost_updated' => 'integer'
    ];

    public function translations()
    {
        return $this->morphMany('App\Model\Translation', 'translationable');
    }



    public function scopeActive($query)
    {
        $brand_setting = Helpers::get_business_settings('product_brand');
        $digital_product_setting = Helpers::get_business_settings('digital_product');

        if (!$digital_product_setting) {
            $product_type = ['physical'];
        } else {
            $product_type = ['digital', 'physical'];
        }

        return $query->when($brand_setting, function ($q) {
            $q->whereHas('brand', function ($query) {
                $query->where(['status' => 1]);
            });
        })->when(!$brand_setting, function ($q) {
            $q->whereNull('brand_id');
        })->where(['status' => 1])->orWhere(function ($query) {
            $query->whereNull('brand_id')->where('status', 1);
        })->SellerApproved()->whereIn('product_type', $product_type);
    }

    public function scopeSellerApproved($query)
    {
        $query->whereHas('seller', function ($query) {
            $query->where(['status' => 'approved']);
        })->orWhere(function ($query) {
            $query->where(['status' => 1]);
        });
    }

    public function scopeApproveded($query)
    {
        return   $query->whereHas('Seller', function ($query) {
            $query->where(['status' => 'approved']);
        });
    }

    public function scopeTemporary($query)
    {
        return   $query->join('shops', 'products.user_id', '=', 'shops.seller_id')
            ->select('products.*')
            ->where(['shops.temporary_close' => 0, 'shops.vacation_status' => 0]);
    }

    public function scopeApproveded333()
    {
        return $this->belongsTo(Seller::class, 'user_id')
            ->where('status', 'approved');
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function scopeStatus($query)
    {
        return $query->where('featured_status', 1);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'seller_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'user_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function rating()
    {
        return $this->hasMany(Review::class)
            ->select(DB::raw('avg(rating) average, product_id'))
            ->whereNull('delivery_man_id')
            ->groupBy('product_id');
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }


    public function order_delivered()
    {
        return $this->hasMany(OrderDetail::class, 'product_id')
            ->where('delivery_status', 'delivered');
    }

    public function wish_list()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function product_tags()
    {
        return $this->hasMany(ProductTag::class);
    }

    public function flash_deal_product()
    {
        return $this->hasMany(FlashDealProduct::class);
    }

    public function deal_product()
    {
        return $this->hasMany(DealOfTheDay::class, 'product_id')
            ->where('deal_of_the_days.status', 1)
            ->whereDate('start_date_time', '<=', now())
            ->whereDate('expire_date_time', '>=', now());
    }



    public function compare_list()
    {
        return $this->hasMany(ProductCompare::class);
    }

    public function getNameAttribute($name)
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }

    public function getDetailsAttribute($detail)
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/seller')) {
            return $detail;
        }
        return $this->translations[1]->value ?? $detail;
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                if (strpos(url()->current(), '/api')) {
                    return $query->where('locale', App::getLocale());
                } else {
                    return $query->where('locale', Helpers::default_lang());
                }
            }, 'reviews' => function ($query) {
                $query->whereNull('delivery_man_id');
            }])->withCount(['reviews' => function ($query) {
                $query->whereNull('delivery_man_id');
            }]);
        });
    }
}
