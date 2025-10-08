@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Membership Plan'))

@push('css_or_js')
    <link href="{{ asset('assets/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/custom.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{asset('/public/assets/back-end/img/coupon_setup.png')}}" alt="">
                {{\App\CPU\translate('Plan update')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
         <div class="row">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                             <form action="{{route('admin.Membership_plan.update',[$m['id']])}}" method="post">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="plan_name" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('plan_name')}}</label>
                                    <input type="text" name="plan_name" class="form-control" value="{{ $m->plan_name }}" id="plan_name"
                                           placeholder="{{\App\CPU\translate('Plan Name')}}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="plan_description" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('plan_description')}}</label>
                                    <input type="text" name="plan_description" class="form-control" value="{{ $m->plan_description }}" id="plan_description"
                                           placeholder="{{\App\CPU\translate('Plan Description')}}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="price" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('price')}}</label>
                                    <input type="number" min="0" step="0.01" name="price" class="form-control" value="{{ $m->price }}" id="price"
                                           placeholder="{{\App\CPU\translate('Price')}}" required>
                                </div>
                                
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="logo" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('validity')}}</label>
                                    <select class="form-control" id="validity" name="validity" required>
                                    <option value="monthly" {{ ($m->validity == 'monthly') ? 'selected' : ''  }} >{{\App\CPU\translate('monthly')}}</option>
                                    <option value="yearly" {{ ($m->validity == 'yearly') ? 'selected' : ''  }}>{{\App\CPU\translate('yearly')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="logo" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('logo')}}</label>
                                    <select class="form-control" id="logo" name="logo" required>
                                        <option value="1" {{ ($m->logo == 1) ? 'selected' : ''  }}>{{\App\CPU\translate('Yes')}}</option>
                                        <option value="0" {{ ($m->logo == 0) ? 'selected' : ''  }}>{{\App\CPU\translate('No')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="trusted_partner_tag" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('trusted_partner_tag')}}</label>
                                    <select class="form-control" id="trusted_partner_tag" name="trusted_partner_tag" required>
                                        <option value="1" {{ ($m->trusted_partner_tag == 1) ? 'selected' : ''  }}>{{\App\CPU\translate('Yes')}}</option>
                                        <option value="0" {{ ($m->trusted_partner_tag == 0) ? 'selected' : ''  }}>{{\App\CPU\translate('No')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="profile_image" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('profile_image')}}</label>
                                    <select class="form-control" id="profile_image" name="profile_image" required>
                                        <option value="1" {{ ($m->profile_image == 1) ? 'selected' : ''  }}>{{\App\CPU\translate('Yes')}}</option>
                                        <option value="0" {{ ($m->profile_image == 0) ? 'selected' : ''  }}>{{\App\CPU\translate('No')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="contact_no_show" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('contact_no_show')}}</label>
                                    <select class="form-control" id="contact_no_show" name="contact_no_show" required>
                                        <option value="1" {{ ($m->contact_no_show == 1) ? 'selected' : ''  }}>{{\App\CPU\translate('Yes')}}</option>
                                        <option value="0" {{ ($m->contact_no_show == 0) ? 'selected' : ''  }}>{{\App\CPU\translate('No')}}</option>
                                    </select>
                                </div>
                                
                                 <div class="col-md-6 col-lg-4 form-group">
                                    <label for="mail_id" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('Mail Id')}}</label>
                                    <select class="form-control" id="mail_id" name="mail_id" required>
                                        <option value="1" {{ ($m->mail_id == 1) ? 'selected' : ''  }}>{{\App\CPU\translate('Yes')}}</option>
                                        <option value="0" {{ ($m->mail_id == 0) ? 'selected' : ''  }}>{{\App\CPU\translate('No')}}</option>
                                    </select>
                                </div>
                                
                                 <div class="col-md-6 col-lg-4 form-group">
                                    <label for="whatapp_contact" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('Whatsapp contact')}}</label>
                                    <select class="form-control" id="whatapp_contact" name="whatapp_contact" required>
                                        <option value="1" {{ ($m->whatapp_contact == 1) ? 'selected' : ''  }}>{{\App\CPU\translate('Yes')}}</option>
                                        <option value="0" {{ ($m->whatapp_contact == 0) ? 'selected' : ''  }}>{{\App\CPU\translate('No')}}</option>
                                    </select>
                                </div>
                                
                                 <div class="col-md-6 col-lg-4 form-group">
                                    <label for="social_media_link" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('Social Media Link Info')}}</label>
                                    <select class="form-control" id="social_media_link" name="social_media_link" required>
                                        <option value="1" {{ ($m->social_media_link == 1) ? 'selected' : ''  }}>{{\App\CPU\translate('Yes')}}</option>
                                        <option value="0" {{ ($m->social_media_link == 0) ? 'selected' : ''  }}>{{\App\CPU\translate('No')}}</option>
                                    </select>
                                </div>
                                
                                 <div class="col-md-6 col-lg-4 form-group">
                                    <label for="website" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('website')}}</label>
                                    <select class="form-control" id="website" name="website" required>
                                        <option value="1" {{ ($m->website == 1) ? 'selected' : ''  }}>{{\App\CPU\translate('Yes')}}</option>
                                        <option value="0" {{ ($m->website == 0) ? 'selected' : ''  }}>{{\App\CPU\translate('No')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="free_2d_design" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('free_2d_design')}}</label>
                                    <input type="number" min="0" name="free_2d_design" class="form-control" value="{{ $m->free_2d_design }}" id="free_2d_design"
                                           placeholder="{{\App\CPU\translate('Free 2D Design')}}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="free_3d_design" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('free_3d_design')}}</label>
                                    <input type="number" min="0" name="free_3d_design" class="form-control" value="{{ $m->free_3d_design }}" id="free_3d_design"
                                           placeholder="{{\App\CPU\translate('Free 3D Design')}}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="rewards_on_self_purchase" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('rewards_on_self_purchase')}}</label>
                                    <select class="form-control" id="rewards_on_self_purchase" name="rewards_on_self_purchase" required>
                                        <option value="1" {{ ($m->rewards_on_self_purchase == 1) ? 'selected' : '' }}>{{\App\CPU\translate('Yes')}}</option>
                                        <option value="0" {{ ($m->rewards_on_self_purchase == 0) ? 'selected' : '' }}>{{\App\CPU\translate('No')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="rewards_on_client_purchase" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('rewards_on_client_purchase')}}</label>
                                    <select class="form-control" id="rewards_on_client_purchase" name="rewards_on_client_purchase" required>
                                        <option value="1" {{ ($m->rewards_on_client_purchase == 1) ? 'selected' : '' }} >{{\App\CPU\translate('Yes')}}</option>
                                        <option value="0" {{ ($m->rewards_on_client_purchase == 0) ? 'selected' : '' }} >{{\App\CPU\translate('No')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="reward_value" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('reward_value')}}</label>
                                    <input type="number" min="0" step="0.01" name="reward_value" class="form-control" value="{{ $m->reward_value }}" id="reward_value"
                                           placeholder="{{\App\CPU\translate('Reward Value')}}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="listing_view" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('listing_view')}}</label>
                                    <select class="form-control" id="listing_view" name="listing_view" required>
                                        <option value="Posting Date Wise" {{ ($m->listing_view == 'Posting Date Wise') ? 'selected' : '' }}>{{\App\CPU\translate('Posting Date Wise')}}</option>
                                        <option value="Rotation Wise & Business Wise" {{ ($m->listing_view == 'Rotation Wise & Business Wise') ? 'selected' : '' }}>{{\App\CPU\translate('Rotation Wise & Business Wise')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="advertisement" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('advertisement')}}</label>
                                    <select class="form-control" id="advertisement" name="advertisement" required>
                                        <option value="1" {{ ($m->advertisement == 1) ? 'selected' : '' }}>{{\App\CPU\translate('Yes')}}</option>
                                        <option value="0" {{ ($m->advertisement == 0) ? 'selected' : '' }}>{{\App\CPU\translate('No')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="scheme_participation" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('scheme_participation')}}</label>
                                    <select class="form-control" id="scheme_participation" name="scheme_participation" required>
                                        <option value="1" {{ ($m->scheme_participation == 1) ? 'selected' : '' }}>{{\App\CPU\translate('Yes')}}</option>
                                        <option value="0" {{ ($m->scheme_participation == 0) ? 'selected' : '' }}>{{\App\CPU\translate('No')}}</option> 
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="discount_on_delivery" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('discount_on_delivery')}}</label>
                                    <input type="number" min="0" step="0.01" name="discount_on_delivery" class="form-control" value="{{ $m->discount_on_delivery }}" id="discount_on_delivery"
                                           placeholder="{{\App\CPU\translate('Discount on Delivery')}}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="discount_on_yearly_plan" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('discount_on_yearly_plan')}}</label>
                                    <input type="number" min="0" step="0.01" name="discount_on_yearly_plan" class="form-control" value="{{ $m->discount_on_yearly_plan }}" id="discount_on_yearly_plan"
                                           placeholder="{{\App\CPU\translate('Discount on Yearly Plan')}}" required> 
                                </div>
                                <!-- Add other fields here according to the provided list -->
                            </div>

                            <div class="d-flex align-items-center justify-content-end flex-wrap gap-10">
                                <button type="reset" class="btn btn-secondary px-4">{{\App\CPU\translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary px-4">{{\App\CPU\translate('Update')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
             </div>
            
    </div>
@endsection

@push('script')
    <!-- Add your scripts here -->
@endpush
