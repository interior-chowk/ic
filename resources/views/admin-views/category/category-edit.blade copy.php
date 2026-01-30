@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Category'))
<style>
    .input-box {
        display: none;
        /* By default input box hidden hoga */
        margin-top: 10px;
    }
</style>
@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0">
                <img src="{{ asset('/public/assets/back-end/img/brand-setup.png') }}" class="mb-1 mr-1" alt="">
                @if ($category['position'] == 1)
                    {{ \App\CPU\translate('Sub') }}
                @elseif($category['position'] == 2)
                    {{ \App\CPU\translate('Sub Sub') }}
                @endif
                {{ \App\CPU\translate('Category') }}
                {{ \App\CPU\translate('Update') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <!-- <div class="card-header">
                                                            {{ \App\CPU\translate('category_form') }}
                                                        </div> -->
                    <div class="card-body"
                        style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
                        <form action="{{ route('admin.category.update', [$category['id']]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @php($language = \App\Model\BusinessSetting::where('type', 'pnc_language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = 'en')

                            @php($default_lang = json_decode($language)[0])
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach (json_decode($language) as $lang)
                                    <li class="nav-item text-capitalize">
                                        <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}"
                                            href="#"
                                            id="{{ $lang }}-link">{{ \App\CPU\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="row">
                                <div
                                    class="{{ $category['parent_id'] == 0 || $category['position'] == 1 ? 'col-lg-6' : 'col-12' }}">
                                    @foreach (json_decode($language) as $lang)
                                        <div>
                                            <?php
                                            if (count($category['translations'])) {
                                                $translate = [];
                                                foreach ($category['translations'] as $t) {
                                                    if ($t->locale == $lang && $t->key == 'name') {
                                                        $translate[$lang]['name'] = $t->value;
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="form-group {{ $lang != $default_lang ? 'd-none' : '' }} lang_form"
                                                id="{{ $lang }}-form">
                                                <label class="title-color">{{ \App\CPU\translate('Category_Name') }}
                                                    ({{ strtoupper($lang) }})
                                                </label>
                                                <input type="text" name="name[]" required
                                                    value="{{ $lang == $default_lang ? $category['name'] : $translate[$lang]['name'] ?? '' }}"
                                                    class="form-control"
                                                    placeholder="{{ \App\CPU\translate('New') }} {{ \App\CPU\translate('Category') }}"
                                                    {{ $lang == $default_lang ? 'required' : '' }}>
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        </div>
                                    @endforeach

                                    @if ($category['position'] == 1)
                                        <div class="form-group " id="{{ $lang }}-form">
                                            <label class="title-color"
                                                for="exampleFormControlInput1">{{ \App\CPU\translate('commission') }} <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <input required type="number" name="commission" class="form-control"
                                                placeholder="{{ \App\CPU\translate('commission') }}"
                                                value="{{ $category['commission'] }}"
                                                {{ $lang == $default_lang ? 'required' : '' }}>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label class="title-color"
                                            for="priority">{{ \App\CPU\translate('priority') }}</label>
                                        <select class="form-control" name="priority" id="" required>
                                            @for ($i = 0; $i <= 10; $i++)
                                                <option value="{{ $i }}"
                                                    {{ $category['priority'] == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <!--image upload only for main category-->
                                    @if ($category['parent_id'] == 0 || $category['position'] == 1 || $category['parent_id'] != 0)
                                        <div class="from_part_2">
                                            <label class="title-color">{{ \App\CPU\translate('Category Logo') }}</label>
                                            <span class="text-info">({{ \App\CPU\translate('ratio') }} 1:1)</span>
                                            <div class="custom-file text-left">
                                                <input type="file" name="image" id="customFileEg1" required
                                                    class="custom-file-input"
                                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                <label class="custom-file-label"
                                                    for="customFileEg1">{{ \App\CPU\translate('choose') }}
                                                    {{ \App\CPU\translate('file') }}</label>
                                            </div>
                                        </div>
                                </div>
                                <div class="col-lg-6 mt-5 mt-lg-0 from_part_2">
                                    <div class="form-group">
                                        <center>
                                            <img class="upload-img-view" id="viewer"
                                                onerror="this.src='{{ asset('public/assets/front-end/img/image-place-holder.png') }}'"
                                                src="{{ env('CLOUDFLARE_R2_PUBLIC_URL') }}/{{ $category['icon'] }}"
                                                alt="" />
                                        </center>
                                    </div>
                                </div>
                                @endif
                                {{-- <div class="col-12">
                                    <button type="button" class="btn btn--primary" id="btn1">Specificatons</button>
                                    <div class="input-box" id="inputBox1">
                                        <input type="text" name="specification" required
                                            placeholder="Enter text for specification key" class="form-control"
                                            value="{{ $category->specification }}">
                                    </div>
                                </div>
                                <br>
                                <div class="col-12">
                                    <button type="button" class="btn btn--primary" id="btn2">key features</button>
                                    <div class="input-box" id="inputBox2">
                                        <input type="text" required name="key_features"
                                            placeholder="Enter text for key features" name="" class="form-control"
                                            value="{{ $category->key_features }}">
                                    </div>
                                </div>
                                <br>
                                <div class="col-12">
                                    <button type="button" class="btn btn--primary" id="btn3">Technical
                                        Specificatons</button>
                                    <div class="input-box" id="inputBox3">
                                        <input type="text" required name="technical_specification"
                                            placeholder="Enter text for specification key" class="form-control"
                                            value="{{ $category->technical_specification }}">
                                    </div>
                                </div>
                                <br>
                                <div class="col-12">
                                    <button type="button" class="btn btn--primary" id="btn4">Other Details</button>
                                    <div class="input-box" id="inputBox4">
                                        <input type="text" required name="other_details"
                                            placeholder="Enter text for key features" name="" class="form-control"
                                            value="{{ $category->other_details }}">
                                    </div>
                                </div> --}}
                                <br><br>

                                <div class="col-lg-6 from_part_2">
                                    <label class="title-color">{{ \App\CPU\translate('Meta_Title') }}</label>
                                    <span class="text-info"><span class="text-danger">*</span></span>
                                    <div class="custom-file text-left">
                                        <input type="text" required name="meta_title" id="" class="form-control"
                                            placeholder="meta title" value="{{ $category->meta_title }}">
                                    </div>
                                </div>
                                <div class="col-lg-6 form-group">
                                    <label class="" for="">{{ \App\CPU\translate('meta description') }}
                                        ({{ strtoupper($lang) }})</label>
                                    <textarea id="" required class="form-control" name="meta_description" rows="4" cols="50">{{ $category->meta_description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label class="title-color"
                                        for="{{ $lang }}_description">{{ \App\CPU\translate('page content') }}
                                        ({{ strtoupper($lang) }}) <span class="ml-2" data-toggle="tooltip"
                                            data-placement="top"
                                            title="{{ \App\CPU\translate('description contains about product detail , quality, features, specifications, about manufacturer and warranty') }}">
                                            <img class="info-img"
                                                src="{{ asset('/public/assets/back-end/img/info-circle.svg') }}"
                                                alt="img">
                                        </span></label>
                                    <textarea name="page_content" required class="editor textarea" cols="30" rows="10">{{ $category->page_content }}</textarea>
                                </div>

                                <div class="col-12">
                                    <label class="switcher mx-auto">
                                        <input type="checkbox" class="status switcher_input" id="{{ $category->id }}"
                                            {{ $category->status == 1 ? 'checked' : '' }}>
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>

                                <script>
                                    // JavaScript to toggle the input boxes
                                    document.getElementById("btn1").addEventListener("click", function() {
                                        const inputBox1 = document.getElementById("inputBox1");
                                        inputBox1.style.display = inputBox1.style.display === "block" ? "none" : "block";
                                    });

                                    document.getElementById("btn2").addEventListener("click", function() {
                                        const inputBox2 = document.getElementById("inputBox2");
                                        inputBox2.style.display = inputBox2.style.display === "block" ? "none" : "block";
                                    });

                                    document.getElementById("btn3").addEventListener("click", function() {
                                        const inputBox2 = document.getElementById("inputBox3");
                                        inputBox2.style.display = inputBox2.style.display === "block" ? "none" : "block";
                                    });

                                    document.getElementById("btn4").addEventListener("click", function() {
                                        const inputBox2 = document.getElementById("inputBox4");
                                        inputBox2.style.display = inputBox2.style.display === "block" ? "none" : "block";
                                    });
                                </script>

                                @if ($category['position'] == 2)
                                    <div class="d-flex justify-content-end gap-3">
                                        <button type="reset" id="reset"
                                            class="btn btn-secondary px-4">{{ \App\CPU\translate('reset') }}</button>
                                        <button type="submit"
                                            class="btn btn--primary px-4">{{ \App\CPU\translate('update') }}</button>
                                    </div>
                            </div>
                            @endif
                    </div>

                    @if ($category['parent_id'] == 0 || $category['position'] == 1)
                        <div class="d-flex justify-content-end gap-3">
                            <button type="reset" id="reset"
                                class="btn btn-secondary px-4">{{ \App\CPU\translate('reset') }}</button>
                            <button type="submit"
                                class="btn btn--primary px-4">{{ \App\CPU\translate('update') }}</button>
                        </div>
                    @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('script')
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
                $(".from_part_2").removeClass('d-none');
            } else {
                $(".from_part_2").addClass('d-none');
            }
        });

        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function() {
            readURL(this);
        });







        $(document).on('change', '.status', function() {
            var id = $(this).attr("id");
            console.log(id);
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
                url: "{{ route('admin.sub-sub-category.top_view') }}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function(data) {
                    console.log(data)
                    if (data.success == true) {
                        toastr.success('{{ \App\CPU\translate('Status updated successfully') }}');
                    } else {
                        toastr.error(
                            '{{ \App\CPU\translate('Status updated failed. Product must be approved') }}'
                        );
                        location.reload();
                    }
                }
            });
        });
    </script>

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
