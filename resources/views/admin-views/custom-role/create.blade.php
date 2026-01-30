@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Create Role'))
@push('css_or_js')
    <link href="{{ asset('public/assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">

        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ asset('/public/assets/back-end/img/add-new-seller.png') }}" alt="">
                {{ \App\CPU\translate('Employee_Role_Setup') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <style>
            /* Subtle, clean styling */
            .perm-card {
                border: 1px solid #e9ecef;
                border-radius: 10px;
                padding: 12px;
                background: #fafafa;
            }

            .perm-title {
                font-weight: 600;
                color: #343a40;
                cursor: pointer;
            }

            .sub-modules {
                margin-left: 20px;
                border-left: 2px dashed #e5e7eb;
                padding-left: 14px;
                margin-top: 8px;
            }

            .product-actions {
                margin-top: 8px;
            }

            .product-actions .form-check-inline,
            .product-actions .d-flex {
                margin-right: 10px;
            }

            .gap-2>* {
                margin-right: .5rem;
            }

            .gap-4>* {
                margin-right: 1rem;
            }
        </style>

        <div class="card mb-4">
            <div class="card-body">
                <form id="submit-create-role" method="post" action="{{ route('admin.custom-role.store') }}"
                    style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="name" class="title-color">{{ \App\CPU\translate('role_name') }}</label>
                                <input type="text" name="name" class="form-control" id="name"
                                    placeholder="{{ \App\CPU\translate('Ex') }} : {{ \App\CPU\translate('Store') }}"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-4 flex-wrap mb-3 align-items-center">
                        <label class="title-color font-weight-bold mb-0">
                            {{ \App\CPU\translate('module_permission') }}
                        </label>
                        <div class="form-group d-flex gap-2 mb-0">
                            <input type="checkbox" id="select_all">
                            <label class="title-color mb-0" for="select_all">{{ \App\CPU\translate('Select All') }}</label>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-sm-12 col-lg-6 mb-3">
                            <div class="form-group perm-card">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="module-checkbox parent" name="modules[]" value="dashboard"
                                        id="dashboard">
                                    <label for="dashboard"
                                        class="title-color mb-0 perm-title">{{ \App\CPU\translate('Dashboard') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-lg-6 mb-3">
                            <div class="form-group perm-card">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="module-checkbox parent" name="modules[]" value="orders"
                                        id="orders">
                                    <label for="orders"
                                        class="title-color mb-0 perm-title">{{ \App\CPU\translate('Orders') }}</label>
                                </div>

                                <div class="ms-4 sub-modules" style="display:none;">
                                    @foreach (['standard_delivery' => 'Standard Delivery', 'instant_delivery' => 'Instant Delivery'] as $key => $label)
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                                value="{{ $key }}" id="{{ $key }}">
                                            <label for="{{ $key }}"
                                                class="mb-0">{{ \App\CPU\translate($label) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-lg-6 mb-3">
                            <div class="form-group perm-card">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="module-checkbox parent" name="modules[]" value="approval"
                                        id="approval">
                                    <label for="approval"
                                        class="title-color mb-0 perm-title">{{ \App\CPU\translate('Approval') }}</label>
                                </div>

                                <div class="ms-4 sub-modules" style="display:none;">
                                    @foreach (['products' => 'Products', 'Coupon' => 'Coupon', 'Seller' => 'Seller', 'Service Provider' => 'Service Provider', 'Return & Refund' => 'Return & Refund', 'Wallet' => 'Wallet'] as $key => $label)
                                        <div class="form-group">
                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox parent" name="sub_modules[]"
                                                    value="{{ $key }}" id="{{ $key }}">
                                                <label for="{{ $key }}"
                                                    class="mb-0">{{ \App\CPU\translate($label) }}</label>
                                            </div>

                                            @if ($key === 'products')
                                                <div class="ms-4 sub-modules" style="display:none;">
                                                    @foreach (['view', 'add', 'edit', 'delete'] as $act)
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" class="action-checkbox"
                                                                name="{{ $key }}_permissions[]"
                                                                value="{{ $act }}"
                                                                id="{{ $key }}_{{ $act }}">
                                                            <label for="{{ $key }}_{{ $act }}"
                                                                class="mb-0 text-capitalize">{{ $act }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-lg-6 mb-3">
                            <div class="form-group perm-card">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="module-checkbox parent" name="modules[]" value="create"
                                        id="create">
                                    <label for="create"
                                        class="title-color mb-0 perm-title">{{ \App\CPU\translate('Create') }}</label>
                                </div>

                                <div class="ms-4 sub-modules" style="display:none;">

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="home_products" id="home_products">
                                        <label for="home_products"
                                            class="mb-0">{{ \App\CPU\translate('Home Products') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="brands" id="brands">
                                        <label for="brands" class="mb-0">{{ \App\CPU\translate('Brands') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="product_attribute" id="product_attribute">
                                        <label for="product_attribute"
                                            class="mb-0">{{ \App\CPU\translate('Product Attribute') }}</label>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="checkbox" class="child-checkbox parent" name="sub_modules[]"
                                                value="inhouse_product" id="inhouse_product">
                                            <label for="inhouse_product"
                                                class="mb-0">{{ \App\CPU\translate('Inhouse Product') }}</label>
                                        </div>

                                        <div class="ms-4 sub-modules" style="display:none;">

                                            <div class="form-group">
                                                <div class="d-flex gap-2 align-items-center">
                                                    <input type="checkbox" class="child-checkbox parent"
                                                        name="sub_sub_modules[]" value="inhouse_products"
                                                        id="inhouse_products">
                                                    <label for="inhouse_products"
                                                        class="mb-0">{{ \App\CPU\translate('Products') }}</label>
                                                </div>

                                                <div class="ms-4 sub-modules" style="display:none;">
                                                    @foreach (['add', 'edit', 'view', 'delete'] as $act)
                                                        <div class="d-flex gap-2 align-items-center">
                                                            <input type="checkbox" name="permissions[inhouse_products][]"
                                                                value="{{ $act }}"
                                                                id="inhouse_products_{{ $act }}">
                                                            <label for="inhouse_products_{{ $act }}"
                                                                class="mb-0 text-capitalize">{{ $act }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox" name="sub_sub_modules[]"
                                                    value="bulk_import" id="bulk_import">
                                                <label for="bulk_import"
                                                    class="mb-0">{{ \App\CPU\translate('Bulk Import') }}</label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="checkbox" class="child-checkbox parent" name="sub_modules[]"
                                                value="product_category" id="product_category">
                                            <label for="product_category"
                                                class="mb-0">{{ \App\CPU\translate('Product Category') }}</label>
                                        </div>

                                        <div class="ms-4 sub-modules" style="display:none;">


                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox" name="sub_sub_modules[]"
                                                    value="categories" id="categories">
                                                <label for="categories"
                                                    class="mb-0">{{ \App\CPU\translate('categories') }}</label>
                                            </div>

                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox" name="sub_sub_modules[]"
                                                    value="sub_categories" id="sub_categories">
                                                <label for="sub_categories"
                                                    class="mb-0">{{ \App\CPU\translate('Sub Categories') }}</label>
                                            </div>

                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox" name="sub_sub_modules[]"
                                                    value="sub_sub_categories" id="sub_sub_categories">
                                                <label for="sub_sub_categories"
                                                    class="mb-0">{{ \App\CPU\translate('Sub Sub Categories') }}</label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="checkbox" class="child-checkbox parent" name="sub_modules[]"
                                                value="service_category" id="service_category">
                                            <label for="service_category"
                                                class="mb-0">{{ \App\CPU\translate('Service Category') }}</label>
                                        </div>

                                        <div class="ms-4 sub-modules" style="display:none;">


                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox" name="sub_sub_modules[]"
                                                    value="categories" id="categories">
                                                <label for="categories"
                                                    class="mb-0">{{ \App\CPU\translate('categories') }}</label>
                                            </div>

                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox" name="sub_sub_modules[]"
                                                    value="sub_categories" id="sub_categories">
                                                <label for="sub_categories"
                                                    class="mb-0">{{ \App\CPU\translate('Sub Categories') }}</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="col-sm-12 col-lg-6 mb-3">
                            <div class="form-group perm-card">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="module-checkbox parent" name="modules[]"
                                        value="list" id="list">
                                    <label for="list"
                                        class="title-color mb-0 perm-title">{{ \App\CPU\translate('List') }}</label>
                                </div>

                                <div class="ms-4 sub-modules" style="display:none;">

                                    <div class="form-group">
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="checkbox" class="child-checkbox parent" name="sub_modules[]"
                                                value="products_list" id="products_list">
                                            <label for="products_list"
                                                class="mb-0">{{ \App\CPU\translate('Products List') }}</label>
                                        </div>

                                        <div class="ms-4 sub-modules" style="display:none;">

                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox" name="sub_sub_modules[]"
                                                    value="approved_products" id="approved_products">
                                                <label for="approved_products"
                                                    class="mb-0">{{ \App\CPU\translate('Approved Products') }}</label>
                                            </div>

                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox" name="sub_sub_modules[]"
                                                    value="denied_products" id="denied_products">
                                                <label for="denied_products"
                                                    class="mb-0">{{ \App\CPU\translate('Denied Products') }}</label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="brand_list" id="brand_list">
                                        <label for="brand_list"
                                            class="mb-0">{{ \App\CPU\translate('Brand List') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="customer_list" id="customer_list">
                                        <label for="customer_list"
                                            class="mb-0">{{ \App\CPU\translate('Customer List') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="seller_list" id="seller_list">
                                        <label for="seller_list"
                                            class="mb-0">{{ \App\CPU\translate('Seller List') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="service_provider_list" id="service_provider_list">
                                        <label for="service_provider_list"
                                            class="mb-0">{{ \App\CPU\translate('Service Provider List') }}</label>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-lg-6 mb-3">
                            <div class="form-group perm-card">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="module-checkbox parent" name="modules[]"
                                        value="offers_deals" id="offers_deals">
                                    <label for="offers_deals"
                                        class="title-color mb-0 perm-title">{{ \App\CPU\translate('Offers & Deals') }}</label>
                                </div>

                                <div class="ms-4 sub-modules" style="display:none;">

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="coupon" id="coupon">
                                        <label for="coupon" class="mb-0">{{ \App\CPU\translate('Coupon') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="deal_of_the_day" id="deal_of_the_day">
                                        <label for="deal_of_the_day"
                                            class="mb-0">{{ \App\CPU\translate('Deal of the day') }}</label>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="checkbox" class="child-checkbox parent" name="sub_modules[]"
                                                value="banners" id="banners">
                                            <label for="banners"
                                                class="mb-0">{{ \App\CPU\translate('Banners') }}</label>
                                        </div>

                                        <div class="ms-4 sub-modules" style="display:none;">

                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox" name="sub_sub_modules[]"
                                                    value="delivery_banners" id="delivery_banners">
                                                <label for="delivery_banners"
                                                    class="mb-0">{{ \App\CPU\translate('Delivery banners') }}</label>
                                            </div>

                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox" name="sub_sub_modules[]"
                                                    value="service_provider_banners" id="service_provider_banners">
                                                <label for="service_provider_banners"
                                                    class="mb-0">{{ \App\CPU\translate('Service provider banners') }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="push_notification" id="push_notification">
                                        <label for="push_notification"
                                            class="mb-0">{{ \App\CPU\translate('Push Notification') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="push_notification_provider" id="push_notification_provider">
                                        <label for="push_notification_provider"
                                            class="mb-0">{{ \App\CPU\translate('Push Notification provider') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="membership_plan" id="membership_plan">
                                        <label for="membership_plan"
                                            class="mb-0">{{ \App\CPU\translate('Membership plan') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="scheme_management" id="scheme_management">
                                        <label for="scheme_management"
                                            class="mb-0">{{ \App\CPU\translate('Scheme management') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="blogs" id="blogs">
                                        <label for="blogs" class="mb-0">{{ \App\CPU\translate('Blogs') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="seo" id="seo">
                                        <label for="seo" class="mb-0">{{ \App\CPU\translate('Seo') }}</label>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-lg-6 mb-3">
                            <div class="form-group perm-card">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="module-checkbox parent" name="modules[]"
                                        value="reviews" id="reviews">
                                    <label for="reviews"
                                        class="title-color mb-0 perm-title">{{ \App\CPU\translate('Reviews') }}</label>
                                </div>

                                <div class="ms-4 sub-modules" style="display:none;">

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="customer_product_reviews" id="customer_product_reviews">
                                        <label for="customer_product_reviews"
                                            class="mb-0">{{ \App\CPU\translate('Customer Product Reviews') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="customer_service_reviews" id="customer_service_reviews">
                                        <label for="customer_service_reviews"
                                            class="mb-0">{{ \App\CPU\translate('Customer Service Reviews') }}</label>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-lg-6 mb-3">
                            <div class="form-group perm-card">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="module-checkbox parent" name="modules[]"
                                        value="reports" id="reports">
                                    <label for="reports"
                                        class="title-color mb-0 perm-title">{{ \App\CPU\translate('Reports') }}</label>
                                </div>

                                <div class="ms-4 sub-modules" style="display:none;">

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="shipping_reports" id="shipping_reports">
                                        <label for="shipping_reports"
                                            class="mb-0">{{ \App\CPU\translate('Shipping Reports') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="coupon_reports" id="coupon_reports">
                                        <label for="coupon_reports"
                                            class="mb-0">{{ \App\CPU\translate('Coupon Reports') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="cancellation_report" id="cancellation_report">
                                        <label for="cancellation_report"
                                            class="mb-0">{{ \App\CPU\translate('Cancellation Report') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="sale_return_report" id="sale_return_report">
                                        <label for="sale_return_report"
                                            class="mb-0">{{ \App\CPU\translate('Sale Return Report') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="product_report" id="product_report">
                                        <label for="product_report"
                                            class="mb-0">{{ \App\CPU\translate('Product Report') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="sales_orders_report" id="sales_orders_report">
                                        <label for="sales_orders_report"
                                            class="mb-0">{{ \App\CPU\translate('Sales & Orders Report') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="claim_report" id="claim_report">
                                        <label for="claim_report"
                                            class="mb-0">{{ \App\CPU\translate('Claim Report') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="payment_report" id="payment_report">
                                        <label for="payment_report"
                                            class="mb-0">{{ \App\CPU\translate('Payment Report') }}</label>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-lg-6 mb-3">
                            <div class="form-group perm-card">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="module-checkbox parent" name="modules[]"
                                        value="employees" id="employees">
                                    <label for="employees"
                                        class="title-color mb-0 perm-title">{{ \App\CPU\translate('Employees') }}</label>
                                </div>

                                <div class="ms-4 sub-modules" style="display:none;">

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="employee_role_setup" id="employee_role_setup">
                                        <label for="employee_role_setup"
                                            class="mb-0">{{ \App\CPU\translate('Employee Role Setup') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="employees_list" id="employees_list">
                                        <label for="employees_list"
                                            class="mb-0">{{ \App\CPU\translate('Employees') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="career" id="career">
                                        <label for="career" class="mb-0">{{ \App\CPU\translate('Career') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="applicants" id="applicants">
                                        <label for="applicants"
                                            class="mb-0">{{ \App\CPU\translate('Applicants') }}</label>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-lg-6 mb-3">
                            <div class="form-group perm-card">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="module-checkbox parent" name="modules[]"
                                        value="system_settings" id="system_settings">
                                    <label for="system_settings"
                                        class="title-color mb-0 perm-title">{{ \App\CPU\translate('System settings') }}</label>
                                </div>

                                <div class="ms-4 sub-modules" style="display:none;">

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="business_setup" id="business_setup">
                                        <label for="business_setup"
                                            class="mb-0">{{ \App\CPU\translate('Business Setup') }}</label>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" class="child-checkbox" name="sub_modules[]"
                                            value="third_party" id="third_party">
                                        <label for="third_party"
                                            class="mb-0">{{ \App\CPU\translate('3rd party') }}</label>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="checkbox" class="child-checkbox parent" name="sub_modules[]"
                                                value="pages_media" id="pages_media">
                                            <label for="pages_media"
                                                class="mb-0">{{ \App\CPU\translate('Pages & Media') }}</label>
                                        </div>

                                        <div class="ms-4 sub-modules" style="display:none;">

                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox" name="sub_sub_modules[]"
                                                    value="pages" id="pages">
                                                <label for="pages"
                                                    class="mb-0">{{ \App\CPU\translate('Pages') }}</label>
                                            </div>

                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox" name="sub_sub_modules[]"
                                                    value="social_media_links" id="social_media_links">
                                                <label for="social_media_links"
                                                    class="mb-0">{{ \App\CPU\translate('Social Media Links') }}</label>
                                            </div>

                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="checkbox" class="child-checkbox" name="sub_sub_modules[]"
                                                    value="sp_social_media_links" id="sp_social_media_links">
                                                <label for="sp_social_media_links"
                                                    class="mb-0">{{ \App\CPU\translate('SP Social Media Links') }}</label>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn--primary">{{ \App\CPU\translate('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            const ACTIONS = ['view', 'add', 'edit', 'delete'];

            function slugify(str) {
                return (str || '')
                    .toString()
                    .trim()
                    .toLowerCase()
                    .replace(/&/g, 'and')
                    .replace(/[^a-z0-9]+/g, '_')
                    .replace(/^_+|_+$/g, '');
            }

            function hasExistingProductActions(scopeEl, keySlug) {
                const selector = `input[name^="permissions"][name*="${keySlug}"]`;
                return !!scopeEl.querySelector(selector);
            }

            const labels = Array.from(document.querySelectorAll(
                '#submit-create-role .form-group > .d-flex > .form-check > label'
            )).filter(lb => {
                const txt = (lb.textContent || '').toLowerCase();
                return txt.includes('product');
            });

            labels.forEach((label, idx) => {
                const forId = label.getAttribute('for');
                const relatedInput = forId ? document.getElementById(forId) : null;
                const formGroup = label.closest('.form-group');
                if (!formGroup) return;

                const baseSlug = slugify(forId || label.textContent);
                const keySlug = baseSlug + '_' + idx;

                if (hasExistingProductActions(formGroup, keySlug)) return;

                let container = formGroup.querySelector(':scope > .sub-modules');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'ms-4 sub-modules';
                    container.style.display = 'none';
                    formGroup.appendChild(container);
                }

                const actionsWrap = document.createElement('div');
                actionsWrap.className = 'product-actions mt-2';

                ACTIONS.forEach(act => {
                    const wrap = document.createElement('div');
                    wrap.className = 'form-check form-check-inline d-inline-flex align-items-center';

                    const input = document.createElement('input');
                    input.type = 'checkbox';
                    input.className = 'action-checkbox';
                    input.name = `permissions[${keySlug}][]`;
                    input.value = act;
                    input.id = `${keySlug}_${act}`;

                    const lb = document.createElement('label');
                    lb.className = 'mb-0 text-capitalize ms-1';
                    lb.setAttribute('for', input.id);
                    lb.textContent = act;

                    wrap.appendChild(input);
                    wrap.appendChild(lb);
                    actionsWrap.appendChild(wrap);
                });

                container.appendChild(actionsWrap);

                const controller = relatedInput;
                if (controller) {
                    const reveal = () => {
                        container.style.display = controller.checked ? 'block' : 'none';
                    };
                    controller.addEventListener('change', reveal);
                    reveal();
                }
            });
        </script>



        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ \App\CPU\translate('role_table') }}</h5>
            </div>
            <div class="table-responsive">
                <table id="dataTable"
                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ \App\CPU\translate('SL') }}</th>
                            <th>{{ \App\CPU\translate('Role Name') }}</th>
                            <th>{{ \App\CPU\translate('Created At') }}</th>
                            <th>{{ \App\CPU\translate('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rl as $k => $role)
                            <tr>
                                <td>{{ $k + 1 }}</td>
                                <td>{{ $role['name'] }}</td>
                                <td>{{ $role['created_at']->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('admin.custom-role.update', [$role['id']]) }}"
                                            class="btn btn-outline--primary btn-sm square-btn"
                                            title="{{ \App\CPU\translate('Edit') }}">
                                            <i class="tio-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-danger btn-sm delete"
                                            title="{{ \App\CPU\translate('Delete') }}" id="{{ $role['id'] }}">
                                            <i class="tio-delete"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <script>
        // ✅ Select All
        $("#select_all").on('change', function() {
            const isChecked = this.checked;
            $('input[type="checkbox"]').prop('checked', isChecked);
            if (isChecked) {
                $('.sub-modules').show();
            } else {
                $('.sub-modules').hide();
            }
        });

        // ✅ Parent -> Child auto toggle
        $(document).on('change', '.parent', function() {
            const container = $(this).closest('.form-group');
            const subModules = container.find('> .sub-modules').first();

            if ($(this).is(':checked')) {
                subModules.show();
                subModules.find('input[type="checkbox"]').prop('checked', true);
            } else {
                subModules.hide();
                subModules.find('input[type="checkbox"]').prop('checked', false);
            }
        });

        // ✅ Child -> auto-check parent
        $(document).on('change', '.child-checkbox', function() {
            if ($(this).is(':checked')) {
                $(this).closest('.form-group').find('.parent').first().prop('checked', true).trigger('change');
            }
        });

        // ✅ Prevent submit if no module selected
        $('#submit-create-role').on('submit', function() {
            if ($("input[name='modules[]']:checked").length === 0) {
                toastr.warning('{{ \App\CPU\translate('select_minimum_one_selection_box') }}');
                return false;
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            Swal.fire({
                title: '{{ \App\CPU\translate('Are_you_sure_delete_this_role') }}?',
                text: "{{ \App\CPU\translate('You_will_not_be_able_to_revert_this') }}!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ \App\CPU\translate('Yes') }}, {{ \App\CPU\translate('delete_it') }}!',
                cancelButtonText: "{{ \App\CPU\translate('cancel') }}",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('admin.custom-role.delete') }}",
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success(
                                '{{ \App\CPU\translate('Role_deleted_successfully') }}');
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>

    <script>
        $(document).on('change', '.employee-role-status', function() {
            var id = $(this).attr("id");
            if ($(this).prop("checked") == true) {
                var status = 1;
            } else if ($(this).prop("checked") == false) {
                var status = 0;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.custom-role.employee-role-status') }}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function(data) {
                    if (data.success == true) {
                        toastr.success('{{ \App\CPU\translate('Status updated successfully') }}');
                    }
                }
            });
        });
    </script>
@endpush
