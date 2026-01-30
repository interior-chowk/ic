@extends('layouts.back-end.app-seller')

@section('title',\App\CPU\translate('Product List'))

@push('css_or_js')

@endpush




<style>
    <style>
.dropbtn {
  background-color: #3498DB;
  color: white;
  padding: 16px;
  font-size: 16px;
  border: none;
  cursor: pointer;
}

.dropbtn:hover, .dropbtn:focus {
  background-color: #2980B9;
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f1f1f1;
  min-width: 160px;
  overflow: auto;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown a:hover {background-color: #ddd;}

.show {display: block;}
</style>

    </style>

@section('content')
    <div class="content container-fluid">

        <!-- Page Title -->
        <div class="mb-4">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{asset('/public/assets/back-end/img/products.png')}}" alt="">
                {{\App\CPU\translate('Products')}}
                <span class="badge badge-soft-dark radius-50 fz-14 ml-1">{{ $products->total() }}</span>
            </h2>
        </div>
        <!-- End Page Title -->

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-lg-5">
                                <form action="{{ url()->current() }}" method="GET">
                                    <!-- Search -->
                                    <div class="input-group input-group-merge input-group-custom">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{\App\CPU\translate('Search by Product Name or SKU or HSN')}}" aria-label="Search orders" value="{{ $search }}" required>
                                        <button type="submit" class="btn btn--primary">{{\App\CPU\translate('search')}}</button>
                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div>
                            <!-- start filter  -->
                            
                            
                        <button class="btn  btn-primary"
                                data-toggle="modal"
                               data-target="#productFilterModal">
                                 <span class="text">{{\App\CPU\translate('Filter')}} <i class="tio-filter"></i></span>
                       </button>
                       
                            <!-- End  filter  -->
                            
                            <div class="col-lg-6 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                                <div style="display:none;">
                                    <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                                        <i class="tio-download-to"></i>
                                        {{\App\CPU\translate('export')}}
                                        <i class="tio-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a class="dropdown-item" href="{{ route('seller.product.bulk-export') }}">{{\App\CPU\translate('excel')}}</a></li>
                                        <div class="dropdown-divider"></div>
                                    </ul>
                                </div>
                                <a href="{{route('seller.product.stock-limit-list',['in_house', ''])}}" class="btn btn-info">
                                    <i class="tio-add-circle"></i>
                                    <span class="text">{{\App\CPU\translate('Limited_Stocks')}}</span>
                                </a>

                                <div class="dropdown">
  <button onclick="myFunction()" class="dropbtn btn btn--primary"><i class="tio-add"></i>{{\App\CPU\translate('Add new product')}}</button>
  <div id="myDropdown" class="dropdown-content">
    <a href="{{route('seller.product.add-search-new')}}">Single Listing</a>
    <a href="{{route('seller.product.search_bulk-import')}}">Bulk Import</a>
    
  </div>
</div>
<script>
/* When the user clicks on the button, 
toggle between hiding and showing the dropdown content */
function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
</script>
                                <!-- <a href="{{route('seller.product.add-new')}}" class="btn btn--primary">
                                    <i class="tio-add"></i>
                                    <span class="text">{{\App\CPU\translate('Add new product')}}</span>
                                </a> -->
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{\App\CPU\translate('Product Id')}}</th>
                                <th>{{\App\CPU\translate('Product Name')}}</th>
                                <th>{{\App\CPU\translate('Unit')}}</th>
                               <!-- <th>{{\App\CPU\translate('Product Type')}}</th>
                                <th>{{\App\CPU\translate('purchase_price')}}</th>-->
                                <th>{{\App\CPU\translate('selling_price')}}</th>
                                <th>{{\App\CPU\translate('verify_status')}}</th>
                                <th>{{\App\CPU\translate('Active')}} {{\App\CPU\translate('status')}}</th>
                                <th class="text-center __w-5px">{{\App\CPU\translate('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $k=>$p)
                            @php
$new_sku_product = DB::table('sku_product_new')
    ->where('product_id', $p->id)
    ->whereNotNull('thumbnail_image')
    ->first();
@endphp

                                <tr>
                                    <th scope="row">PR{{$p->id}}</th>
                                    <td>
                                        <a href="{{route('seller.product.view',[$p['id']])}}" class="media align-items-center gap-2 w-max-content">
                                            <!-- <img src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$p['thumbnail']}}"
                                                 onerror="this.src='{{asset('/public/asset/img/logo.png')}}'"class="avatar border" alt=""> -->
                                                 @if($new_sku_product)
    <img src="{{ asset('storage/app/public/images/' . $new_sku_product->thumbnail_image) }}" alt="Product Image" width="25px" height="25px">
@else
<img src="{{ asset('/public/asset/img/logo.png') }}" alt="Product Image" width="25px" height="25px">
@endif

                                            <span class="media-body title-color hover-c1">
                                                {{\Illuminate\Support\Str::limit($p['name'],30)}}
                                            </span>
                                        </a>
                                    </td>
                                   <!-- <td>{{ ucfirst($p['product_type']) }}</td>
                                    <td>
                                        {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($p['purchase_price']))}}
                                    </td>-->
                                    <td>
                                        {{ $p['unit'] }}
                                    </td>
                                    <td>
                                        @php
                                        if ($p['discount_type'] == 'percent') {
                                            $selling_price = $p['unit_price'] - ($p['unit_price'] * $p['discount'] / 100);
                                        } else {
                                            $selling_price = $p['unit_price'] - $p['discount'];
                                        }
                                    @endphp

                                        <!-- {{ \App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($selling_price))}} -->
                                        @if($new_sku_product)
                {{ $new_sku_product->listed_price }}
            @else
                No Price Available
            @endif
                                    </td>
                                    <td>
                                        @if($p->request_status == 0)
                                            <label class="badge badge-soft-warning">{{\App\CPU\translate('New Request')}}</label>
                                        @elseif($p->request_status == 1)
                                            <label class="badge badge-soft-success">{{\App\CPU\translate('Approved')}}</label>
                                        @elseif($p->request_status == 2)
                                            <label class="badge badge-soft-danger">{{\App\CPU\translate('Denied')}}</label>
                                        @endif
                                    </td>
                                    <td>
                                        <label class="switcher">
                                            <input type="checkbox" class="status switcher_input"
                                                    id="{{$p['id']}}" {{$p->status == 1?'checked':''}}>
                                            <span class="switcher_control"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-10">
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ \App\CPU\translate('barcode') }}"
                                                href="{{ route('seller.product.barcode', [$p['id']]) }}" style="display:none;">
                                                <i class="tio-barcode"></i>
                                            </a>

                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                title="{{\App\CPU\translate('view')}}"
                                                href="{{route('seller.product.view',[$p['id']])}}">
                                                <i class="tio-invisible"></i>
                                            </a>
                                            <a  class="btn btn-outline-primary btn-sm square-btn"
                                                title="{{\App\CPU\translate('Edit')}}"
                                                href="{{route('seller.product.edit',[$p['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a  class="btn btn-outline-danger btn-sm square-btn" href="javascript:"
                                                title="{{\App\CPU\translate('Delete')}}"
                                                onclick="form_alert('product-{{$p['id']}}','{{\App\CPU\translate("Want to delete this item")}} ?')">
                                                <i class="tio-delete"></i>
                                            </a>
                                        </div>
                                        <form action="{{route('seller.product.delete',[$p['id']])}}"
                                                method="post" id="product-{{$p['id']}}">
                                            @csrf @method('delete')
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
                            {{$products->links()}}
                        </div>
                    </div>

                    @if(count($products)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description">
                            <p class="mb-0">{{\App\CPU\translate('No data to show')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });

        $('.status').on('change', function () {
            var id = $(this).attr("id");
            if ($(this).prop("checked") == true) {
                var status = 1;
            } else if ($(this).prop("checked") == false) {
                var status = 0;
            }
            let t = $(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('seller.product.status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function (data) {
                    if(data.success == true) {
                        toastr.success('{{\App\CPU\translate('Status updated successfully')}}');
                    }
                    else if(data.success == false) {
                        t.removeAttr('checked');
                        toastr.error('{{\App\CPU\translate('Status updated failed. Product must be approved')}}');
                    }
                }
            });
        });
    </script>
@endpush
