<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ShipyaariCredential extends Model
{
    protected $table = 'shipyaari_credential';

    protected $fillable = [
        'seller_id','name','email','contact_number','token','jwt','company_uuid','private_company_id',
        'brand_name','business_type','gst_number','gst_verified','pan_number','pan_verified',
        'aadhar_number','aadhar_verified','full_address','is_kyc_done',
        'is_wallet_recharge','is_returning_user','is_migrated','is_masked_user','is_wallet_blacklisted',
        'next_step','private_company','kyc_details','raw_response'
    ];

    protected $casts = [
        'next_step' => 'array',
        'private_company' => 'array',
        'kyc_details' => 'array',
        'raw_response' => 'array',
    ];
}
