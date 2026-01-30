@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Edit SEO Page'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ asset('/public/assets/back-end/img/Pages.png') }}" width="20" alt="">
                {{ \App\CPU\translate('Edit SEO Page') }}
            </h2>
        </div>

        <!-- SEO Edit Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.seo.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label
                                    for="page">{{ \App\CPU\translate('Page Route (e.g. about-us, contact-us)') }}</label>
                                <input type="text" name="page" class="form-control" id="page" required
                                    placeholder="e.g. about-us" value="{{ old('page', $data->page) }}">
                            </div>

                            <div class="form-group">
                                <label for="meta_title">{{ \App\CPU\translate('Meta Title') }}</label>
                                <input type="text" name="meta_title" class="form-control" id="meta_title"
                                    placeholder="Enter meta title" value="{{ old('meta_title', $data->meta_title) }}">
                            </div>

                            <div class="form-group">
                                <label for="meta_description">{{ \App\CPU\translate('Meta Description') }}</label>
                                <textarea name="meta_description" class="form-control" rows="3" id="meta_description"
                                    placeholder="Enter meta description">{{ old('meta_description', $data->meta_description) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label
                                    for="meta_keywords">{{ \App\CPU\translate('Meta Keywords (comma-separated)') }}</label>
                                <input type="text" name="meta_keywords" class="form-control" id="meta_keywords"
                                    placeholder="keyword1, keyword2, ..."
                                    value="{{ old('meta_keywords', $data->meta_keywords) }}">
                            </div>

                            <div class="form-group">
                                <label for="canonical">{{ \App\CPU\translate('Canonical URL (optional)') }}</label>
                                <input type="url" name="canonical" class="form-control" id="canonical"
                                    placeholder="https://example.com/page"
                                    value="{{ old('canonical', $data->canonical) }}">
                            </div>

                            @php
                                $og = json_decode($data->og_tags, true);
                            @endphp

                            <h5 class="text-capitalize mt-4 mb-3">
                                {{ \App\CPU\translate('Open Graph Tags (for social media)') }}</h5>

                            <div class="form-group">
                                <label for="og_title">{{ \App\CPU\translate('OG Title') }}</label>
                                <input type="text" name="og_title" class="form-control" id="og_title"
                                    placeholder="Enter Open Graph Title" value="{{ old('og_title', $og['title'] ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="og_description">{{ \App\CPU\translate('OG Description') }}</label>
                                <textarea name="og_description" class="form-control" rows="3" id="og_description"
                                    placeholder="Enter Open Graph Description">{{ old('og_description', $og['description'] ?? '') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="og_image">{{ \App\CPU\translate('OG Image URL') }}</label>
                                <input type="url" name="og_image" class="form-control" id="og_image"
                                    placeholder="https://example.com/image.jpg"
                                    value="{{ old('og_image', $og['image'] ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="content">{{ \App\CPU\translate('Content') }}</label>
                                <textarea name="content" class="form-control" rows="3" id="editor" placeholder="Enter Content">{{ old('content', $data->content) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.seo.index') }}"
                                    class="btn btn-secondary mr-2">{{ \App\CPU\translate('Cancel') }}</a>
                                <button type="submit" class="btn btn--primary">{{ \App\CPU\translate('Update') }}</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        $('#editor').ckeditor({
            contentsLangDirection: '{{ Session::get('direction') }}',
        });
    </script>
    {{-- ck editor --}}
@endpush
