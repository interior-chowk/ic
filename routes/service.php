<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Service',
    'prefix' => 'service',
    'as' => 'service.'
], function () {

    Route::get('/dashboard', 'DashboardController@dashboard')->name('dashboard');
    Route::get('/schemes', 'SchemeController@list')->name('scheme');
    Route::get('/subscription-plan', 'SubscriptionController@list')->name('subscription');
    Route::get('/my-shop', 'MyshopController@list')->name('myshop');
    Route::post('/razorpay/create-order', 'SubscriptionController@createOrder')->name('razorpay.create.order');
    Route::post('/razorpay/verify', 'SubscriptionController@verifyPayment')->name('razorpay.verify');
    Route::get('/payment-success', fn () => view('payment.success'))->name('payment.success');

    });