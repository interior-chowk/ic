@extends('layouts.back-end.app-seller')

@section('title', \App\CPU\translate('product_add'))

@push('css_or_js')
    <link href="{{ asset('assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
<style>

/* Modal Styles */
/* Modal Styles */
.modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 300px;
  top: 0;

  height: 100%;

  padding-top: 60px;
}

.modal-content {
  background-color: #fefefe;
  margin: 5% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}


body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .state-block, .city-block {
            margin-bottom: 10px;
        }
        .city-list {
            margin-left: 20px;
        }
        .hidden {
            display: none;
        }

</style>

@section('content')

<div class="content container-fluid">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0">
            <img src="http://localhost/6valley/public/assets/back-end/img/all-orders.png" class="mb-1 mr-1" alt="">
            {{ \App\CPU\translate('Add_new_product') }}
        </h2>
    </div>

    @php
        $specifications = DB::table('categories')->where('id', $sub_sub_category_id)->first();
        $specificationArray = $specifications && $specifications->specification
            ? explode(',', $specifications->specification)
            : [];
        $key_feature = DB::table('categories')->where('id', $sub_sub_category_id)->first();
        $key_featureArray = $key_feature && $key_feature->key_features
            ? explode(',', $key_feature->key_features)
            : [];

    $technical_spacification = DB::table('categories')->where('id', $sub_sub_category_id)->first();
        $technical_specificationArray =  $technical_spacification &&  $technical_spacification->technical_specification
            ? explode(',', $technical_spacification->technical_specification)
            : [];

            $other_details = DB::table('categories')->where('id', $sub_sub_category_id)->first();
        $other_detailsArray =  $other_details &&  $other_details->other_details
            ? explode(',', $other_details->other_details)
            : [];

    @endphp
    @php
   $commission = DB::table('sellers')->where('id', auth('seller')->id())->first();
@endphp

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn--primary">
                    <a style="color:#fff" href="{{ route('seller.product.add-search-new') }}">
                        {{ \App\CPU\translate('Back') }}
                    </a>
                </button>
            </div>

            <form class="product-form" action="{{ route('seller.product.add-new') }}" method="post" enctype="multipart/form-data"
                style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};" id="product_form">
                @csrf
                <input type="hidden" name="category_id" value="{{ $category_id }}">
                <input type="hidden" name="sub_category_id" value="{{ $sub_category_id }}">
                <input type="hidden" name="sub_sub_category_id" value="{{ $sub_sub_category_id }}">

                <div class="card">
                    <div class="px-4 pt-3">
                        @php($language = \App\Model\BusinessSetting::where('type', 'pnc_language')->first())
                        @php($language = $language->value ?? null)
                        @php($default_lang = 'en')
                        @php($default_lang = json_decode($language)[0])

                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach (json_decode($language) as $lang)
                                <li class="nav-item text-capitalize">
                                    <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}"
                                        href="#" id="{{ $lang }}-link">
                                        {{ \App\CPU\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h5 class="mb-0">{{ \App\CPU\translate('General_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    @if($brand_setting)
                                    <div class="col-md-4 mb-3">
                                        <label for="name" class="title-color">{{ \App\CPU\translate('Brand') }} </label>
                                        <select class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="brand_id">
                                            <option value="{{ null }}" selected disabled>
                                                ---{{ \App\CPU\translate('Select') }}---</option>
                                            @foreach ($br as $b)
                                                <option value="{{ $b['id'] }}">{{ $b['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                    <div class="col-md-4 form-group">
                                        <label class="title-color">{{ \App\CPU\translate('Return_Days') }}</label>
                                        <input type="number" min="0" step="0.01" placeholder="{{ \App\CPU\translate('Return days') }}"
                                            value="{{ old('Return_days') }}" name="Return_days" id="Return_days" class="form-control">
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label class="title-color">{{ \App\CPU\translate('Replacement_days') }}</label>
                                        <input type="number" min="0" step="0.01" placeholder="{{ \App\CPU\translate('Replacement day') }}"
                                            value="{{ old('Return_days') }}" name="Replacement_days" id="Replacement_days" class="form-control">
                                    </div>
<script>
$(document).ready(function() {
    $('#Return_days').on('input', function() {
        let value = $(this).val();

        if (value && value != 0) {
            $('#Replacement_days').val(value);
            $('#Replacement_days').prop('disabled', true); // disable input
        } else if (value == 0) {
            $('#Replacement_days').val('');
            $('#Replacement_days').prop('disabled', false); // enable if 0
        } else {
            $('#Replacement_days').val('');
            $('#Replacement_days').prop('disabled', false); // enable if empty or invalid
        }
    });
});
</script>






                                    <div class="col-md-4 mb-3">
                                        <label for="code" class="title-color">{{ \App\CPU\translate('product_code_sku') }}
                                            <span class="text-danger">*</span>
                                            <a class="style-one-pro" onclick="document.getElementById('generate_number').value = getRndIntegerAlpha()">
                                                {{ \App\CPU\translate('generate') }} {{ \App\CPU\translate('code') }}
                                            </a>
                                        </label>
                                        <input type="text" id="generate_number" name="code" class="form-control"
                                            value="{{ old('code') }}" placeholder="{{ \App\CPU\translate('code') }}">
                                    </div>

                                    <div class="col-md-4 mb-3 physical_product_show">
                                        <label for="name" class="title-color">{{ \App\CPU\translate('Unit') }}</label>
                                        <select class="js-example-basic-multiple form-control" name="unit">
                                            @foreach (\App\CPU\Helpers::units() as $x)
                                                <option value="{{ $x }}" {{ old('unit') == $x ? 'selected' : '' }}>{{ $x }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- <div class="col-md-4 mb-3">
                                        <label class="title-color">{{ \App\CPU\translate('Tax Included') }}</label>
                                        <label class="badge badge-soft-info">{{ \App\CPU\translate('Percent') }} ( % )</label>
                                        <input type="number" min="0" value="0" step="0.01" placeholder="{{ \App\CPU\translate('Tax') }}"
                                            name="tax" value="{{ old('tax') }}" class="form-control">
                                        <input name="tax_type" value="percent" class="d--none">
                                    </div> -->

                                    <div class="col-md-4 mb-3" id="minimum_order_qty">
                                        <label class="title-color">{{ \App\CPU\translate('minimum_order_quantity') }}</label>
                                        <input type="number" min="1" value="1" step="1" placeholder="{{ \App\CPU\translate('minimum_order_quantity') }}"
                                            name="minimum_order_qty" class="form-control">
                                    </div>
                                    <div class="col-md-4 mb-3">
    <label for="warehouse-select" class="title-color">Warehouse / Pickup Address<span class="ml-2" data-toggle="tooltip" data-placement="top" title="{{\App\CPU\translate('From where the product is to be picked up by our shipping partner.')}}">
                                            <img class="info-img" src="{{asset('/public/assets/back-end/img/info-circle.svg')}}" alt="img">
                                         </span></label>
    <select class="form-control" id="warehouse-select" name="warehouse">
        <option value="" selected disabled>---Select---</option>
        @foreach ($warehouse as $b)
            <option value="{{ $b->id }}">{{ $b->title }}</option>
        @endforeach
    </select>
</div>

                              <div class="col-md-4 form-group">
                                        <label class="title-color">{{ \App\CPU\translate('Hsn_Code') }}</label>
                                        <input type="number" min="0" step="0.01" placeholder="{{ \App\CPU\translate('Hsn_code') }}"
                                            value="" name="HSN_code" class="form-control">
                                    </div>

                                    <div class="col-md-4 physical_product_show" id="shipping_cost_multy1" style="margin-top: 30px;">
                                        <div class="rounded py-2 min-h-40 d-flex justify-content-between gap-3">
                                            <label class="title-color mb-0"> {{ 'Free Delivery ' . strtolower('by') . ' Seller' }}

                                                <span class="ml-2" data-toggle="tooltip" data-placement="top"
                                                    title="{{('Once enabled , The Delivery charges of this product will be borne by seller / you.') }}">
                                                    <img class="info-img" src="{{ asset('/public/assets/back-end/img/info-circle.svg') }}" alt="img">
                                                </span>
                                            </label>
                                            <label class="switcher">
                                                <input class="switcher_input form-control" type="checkbox" name="free_delivery" >
                                                <span class="switcher_control"></span>
                                            </label>
                                        </div>
                                    </div>
                                   
                                    


                                </div>
                            </div>

                            <div class="row align-items-center mb-3 physical_product_show" id="shipping_cost_multy2" >
                                <div class="col-md-4" >
                                    <div class="border rounded px-3 py-2 d-flex align-items-center justify-content-between" height="400px">
                                        <label class="switcher mr-5">
                                            <input class="switcher_input" type="checkbox" name="available_instant_delivery" id="instant_switcher">
                                            <span class="switcher_control"></span>
                                        </label>
                                        <label class="title-color mb-0" style="text-transform: none;">
                                            <span>If this product is heavy, flammable, fragile etc. So Please enable it. After activating it, you will get orders for this product from your city itself and you will have to deliver this product yourself.</span>
                                        </label>
                                    </div>
                                </div>

                               

<!-- <div class="col-md-4 mb-3">
    <label for="input-container" class="title-color">Selected Warehouses</label>
    <div id="input-container" style="display: flex; flex-wrap: wrap; gap: 5px; padding: 5px; border: 1px solid #ccc; border-radius: 5px; min-height: 40px;"></div>
</div> -->

<!-- Hidden input to hold selected warehouse IDs -->
 
<div id="state-selector" class="state-block" style="visibility: hidden">
        <label for="state">Select State:</label>
        <select id="state-dropdown">
            <option value="">--Select State--</option>
        </select>
        <button type="button" onclick="addState()">Add State</button>
    </div>

    <div id="selected-locations" name="selected-locations">
        <!-- Dynamically added states and cities will appear here -->
    </div>

    <div id="selected-cities" class="selected-cities" style="visibility: hidden">
        <h3>Selected Cities:</h3>
        <!-- Display selected cities as tags -->
        <div id="selected-cities-tags" 
             style="border: 1px solid #ccc; padding: 10px; min-height: 50px; display: flex; flex-wrap: wrap; gap: 5px;">
        </div>
    </div>

    <!-- Hidden input to hold selected city values -->
    <input type="hidden" id="city-input" name="cities">

    <script>
        // States and cities data
        const statesAndCities = {
            "Up": ["Noida", "Greater Noida"],
            "Mp": ["Bhopal"]
        };

        // Populate the state dropdown
        const stateDropdown = document.getElementById("state-dropdown");
        for (const state in statesAndCities) {
            const option = document.createElement("option");
            option.value = state;
            option.textContent = state;
            stateDropdown.appendChild(option);
        }

        // Add selected state and its cities to the DOM
        function addState() {
            const state = stateDropdown.value;
            if (!state) {
                alert("Please select a state.");
                return;
            }

            // Check if the state is already added
            if (document.getElementById(`state-${state}`)) {
                alert("State already added.");
                return;
            }

            // Create the state block
            const stateBlock = document.createElement("div");
            stateBlock.id = `state-${state}`;
            stateBlock.className = "state-block";
            stateBlock.innerHTML = `
                <label>
                    <input type="checkbox" onchange="toggleStateCities('${state}')" id="state-checkbox-${state}"> 
                    <strong>${state}</strong>
                </label>
                <button type="button" onclick="viewCities('${state}')">View Cities</button>
                <button type="button" onclick="removeState('${state}')">Remove State</button>
                <div class="city-list hidden" id="city-list-${state}">
                    ${statesAndCities[state]
                        .map(city => `
                            <label>
                                <input type="checkbox" value="${city}" name="${state}-cities" 
                                    class="city-checkbox-${state}" 
                                    onchange="updateSelectedCities('${state}', '${city}', this.checked)"> 
                                    ${city}
                            </label>
                        `).join("<br>")}
                </div>
            `;

            // Append to the selected-locations section
            document.getElementById("selected-locations").appendChild(stateBlock);
        }

        // Show cities when "View Cities" is clicked
        function viewCities(state) {
            const cityList = document.getElementById(`city-list-${state}`);
            cityList.classList.toggle("hidden");
        }

        // Select/Deselect all cities when state checkbox is toggled
        function toggleStateCities(state) {
            const stateCheckbox = document.getElementById(`state-checkbox-${state}`);
            const cityCheckboxes = document.querySelectorAll(`.city-checkbox-${state}`);
            cityCheckboxes.forEach(checkbox => {
                checkbox.checked = stateCheckbox.checked;
                updateSelectedCities(state, checkbox.value, stateCheckbox.checked);
            });
        }

        // Update selected cities tags
        function updateSelectedCities(state, city, isChecked) {
            const selectedCitiesTags = document.getElementById("selected-cities-tags");
            const cityInput = document.getElementById("city-input");

            if (isChecked) {
                // Create a new tag for the selected city
                const cityTag = document.createElement("span");
                cityTag.id = `tag-${city}`;
                cityTag.style = `
                    display: inline-flex;
                    align-items: center;
                    background-color: #f1f1f1;
                    border: 1px solid #ccc;
                    padding: 5px 10px;
                    border-radius: 15px;
                `;
                cityTag.innerHTML = `
                    ${city} 
                    <button style="background: none; border: none; margin-left: 5px; color: red; cursor: pointer;" 
                            onclick="removeCity('${state}', '${city}')">&times;</button>
                `;
                selectedCitiesTags.appendChild(cityTag);
            } else {
                // Remove the tag if city is unchecked
                const cityTag = document.getElementById(`tag-${city}`);
                if (cityTag) cityTag.remove();
            }

            // Update the hidden input field with selected city values
            const selectedCities = Array.from(selectedCitiesTags.children).map(tag => tag.textContent.trim().slice(0, -1)); // Remove cut symbol
            cityInput.value = selectedCities.join(",");
        }

        // Remove a city directly via the "cut" button
        function removeCity(state, city) {
            // Uncheck the city checkbox
            const cityCheckbox = document.querySelector(`.city-checkbox-${state}[value="${city}"]`);
            if (cityCheckbox) cityCheckbox.checked = false;

            // Remove the city tag
            const cityTag = document.getElementById(`tag-${city}`);
            if (cityTag) cityTag.remove();

            // Update the hidden input field
            const cityInput = document.getElementById("city-input");
            const selectedCitiesTags = document.getElementById("selected-cities-tags");
            const selectedCities = Array.from(selectedCitiesTags.children).map(tag => tag.textContent.trim().slice(0, -1));
            cityInput.value = selectedCities.join(",");
        }

        // Remove a state and its cities
        function removeState(state) {
            const stateBlock = document.getElementById(`state-${state}`);
            if (stateBlock) {
                stateBlock.remove();
            }

            // Remove all cities of the state from selected list
            const cityCheckboxes = document.querySelectorAll(`.city-checkbox-${state}`);
            cityCheckboxes.forEach(checkbox => {
                updateSelectedCities(state, checkbox.value, false);
            });
        }
    </script>

    <style>
        .hidden {
            display: none;
        }
    </style>

                                    


<script>
  $(document).ready(function() {
    $('#instant_switcher').change(function() {
      if ($(this).is(':checked')) {
        $('.nested-select-container, .selected-container, .state-block, .selected-cities').css('visibility', 'visible');
      } else {
        $('.nested-select-container, .selected-container, .state-block, .selected-cities').css('visibility', 'hidden');
      }
    });
  });
</script>


<!-- @if($commission->commission_fee == 3)
<div class="col-md-4 form-group">
                                        <label class="title-color">{{ \App\CPU\translate('Transfer Price') }}</label>
                                        <input type="number" placeholder="{{ \App\CPU\translate('transfer price') }}"
                                            value="" name="Return_days" class="form-control">
                                    </div>
 @endif -->
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card mt-2 rest-part">
    <div class="card-body __coba-aspect">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <label class="title-color mb-0">{{ \App\CPU\translate('Youtube video link') }}</label>
                    <span class="badge badge-soft-info">(
                        {{ \App\CPU\translate('optional') }},
                        {{ \App\CPU\translate('please_provide_embed_link_not_direct_link') }}
                    )</span>
                </div>

                <input type="text" name="video_link"
                    placeholder="EX: https://www.youtube.com/embed/5R06LRdUCSE"
                    class="form-control" />
            </div>
        </div>
    </div>
</div>



<div class="card mt-2 rest-part">
                        <div class="card-body __coba-aspect">
                            <div class="row">
                                <div class="col-md-12 mb-4">

                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label for="name" class="title-color mb-0"><h2>{{ \App\CPU\translate('Product Details') }}</h2></label>
                                        <span class="text-info"></span>

                                        @if(!empty($specificationArray))
    <h6>Specifications</h6>
    <table border="0" style="width: 70%; border-collapse: collapse;">
        <tbody>
            @foreach($specificationArray as $index => $spec)
                <tr>
                    <td style="width: 20%;">
                        <input type="text" name="specifications[]" value="{{ $spec }}" class="form-control" readonly style="background-color: #F9F9FA; margin: 10px; text-align: center;"/>
                    </td>
                    <td style="width: 40%;">
                        <input type="text" name="specification_values[{{ $index }}]" value="" class="form-control" style="margin: 10px;" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>No specifications found.</p>
@endif


@if(!empty($key_featureArray))
    <h6 style="margin-top: 10px;">Key features</h6>
    <table border="0" style="width: 70%; border-collapse: collapse;">
        <tbody>
            @foreach($key_featureArray as $index => $feature)
                <tr>
                    <td style="width: 20%;">
                        <input type="text" name="features[]" value="{{ $feature }}" class="form-control" readonly style="background-color: #F9F9FA; margin: 10; text-align: center;"/>
                    </td>
                    <td style="width: 40%;">
                        <input type="text" name="features_values[{{ $index }}]" value="" class="form-control" style="margin: 10;"/>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>No key features found.</p>
@endif




@if(!empty($technical_specificationArray))
    <h6 style="margin-top: 10px;">Technical Specification</h6>
    <table class="table" style="width: 70%; border-collapse: collapse;">
        <tbody>
            @foreach($technical_specificationArray as $index => $technical)
                <tr>
                    <td style="width: 20%;">
                        <input type="text" name="technical_specification[]" 
                               value="{{ $technical }}" 
                               class="form-control" 
                               readonly 
                               style="background-color: #F9F9FA; margin: 10px; text-align: center;"/>
                    </td>
                    <td style="width: 40%;">
                        <input type="text" name="technical_specification_values[{{ $index }}]" 
                               value="" 
                               class="form-control" 
                               style="margin: 10px;"/>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>No technical specification found.</p>
@endif



@if(!empty($other_detailsArray))
    <h6 style="margin-top: 10px;"> Other Details</h6>
    <table border="0" style="width: 70%; border-collapse: collapse;">
        <tbody>
            @foreach($other_detailsArray as $index => $other)
                <tr>
                    <td style="width: 20%;">
                        <input type="text" name="other_details[]" 
                               value="{{ $other }}" 
                               class="form-control" 
                               readonly 
                               style="background-color: #F9F9FA; margin: 10px; text-align: center;"/>
                    </td>
                    <td style="width: 40%;">
                        <input type="text" name="other_details_values[{{ $index }}]" 
                               value="" 
                               class="form-control" 
                               style="margin: 10px;"/>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>No other details found.</p>
@endif



                                    </div>

                                    <!-- <div class="row g-2" id="thumbnail"></div> -->
                                </div>




                                <div class="col-md-8 mb-3">

                                </div>
                                <div class="mb-2">
                                        <span>Choose a PDF file:</span><br>
                                        <!-- <span class="text-info">* ( {{ \App\CPU\translate('ratio 1:1') }} )</span> -->
                                        <input type="file" name="pdf" id="pdf" accept="image" />

                                    </div>

                               
                                </div>
                            </div>
                        </div>
                    </div>
                    


                    <div class="card mt-2 mb-2 rest-part">
                    <div class="card-body">
                            @foreach (json_decode($language) as $lang)
                                <div class="{{ $lang != $default_lang ? 'd-none' : '' }} lang_form"
                                    id="{{ $lang }}-form">
                                    <div class="form-group">
                                        <label class="title-color"
                                            for="{{ $lang }}_name">{{ \App\CPU\translate('Product title') }}
                                            ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" {{ $lang == $default_lang ? 'required' : '' }} name="name[]"
                                            id="{{ $lang }}_name" class="form-control" placeholder="{{ \App\CPU\translate('product_title') }}"
                                            >
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                                    <div class="form-group pt-4">
                                        <label class="title-color"
                                            for="{{ $lang }}_description">{{ \App\CPU\translate('description') }}
                                            ({{ strtoupper($lang) }}) <span class="ml-2" data-toggle="tooltip" data-placement="top" title="{{\App\CPU\translate('description contains about product detail , quality, features, specifications, about manufacturer and warranty')}}">
                                            <img class="info-img" src="{{asset('/public/assets/back-end/img/info-circle.svg')}}" alt="img">
                                         </span></label>
                                        <textarea name="description[]" class="editor textarea" cols="30" rows="10">{{ old('details') }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="card-header">
                            <h5 class="card-title">
                                <span>{{ \App\CPU\translate('tags') }}</span>
                            </h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="title-color">{{ \App\CPU\translate('search_tags') }}  <span class="ml-2" data-toggle="tooltip" data-placement="top" title="{{\App\CPU\translate('Create product tags and keywords for customers so that when they search for relevant products, your product will appear on app .')}}">
                                            <img class="info-img" src="{{asset('/public/assets/back-end/img/info-circle.svg')}}" alt="img">
                                         </span></label>
                                        <input type="text" class="form-control" name="tags" data-role="tagsinput">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>         
                    <div class="card mt-2 rest-part physical_product_show">
                        <div class="card-header">
                            <h5 class="mb-0">{{ \App\CPU\translate('Variations') }} <span class="ml-2" data-toggle="tooltip" data-placement="top" title="{{\App\CPU\translate('Create product variations like colours, types, sizes etc.')}}">
                                            <img class="info-img" src="{{asset('/public/assets/back-end/img/info-circle.svg')}}" alt="img">
                                         </span></h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row align-items-end">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex gap-2 mb-1">
                                            <label for="colors" class="title-color">
                                                {{ \App\CPU\translate('Colors') }} :
                                            </label>
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher_input" id="color_switcher" value="{{ old('colors_active') }}"
                                                       name="colors_active" required>
                                                <span class="switcher_control"></span>
                                            </label>
                                        </div>
                                        <select class="js-example-basic-multiple js-states js-example-responsive form-control color-var-select"
                                            name="colors[]" multiple="multiple" id="colors-selector" disabled>
                                            @foreach (\App\Model\Color::orderBy('name', 'asc')->get() as $key => $color)
                                                <option value="{{ $color->code }}">
                                                    {{ $color['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="attributes" class="title-color">
                                            {{ \App\CPU\translate('Attributes') }} :
                                        </label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="choice_attributes[]" id="choice_attributes" multiple="multiple">
                                            @foreach (\App\Model\Attribute::orderBy('name', 'asc')->get() as $key => $a)
                                                <option value="{{ $a['id'] }}">
                                                    {{ $a['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="customer_choice_options" id="customer_choice_options">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h5 class="mb-0">{{ \App\CPU\translate('Product_price_&_stock') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row align-items-end">
                                    <div class="col-md-4 mb-3" id="c1">
                                        <label class="title-color">{{ \App\CPU\translate('MRP') }} </label>
                                        <input type="number" min="0" step="0.01"
                                            placeholder="MRP" name="unit_price" id="unit_price_"
                                            value="{{ old('unit_price') }}" class="form-control">
                                    </div>

                                    <div class="col-md-4 mb-3" id="c2">
                                        <label class="title-color">{{ \App\CPU\translate('discount_type') }}</label>
                                        <select class="form-control js-select2-custom" name="discount_type" id="discount_type_">
                                            <option value="flat">{{ \App\CPU\translate('Flat') }}</option>
                                            <option value="percent">{{ \App\CPU\translate('Percent') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3" id="c3">
                                        <label class="title-color">{{ \App\CPU\translate('Discount') }}</label>
                                        <input type="number" min="0" value="0" step="0.01"
                                            placeholder="{{ \App\CPU\translate('Discount') }}" name="discount"
                                            value="{{ old('discount') }}" id="discount_" class="form-control">
                                    </div>
                                    <div class="col-md-4 mb-3 physical_product_show" id="c4">
                                        <label class="title-color">{{ \App\CPU\translate('total') }}
                                            {{ \App\CPU\translate('Quantity') }}</label>
                                        <input type="number" min="0" value="0" step="1"
                                               placeholder="{{ \App\CPU\translate('Quantity') }}" name="current_stock"
                                               value="{{ old('current_stock') }}" class="form-control">
                                               
                                    </div>
                                    <div class="col-md-4 mb-3 physical_product_show" id="c5">
                                        <strong>Selling / Listed price after discount ₹<span id="selling_price_">0.00</strong></h1>
                                           
                    </div>


                                <div class="sku_combination mb-3" id="sku_combination"></div>
                            </div>
                        </div>
                    </div>


                  
                        <div class="col-12">

<div class="d-flex justify-content-end mt-3">
<button type="submit" class="btn btn--primary">{{ \App\CPU\translate('Submit') }}</button>
</div>
</div>
                    </div>

                   


                  
                </form>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

        $(document).ready(function() {
            function calculateSellingPrice() {
                var discountType = $('#discount_type_').val();
                var unit_price = parseFloat($('#unit_price_').val());
                var discount = parseFloat($('#discount_').val());

                if (isNaN(unit_price) || isNaN(discount)) {
                    $('#selling_price_').text('0.00');
                    return;
                }

                var selling_price;
                if (discountType === 'percent') {
                    selling_price = unit_price - (unit_price * discount / 100);
                } else if (discountType === 'flat') {
                    selling_price = unit_price - discount;
                }

                $('#selling_price_').text(selling_price.toFixed(2));
            }

            // Bind the change event to the discount type select element
            $('#discount_type_').change(function() {
                calculateSellingPrice();
            });

            // Bind the keyup event to the discount input element
            $('#discount_').keyup(function() {
                calculateSellingPrice();
            });

            // Bind the keyup event to the unit price input element
            $('#unit_price_').keyup(function() {
                calculateSellingPrice();
            });
        });
    </script>

@push('script_2')
    <script src="{{ asset('assets/back-end') }}/js/tags-input.min.js"></script>
    <script src="{{ asset('assets/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/back-end/js/spartan-multi-image-picker.js') }}"></script>




    <script>
    let uploadedFiles = [];
        $(function() {
            $('#color_switcher').click(function(){
                var checkBoxes = $("#color_switcher");
                if ($('#color_switcher').prop('checked')) {

                    $('#c1').hide();
                    $('#c2').hide();
                    $('#c3').hide();
                    $('#c4').hide();
                    $('#c5').hide();
                    $('#color_wise_image').show();
                } else {
                    $('#c1').show();
                    $('#c2').show();
                    $('#c3').show();
                    $('#c4').show();
                    $('#c5').show();
                    $('#color_wise_image').hide();
                }
            });


            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: 10,
                // rowHeight: '220px',
                groupClassName: 'col-6 col-lg-4 col-xl-3',
                maxFileSize: 5 * 1024 * 1024,
                placeholderImage: {
                    image: '{{ asset('assets/back-end/img/400x400/img2.jpg') }}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {
                   uploadedFiles.push(file);
                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {
                    uploadedFiles.splice(index, 1);
                },
                onExtensionErr: function(index, file) {
                    toastr.error(
                        '{{ \App\CPU\translate('Please only input png or jpg type file') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                },
                onSizeErr: function(index, file) {
                    toastr.error('{{ \App\CPU\translate('File size too big') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

            $("#thumbnail").spartanMultiImagePicker({
                fieldName: 'image',
                maxCount: 1,
                rowHeight: 'auto',
                groupClassName: 'col-6 col-md-12 col-lg-8 col-xl-6',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{ asset('assets/back-end/img/400x400/img2.jpg') }}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function(index, file) {
                    toastr.error(
                        '{{ \App\CPU\translate('Please only input png or jpg type file') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                },
                onSizeErr: function(index, file) {
                    toastr.error('{{ \App\CPU\translate('File size too big') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

            $("#meta_img").spartanMultiImagePicker({
                fieldName: 'meta_image',
                maxCount: 1,
                // rowHeight: '220px',
                groupClassName: '',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{ asset('assets/back-end/img/400x400/img2.jpg') }}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function(index, file) {
                    toastr.error(
                        '{{ \App\CPU\translate('Please only input png or jpg type file') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                },
                onSizeErr: function(index, file) {
                    toastr.error('{{ \App\CPU\translate('File size too big') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });

        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>

    <script>
        function getRequest(route, id, type) {
            $.get({
                url: route,
                dataType: 'json',
                success: function(data) {
                    if (type == 'select') {
                        $('#' + id).empty().append(data.select_tag);
                    }
                },
            });
        }

        $('input[name="colors_active"]').on('change', function() {
            if (!$('input[name="colors_active"]').is(':checked')) {
                $('#colors-selector').prop('disabled', true);
            } else {
                $('#colors-selector').prop('disabled', false);
            }
        });

       $('#choice_attributes').on('change', function() {
        // Preserve existing options
        let existingOptions = {};
        $('#customer_choice_options .row').each(function() {
            let choiceNo = $(this).find('input[name="choice_no[]"]').val();
            let choiceOptions = $(this).find('input[name^="choice_options_"]').val();
            existingOptions[choiceNo] = choiceOptions;
        });

        $('#customer_choice_options').html(null);

            // Add the selected options, including previously filled values
            $.each($("#choice_attributes option:selected"), function() {
                add_more_customer_choice_option($(this).val(), $(this).text(), existingOptions[$(this).val()]);
            });
        });

    function add_more_customer_choice_option(i, name, existingValue = '') {
        let n = name.split(' ').join('');
        $('#customer_choice_options').append(
            '<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i +
            '"><input type="text" class="form-control" name="choice[]" value="' + n +
            '" placeholder="{{ trans('Choice Title') }}" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' +
            i +
            '[]" placeholder="{{ trans('Enter choice values') }}" data-role="tagsinput" value="' + (existingValue || '') + '" onchange="update_sku()" required></div></div>'
        );

        $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
    }

        $('#colors-selector').on('change', function() {
             
            update_sku();
            $('#color_switcher').prop('checked')
            {

                color_wise_image($('#colors-selector'));
            }
        });

        $('input[name="unit_price"]').on('keyup', function() {
            let product_type = $('#product_type').val();
            if(product_type === 'physical') {
                update_sku();
            }
        });

        function color_wise_image(t){
            let colors = t.val();
            $('#color_wise_image').html('')
            $.each(colors, function(key, value){
                let value_id = value.replace('#','');
                let color= "color_image_"+value_id;

                let html = ` <div class='col-6 col-lg-4 col-xl-3'> <label style='border: 2px dashed #ddd; border-radius: 3px; cursor: pointer; text-align: center; overflow: hidden; padding: 5px; margin-top: 5px; margin-bottom : 5px; position : relative; display: flex; align-items: center; margin: auto; justify-content: center; flex-direction: column;'>
                                <span class="upload--icon" style="background: ${value}">
                                <i class="tio-edit"></i>
                                    <input type="file" name="`+color+`" id="`+value_id+`" class="d-none" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                </span>
                                <img src="{{ asset('assets/back-end/img/400x400/img2.jpg') }}" style="object-fit: cover;aspect-ratio:1"  alt="public/img">
                              </label> </div>`;
                $('#color_wise_image').append(html)

                $("#color_wise_image input[type='file']").each(function () {

                    var $this = $(this).closest('label');

                    function proPicURL(input) {
                        if (input.files && input.files[0]) {
                            var uploadedFile = new FileReader();
                            uploadedFile.onload = function (e) {
                                $this.find('img').attr('src', e.target.result);
                                $this.fadeIn(300);
                            };
                            uploadedFile.readAsDataURL(input.files[0]);
                        }
                    }
                    $(this)
                        .on("change", function () {
                            proPicURL(this);
                        });
                });
            });
        }

        function update_sku() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{ route('seller.product.sku-combination') }}',
                data: $('#product_form').serialize(),
                success: function(data) {
                    $('#sku_combination').html(data.view);
                    $('#sku_combination').addClass('pt-4');
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        };

        $(document).ready(function() {
            // color select select2
            $('.color-var-select').select2({
                templateResult: colorCodeSelect,
                templateSelection: colorCodeSelect,
                escapeMarkup: function(m) {
                    return m;
                }
            });

            function colorCodeSelect(state) {
                var colorCode = $(state.element).val();
                if (!colorCode) return state.text;
                return "<span class='color-preview' style='background-color:" + colorCode + ";'></span>" + state
                    .text;
            }
        });
    </script>

    <script>
        function check() {


            let totalSize = 0;
            const imageFileInputs = document.querySelectorAll('input[name="images[]"]');
            const imageFile = document.querySelector('input[name="image"]').files[0];


            if (imageFile != undefined) {
                totalSize += imageFile.size;
            }else{
               totalSize = 0;
            }


                imageFileInputs.forEach(input => {
                    if (input.files.length > 0) {
                        for (let i = 0; i < input.files.length; i++) {
                            totalSize += input.files[i].size;
                        }
                    }
                });




            let totalSizeMB = totalSize / 1024 / 1024;


            if (totalSizeMB > 5) {

                Swal.fire({
                    title: '{{ \App\CPU\translate('File size too big') }}',
                    text: '{{ \App\CPU\translate('The total file size should be 5MB or less.') }}',
                    icon: 'error',
                    confirmButtonColor: '#377dff',
                    confirmButtonText: '{{ \App\CPU\translate('OK') }}'
                });
                return false;
            }

            Swal.fire({
                title: '{{ \App\CPU\translate('Are you sure') }}?',
                text: '',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#377dff',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
                var formData = new FormData(document.getElementById('product_form'));
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.post({
                    url: '{{ route('seller.product.add-new') }}',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        if (data.errors) {
                            for (var i = 0; i < data.errors.length; i++) {
                                toastr.error(data.errors[i].message, {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                            }
                        } else {
                            toastr.success(
                                '{{ \App\CPU\translate('product updated successfully!') }}', {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                            $('#product_form').submit();
                        }
                    }
                });
            })
        };
    </script>

    <script>
        $(".lang_link").click(function(e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            console.log(lang);
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{ $default_lang }}') {
                $(".rest-part").removeClass('d-none');
            } else {
                $(".rest-part").addClass('d-none');
            }
        })

        $(document).ready(function(){
            product_type();
            digital_product_type();

            $('#product_type').change(function(){
                product_type();
            });

            $('#digital_product_type').change(function(){
                digital_product_type();
            });
        });

        function product_type(){
            let product_type = $('#product_type').val();

            if(product_type === 'physical'){
                $('#digital_product_type_show').hide();
                $('#digital_file_ready_show').hide();
                $('.physical_product_show').show();
                $('#digital_product_type').val($('#digital_product_type option:first').val());
                $('#digital_file_ready').val('');
            }else if(product_type === 'digital'){
                $('#digital_product_type_show').show();
                $('.physical_product_show').hide();

            }
        }

        function digital_product_type(){
            let digital_product_type = $('#digital_product_type').val();
            if (digital_product_type === 'ready_product') {
                $('#digital_file_ready_show').show();
            } else if (digital_product_type === 'ready_after_sell') {
                $('#digital_file_ready_show').hide();
                $("#digital_file_ready").val('');
            }
        }
    </script>

    {{-- ck editor --}}
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        $('.textarea').ckeditor({
            contentsLangDirection: '{{ Session::get('direction') }}',
        });
    </script>
    {{-- ck editor --}}
@endpush
<script>
    function getRndIntegerAlpha() {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const length = 8; // You can adjust the length of the alphanumeric string
    let result = '';
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    return result;
}


document.addEventListener('DOMContentLoaded', function () {
        const selectElement = document.getElementById('warehouse-select');
        const inputContainer = document.getElementById('input-container');
        const hiddenInput = document.getElementById('hidden-warehouse-input');

        // Store selected IDs
        let selectedIds = [];

        selectElement.addEventListener('change', function () {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const id = selectedOption.value;
            const title = selectedOption.text;

            if (!selectedIds.includes(id)) {
                selectedIds.push(id);

                // Create button element
                const button = document.createElement('div');
                button.style.display = 'inline-flex';
                button.style.alignItems = 'center';
                button.style.padding = '5px 10px';
                button.style.backgroundColor = '#f0f0f0';
                button.style.border = '1px solid #ccc';
                button.style.borderRadius = '5px';
                button.style.margin = '5px 0';
                button.style.cursor = 'default';

                button.innerHTML = `
                    <span>${title}</span>
                    <button style="
                        margin-left: 10px;
                        border: none;
                        background: none;
                        font-size: 14px;
                        cursor: pointer;
                        color: #ff0000;
                    ">&times;</button>
                `;

                // Remove item on "cut" button click
                button.querySelector('button').addEventListener('click', function () {
                    selectedIds = selectedIds.filter(item => item !== id);
                    inputContainer.removeChild(button);

                    // Update the hidden input value
                    hiddenInput.value = selectedIds.join(',');
                    console.log(hiddenInput);

                });

                inputContainer.appendChild(button);

                // Update the hidden input value
                hiddenInput.value = selectedIds.join(',');
                console.log(hiddenInput);
            }

            // Reset the dropdown
            selectElement.selectedIndex = 0;
        });
    });


    $(document).on('input', '#tax', function () {
    // Get the tax value from the input field
    let tax = parseFloat($('#tax').val()) || 18; // Default to 18 if input is empty or invalid

    // Define the variant (replace this with actual value)
    let variant = 236; // Example value for variant

    // Calculate the result
    let a = (tax + 100) / 100;
    let b = variant / a;

    // Display the result (optional)
    console.log("Calculated value:", b);
});



</script>
