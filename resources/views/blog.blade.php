@extends('layouts.back-end.common_seller_1')

@section('content')
    <style>
        .alert-danger {
            color: #fff;
            background-color: #da2828;
            width: max-content;
            border-radius: 12px;
            margin: auto;
            margin-top: 15px;
        }

        .blogBan1 {
            width: 100%
        }

        .blogNewsWrapper {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .blogNewsWrapper img {
            max-width: 150px;
            border-radius: 8px;
        }

        @media screen and (max-width: 767.98px) {
            .blogNewsWrapper h4 {
                font-size: 1.6rem;
            }
        }
    </style>
    <div class="page-wrapper d-none d-md-block">
        <main class="main">
            <div class="page-content pb-0 containerBg">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-sm-12 mt-2 mb-3">
                            <img src="{{ asset('public/website/assets/images/backgrounds/image 58.png') }}"
                                class="img-fluid blogBan1" alt="blog_bg_1">
                        </div>
                        <div class="col-12 col-sm-6">
                            @foreach ($blogs as $blog)
                                <div class="blogNewsWrapper">
                                    <div>
                                        <img src="{{ asset('storage/app/public/' . $blog->image) }}" class="img-fluid"
                                            alt="blog_bg_4">
                                    </div>
                                    <div class="ml-4">
                                        <h4>{{ $blog->title }}</h4>
                                        <div>
                                            <p class="shownContent">{{ Str::limit($blog->content, 100) }}</p>
                                            <a href="{{ route('blog.details', $blog->slug) }}"><span class="toggle-button"
                                                    onclick="toggleContent()">Read More</span></a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
                        <div class="col-12 col-sm-12">
                            <img src="{{ asset('public/website/assets/images/backgrounds/image 58.png') }}"
                                class="img-fluid blogBan1" alt="blog_bg_1">
                        </div>
                        
                        <div class="col-12 col-sm-12">
                            <h4 class="latNewsHead mt-2">Latest News</h4>
                            @foreach ($blogs as $blog)
                                <div class="blogNewsWrapper">
                                    <div>
                                        <img src="{{ asset('storage/app/public/' . $blog->image) }}" class="img-fluid"
                                            alt="blog_bg_4">

                                    </div>
                                    <div class="ml-4">
                                        <h4>{{ $blog->title }}</h4>
                                        <div>
                                            <p class="shownContent">{{ Str::limit($blog->content, 100) }}</p>
                                            <a href="{{ route('blog.details', $blog->slug) }}"> <span class="toggle-button"
                                                    onclick="toggleContent()">Read More</span></a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
