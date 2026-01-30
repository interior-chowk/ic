@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Add New Blog'))

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/select2.min.css') }}" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ asset('/public/assets/back-end/img/blog.png') }}" alt="" width="20">
                {{ \App\CPU\translate('Add_New_Blog_Post') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form method="POST" action="{{ route('admin.blog.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Title -->
                        <div class="form-group col-md-6">
                            <label>{{ \App\CPU\translate('Title') }}</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter blog title"
                                required>
                        </div>

                        <!-- Category -->
                        <div class="form-group col-md-6">
                            <label>{{ \App\CPU\translate('Category') }}</label>
                            <input type="text" name="category" class="form-control" placeholder="e.g. Technology"
                                required>
                        </div>

                        <!-- Image -->
                        <div class="form-group col-md-6">
                            <label>{{ \App\CPU\translate('Image') }}</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>

                        <!-- Banner Image -->
                        <div class="form-group col-md-6">
                            <label>{{ \App\CPU\translate('Banner_Image') }}</label>
                            <input type="file" name="banner" class="form-control" accept="image/*" required>
                        </div>

                        <!-- Content -->
                        <div class="form-group col-12">
                            <label>{{ \App\CPU\translate('Content') }}</label>
                            <textarea name="content" class="form-control" rows="6" placeholder="Write full blog content here..." required></textarea>
                        </div>

                        <!-- Description -->
                        <div class="form-group col-12">
                            <label>{{ \App\CPU\translate('Description') }}</label>
                            <textarea name="description" id="description-editor" class="form-control" rows="6"
                                placeholder="Write full blog description here..." required></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success mt-3">{{ \App\CPU\translate('Publish_Blog') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('public/assets/back-end/js/select2.min.js') }}"></script>
    <script src="{{ url('vendor/ckeditor/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ url('vendor/ckeditor/ckeditor/adapters/jquery.js') }}"></script>
    <script>
        $('#description-editor').ckeditor({
            contentsLangDirection: 'ltr',
            filebrowserUploadUrl: "{{ route('admin.blog.ckeditor.upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: 'form'
        });
    </script>
    <script>
        $(".js-example-theme-single").select2({
            theme: "classic"
        });
        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>
@endpush
