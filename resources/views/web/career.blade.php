@extends('layouts.back-end.common_seller_1')

@section('content')
    @push('style')
        <link rel="stylesheet" href="{{ asset('public/website/assets/css/career.css') }}">
    @endpush

    <div class="page-wrapper">
        <main class="main mt-3">
            <div class="page-content">
                <div class="container">

                    <!-- Banner -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="banner-head">
                                <img src="{{ asset('public/website/assets/images/banners/career.webp') }}"
                                    alt="Career Banner" class="img-fluid" />
                            </div>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <div id="alertBox">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <!-- Career Listings -->
                    @if (!$careers->count())
                        <div class="alert alert-warning">There are no vacancies open currently.</div>
                    @else
                        @foreach ($careers as $career)
                            <div class="jobWrapper mb-4">
                                <div class="row">
                                    <div class="col-md-3 borRight">
                                        <h5>{{ $career->title }}</h5>
                                        <div><strong>Department:</strong> {{ $career->department }}</div>
                                        <div><strong>Type:</strong> {{ $career->employment_type }}</div>
                                        <div><strong>Experience:</strong> {{ $career->experience }}</div>
                                        <div><strong>Salary:</strong> {{ $career->salary }}</div>
                                        <div><strong>Location:</strong> {{ $career->location }}</div>
                                        <div><strong>Openings:</strong> {{ $career->openings }}</div>
                                        <div><strong>Applicants:</strong> {{ $career->applicants }}</div>
                                    </div>

                                    <div class="col-md-3 borRight">
                                        <h6>Education:</h6>
                                        <ul>
                                            @foreach (json_decode($career->education) as $edu)
                                                <li>{{ $edu }}</li>
                                            @endforeach
                                        </ul>
                                        <h6>Skills:</h6>
                                        <ul>
                                            @foreach (json_decode($career->skills) as $skill)
                                                <li>{{ $skill }}</li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="col-md-6">
                                        <h6>Job Description:</h6>
                                        <ul>
                                            @foreach (json_decode($career->job_description) as $desc)
                                                <li>{{ $desc }}</li>
                                            @endforeach
                                        </ul>
                                        <div class="text-right">
                                            <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal"
                                                data-bs-target="#careerModal-{{ $career->id }}">Apply Now</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="careerModal-{{ $career->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5>Apply for {{ $career->title }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="careerForm" method="POST"
                                                action="{{ route('career.apply', $career->id) }}"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="career_id" value="{{ $career->id }}">

                                                <div class="mb-3">
                                                    <label class="form-label">Full Name *</label>
                                                    <input type="text" name="full_name" class="form-control">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">City *</label>
                                                    <input type="text" name="city" class="form-control">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Phone *</label>
                                                    <input type="text" name="phone" class="form-control"
                                                        maxlength="10">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Email *</label>
                                                    <input type="email" name="email" class="form-control">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Experience (years-months)</label>
                                                    <input type="text" name="experience" class="form-control"
                                                        placeholder="e.g. 2-6">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Portfolio Links</label>
                                                    <textarea name="portfolio_links" class="form-control" maxlength="500" placeholder="Comma-separated links"></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Resume (jpg, png, pdf, doc, docx, webp)
                                                        *</label>
                                                    <input type="file" name="resume" class="form-control">
                                                </div>

                                                <div class="d-flex justify-content-between mt-3">
                                                    <button type="reset" class="btn btn-secondary">Clear</button>
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <!-- About Company -->
                    <div class="aboutComp mt-4">
                        <h2>About the Company</h2>
                        <p>{!! $seo->content ?? '' !!}</p>
                    </div>

                </div>
            </div>
        </main>
    </div>
@endsection
