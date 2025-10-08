@extends('layouts.back-end.common_seller_1')

@section('content')
@push('head')
    <title>{{ $blogs->title }}</title>
    <meta name="description" content="{{ Str::limit(strip_tags($blogs->content), 150) }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $blogs->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($blogs->content), 150) }}">
    <meta property="og:image" content="{{ secure_url('storage/' . $blogs->banner) }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $blogs->title }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($blogs->content), 150) }}">
    <meta name="twitter:image" content="{{ secure_url('storage/' . $blogs->banner) }}">

    <!-- JSON-LD Schema.org -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ url()->current() }}"
        },
        "headline": "{{ $blogs->title }}",
        "description": "{{ Str::limit(strip_tags($blogs->content), 150) }}",
        "image": "{{ secure_url('storage/' . $blogs->banner) }}",
        "author": {
            "@type": "Person",
            "name": "{{ $blogs->author ?? 'InteriorChowk Team' }}"
        },
        "datePublished": "{{ $blogs->created_at->toIso8601String() }}",
        "dateModified": "{{ $blogs->updated_at->toIso8601String() }}",
        "articleBody": "{{ Str::limit(strip_tags($blogs->content), 500) }}"
    }
    </script>
@endpush

<div class="page-wrapper d-none d-md-block">
    <main class="main">
        <div class="page-content pb-0 containerBg">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <img src="{{ asset('storage/' . $blogs->banner) }}" style="
                                width: -webkit-fill-available;
                            " class="img-fluid blogBan1"
                            alt="blog_bg_1">
                    </div>
                    <div class="col-12">
                        <h2>{{$blogs->title}}</h2>
                        <p>{{$blogs->created_at}}</p>
                        <p>{{$blogs->category}}</p>
                    </div>

                    <div class="col-12">
                        {!! $blogs->description !!}
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<div class="page-wrapper d-md-none">
    <main class="main">
        <div class="page-content pb-0 containerBg">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <img src="{{ asset('storage/' . $blogs->banner) }}" style="
                                width: -webkit-fill-available;
                            " class="img-fluid blogBan1"
                            alt="blog_bg_1">
                    </div>
                    <div class="col-12">
                        <h2>{{$blogs->title}}</h2>
                        <p>{{$blogs->created_at}}</p>
                        <p>{{$blogs->category}}</p>
                    </div>
                    <div class="col-12">
                        {!! $blogs->description !!}
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection