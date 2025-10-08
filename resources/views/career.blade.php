@extends('layouts.back-end.common_seller_1')

@section('content')
    @push('style')
        <link rel="stylesheet" href="{{ asset('website/assets/css/career.css') }}">
    @endpush

    <div class="page-wrapper">
        <main class="main mt-3">
            <div class="page-content">
                <div class="container">
                    <!-- Banner -->
                    <div class="row">
                        <div class="col-12">
                            <div class="banner-head">
                                <img src="{{ asset('website/assets/images/banners/career.webp') }}" alt="banner-3" />
                            </div>
                        </div>
                    </div>

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- No Careers -->
                    @if (!$careers->count())
                        <div class="alert alert-danger">There are no vacancies open</div>
                    @else
                        <!-- Career Listings -->
                        @foreach ($careers as $career)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="jobWrapper">
                                        <div class="row">
                                            <!-- Left Column -->
                                            <div class="col-sm-3 borRight">
                                                <div class="jobLeftWrapper">
                                                    <div class="jobHead jobtitlelogo">
                                                        <h5 class="jobtitle">{{ $career->title }}</h5>
                                                    </div>
                                                    <div class="jobDescWrapper">
                                                        <span class="jobDescHead">Department:</span>
                                                        <span class="jobDescData">{{ $career->department }}</span>
                                                    </div>
                                                    <div class="jobDescWrapper">
                                                        <span class="jobDescHead">Employment Type:</span>
                                                        <span class="jobDescData">{{ $career->employment_type }}</span>
                                                    </div>
                                                    <div class="jobDescWrapper">
                                                        <div class="jobDetail mr-4"><i class="fa fa-suitcase"></i> <span>{{ $career->experience }}</span></div>
                                                        <div class="jobDetail"><i class="fa fa-inr"></i> <span>{{ $career->salary }}</span></div>
                                                    </div>
                                                    <div class="jobDescWrapper">
                                                        <div class="jobDetail border-0"><i class="fa fa-map-marker"></i> <span>{{ $career->location }}</span></div>
                                                    </div>
                                                    <hr>
                                                    <div class="jobDescWrapper">
                                                        <div class="jobDetail mr-4 border-0">
                                                            <span class="jobDescHead mr-1">Openings:</span>
                                                            <span class="jobDescData">{{ $career->openings }}</span>
                                                        </div>
                                                        <div class="jobDetail">
                                                            <span class="jobDescHead mr-1">Applicants:</span>
                                                            <span class="jobDescData">{{ $career->applicants }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="jobDescWrapper">
                                                        <div class="jobDetail border-0">
                                                            <span class="jobDescHead">Posted:</span>
                                                            <span class="jobDescData">{{ $career->created_at->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Middle Column -->
                                            <div class="col-sm-3 borRight">
                                                <div class="jobLeftWrapper px-1">
                                                    <div class="jobHead"><h5>Education:</h5></div>
                                                    @foreach (json_decode($career->education) as $education)
                                                        <div class="jobDescWrapper">
                                                            <span class="jobDescHead education">{{ $education }}</span>
                                                        </div>
                                                    @endforeach
                                                    <hr>
                                                    <div class="mt-2">
                                                        <div class="jobHead"><h5>Skills Required:</h5></div>
                                                        <div class="skills">
                                                            @foreach (json_decode($career->skills) as $skill)
                                                                <span class="mr-1"><i class="fa fa-star-o mr-1"></i>{{ $skill }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Right Column -->
                                            <div class="col-sm-6">
                                                <div class="jobLeftWrapper pl-2">
                                                    <div class="jobHead"><h5>Job Description:</h5></div>
                                                    <div class="jobDescWrapper d-block">
                                                        <p>Responsibilities:</p>
                                                        <ul>
                                                            @foreach (json_decode($career->job_description) as $desc)
                                                                <li>{{ $desc }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button type="button" class="btn btnOrdCan btnOrdCan2 mt-2" data-toggle="modal" data-target="#carrierApplyModal-{{ $career->id }}">
                                                Apply Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="carrierApplyModal-{{ $career->id }}" tabindex="-1" role="dialog" aria-labelledby="carrierApplyModalLabel-{{ $career->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h4 id="carrierApplyModalLabel-{{ $career->id }}">Applying for {{ $career->title }}</h4>
                                                </div>
                                                <div class="col-12">
                                                    <div class="applyForm">
                                                        <form method="POST" action="{{ route('career.apply', $career->id) }}" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="career_id" value="{{ $career->id }}">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Full Name</label>
                                                                    <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" required>
                                                                    @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">City</label>
                                                                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" required>
                                                                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Phone Number</label>
                                                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                                                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Email</label>
                                                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Experience <span>(years-months)</span></label>
                                                                    <input type="text" name="experience" class="form-control @error('experience') is-invalid @enderror" value="{{ old('experience') }}">
                                                                    @error('experience')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Portfolio Links</label>
                                                                    <textarea name="portfolio_links" class="form-control @error('portfolio_links') is-invalid @enderror" rows="3">{{ old('portfolio_links') }}</textarea>
                                                                    @error('portfolio_links')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Upload Resume/Image (Max: 100KB)</label>
                                                                    <input type="file" name="resume" class="form-control @error('resume') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf" required>
                                                                    @error('resume')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                                </div>

                                                                @php($recaptcha = \App\CPU\Helpers::get_business_settings('recaptcha'))
                                                                @if (isset($recaptcha) && $recaptcha['status'] == 1)
                                                                    <div id="recaptcha_element_{{ $career->id }}" class="w-100 mt-2" data-type="image"></div>
                                                                @else
                                                                    <div class="row mt-2">
                                                                        <div class="col-6 pr-0">
                                                                            <input type="text" class="form-control" name="default_captcha_value" placeholder="{{ \App\CPU\translate('Enter captcha value') }}" autocomplete="off">
                                                                        </div>
                                                                        <div class="col-6 input-icons">
                                                                            <a onclick="re_captcha();" class="d-flex align-items-center">
                                                                                <img src="{{ URL('/admin/auth/code/captcha/1') }}" class="rounded __h-40" id="default_recaptcha_id">
                                                                                <i class="tio-refresh position-relative cursor-pointer p-2"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                <div class="col-12 d-flex justify-content-between mt-3">
                                                                    <button type="reset" class="btn btn-secondary">Clear</button>
                                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <!-- Company Info -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="aboutComp">
                                <h1>About the Company</h1>
                                <p>{!! $seo->content ?? '' !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Recaptcha Scripts -->
    @if (isset($recaptcha) && $recaptcha['status'] == 1)
        <script type="text/javascript">
            var onloadCallback = function () {
                @foreach($careers as $career)
                    grecaptcha.render('recaptcha_element_{{ $career->id }}', {
                        'sitekey': '{{ $recaptcha['site_key'] }}'
                    });
                @endforeach
            };
        </script>
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
    @else
        <script>
            function re_captcha() {
                var url = "{{ URL('/admin/auth/code/captcha') }}" + "/" + Math.random();
                document.getElementById('default_recaptcha_id').src = url;
            }
        </script>
    @endif
@endsection
