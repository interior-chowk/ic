@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Home Products'))

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
                {{ \App\CPU\translate('home_products_setup') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.product.home-products-store') }}" method="post">
                            @csrf

                            <div class="row">

                                <div class="col-md-6 col-lg-4 form-group coupon_by first_order">
                                    <label for="name"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('Home_page_section') }}</label>
                                    <select class="js-example-basic-multiple js-states js-example-responsive form-control"
                                        name="section_type" id="section_type_">
                                        <option disabled selected>{{ \App\CPU\translate('select_section') }}</option>
                                        <option value="best_seller">{{ \App\CPU\translate('best_seller') }}</option>
                                        <option value="feature">{{ \App\CPU\translate('feature') }}</option>
                                        <option value="discounted">{{ \App\CPU\translate('discounted') }}</option>
                                        <option value="new_arrival">{{ \App\CPU\translate('new_arrival') }}</option>
                                        <option value="top_products">{{ \App\CPU\translate('top_products') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group coupon_type first_order">
                                    <label for="name"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('products') }}</label>
                                    <select class="js-example-basic-multiple js-states js-example-theme-single form-control"
                                        id="product_id_" name="product_id">

                                        <option disabled selected>{{ \App\CPU\translate('Select_product') }}</option>

                                    </select>
                                </div>

                                <div class="col-md-6 col-lg-4 form-group coupon_type first_order">
                                    <label for="name"
                                        class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('priority') }}</label>
                                    <input type="text" class="form-control " id="priority_" name="priority"
                                        placeholder="{{ \App\CPU\translate('priority') }}">
                                </div>


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
                                    {{ \App\CPU\translate('home_products_list') }}
                                    <span
                                        class="badge badge-soft-dark radius-50 fz-12 ml-1">{{ $home_products->total() }}</span>
                                </h5>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table {{ Session::get('direction') === 'rtl' ? 'text-right' : 'text-left' }}">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ \App\CPU\translate('SL') }}</th>
                                    <th>{{ \App\CPU\translate('section_Type') }}</th>
                                    <th>{{ \App\CPU\translate('product_name') }}</th>
                                    <th>{{ \App\CPU\translate('priority') }}</th>
                                    <th class="text-center">{{ \App\CPU\translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($home_products as $k => $c)
                                    <tr>
                                        <td>{{ $home_products->firstItem() + $k }}</td>
                                        <td class="text-capitalize">
                                            <strong>{{ \App\CPU\translate($c['section_type']) }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                {{ \Illuminate\Support\Str::limit($c['product']['name'] ?? 'N/A', 60, '...') }}
                                            </div>
                                        </td>
                                        <td>
                                            {{ $c['priority'] }}
                                        </td>
                                        <td>
                                            <div class="d-flex gap-10 justify-content-center">

                                                <a class="btn btn-outline-danger btn-sm delete" href="javascript:"
                                                    onclick="form_alert('coupon-{{ $c['id'] }}','Want to delete this ?')"
                                                    title="{{ \App\CPU\translate('delete') }}">
                                                    <i class="tio-delete"></i>
                                                </a>
                                                <form
                                                    action="{{ route('admin.product.home-products-delete', [$c['id']]) }}"
                                                    method="post" id="coupon-{{ $c['id'] }}">
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
                            {{ $home_products->links() }}
                        </div>
                    </div>

                    @if (count($home_products) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ asset('public/assets/back-end') }}/svg/illustrations/sorry.svg"
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
    <script></script>

    <script src="{{ asset('public/assets/back-end') }}/js/select2.min.js"></script>
    <script>
        $(".js-example-responsive").select2({
            width: 'resolve'
        });

        // Initialize Select2 with AJAX support
        $(".js-example-theme-single").select2({
            width: 'resolve',
            ajax: {
                url: '{{ route('admin.product.home-products-search') }}',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {

                    return {
                        results: data.map(function(product) {
                            return {
                                id: product.id,
                                text: product.name
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: "{{ \App\CPU\translate('Select_product') }}"
        });
    </script>

    <!-- Page level plugins -->
    <script src="{{ asset('public/assets/back-end') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('public/assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('public/assets/back-end') }}/js/demo/datatables-demo.js"></script>
@endpush
