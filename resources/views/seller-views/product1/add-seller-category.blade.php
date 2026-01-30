@extends('layouts.back-end.app-seller')

@section('title', \App\CPU\translate('product_add'))

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0">
                <img src="http://localhost/6valley/public/assets/back-end/img/all-orders.png" class="mb-1 mr-1" alt="">
                {{ \App\CPU\translate('Add_new_Category') }}
            </h2>
        </div>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        <form action="{{route('admin.category.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @php($language=\App\Model\BusinessSetting::where('type','pnc_language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = 'en')
                            @php($default_lang = json_decode($language)[0])
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach(json_decode($language) as $lang)
                                    <li class="nav-item text-capitalize">
                                        <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}"
                                           href="#"
                                           id="{{$lang}}-link">{{ucfirst(\App\CPU\Helpers::get_language_name($lang)).'('.strtoupper($lang).')'}}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div>
                                        @foreach(json_decode($language) as $lang)
                                        <div class="form-group {{$lang != $default_lang ? 'd-none':''}} lang_form"
                                            id="{{$lang}}-form">
                                            <label class="title-color">{{\App\CPU\translate('Category_Name')}}<span class="text-danger">*</span> ({{strtoupper($lang)}})</label>
                                            <input type="text" name="name[]" class="form-control"
                                                placeholder="{{\App\CPU\translate('New')}} {{\App\CPU\translate('Category')}}" {{$lang == $default_lang? 'required':''}}>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                        @endforeach
                                        <input name="position" value="0" class="d-none">
                                    </div>
                                    <div class="form-group">
                                        <label class="title-color" for="priority">{{\App\CPU\translate('priority')}}
                                            <span>
                                            <i class="tio-info-outined" title="{{\App\CPU\translate('the_lowest_number_will_get_the_highest_priority')}}"></i>
                                            </span>
                                        </label>

                                        <select class="form-control" name="priority" id="" required>
                                            <option disabled selected>{{\App\CPU\translate('Set_Priority')}}</option>
                                            @for ($i = 0; $i <= 10; $i++)
                                            <option
                                            value="{{$i}}" >{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group">
                                            <label class="title-color">{{ \App\CPU\translate('HSN_code') }}</label>
                                            <input type="text"
                                                   placeholder="{{ \App\CPU\translate('HSN Code') }}"
                                                   value="{{ old('HSN_code') }}" name="HSN_code"
                                                   class="form-control" required>
                                        </div>
                                    <div class="from_part_2">
                                        <label class="title-color">{{\App\CPU\translate('Category_Logo')}}</label>
                                        <span class="text-info"><span class="text-danger">*</span> ( {{\App\CPU\translate('ratio')}} 1:1 )</span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="customFileEg1"
                                                class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                required>
                                            <label class="custom-file-label"
                                                for="customFileEg1">{{\App\CPU\translate('choose')}} {{\App\CPU\translate('file')}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-4 mt-lg-0 from_part_2">
                                    <div class="form-group">
                                        <center>
                                            <img
                                                class="upload-img-view"
                                                id="viewer"
                                                src="{{asset('public/assets/back-end/img/900x400/img1.jpg')}}"
                                                alt="image"/>
                                        </center>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                <button type="reset" id="reset" class="btn btn-secondary">{{\App\CPU\translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{\App\CPU\translate('submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
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
    <script src="{{ asset('public/assets/back-end') }}/js/tags-input.min.js"></script>
    <script src="{{ asset('public/assets/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    
       
    
    
    <script>
    let uploadedFiles = [];
        $(function() {
            $('#color_switcher').click(function(){
                var checkBoxes = $("#color_switcher");
                if ($('#color_switcher').prop('checked')) {
                    $('#color_wise_image').show();
                } else {
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
                    image: '{{ asset('public/assets/back-end/img/400x400/img2.jpg') }}',
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
                    image: '{{ asset('public/assets/back-end/img/400x400/img2.jpg') }}',
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
                    image: '{{ asset('public/assets/back-end/img/400x400/img2.jpg') }}',
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
            '[]" placeholder="{{ trans('Enter choice values') }}" data-role="tagsinput" value="' + (existingValue || '') + '" onchange="update_sku()"></div></div>'
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
                                    <input type="file" name="`+color+`" id="`+value_id+`" class="d-none" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required="">
                                </span>
                                <img src="{{ asset('public/assets/back-end/img/400x400/img2.jpg') }}" style="object-fit: cover;aspect-ratio:1"  alt="public/img">
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
</script>

