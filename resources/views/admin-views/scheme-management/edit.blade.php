@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Scheme Management'))

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
                {{\App\CPU\translate('Scheme')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
         <div class="row">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                             <form action="{{route('admin.Scheme_management.update',[$sm['id']])}}" method="post">
                            @csrf

                             <div class="row">
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="scheme_name" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('scheme_name')}}</label>
                                    <input type="text" name="scheme_name" class="form-control" value="{{ $sm->scheme_name }}" id="scheme_name"
                                           placeholder="{{\App\CPU\translate('Scheme Name')}}" required>
                                </div>
                                
                                  <div class="col-md-6 col-lg-4 form-group">
                                    <label for="plan_description" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('scheme_type')}}</label>
                                    <select class="form-control" id="scheme_type_id" name="scheme_type" required>
                                        <option selected disabled>{{\App\CPU\translate('select_scheme_type')}}</option>
                                        
                                        <option value="1" {{ (1 == $sm->scheme_type) ? 'selected' : '' }} >{{\App\CPU\translate('brand')}}</option>
                                        <option value="2" {{ (2 == $sm->scheme_type) ? 'selected' : '' }} >{{\App\CPU\translate('seller_/_store')}}</option>
                                        <option value="3" {{ (3 == $sm->scheme_type) ? 'selected' : '' }}>{{\App\CPU\translate('Products')}}</option>
                                       
                                    </select>
                                </div>
                                
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="plan_description" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('plans')}}</label>
                                    <select class="form-control" id="plan_id" name="plan_id" required>
                                        <option selected disabled>{{\App\CPU\translate('select_plan')}}</option>
                                        @foreach($plans as $id => $plan_name) 
                                        <option value="{{ $id }}"  {{ ($id == $sm->plan_id) ? 'selected' : '' }}>{{ $plan_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="puchase_target_amount" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('puchase_target_amount')}}</label>
                                    <input type="number" min="0" step="0.01" name="puchase_target_amount" class="form-control" value="{{ $sm->puchase_target_amount }}" id="puchase_target_amount"
                                           placeholder="{{\App\CPU\translate('puchase_target_amount')}}" required>
                                </div>
                                 <div class="col-md-6 col-lg-4 form-group">
                                    <label for="duration" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('duration')}}</label>
                                    <input type="number" min="0" step="0.01" name="duration" class="form-control" value="{{ $sm->duration }}" id="duration"
                                           placeholder="{{\App\CPU\translate('duration in days')}}" required>
                                </div>
                               
                                 <div class="col-md-6 col-lg-4 form-group">
                                    <label for="rewards" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('Rewards')}}</label>
                                    <input type="text"  name="rewards" class="form-control" value="{{ $sm->rewards }}" id="rewards"
                                           placeholder="{{\App\CPU\translate('Rewards ')}}" required>
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label for="isActive" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('isActive')}}</label>
                                    <select class="form-control" id="isActive" name="isActive" required>
                                        <option value="1"  {{ (1 == $sm->isActive) ? 'selected' : '' }} >{{\App\CPU\translate('Yes')}}</option>
                                        <option value="0" {{ (0 == $sm->isActive) ? 'selected' : '' }} >{{\App\CPU\translate('No')}}</option>
                                    </select>
                                </div>
                                @if($sm->scheme_type == 1)
                                 <div class="col-md-6 col-lg-12 form-group scheme-section" id="brand_section" >
                                    
                                    <label for="plan_description" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('brands')}}</label>
                                    <select class="form-control" id="brand_id" name="brand_ids[]" multiple="multiple" >
                                        @foreach($brands as $id => $name) 
                                        <option value="{{ $id }}" {{ in_array($id, json_decode($sm->brand_ids, true)) ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @elseif($sm->scheme_type == 2)
                                <div class="col-md-6 col-lg-12 form-group scheme-section" id="seller_section" >
                                    <label for="plan_description" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('sellers')}}</label>
                                    <select class="form-control" id="seller_id" name="seller_ids[]" multiple="multiple" >
                                         @foreach($sellers as $id => $f_name)
                                        <option value="{{ $id }}" {{ in_array($id, json_decode($sm->seller_ids, true)) ? 'selected' : '' }}>{{ $f_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @elseif($sm->scheme_type == 3)
                                 <div class="col-md-6 col-lg-12 form-group scheme-section" id="product_section" >
                                    <label for="plan_description" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('products')}}</label>
                                    <select class="form-control" id="products_id" name="products_id[]" multiple="multiple" required>

                                        @foreach($products as $id => $name) 
                                        <option value="{{ $id }}" {{ in_array($id, json_decode($sm->products_id, true)) ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                              
                                @endif
                                
                                  
                                
                                   <div class="col-md-6 col-lg-12 form-group scheme-section" id="brand_section" style="display: none;">
                                    
                                    <label for="plan_description" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('brands')}}</label>
                                    <select class="form-control" id="brand_id" name="brand_ids[]" multiple="multiple" >
                                        @foreach($brands as $id => $name) 
                                        <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-12 form-group scheme-section" id="seller_section" style="display: none;">
                                    <label for="plan_description" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('sellers')}}</label>
                                    <select class="form-control" id="seller_id" name="seller_ids[]" multiple="multiple" >
                                         @foreach($sellers as $id => $f_name)
                                        <option value="{{ $id }}">{{ $f_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                 <div class="col-md-6 col-lg-12 form-group scheme-section" id="product_section" style="display: none;">
                                    <label for="plan_description" class="title-color font-weight-medium d-flex">{{\App\CPU\translate('products')}}</label>
                                    <select class="form-control" id="products_id" name="products_id[]" multiple="multiple" >
                                        @foreach($products as $id => $name) 
                                        <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                               <div class="col-md-6 col-lg-12 form-group">
                                    <label for="Description" class="title-color font-weight-medium d-flex">{{ \App\CPU\translate('Description') }}</label>
                                    <textarea name="Description" class="form-control" id="Description" placeholder="{{ \App\CPU\translate('Description') }}" required>{{ $sm->Description }}</textarea>
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
