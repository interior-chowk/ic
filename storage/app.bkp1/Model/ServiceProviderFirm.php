<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ServiceProviderFirm extends Model
{
   
  protected $casts = [
        'provider_id ' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
  
    protected $fillable = ['name', 'provider_id', 'firm_name', 'firm_address', 'role_in_firm', 'gstin', 'pan', 'date_of_incorporation', 'gst_image', 'pan_image', 'number_of_team', 'about_work', 'specialization_in', 'work_done_yet_image', 'bank_name', 'account_no', 'ifsc_code', 'branch'];
}
