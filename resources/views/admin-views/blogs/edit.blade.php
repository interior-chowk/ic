@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Edit Blog'))

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/select2.min.css') }}" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ asset('/public/assets/back-end/img/blog.png') }}" alt="" width="20">
                {{ \App\CPU\translate('Edit_Blog_Post') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form method="POST" action="{{ route('admin.blog.update', $blog->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <!-- Title -->
                        <div class="form-group col-md-6">
                            <label>{{ \App\CPU\translate('Title') }}</label>
                            <input type="text" name="title" class="form-control" value="{{ $blog->title }}" required>
                        </div>

                        <!-- Category -->
                        <div class="form-group col-md-6">
                            <label>{{ \App\CPU\translate('Category') }}</label>
                            <input type="text" name="category" class="form-control" value="{{ $blog->category }}"
                                required>
                        </div>

                        <!-- Image -->
                        <div class="form-group col-md-6">
                            <label>{{ \App\CPU\translate('Image') }}</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            @if ($blog->image)
                                <img src="{{ asset('storage/app/public/' . $blog->image) }}" width="100" class="mt-2">
                            @endif
                        </div>

                        <!-- Banner Image -->
                        <div class="form-group col-md-6">
                            <label>{{ \App\CPU\translate('Banner_Image') }}</label>
                            <input type="file" name="banner" class="form-control" accept="image/*">
                            @if ($blog->banner)
                                <img src="{{ asset('storage/app/public/' . $blog->banner) }}" width="100"
                                    class="mt-2">
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="form-group col-12">
                            <label>{{ \App\CPU\translate('Content') }}</label>
                            <textarea name="content" class="form-control" rows="6" required>{{ $blog->content }}</textarea>
                        </div>

                        <!-- Description (CKEditor) -->
                        <div class="form-group col-12">
                            <label>{{ \App\CPU\translate('Description') }}</label>
                            <textarea name="description" id="description-editor" class="form-control" rows="6" required>{!! $blog->description !!}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn--primary mt-3">{{ \App\CPU\translate('Update_Blog') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('public/assets/back-end/js/select2.min.js') }}"></script>
    <!-- <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script> -->

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
