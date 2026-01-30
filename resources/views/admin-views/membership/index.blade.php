@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Membership Plan'))

@push('css_or_js')
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/back-end/css/custom.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ asset('/public/assets/back-end/img/coupon_setup.png') }}" alt="">
                {{ \App\CPU\translate('Plan') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.Membership_plan.add-data') }}" method="post">

                            @csrf

                            <div class="row">
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="plan_name"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('plan_name') }}</label>
                                    <input type="text" name="plan_name" class="form-control"
                                        value="{{ old('plan_name') }}" id="plan_name"
                                        placeholder="{{ \App\CPU\translate('Plan Name') }}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="plan_description"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('plan_description') }}</label>
                                    <input type="text" name="plan_description" class="form-control"
                                        value="{{ old('plan_description') }}" id="plan_description"
                                        placeholder="{{ \App\CPU\translate('Plan Description') }}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="price"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('price') }}</label>
                                    <input type="number" min="0" step="0.01" name="price" class="form-control"
                                        value="{{ old('price') }}" id="price"
                                        placeholder="{{ \App\CPU\translate('Price') }}" required>
                                </div>

                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="logo"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('validity') }}</label>
                                    <select class="form-control" id="validity" name="validity" required>
                                        <option value="monthly">{{ \App\CPU\translate('monthly') }}</option>
                                        <option value="yearly">{{ \App\CPU\translate('yearly') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="logo"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('logo') }}</label>
                                    <select class="form-control" id="logo" name="logo" required>
                                        <option value="1">{{ \App\CPU\translate('Yes') }}</option>
                                        <option value="0">{{ \App\CPU\translate('No') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="trusted_partner_tag"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('trusted_partner_tag') }}</label>
                                    <select class="form-control" id="trusted_partner_tag" name="trusted_partner_tag"
                                        required>
                                        <option value="1">{{ \App\CPU\translate('Yes') }}</option>
                                        <option value="0">{{ \App\CPU\translate('No') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="profile_image"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('profile_image') }}</label>
                                    <select class="form-control" id="profile_image" name="profile_image" required>
                                        <option value="1">{{ \App\CPU\translate('Yes') }}</option>
                                        <option value="0">{{ \App\CPU\translate('No') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="contact_no_show"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('contact_no_show') }}</label>
                                    <select class="form-control" id="contact_no_show" name="contact_no_show" required>
                                        <option value="1">{{ \App\CPU\translate('Yes') }}</option>
                                        <option value="0">{{ \App\CPU\translate('No') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="mail_id"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('Mail Id') }}</label>
                                    <select class="form-control" id="mail_id" name="mail_id" required>
                                        <option value="1">{{ \App\CPU\translate('Yes') }}</option>
                                        <option value="0">{{ \App\CPU\translate('No') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="whatapp_contact"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('Whatsapp contact') }}</label>
                                    <select class="form-control" id="whatapp_contact" name="whatapp_contact" required>
                                        <option value="1">{{ \App\CPU\translate('Yes') }}</option>
                                        <option value="0">{{ \App\CPU\translate('No') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="social_media_link"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('Social Media Link Info') }}</label>
                                    <select class="form-control" id="social_media_link" name="social_media_link"
                                        required>
                                        <option value="1">{{ \App\CPU\translate('Yes') }}</option>
                                        <option value="0">{{ \App\CPU\translate('No') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="website"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('Website') }}</label>
                                    <select class="form-control" id="website" name="website" required>
                                        <option value="1">{{ \App\CPU\translate('Yes') }}</option>
                                        <option value="0">{{ \App\CPU\translate('No') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="free_2d_design"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('free_2d_design') }}</label>
                                    <input type="number" min="0" name="free_2d_design" class="form-control"
                                        value="{{ old('free_2d_design') }}" id="free_2d_design"
                                        placeholder="{{ \App\CPU\translate('Free 2D Design') }}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="free_3d_design"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('free_3d_design') }}</label>
                                    <input type="number" min="0" name="free_3d_design" class="form-control"
                                        value="{{ old('free_3d_design') }}" id="free_3d_design"
                                        placeholder="{{ \App\CPU\translate('Free 3D Design') }}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="rewards_on_self_purchase"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('rewards_on_self_purchase') }}</label>
                                    <select class="form-control" id="rewards_on_self_purchase"
                                        name="rewards_on_self_purchase" required>
                                        <option value="1">{{ \App\CPU\translate('Yes') }}</option>
                                        <option value="0">{{ \App\CPU\translate('No') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="rewards_on_client_purchase"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('rewards_on_client_purchase') }}</label>
                                    <select class="form-control" id="rewards_on_client_purchase"
                                        name="rewards_on_client_purchase" required>
                                        <option value="1">{{ \App\CPU\translate('Yes') }}</option>
                                        <option value="0">{{ \App\CPU\translate('No') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="reward_value"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('reward_value') }}</label>
                                    <input type="number" min="0" step="0.01" name="reward_value"
                                        class="form-control" value="{{ old('reward_value') }}" id="reward_value"
                                        placeholder="{{ \App\CPU\translate('Reward Value') }}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="listing_view"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('listing_view') }}</label>
                                    <select class="form-control" id="listing_view" name="listing_view" required>
                                        <option value="Posting Date Wise">{{ \App\CPU\translate('Posting Date Wise') }}
                                        </option>
                                        <option value="Rotation Wise & Business Wise">
                                            {{ \App\CPU\translate('Rotation Wise & Business Wise') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="advertisement"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('advertisement') }}</label>
                                    <select class="form-control" id="advertisement" name="advertisement" required>
                                        <option value="1">{{ \App\CPU\translate('Yes') }}</option>
                                        <option value="0">{{ \App\CPU\translate('No') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="scheme_participation"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('scheme_participation') }}</label>
                                    <select class="form-control" id="scheme_participation" name="scheme_participation"
                                        required>
                                        <option value="1">{{ \App\CPU\translate('Yes') }}</option>
                                        <option value="0">{{ \App\CPU\translate('No') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="discount_on_delivery"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('discount_on_delivery') }}</label>
                                    <input type="number" min="0" step="0.01" name="discount_on_delivery"
                                        class="form-control" value="{{ old('discount_on_delivery') }}"
                                        id="discount_on_delivery"
                                        placeholder="{{ \App\CPU\translate('Discount on Delivery') }}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="discount_on_yearly_plan"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('discount_on_yearly_plan') }}</label>
                                    <input type="number" min="0" step="0.01" name="discount_on_yearly_plan"
                                        class="form-control" value="{{ old('discount_on_yearly_plan') }}"
                                        id="discount_on_yearly_plan"
                                        placeholder="{{ \App\CPU\translate('Discount on Yearly Plan') }}" required>
                                </div>
                                <!-- Add other fields here according to the provided list -->
                            </div>

                            <div class="d-flex align-items-center justify-content-end flex-wrap gap-10">
                                <button type="reset"
                                    class="btn btn-secondary px-4">{{ \App\CPU\translate('reset') }}</button>
                                <button type="submit"
                                    class="btn btn--primary px-4">{{ \App\CPU\translate('Submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row justify-content-between align-items-center flex-grow-1">
                            <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                <h5 class="mb-0 text-capitalize d-flex gap-2">
                                    {{ \App\CPU\translate('plan_list') }}
                                    <span class="badge badge-soft-dark radius-50 fz-12 ml-1">{{ $mem->total() }}</span>
                                </h5>
                            </div>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-merge input-group-custom">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{ \App\CPU\translate('Search by Plan or Price or Reward value') }}"
                                            value="{{ $search }}" aria-label="Search orders" required>
                                        <button type="submit"
                                            class="btn btn--primary">{{ \App\CPU\translate('search') }}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table {{ Session::get('direction') === 'rtl' ? 'text-right' : 'text-left' }}">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ \App\CPU\translate('SL') }}</th>
                                    <th>{{ \App\CPU\translate('Plan Name') }}</th>
                                    <th>{{ \App\CPU\translate('Price') }}</th>
                                    <th>{{ \App\CPU\translate('Logo') }}</th>
                                    <th>{{ \App\CPU\translate('Reward Value') }}</th>
                                    <th>{{ \App\CPU\translate('Advertisement') }}</th>
                                    <th>{{ \App\CPU\translate('Enabled/Disabled') }}</th>
                                    <th class="text-center">{{ \App\CPU\translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mem as $k => $m)
                                    <tr>
                                        <td>{{ $mem->firstItem() + $k }}</td>
                                        <td>
                                            <strong>
                                                <div>{{ substr($m['plan_name'], 0, 20) }}</div>
                                            </strong>

                                        </td>
                                        <td class="text-capitalize">{{ $m['price'] }}</td>
                                        <td>
                                            @if ($m['logo'] == 1)
                                                {{ 'Yes' }}
                                            @else
                                                {{ 'No' }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $m['reward_value'] }}
                                        </td>
                                        <td>
                                            @if ($m['advertisement'] == 1)
                                                {{ 'Yes' }}
                                            @else
                                                {{ 'No' }}
                                            @endif

                                        </td>
                                        <td>
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher_input"
                                                    onclick="location.href='{{ route('admin.Membership_plan.status', [$m['id'], $m->status ? 0 : 1]) }}'"
                                                    class="toggle-switch-input" {{ $m->status ? 'checked' : '' }}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-10 justify-content-center">
                                                <!--<button class="btn btn-outline--primary square-btn btn-sm mr-1" onclick="get_details(this)" data-id="{{ $m['id'] }}" data-toggle="modal" data-target="#exampleModalCenter">
                                                    <img src="{{ asset('/public/assets/back-end/img/eye.svg') }}" class="svg" alt="">
                                                </button>-->
                                                <a class="btn btn-outline--primary btn-sm edit"
                                                    href="{{ route('admin.Membership_plan.update', [$m['id']]) }}"
                                                    title="{{ \App\CPU\translate('Edit') }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm delete" href="javascript:"
                                                    onclick="form_alert('coupon-{{ $m['id'] }}','Want to delete this plan ?')"
                                                    title="{{ \App\CPU\translate('delete') }}">
                                                    <i class="tio-delete"></i>
                                                </a>
                                                <!--<a class="btn btn-outline-success square-btn btn-sm mr-1" target="_blank" title="{{ \App\CPU\translate('invoice') }}"
                                                    href="{{ route('admin.Membership_plan.generate-invoice', $m['id']) }}">
                                                    <i class="tio-file"></i>
                                                </a>-->
                                                <form action="{{ route('admin.Membership_plan.delete', [$m['id']]) }}"
                                                    method="post" id="coupon-{{ $m['id'] }}">
                                                    @csrf @method('delete')
                                                </form>
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="modal fade" id="quick-view" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered coupon-details" role="document">
                                <div class="modal-content" id="quick-view-modal">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {{ $mem->links() }}
                        </div>
                    </div>

                    @if (count($mem) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ asset('public/assets/back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description">
                            <p class="mb-0">{{ \App\CPU\translate('No data to show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <!-- Add your scripts here -->
@endpush
