@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Sub Sub Category'))

@push('css_or_js')
@endpush

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
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{ asset('/public/assets/back-end/img/brand-setup.png') }}" alt="">
                {{ \App\CPU\translate('Sub') }} {{ \App\CPU\translate('Sub') }} {{ \App\CPU\translate('Category') }}
                {{ \App\CPU\translate('Setup') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
                        <form action="{{ route('admin.sub-sub-category.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @php($language = \App\Model\BusinessSetting::where('type', 'pnc_language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = 'en')
                            @if ($language)
                                @php($default_lang = json_decode($language)[0])
                                <ul class="nav nav-tabs w-fit-content mb-4">
                                    @foreach (json_decode($language) as $lang)
                                        <li class="nav-item text-capitalize">
                                            <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}"
                                                href="#"
                                                id="{{ $lang }}-link">{{ ucfirst(\App\CPU\Helpers::get_language_name($lang)) . '(' . strtoupper($lang) . ')' }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="row">
                                    @foreach (json_decode($language) as $lang)
                                        <div class="col-12 form-group {{ $lang != $default_lang ? 'd-none' : '' }} lang_form"
                                            id="{{ $lang }}-form">
                                            <label class="title-color"
                                                for="exampleFormControlInput1">{{ \App\CPU\translate('Sub_sub_category') }}
                                                {{ \App\CPU\translate('name') }}<span class="text-danger">*</span>
                                                ({{ strtoupper($lang) }})
                                            </label>
                                            <input type="text" name="name[]" class="form-control"
                                                placeholder="{{ \App\CPU\translate('New_Sub_Sub_Category') }}"
                                                {{ $lang == $default_lang ? 'required' : '' }}>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{ $lang }}">
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <div class="form-group lang_form" id="{{ $default_lang }}-form">
                                            <label class="title-color">{{ \App\CPU\translate('Sub_sub_category') }}
                                                {{ \App\CPU\translate('name') }}<span class="text-danger">*</span>
                                                ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="name[]" class="form-control"
                                                placeholder="{{ \App\CPU\translate('New_Sub_Category') }}" required>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{ $default_lang }}">
                                    </div>
                            @endif

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="title-color">{{ \App\CPU\translate('main') }}
                                        {{ \App\CPU\translate('category') }}
                                        <span class="text-danger">*</span></label>
                                    <select name="parent_id" class="form-control" id="cat_id" required>
                                        <option value="" disabled selected>
                                            {{ \App\CPU\translate('Select_main_category') }}</option>
                                        @foreach (\App\Model\Category::where(['position' => 0])->get() as $category)
                                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="title-color text-capitalize"
                                        for="name">{{ \App\CPU\translate('sub_category') }}
                                        {{ \App\CPU\translate('name') }}<span class="text-danger">*</span></label>
                                    <select name="sub_parent_id" id="parent_id" class="form-control">
                                        <option value="" disabled selected>
                                            {{ \App\CPU\translate('Select_sub_category') }}</option>
                                        @foreach (\App\Model\Category::where(['position' => 1])->orderBy('name')->get() as $sub_category)
                                            <option value="{{ $sub_category['id'] }}">{{ $sub_category['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="title-color text-capitalize"
                                        for="priority">{{ \App\CPU\translate('priority') }}
                                        <span>
                                            <i class="tio-info-outined"
                                                title="{{ \App\CPU\translate('the_lowest_number_will_get_the_highest_priority') }}"></i>
                                        </span>
                                    </label>
                                    <select class="form-control" name="priority" id="" required>
                                        <option disabled selected>{{ \App\CPU\translate('Set_Priority') }}</option>
                                        @for ($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="button" class="btn btn--primary" id="btn1">Specificatons</button>
                                <div class="input-box" id="inputBox1">
                                    <input type="text" name="specification"
                                        placeholder="Enter text for specification key" class="form-control">
                                </div>
                            </div>
                            <br><br>
                            <div class="col-12">
                                <button type="button" class="btn btn--primary" id="btn2">key features</button>
                                <div class="input-box" id="inputBox2">
                                    <input type="text" name="key_features" placeholder="Enter text for key features"
                                        name="" class="form-control">
                                </div>
                            </div>
                            <br><br>
                            <div class="col-12">
                                <button type="button" class="btn btn--primary" id="btn3">Technical
                                    Specificatons</button>
                                <div class="input-box" id="inputBox3">
                                    <input type="text" name="technical_specification"
                                        placeholder="Enter text for specification key" class="form-control">
                                </div>
                            </div>
                            <br><br>
                            <div class="col-12">
                                <button type="button" class="btn btn--primary" id="btn4">Other Details</button>
                                <div class="input-box" id="inputBox4">
                                    <input type="text" name="other_details" placeholder="Enter text for key features"
                                        name="" class="form-control">
                                </div>
                            </div>
                            <div class="from_part_2">
                                <label class="title-color">{{ \App\CPU\translate('Sub_Sub_Category_Logo') }}</label>
                                <span class="text-info"><span class="text-danger">*</span> (
                                    {{ \App\CPU\translate('ratio') }} 1:1 )</span>
                                <div class="custom-file text-left">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label"
                                        for="customFileEg1">{{ \App\CPU\translate('choose') }}
                                        {{ \App\CPU\translate('file') }}</label>
                                </div>
                            </div>
                    </div>

                </div>

                <div class="col-lg-6 from_part_2">
                    <label class="title-color">{{ \App\CPU\translate('Meta_Title') }}</label>
                    <span class="text-info"><span class="text-danger">*</span></span>
                    <div class="custom-file text-left">
                        <input type="text" name="meta_title" id="" class="form-control" value=""
                            placeholder="meta title">
                    </div>
                </div>
                <div class="col-lg-6 form-group pt-4">
                    <label class="" for="">{{ \App\CPU\translate('meta description') }}
                        ({{ strtoupper($lang) }})</label>
                    <textarea id="" class="form-control" name="meta_description" rows="4" cols="50"></textarea>
                </div>
                <div class="form-group">
                    <label class="title-color"
                        for="{{ $lang }}_description">{{ \App\CPU\translate('page content') }}
                        ({{ strtoupper($lang) }}) <span class="ml-2" data-toggle="tooltip" data-placement="top"
                            title="{{ \App\CPU\translate('description contains about product detail , quality, features, specifications, about manufacturer and warranty') }}">
                            <img class="info-img" src="{{ asset('/public/assets/back-end/img/info-circle.svg') }}"
                                alt="img">
                        </span></label>
                    <textarea name="page_content" class="editor textarea" cols="30" rows="10">{{ old('details') }}</textarea>
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
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                        <button type="reset" class="btn btn-secondary">{{ \App\CPU\translate('reset') }}</button>
                        <button type="submit" class="btn btn--primary">{{ \App\CPU\translate('submit') }}</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
    </div>
    </div>

    <div class="row mt-20" id="cate-table">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <div class="row align-items-center">
                        <div class="col-sm-5 col-md-3 col-lg-3 mb-2 mb-sm-0">
                            <h5 class="text-capitalize d-flex gap-2">
                                {{ \App\CPU\translate('sub_sub_category_list') }}
                                <span class="badge badge-soft-dark radius-50 fz-12">{{ $categories->total() }}</span>
                            </h5>
                        </div>
                        <div class="col-sm-8 col-md-4 col-lg-4">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-custom input-group-merge">
                                    <select name="filter" class="form-control">
                                        <option value="" selected disabled>
                                            {{ \App\CPU\translate('filter_sub_category') }}</option>
                                        @foreach (\App\Model\Category::where(['position' => 1])->orderBy('name')->get() as $sub_category)
                                            <option value="{{ $sub_category['id'] }}"
                                                {{ $filter == $sub_category['id'] ? 'selected' : '' }}>
                                                {{ $sub_category['name'] }}</option>
                                        @endforeach

                                    </select>

                                    <button type="submit"
                                        class="btn btn--primary">{{ \App\CPU\translate('filter') }}</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-sm-7 col-md-3 col-lg-3">
                            <!-- Search -->
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-custom input-group-merge">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="search" class="form-control"
                                        placeholder="{{ \App\CPU\translate('Search_by_Sub_Sub_Category') }}"
                                        aria-label="Search orders" value="{{ $search }}" required>
                                    <button type="submit"
                                        class="btn btn--primary">{{ \App\CPU\translate('search') }}</button>
                                </div>
                            </form>
                            <!-- End Search -->
                        </div>
                        <div class="col-sm-7 col-md-2 col-lg-2">
                            <button type="button" class="btn btn-outline--primary text-nowrap btn-block"
                                data-toggle="dropdown">
                                <i class="tio-download-to"></i>
                                {{ \App\CPU\translate('export') }}
                                <i class="tio-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('admin.sub-sub-category.all-category-excel') }}">
                                        {{ \App\CPU\translate('excel') }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

                <div class="table-responsive">
                    <table style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ \App\CPU\translate('ID') }}</th>
                                <th>{{ \App\CPU\translate('main_category') }}</th>
                                <th>{{ \App\CPU\translate('sub_category') }}</th>
                                <th>{{ \App\CPU\translate('sub_sub_category_name') }}</th>
                                <th>{{ \App\CPU\translate('priority') }}</th>
                                <th class="text-center">{{ \App\CPU\translate('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $key => $category)
                                <tr>
                                    <td>{{ $category['id'] }}</td>
                                    @php($parent_category = App\Model\Category::where('id', $category['parent_id'])->first())
                                    @if ($parent_category)
                                        <td>{{ $parent_category['name'] }}</td>
                                    @else
                                        <td> </td>
                                    @endif

                                    @php($sub_parent_category = App\Model\Category::where('id', $category['sub_parent_id'])->first())

                                    @if ($sub_parent_category)
                                        <td>{{ $sub_parent_category['name'] }}</td>
                                    @else
                                        <td> </td>
                                    @endif

                                    <td>{{ $category['name'] }}</td>
                                    <td>{{ $category['priority'] }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                title="{{ \App\CPU\translate('Edit') }}"
                                                href="{{ route('admin.category.edit', [$category['id']]) }}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-danger btn-sm delete square-btn"
                                                title="{{ \App\CPU\translate('Delete') }}" id="{{ $category['id'] }}">
                                                <i class="tio-delete"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        <!-- Pagination -->
                        {{ $categories->links() }}
                    </div>
                </div>

                @if (count($categories) == 0)
                    <div class="text-center p-4">
                        <img class="mb-3 w-160" src="{{ asset('public/assets/back-end') }}/svg/illustrations/sorry.svg"
                            alt="Image Description">
                        <p class="mb-0">{{ \App\CPU\translate('No_data_to_show') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{ asset('public/assets/back-end') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('public/assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
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
        $('#cat_id').on('change', function() {

            var id = $(this).val();
            if (id) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.sub-sub-category.getSubCategory') }}',
                    data: {
                        id: id
                    },
                    success: function(result) {
                        $("#parent_id").html(result);
                    }
                });
            }
        });

        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            Swal.fire({
                title: '{{ \App\CPU\translate('Are_you_sure_to_delete_this?') }}',
                text: "{{ \App\CPU\translate('You_wont_be_able_to_revert_this!') }}",
                showCancelButton: true,
                type: 'warning',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ \App\CPU\translate('Yes') }}, {{ \App\CPU\translate('delete_it') }}!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('admin.sub-sub-category.delete') }}",
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success(
                                '{{ \App\CPU\translate('Sub_Sub_Category_Deleted_Successfully') }}.'
                                );
                            location.reload();
                        }
                    });
                }
            })
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
