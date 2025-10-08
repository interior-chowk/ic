@extends('layouts.back-end.common_seller_1')

@section('content')
@push('style')
  <link rel="stylesheet" href="{{asset('website/assets/css/faq.css')}}">

<script type="application/ld+json">
@php
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => []
    ];

    foreach($subcategories as $subcategory) {
        foreach($subcategory->faqs as $faq) {
            $faqSchema['mainEntity'][] = [
                '@type' => 'Question',
                'name' => strip_tags($faq->question),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($faq->answer)
                ]
            ];
        }
    }

    foreach($faqsWithoutCategory as $faq) {
        $faqSchema['mainEntity'][] = [
            '@type' => 'Question',
            'name' => strip_tags($faq->question),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => strip_tags($faq->answer)
            ]
        ];
    }
@endphp

{!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

</script>

@endpush
<div class="page-wrapper d-none d-md-block">
    <main class="main mt-3">
        <div class="page-content">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="faqHeadWrapper">
                            <h1>Frequently Asked Questions</h1>
                            <div class="faqspage-contact">
                                <p>Still need help?</p>
                                <a href="https://www.myntra.com/contactus" class="faqspage-linkButton">Contact us</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        <div class="row">
                            <div class="col-sm-3 bor-right pr-0">
                                <nav id="scrollspyNavbar" class="scrollspyNavbar">
                                    <ul class="nav-menu">
                                        @foreach($subcategories as $subcategory)
                                        <li>
                                            <a data-scroll="#subcat-{{ $subcategory->id }}" href="#subcat-{{ $subcategory->id }}" class="dot menu-link">
                                                <span>{{ $subcategory->sub_cat_name }}</span>
                                            </a>
                                        </li>
                                        @endforeach
                                        @if($faqsWithoutCategory->count())
                                        <li>
                                            <a data-scroll="#others" href="#others" class="dot menu-link">
                                                <span>Others</span>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>

                            <div class="col-sm-9">
                                @foreach($subcategories as $subcategory)

                                @if($subcategory->faqs->count())
                                <section id="subcat-{{ $subcategory->id }}" class="faqSection">
                                    <div class="faqSection-section">
                                        <h2>{{ $subcategory->sub_cat_name }}</h2>
                                        <div class="faqSection-link"><span>{{$subcategory->link_short_description}}</span>
                                            <div class="faqSection-links"><a href="{{$subcategory->link}}">{{$subcategory->link_name}}</a>
                                            </div>
                                        </div>
                                        @foreach($subcategory->faqs as $index => $faq)
                                        <div class="faqSection-query">
                                            <div class="accordion" id="accordion-{{ $subcategory->id }}">
                                                <div class="card">
                                                    <div class="card-header" id="heading-{{ $subcategory->id }}-{{ $index }}">
                                                        <h2 class="mb-0">
                                                            <button class="btn btn-link btn-block text-left collapsed faqSection-question"
                                                                type="button" data-toggle="collapse"
                                                                data-target="#collapse-{{ $subcategory->id }}-{{ $index }}"
                                                                aria-expanded="false"
                                                                aria-controls="collapse-{{ $subcategory->id }}-{{ $index }}">
                                                                {{ $faq->question }}
                                                            </button>
                                                        </h2>
                                                    </div>

                                                    <div id="collapse-{{ $subcategory->id }}-{{ $index }}" class="collapse"
                                                        aria-labelledby="heading-{{ $subcategory->id }}-{{ $index }}"
                                                        data-parent="#accordion-{{ $subcategory->id }}">
                                                        <div class="card-body">
                                                            {!! $faq->answer !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </section>
                                @endif
                                @endforeach

                                @if($faqsWithoutCategory->count())
                                <section id="others" class="faqSection">
                                    <div class="faqSection-section">
                                        <h2>Others</h2>
                                        @foreach($faqsWithoutCategory as $index => $faq)
                                        <div class="faqSection-query">
                                            <div class="accordion" id="accordion-others">
                                                <div class="card">
                                                    <div class="card-header" id="heading-others-{{ $index }}">
                                                        <h2 class="mb-0">
                                                            <button class="btn btn-link btn-block text-left collapsed faqSection-question"
                                                                type="button" data-toggle="collapse"
                                                                data-target="#collapse-others-{{ $index }}"
                                                                aria-expanded="false"
                                                                aria-controls="collapse-others-{{ $index }}">
                                                                {{ $faq->question }}
                                                            </button>
                                                        </h2>
                                                    </div>

                                                    <div id="collapse-others-{{ $index }}" class="collapse"
                                                        aria-labelledby="heading-others-{{ $index }}"
                                                        data-parent="#accordion-others">
                                                        <div class="card-body">
                                                            {!! $faq->answer !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </section>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<div class="page-wrapper d-md-none">
    <main class="main">
        <div class="page-content">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12">
                        <div class="faqHeadWrapper">
                            <h1>F.A.Q.</h1>
                            <div class="faqspage-contact">
                                <p>Still need help?</p>
                                <a href="https://www.myntra.com/contactus" class="faqspage-linkButton">Contact us</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-12 col-md-12 mt-0">
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                @foreach($subcategories as $subcategory)
                                @if($subcategory->faqs->count())
                                <section id="faq-subcat-{{ $subcategory->id }}" class="faqSection">
                                    <div class="faqSection-section">
                                        <h2>{{ $subcategory->sub_cat_name }}</h2>
                                        <div class="faqSection-link">
                                            <span>{{ $subcategory->link_short_description }}</span>
                                            <div class="faqSection-links">
                                                <a href="{{ $subcategory->link }}">{{ $subcategory->link_name }}</a>
                                            </div>
                                        </div>
                                        @foreach($subcategory->faqs as $index => $faq)
                                        <div class="faqSection-query">
                                            <div class="accordion" id="mob-accordion-{{ $subcategory->id }}">
                                                <div class="card">
                                                    <div class="card-header" id="mob-heading-{{ $subcategory->id }}-{{ $index }}">
                                                        <h2 class="mb-0">
                                                            <button class="btn btn-link btn-block text-left collapsed faqSection-question"
                                                                type="button"
                                                                data-toggle="collapse"
                                                                data-target="#mob-collapse-{{ $subcategory->id }}-{{ $index }}"
                                                                aria-expanded="false"
                                                                aria-controls="mob-collapse-{{ $subcategory->id }}-{{ $index }}">
                                                                {{ $faq->question }}
                                                            </button>
                                                        </h2>
                                                    </div>
                                                    <div id="mob-collapse-{{ $subcategory->id }}-{{ $index }}" class="collapse"
                                                        aria-labelledby="mob-heading-{{ $subcategory->id }}-{{ $index }}"
                                                        data-parent="#mob-accordion-{{ $subcategory->id }}">
                                                        <div class="card-body">
                                                            {!! $faq->answer !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </section>
                                @endif
                                @endforeach

                                @if($faqsWithoutCategory->count())
                                <section id="faq-others" class="faqSection">
                                    <div class="faqSection-section">
                                        <h2>Others</h2>
                                        @foreach($faqsWithoutCategory as $index => $faq)
                                        <div class="faqSection-query">
                                            <div class="accordion" id="mob-accordion-others">
                                                <div class="card">
                                                    <div class="card-header" id="mob-heading-others-{{ $index }}">
                                                        <h2 class="mb-0">
                                                            <button class="btn btn-link btn-block text-left collapsed faqSection-question"
                                                                type="button"
                                                                data-toggle="collapse"
                                                                data-target="#mob-collapse-others-{{ $index }}"
                                                                aria-expanded="false"
                                                                aria-controls="mob-collapse-others-{{ $index }}">
                                                                {{ $faq->question }}
                                                            </button>
                                                        </h2>
                                                    </div>
                                                    <div id="mob-collapse-others-{{ $index }}" class="collapse"
                                                        aria-labelledby="mob-heading-others-{{ $index }}"
                                                        data-parent="#mob-accordion-others">
                                                        <div class="card-body">
                                                            {!! $faq->answer !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </section>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    const myDiv = document.getElementById('scrollspyNavbar');
    const triggerPoint = 320;

    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        const divTop = myDiv.getBoundingClientRect().top;

        if (scrollY >= triggerPoint && divTop <= 0) {
            myDiv.classList.add('fixed');
        } else if (scrollY < triggerPoint) {
            myDiv.classList.remove('fixed');
        }
    });
</script>
<script>
    $(function() {
        var link = $('#scrollspyNavbar a.dot');

        link.on('click', function(e) {
            var target = $($(this).attr('href'));
            $('html, body').animate({
                scrollTop: target.offset().top - 200
            }, 600);
            link.removeClass('active');
            $(this).addClass('active');
            e.preventDefault();
        });

        $(window).on('scroll', function() {
            scrNav();
        });

        function scrNav() {
            var sTop = $(window).scrollTop();
            $('section').each(function() {
                var id = $(this).attr('id'),
                    offset = $(this).offset().top - 300,
                    height = $(this).height();
                if (sTop >= offset && sTop < offset + height) {
                    link.removeClass('active');
                    $('#scrollspyNavbar').find('[data-scroll="#' + id + '"]').addClass('active');
                }
            });
        }

        scrNav();
    });
</script>

@endsection