@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Scheme Management'))

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
                {{ \App\CPU\translate('Scheme') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.Scheme_management.add-data') }}" method="post">

                            @csrf

                            <div class="row">
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="scheme_name"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('scheme_name') }}</label>
                                    <input type="text" name="scheme_name" class="form-control"
                                        value="{{ old('scheme_name') }}" id="scheme_name"
                                        placeholder="{{ \App\CPU\translate('Scheme Name') }}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="plan_description"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('scheme_type') }}</label>
                                    <select class="form-control" id="scheme_type_id" name="scheme_type" required>
                                        <option selected disabled>{{ \App\CPU\translate('select_scheme_type') }}</option>

                                        <option value="1">{{ \App\CPU\translate('brand') }}</option>
                                        <option value="2">{{ \App\CPU\translate('seller_/_store') }}</option>
                                        <option value="3">{{ \App\CPU\translate('Products') }}</option>

                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="plan_description"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('plans') }}</label>
                                    <select class="form-control" id="plan_id" name="plan_id" required>
                                        <option selected disabled>{{ \App\CPU\translate('select_plan') }}</option>
                                        @foreach ($plans as $id => $plan_name)
                                            <option value="{{ $id }}">{{ $plan_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="puchase_target_amount"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('puchase_target_amount') }}</label>
                                    <input type="number" min="0" step="0.01" name="puchase_target_amount"
                                        class="form-control" value="{{ old('puchase_target_amount') }}"
                                        id="puchase_target_amount"
                                        placeholder="{{ \App\CPU\translate('puchase_target_amount') }}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="duration"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('duration') }}</label>
                                    <input type="number" min="0" step="0.01" name="duration" class="form-control"
                                        value="{{ old('duration') }}" id="duration"
                                        placeholder="{{ \App\CPU\translate('duration in days') }}" required>
                                </div>

                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="rewards"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('Rewards') }}</label>
                                    <input type="text" name="rewards" class="form-control" value="{{ old('rewards') }}"
                                        id="rewards" placeholder="{{ \App\CPU\translate('Rewards ') }}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="isActive"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('isActive') }}</label>
                                    <select class="form-control" id="isActive" name="isActive" required>
                                        <option value="1">{{ \App\CPU\translate('Yes') }}</option>
                                        <option value="0">{{ \App\CPU\translate('No') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-12 form-group scheme-section" id="brand_section"
                                    style="display: none;">

                                    <label for="plan_description"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('brands') }}</label>
                                    <select class="form-control" id="brand_id" name="brand_ids[]" multiple="multiple">
                                        @foreach ($brands as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-12 form-group scheme-section" id="seller_section"
                                    style="display: none;">
                                    <label for="plan_description"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('sellers') }}</label>
                                    <select class="form-control" id="seller_id" name="seller_ids[]" multiple="multiple">
                                        @foreach ($sellers as $id => $f_name)
                                            <option value="{{ $id }}">{{ $f_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 col-lg-12 form-group scheme-section" id="product_section"
                                    style="display: none;">
                                    <label for="plan_description"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('products') }}</label>
                                    <select class="form-control" id="products_id" name="products_id[]"
                                        multiple="multiple">
                                        @foreach ($products as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-12 form-group">
                                    <label for="Description"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('Description') }}</label>
                                    <textarea name="Description" class="form-control" id="Description"
                                        placeholder="{{ \App\CPU\translate('Description') }}" required>{{ old('Description') }}</textarea>
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
                                    {{ \App\CPU\translate('scheme_list') }}
                                    <span class="badge badge-soft-dark radius-50 fz-12 ml-1">{{ $scheme->total() }}</span>
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
                                            placeholder="{{ \App\CPU\translate('Search by Scheme or Price or Reward value') }}"
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
                                    <th>{{ \App\CPU\translate('Scheme Name') }}</th>
                                    <th>{{ \App\CPU\translate('Target_amount') }}</th>
                                    <th>{{ \App\CPU\translate('Reward') }}</th>
                                    <th>{{ \App\CPU\translate('Enabled/Disabled') }}</th>
                                    <th class="text-center">{{ \App\CPU\translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($scheme as $k => $m)
                                    <tr>
                                        <td>{{ $scheme->firstItem() + $k }}</td>
                                        <td>
                                            <strong>
                                                <div>{{ substr($m['scheme_name'], 0, 20) }}</div>
                                            </strong>

                                        </td>
                                        <td class="text-capitalize">{{ $m['puchase_target_amount'] }}</td>

                                        <td>
                                            {{ $m['rewards'] }}
                                        </td>

                                        <td>
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher_input"
                                                    onclick="location.href='{{ route('admin.Scheme_management.status', [$m['id'], $m->isActive ? 0 : 1]) }}'"
                                                    class="toggle-switch-input" {{ $m->isActive ? 'checked' : '' }}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-10 justify-content-center">
                                                <!--<button class="btn btn-outline--primary square-btn btn-sm mr-1" onclick="get_details(this)" data-id="{{ $m['id'] }}" data-toggle="modal" data-target="#exampleModalCenter">
                                                        <img src="{{ asset('/public/assets/back-end/img/eye.svg') }}" class="svg" alt="">
                                                    </button>-->
                                                <a class="btn btn-outline--primary btn-sm edit"
                                                    href="{{ route('admin.Scheme_management.update', [$m['id']]) }}"
                                                    title="{{ \App\CPU\translate('Edit') }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm delete" href="javascript:"
                                                    onclick="form_alert('coupon-{{ $m['id'] }}','Want to delete this scheme ?')"
                                                    title="{{ \App\CPU\translate('delete') }}">
                                                    <i class="tio-delete"></i>
                                                </a>
                                                <form action="{{ route('admin.Scheme_management.delete', [$m['id']]) }}"
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
                            {{ $scheme->links() }}
                        </div>
                    </div>

                    @if (count($scheme) == 0)
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

    <!-- Select2 JS -->

    <script>
        $(document).ready(function() {
            $('#products_id').select2({
                placeholder: "{{ \App\CPU\translate('select_products') }}",
                allowClear: true
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#brand_id').select2({
                placeholder: "{{ \App\CPU\translate('select_brands') }}",
                allowClear: true
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#seller_id').select2({
                placeholder: "{{ \App\CPU\translate('select_sellers') }}",
                allowClear: true
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#scheme_type_id').change(function() {
                $('.scheme-section').hide(); // Hide all sections

                var selectedScheme = $(this).val();

                if (selectedScheme == '1') {
                    $('#brand_section').show();
                } else if (selectedScheme == '2') {
                    $('#seller_section').show();
                } else if (selectedScheme == '3') {
                    $('#product_section').show();
                }
            });
        });
    </script>
@endpush
