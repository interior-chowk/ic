@extends('layouts.back-end.common_seller_1')

@section('content')
    @push('style')
        <link rel="stylesheet" href="{{ asset('public/website/assets/css/service-provider.css') }}">
        <link rel="stylesheet" href="{{ asset('public/website/assets/css/billing.css') }}">
        <style>
            .ratingWrapper {
                box-shadow: unset;
            }
        </style>
    @endpush

    <main class="main">
        <div class="page-content">
            <div class="position-relative">
                <div class="banner-head banner-headBg" style="background-image:url('{{ $data->banner_image }}')">
                    <a href="#" class="heartBadge">
                        <img src="{{ asset('/public/website/assets/images/icons/heart-badge.png') }}" alt="heart badge"
                            width="25" />
                    </a>
                    <a href="#" class="shareBadge">
                        <img src="{{ asset('/public/website/assets/images/icons/share.png') }}" alt="share badge"
                            width="25" />
                    </a>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-12">
                        <div class="PersonalDtlWrap">
                            <div class="imgNamWrapper">
                                <img src="{{ asset('storage/app/public/service-provider/profile') }}/{{ $data->image }}"
                                    class="img-fluid" alt="usrImage" />
                                {{-- <img src="{{asset('/public/website/assets/images/Rectangle_212.png')}}" class="img-fluid" alt="usrImage" /> --}}
                                <div class="namData">
                                    <h2>{{ ucwords($data->name) }}</h2>
                                    <p>{{ ucwords($data->role_name) }}</p>
                                    {{-- @php
                                        $avgRating = \App\Model\ProviderReviews::where('provider_id', $data->id)->avg(
                                            'rating',
                                        );
                                        $reviews = \App\Model\ProviderReviews::where('provider_id', $data->id)
                                            ->orderBy('id', 'DESC')
                                            ->get();
                                    @endphp
                                    <div class="ratingWrapper">
                                        <div class="star-rating">
                                            <input type="radio" id="1-star" name="rating" value="1" />
                                            <label for="1-star" class="star">&#9733;</label>
                                            <input type="radio" id="2-stars" name="rating" value="2" />
                                            <label for="2-stars" class="star">&#9733;</label>
                                            <input type="radio" id="3-stars" name="rating" value="3" />
                                            <label for="3-stars" class="star">&#9733;</label>
                                            <input type="radio" id="4-stars" name="rating" value="4" />
                                            <label for="4-stars" class="star">&#9733;</label>
                                            <input type="radio" id="5-stars" name="rating" value="5" />
                                            <label for="5-stars" class="star">&#9733;</label>
                                        </div>
                                    </div> --}}

                                    @php
                                        $avgRating = round(
                                            \App\Model\ProviderReviews::where('provider_id', $data->id)->avg('rating'),
                                        );
                                        $reviews = \App\Model\ProviderReviews::where('provider_id', $data->id)
                                            ->orderBy('id', 'DESC')
                                            ->get();
                                    @endphp

                                    <div class="ratingWrapper">
                                        <div class="star-rating">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $avgRating)
                                                    <span class="star" style="color:#f8ce0b;">&#9733;</span>
                                                    {{-- Filled star --}}
                                                @else
                                                    <span class="star" style="color:#ccc;">&#9733;</span>
                                                    {{-- Empty star --}}
                                                @endif
                                            @endfor
                                        </div>
                                    </div>



                                    <span>Views: {{ count($reviews) }}</span>
                                </div>
                            </div>
                            <div class="socialWrap">
                                <a href="https://wa.me/{{ $data->whatsapp_number ?? $data->phone }}" class="wtappLink"
                                    target="_blank">
                                    <img src="{{ asset('/public/website/assets/images/icons/whats app.webp') }}"
                                        class="img-fluid-icon mb-1" alt="whatsapp" />
                                </a>
                                <a href="#" class="fbLink">
                                    <img src="{{ asset('/public/website/assets/images/icons/voice call.webp') }}"
                                        class="img-fluid-icon" alt="call" />
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12">
                        <div class="workWrap">
                            <h3>Our Services</h3>
                            <p class="serPara">
                                {{ $data->type_of_work }}
                            </p>
                            <h3 class="mt-2">About Us</h3>
                            <div class="keyVal">
                                <p>Working since:</p>
                                <span>{{ \Carbon\Carbon::parse($data->working_since)->format('d-M-Y') }}</span>
                            </div>
                            <div class="keyVal">
                                <p>No. of Project Completed:</p>
                                <span>2000</span>
                            </div>
                            <div class="keyVal">
                                <p>Team Strength:</p>
                                <span>{{ $data->team_strength }}</span>
                            </div>
                            <div class="keyVal">
                                <p>Location:</p>
                                <span>
                                    {{ $data->street ?? '' }}
                                    {{ $data->district ?? '' }}
                                    {{ $data->city ?? '' }}
                                </span>
                            </div>
                        </div>

                        <div class="workWrap">
                            <h3>Description</h3>
                            <p>
                                {{ $data->description }}
                            </p>
                        </div>
                        <div class="workWrap">
                            <h3>Achivements</h3>
                            <ul>
                                <li>{{ $data->achievments }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-12">
                        <div class="portfolioWrapper">
                            <h1>Portfolio</h1>
                            <div class="row">
                                <div class="col-12 col-md-12">
                                    <div class="PortfolioImg">
                                        <img src="{{ asset('/public/website/assets/images/Rectangle_215.png') }}"
                                            class="img-fluid" alt="Portfolio_img" />
                                        <img src="{{ asset('/public/website/assets/images/Rectangle_215.png') }}"
                                            class="img-fluid" alt="Portfolio_img" />
                                        <img src="{{ asset('/public/website/assets/images/Rectangle_215.png') }}"
                                            class="img-fluid" alt="Portfolio_img" />
                                    </div>
                                    <div class="PortfolioImg">
                                        <img src="{{ asset('/public/website/assets/images/Rectangle_215.png') }}"
                                            class="img-fluid" alt="Portfolio_img" />
                                        <img src="{{ asset('/public/website/assets/images/Rectangle_215.png') }}"
                                            class="img-fluid" alt="Portfolio_img" />
                                        <img src="{{ asset('/public/website/assets/images/Rectangle_215.png') }}"
                                            class="img-fluid" alt="Portfolio_img" />
                                    </div>
                                    <div class="PortfolioImg">
                                        <img src="{{ asset('/public/website/assets/images/Rectangle_215.png') }}"
                                            class="img-fluid" alt="Portfolio_img" />
                                        <img src="{{ asset('/public/website/assets/images/Rectangle_215.png') }}"
                                            class="img-fluid" alt="Portfolio_img" />
                                        <img src="{{ asset('/public/website/assets/images/Rectangle_215.png') }}"
                                            class="img-fluid" alt="Portfolio_img" />
                                    </div>
                                    <div class="PortfolioImg">
                                        <img src="{{ asset('/public/website/assets/images/Rectangle_215.png') }}"
                                            class="img-fluid" alt="Portfolio_img" />
                                        <img src="{{ asset('/public/website/assets/images/Rectangle_215.png') }}"
                                            class="img-fluid" alt="Portfolio_img" />
                                        <img src="{{ asset('/public/website/assets/images/Rectangle_215.png') }}"
                                            class="img-fluid" alt="Portfolio_img" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12">
                        <div class="contactInfoWrapper">
                            <h4>Contact information</h4>
                            <div class="cntPairWrap">
                                <img src="{{ asset('/public/website/assets/images/icons/Frame_61.png') }}"
                                    class="img-fluid" alt="icon-call" />
                                <p>91-{{ $data->phone ?? '' }}</p>
                            </div>

                            <div class="cntPairWrap">
                                <img src="{{ asset('/public/website/assets/images/icons/Frame_62.png') }}"
                                    class="img-fluid" alt="chat-icon" />
                                <p>{{ $data->email ?? '' }}</p>
                            </div>

                            <div class="cntPairWrap">
                                <img src="{{ asset('/public/website/assets/images/icons/info.png') }}" class="img-fluid"
                                    alt="chat-icon" />
                                <p>{{ $data->website ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12">
                        <div class="socialMedWrapper">
                            <h4>Want updates from {{ $data->name }} follow us on !</h4>
                            <div class="socMedIcon">
                                <a href="{{ $data->insta_link ?? '#' }}">
                                    <img src="{{ asset('/public/website/assets/images/icons/Instagram.png') }}"
                                        class="img-fluid" alt="insta" />
                                </a>
                                <a href="{{ $data->facebook_link ?? '#' }}">
                                    <img src="{{ asset('/public/website/assets/images/icons/facebook.png') }}"
                                        class="img-fluid" alt="fb" />
                                </a>
                                <a href="{{ $data->youtube_link ?? '#' }}">
                                    <img src="{{ asset('/public/website/assets/images/icons/YouTube.png') }}"
                                        class="img-fluid" alt="youtube" />
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12">
                        <div class="reviewWrapper">
                            <h4>Review us!</h4>
                            {{-- <div class="ratingWrapper">
                                <div class="star-rating">
                                    <input type="radio" id="1-star" name="rating" value="1" />
                                    <label for="1-star" class="star">&#9733;</label>
                                    <input type="radio" id="2-stars" name="rating" value="2" />
                                    <label for="2-stars" class="star">&#9733;</label>
                                    <input type="radio" id="3-stars" name="rating" value="3" />
                                    <label for="3-stars" class="star">&#9733;</label>
                                    <input type="radio" id="4-stars" name="rating" value="4" />
                                    <label for="4-stars" class="star">&#9733;</label>
                                    <input type="radio" id="5-stars" name="rating" value="5" />
                                    <label for="5-stars" class="star">&#9733;</label>
                                </div>
                            </div>
                            <textarea id="w3review" class="w-100" name="w3review" rows="4" cols="50"></textarea>
                            <button class="btn btn-revSub">Submit</button> --}}
                            <form action="{{ route('provider.review.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="provider_id" value="{{ $data->id }}">

                                <div class="ratingWrapper">
                                    <div class="star-rating" style="flex-direction:row-reverse;">
                                        <input type="radio" id="1-star" name="rating" value="1" required />
                                        <label for="1-star" class="star">&#9733;</label>
                                        <input type="radio" id="2-stars" name="rating" value="2" />
                                        <label for="2-stars" class="star">&#9733;</label>
                                        <input type="radio" id="3-stars" name="rating" value="3" />
                                        <label for="3-stars" class="star">&#9733;</label>
                                        <input type="radio" id="4-stars" name="rating" value="4" />
                                        <label for="4-stars" class="star">&#9733;</label>
                                        <input type="radio" id="5-stars" name="rating" value="5" />
                                        <label for="5-stars" class="star">&#9733;</label>
                                    </div>
                                </div>

                                <textarea class="w-100" name="comment" rows="4" required>Write your feedback</textarea>

                                <input type="file" name="attachment" accept="image/*">

                                <button type="submit" class="btn btn-revSub">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
