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
</style>
<div class="page-wrapper d-none d-md-block">
    <main class="main">
        <div class="page-content pb-0 containerBg">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <h1 class="blogHead"><span class="bhSp1">Interior</span><span
                                class="bhSp2">Chowk</span><span class="bhSp3"> Blogs</span></h1>
                    </div>
                    <div class="col-12 col-sm-8">
                        <img src="{{asset('website/assets/images/backgrounds/image 58.png')}}" class="img-fluid blogBan1"

                            alt="blog_bg_1">
                    </div>
                    <div class="col-12 col-sm-4">
                        <img src="{{asset('website/assets/images/backgrounds/image 59.png')}}" class="img-fluid blogBan2"
                            alt="blog_bg_2">
                        <img src="{{asset('website/assets/images/backgrounds/image 60.png')}}" class="img-fluid blogBan3"
                            alt="blog_bg_3">
                    </div>
                    <div class="col-12 col-sm-12">
                        <h1 class="blogSecHead"><span class="bhSp1">Interior</span></h1>
                    </div>
                    <div class="col-12 col-sm-8">
                        @foreach($blogs as $blog)
                            <div class="blogNewsWrapper">
                                <div>
                                    <img src="{{asset('storage/'.$blog->image)}}" class="img-fluid" alt="blog_bg_4">
                                </div>
                                <div class="ml-4">
                                    <h4>{{$blog->title}}</h4>
                                    <div>
                                        <p class="shownContent">{{Str::limit($blog->content, 100)}}</p>
                                        <div class="extra-content" id="extra">
                                            <p class="d-inline">
                                                "Read
                                                More".</p>
                                        </div>
                                          <a href="{{route('blog.details',$blog->slug)}}"><span class="toggle-button" onclick="toggleContent()">Read More</span></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="rightBlogListWrap">
                            <h4>Trending Blogs</h4>
                            <ul>
                                @foreach($trendBlog as $trend)
                                    <li><a href="{{route('blog.details',$trend->slug)}}">{{$trend->title}}</a></li>
                                @endforeach
                            </ul>
                        </div>
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
                        <h1 class="blogHead"><span class="bhSp1">Interior</span><span
                                class="bhSp2">Chowk</span><span class="bhSp3"> Blogs</span></h1>
                    </div>
                    <div class="col-12 col-sm-8">
                        <img src="{{asset('website/assets/images/backgrounds/image 58.png')}}" class="img-fluid blogBan1"
                            alt="blog_bg_1">
                    </div>
                    <div class="col-12 col-sm-4">
                        <img src="{{asset('website/assets/images/backgrounds/image 59.png')}}" class="img-fluid blogBan2"
                            alt="blog_bg_2">
                        <img src="{{asset('website/assets/images/backgrounds/image 60.png')}}" class="img-fluid blogBan3"
                            alt="blog_bg_3">
                    </div>
                    <div class="col-12 col-sm-12">
                        <div class="rightBlogListWrap">
                            <div class="form-group">
                                <select class="form-control">
                                    @foreach($trendBlog as $trend)
                                    <a href="{{route('blog.details',$trend->slug)}}"><option>{{$trend->title}}</option></a>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12">
                        <h4 class="latNewsHead">Latest News</h4>
                        @foreach($blogs as $blog)
                            <div class="blogNewsWrapper">
                                <div>
                                    <img src="{{asset('storage/'.$blog->image)}}" class="img-fluid" alt="blog_bg_4">

                                </div>
                                <div class="ml-4">
                                    <h4>{{$blog->title}}</h4>
                                    <div>
                                        <p class="shownContent">{{Str::limit($blog->content, 100)}}</p>
                                        <div class="extra-content" id="extra">
                                          <p class="d-inline">

                                                "Read
                                                More".</p>
                                        </div>
                                         <a href="{{route('blog.details',$blog->slug)}}"> <span class="toggle-button" onclick="toggleContent()">Read More</span></a>
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