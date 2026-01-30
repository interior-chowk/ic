@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Product List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
    <style>
        .modal-content {
            width: 350px;
            height: 386px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                <img src="{{ asset('/public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
                @if ($type == 'in_house')
                    {{ \App\CPU\translate('In-House_Product_List') }}
                @elseif($type == 'seller')
                    {{ \App\CPU\translate('Seller_Product_List') }}
                @endif
                <span class="badge badge-soft-dark radius-50 fz-14 ml-1">{{ $pro->total() }}</span>
            </h2>
        </div>
        <!-- End Page Title -->

        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-lg-5">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{ \App\CPU\translate('Search Product Name or SKU or HSN') }}"
                                            aria-label="Search orders" value="{{ $search }}" required>
                                        <input type="hidden" value="{{ $request_status }}" name="status">
                                        <button type="submit"
                                            class="btn btn--primary">{{ \App\CPU\translate('search') }}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                            @if (isset($request_status) && $request_status == 1)
                                <div class="pull-right">
                                    <button class="btn  btn-primary" data-toggle="modal" data-target="#productFilterModal">
                                        <span class="text">{{ \App\CPU\translate('Filter') }} <i
                                                class="tio-filter"></i></span>
                                    </button>
                                </div>
                            @elseif(Request::is('admin/product/list/in_house'))
                                <div class="pull-right">
                                    <button class="btn  btn-primary" data-toggle="modal" data-target="#productFilterModal">
                                        <span class="text">{{ \App\CPU\translate('Filter') }} <i
                                                class="tio-filter"></i></span>
                                    </button>
                                </div>
                            @else
                            @endif
                            <div class="col-lg-6 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                                @if ($request_status == 1 || $request_status == 0)
                                    <div style="display:block;">
                                        <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                                            <i class="tio-download-to"></i>
                                            {{ \App\CPU\translate('Export') }}
                                            <i class="tio-chevron-down"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a class="dropdown-item"
                                                    href="{{ route('admin.product.export-excel') }}">{{ \App\CPU\translate('Excel') }}</a>
                                            </li>
                                            <div class="dropdown-divider"></div>
                                        </ul>
                                    </div>
                                    <a href="{{ route('admin.product.stock-limit-list', ['in_house']) }}"
                                        class="btn btn-info" style="display:none;">
                                        <span class="text">{{ \App\CPU\translate('Limited Sotcks') }}</span>
                                    </a>
                                @endif
                                @if (!isset($request_status))
                                    <a href="{{ route('admin.product.add-new') }}" class="btn btn--primary">
                                        <i class="tio-add"></i>
                                        <span class="text">{{ \App\CPU\translate('Add_New_Product') }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable"
                            style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ \App\CPU\translate('Product Id ') }}</th>
                                    <th>{{ \App\CPU\translate('Product Name') }}</th>
                                    <th>{{ \App\CPU\translate('Cart Count') }}</th>
                                    <th>{{ \App\CPU\translate('Wishlist Count') }}</th>
                                    <th>{{ \App\CPU\translate('Views Count') }}</th>
                                    <th class="text-center">{{ \App\CPU\translate('shop_name') }}</th>
                                    <th class="text-center">{{ \App\CPU\translate('Show_as_featured') }}</th>
                                    <th class="text-center">{{ \App\CPU\translate('Active') }}
                                        {{ \App\CPU\translate('status') }}</th>
                                    <th class="text-center">QC Failed Reason (if any)</th>
                                    <th class="text-center">{{ \App\CPU\translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pro as $k => $p)
                                    @php
                                        $new_sku_product = DB::table('sku_product_new')
                                            ->where('product_id', $p->id)
                                            ->whereNotNull('thumbnail_image')
                                            ->first();
                                    @endphp
                                    <tr>
                                        <th scope="row">PR{{ $p->id }}</th>
                                        <td>
                                            <a href="{{ route('admin.product.view', [$p['id']]) }}"
                                                class="media align-items-center gap-2">
                                                @if ($new_sku_product)
                                                    <img 
                                                    {{-- src="{{ asset('storage/app/public/images/' . $new_sku_product->thumbnail_image) }}" --}}
                                                    src="{{ rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . ($new_sku_product->thumbnail_image ?? 'default.jpg') }}"
                                                        alt="Product Image" width="25" height="25">
                                                @else
                                                    <img src="{{ asset('/public/asset/img/logo.png') }}"
                                                        alt="Product Image" width="25" height="25">
                                                @endif
                                                <span class="media-body title-color hover-c1">
                                                    {{ \Illuminate\Support\Str::limit($p['name'], 20) }}
                                                </span>
                                            </a>
                                        </td>
                                        @php
                                            $cartcount = DB::table('new_cart')
                                                ->where('new_cart.product_id', $p['id'])
                                                ->sum('new_cart.quantity');

                                            $wishlistCount = DB::table('wishlists')
                                                ->join(
                                                    'sku_product_new',
                                                    'sku_product_new.product_id',
                                                    '=',
                                                    'wishlists.product_id',
                                                )
                                                ->where('sku_product_new.product_id', $p['id'])
                                                ->distinct('wishlists.id')
                                                ->count('wishlists.id');

                                            $recentlyViewsCount = DB::table('recently_view')
                                                ->join(
                                                    'sku_product_new',
                                                    'sku_product_new.product_id',
                                                    '=',
                                                    'recently_view.product_id',
                                                )
                                                ->where('sku_product_new.product_id', $p['id'])
                                                ->count('recently_view.counts');
                                        @endphp
                                        <td>{{ $cartcount }}</td>
                                        <td>{{ $wishlistCount }}</td>
                                        <td>{{ $recentlyViewsCount }}</td>

                                        <td class="text-center">
                                            @php($seller = App\Model\Seller::where('id', $p['user_id'])->first())
                                            @php($shop = App\Model\Shop::where('seller_id', $p['user_id'])->first())
                                            @if ($seller)
                                                <a href="{{ route('admin.sellers.view', $seller->id) }}"
                                                    class="{{ $seller->status == 'approved' ? 'badge badge-success' : 'badge badge-danger' }}">
                                                    {{ $shop['name'] }}
                                                </a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <label class="mx-auto switcher">
                                                <input class="switcher_input" type="checkbox"
                                                    onclick="featured_status('{{ $p['id'] }}')"
                                                    {{ $p->featured == 1 ? 'checked' : '' }}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="mx-auto switcher">
                                                <input type="checkbox" class="status switcher_input"
                                                    id="{{ $p['id'] }}" {{ $p->status == 1 ? 'checked' : '' }}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                onclick="openQcModal({{ $p->id }}, '{{ $p->qc_failed_reason ?? '' }}')">
                                                {{ $p->qc_failed_reason ? 'Edit Reason' : 'Add Reason' }}
                                            </button>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                @if (isset($request_status) && $request_status == 0)
                                                    <a class="btn btn-primary btn-sm" data-toggle="modal"
                                                        onclick="services_tags({{ $p['id'] }})"
                                                        data-target=".bd-example-modal-lg">Tag</a>
                                                @endif
                                                <a class="btn btn-outline-info btn-sm square-btn" title="View"
                                                    href="{{ route('admin.product.view', [$p['id']]) }}">
                                                    <i class="tio-invisible"></i>
                                                </a>
                                                <a class="btn btn-outline--primary btn-sm square-btn"
                                                    title="{{ \App\CPU\translate('Edit') }}"
                                                    href="{{ route('admin.product.edit', [$p['id']]) }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm square-btn" href="javascript:"
                                                    title="{{ \App\CPU\translate('Delete') }}"
                                                    onclick="form_alert('product-{{ $p['id'] }}','Want to delete this item ?')">
                                                    <i class="tio-delete"></i>
                                                </a>
                                            </div>
                                            <form action="{{ route('admin.product.delete', [$p['id']]) }}" method="post"
                                                id="product-{{ $p['id'] }}">
                                                @csrf
                                                @method('delete')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {{ $pro->links() }}
                        </div>
                    </div>

                    @if (count($pro) == 0)
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

    <!-- QC Reason Modal -->
    <div class="modal fade" id="qcReasonModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="qcReasonForm">
                @csrf
                <input type="hidden" name="id" id="qc_product_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">QC Failed Reason</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <textarea name="reason" id="qc_reason_text" class="form-control" rows="4"
                            placeholder="Enter QC Failed Reason"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Reason</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Services Tags Modal -->
    <div class="modal fade bd-example-modal-lg" id="serviceTagModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('admin.product.services-tags-update') }}" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ \App\CPU\translate('services_tags') }} </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body ">
                        @csrf
                        <input type="hidden" id="prd_id" name="id">
                        <div class="">
                            <select class="form-control services-tags" id="service_tag_id" name="service_type[]"
                                multiple>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close_btn" data-dismiss="modal">Close</button>
                        <button type="button" onclick="saveServicesTags()" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#service_tag_id').select2({
                placeholder: "Select an Option",
                allowClear: true
            });
        });

        function openQcModal(id, reason) {
            $('#qc_product_id').val(id);
            $('#qc_reason_text').val(reason);
            $('#qcReasonModal').modal('show');
        }

        // Submit QC Reason
        $('#qcReasonForm').on('submit', function(e) {
            e.preventDefault();

            let id = $('#qc_product_id').val();
            let reason = $('#qc_reason_text').val();

            $.ajax({
                url: "{{ route('admin.product.qc-reason-update-ajax') }}",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                    reason: reason
                },
                success: function(res) {
                    if (res.success) {
                        toastr.success('QC reason saved');
                        $('#qcReasonModal').modal('hide');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        toastr.error('Save failed');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    toastr.error('Server error: ' + xhr.status);
                }
            });

        });
    </script>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{ asset('public/assets/back-end') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('public/assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        function services_tags(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.product.services-tags') }}",
                method: 'POST',
                data: {
                    id: id
                },
                success: function(response) {
                    if (response.html) {
                        $('.services-tags').html(response.html);
                        $('#prd_id').val(id);
                    }
                }
            });
        }

        // Call the dataTables jQuery plugin
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        $(document).on('change', '.status', function() {
            var id = $(this).attr("id");
            var status = $(this).prop("checked") ? 1 : 0;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.product.status-update') }}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function(data) {
                    if (data.success == true) {
                        toastr.success('{{ \App\CPU\translate('Status updated successfully') }}');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(
                            '{{ \App\CPU\translate('Status updated failed. Seller is not approve , please approve ') }}'
                        );
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                }
            });
        });

        function featured_status(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.product.featured-status') }}",
                method: 'POST',
                data: {
                    id: id
                },
                success: function() {
                    toastr.success('{{ \App\CPU\translate('Featured status updated successfully') }}');
                }
            });
        }

        function saveServicesTags() {
            let id = $('#prd_id').val();
            let service_type = $('#service_tag_id').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.product.services-tags-update') }}",
                method: 'POST',
                data: {
                    id: id,
                    service_type: service_type
                },
                success: function() {
                    $('.close_btn').click();
                    toastr.success('{{ \App\CPU\translate('Services Tags updated successfully !') }}');
                }
            });
        }
    </script>
@endpush
