<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
 
  

Route::group(['namespace' => 'api\v5', 'prefix' => 'v5', 'middleware' => ['api_lang']], function () {
    
  
    Route::post('shiprocket-webhook', 'ShippingMethodController@updateShiprocketStatus');

    Route::group(['prefix' => 'auth', 'namespace' => 'auth'], function () {
        Route::post('register-worker', 'RegistrationController@register_worker');
        Route::post('register-contractor', 'RegistrationController@register_individual_contructor');
        Route::post('register-contractor-firm', 'RegistrationController@register_contractor_firm');
        Route::post('login', 'PassportAuthController@login');
        Route::post('check-register-mobile', 'PassportAuthController@check_register_mobile');
         
        Route::get('user/{id}', 'PassportAuthController@user_data');
        
        Route::post('sendotp', 'PassportAuthController@sendotp');
        Route::post('mobile-login', 'PassportAuthController@mobilelogin');
        
        Route::post('check-mobile', 'PassportAuthController@send_otp');
        Route::post('verify-mobile', 'PassportAuthController@Verify_otp');

        Route::post('check-phone', 'PhoneVerificationController@check_phone');
        Route::post('resend-otp-check-phone', 'PhoneVerificationController@resend_otp_check_phone');
        Route::post('verify-phone', 'PhoneVerificationController@verify_phone');

        Route::post('check-email', 'PassportAuthController@email_check');
        Route::post('resend-otp-check-email', 'EmailVerificationController@resend_otp_check_email');
        Route::post('verify-email', 'EmailVerificationController@verify_email');

        Route::post('forgot-password', 'ForgotPassword@reset_password_request');
        Route::post('verify-otp', 'ForgotPassword@otp_verification_submit');
        Route::put('reset-password', 'ForgotPassword@reset_password_submit');

        Route::any('social-login', 'SocialAuthController@social_login');
        Route::post('update-phone', 'SocialAuthController@update_phone');
    });
    
   


    Route::group(['prefix' => 'worker'], function () {
        Route::get('info', 'WorkerController@info');
        Route::post('update-profile', 'WorkerController@update_profile');
        Route::get('account-delete/{id}','WorkerController@account_delete');


      
    });
    
     Route::group(['prefix' => 'contrator'], function () {
        Route::get('info', 'ContractorController@info');
        Route::post('update-profile', 'ContractorController@update_individual_profile');
        Route::post('firm/update-profile', 'ContractorController@update_firm_profile');
        Route::put('cm-firebase-token', 'ContractorController@update_cm_firebase_token');
        Route::get('account-delete/{id}','ContractorController@account_delete');

        Route::get('get-restricted-country-list','ContractorController@get_restricted_country_list');
        Route::get('get-restricted-zip-list','ContractorController@get_restricted_zip_list');

      
    });
    
      Route::group(['prefix' => 'location'], function () {
       
        Route::post('get_city', 'LocationController@get_city');
        Route::get('get_all_city', 'LocationController@get_all_city');
        Route::post('get_pincode', 'LocationController@get_pincode');
        Route::get('get_all_state', 'LocationController@get_all_state');
     
    });
    
    Route::group(['prefix' => 'details'], function () {
       
        Route::post('service-provider-details', 'ServiceProviderController@details_by_id');
        Route::post('all-views', 'ServiceProviderController@all_views');
        Route::post('related-constructor', 'ServiceProviderController@related_constructor');
       // Route::post('add-gallery', 'ServiceProviderController@add_gallery');
       
    });
    
     Route::group(['prefix' => 'provider-gellery'], function () {
       
       
        Route::post('add-gallery', 'ServiceProviderController@add_gallery');
        Route::post('get-gallery-data', 'ServiceProviderController@get_gallery_data');
        Route::post('project-delete','ServiceProviderController@project_delete');
    });
    
    
    Route::group(['prefix' => 'termsCondition'], function () {
       
        Route::get('faq', 'PolicyController@faq');
       
    });
    
    Route::group(['prefix' => 'service-home'], function () {
       
        Route::post('/', 'HomeController@index');
       
    });
    
    Route::group(['prefix' => 'related-service-provider'], function () {
       
        Route::post('/', 'HomeController@related_service_provider');
       
    });
    
    Route::group(['prefix' => 'membership','middleware' => 'auth:api'], function () {
       
        Route::post('buy', 'MembershipController@store');
       
    });
    
    Route::group(['prefix' => 'membership-plan'], function () {
              Route::get('plan-list', 'MembershipController@plan_list');
     });
    
    Route::group(['prefix' => 'scheme','middleware' => 'auth:api'], function () {
       
       // Route::post('scheme-progress ', 'MembershipController@scheme_progress');
        Route::post('scheme-progress ', 'MembershipController@scheme_progress_all');
        Route::post('scheme-transction', 'MembershipController@scheme_transction');
       
    });
    
    Route::group(['prefix' => 'provider','middleware' => 'auth:api'], function () {
        
        Route::get('account-delete/{id}','CustomerController@account_delete');
       Route::post('add-favourite', 'ServiceProviderController@add_favourite');
       Route::post('all-provider-favourite', 'ServiceProviderController@all_favorite_provider');
       
        Route::post('add-review', 'ServiceProviderController@add_review');
       Route::post('all-provider-review', 'ServiceProviderController@all_favorite_review');
       // wallet history api 
       
       Route::get('wallet', 'ServiceProviderController@wallet_history');
       
       // contact us api
       Route::post('contact-us', 'BasicProviderController@contact_us');
       
       // transction of the membership
       Route::get('transactions', 'BasicProviderController@transactions');
       
       // all review of the provider 
       Route::post('all-reviews', 'BasicProviderController@all_reviews');
       
       //Route::get('account-delete/{id}','ServiceProviderController@account_delete');
       
    });
    
    /* Route::group(['prefix' => 'config','middleware' => 'auth:api'], function () {
        Route::get('/', 'ConfigController@configuration');
    });*/
    
    Route::group(['prefix' => 'config'], function () {
        Route::get('/', 'ConfigController@handleConfiguration');
    });
    
     Route::group(['prefix' => 'getUserData','middleware' => 'auth:api'], function () {
        Route::get('/', 'BasicProviderController@getUserData');
    });
    
     Route::group(['prefix' => 'banners'], function () {
        Route::get('/', 'BasicProviderController@get_banners');
    });

    Route::group(['prefix' => 'service-provider'], function () {
      Route::get('generate-invoice/{id}', 'MembershipController@generate_invoice')->name('generate-invoice');
      Route::get('category', 'HomeController@category')->name('category');

    });


});

  


