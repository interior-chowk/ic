@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Edit Career'))

@push('css_or_js')
<link href="{{ asset('assets/back-end/css/select2.min.css') }}" rel="stylesheet" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Title -->
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img src="{{ asset('/public/assets/back-end/img/add-new-employee.png') }}" alt="">
            {{ \App\CPU\translate('Edit_Job') }}
        </h2>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="{{ route('admin.employee.career.update', $career->id) }}">
                @csrf

                @php
                    $education = is_array(json_decode($career->education, true)) ? implode(', ', json_decode($career->education, true)) : '';
                    $skills = is_array(json_decode($career->skills, true)) ? implode(', ', json_decode($career->skills, true)) : '';
                    $job_description = is_array(json_decode($career->job_description, true)) ? implode("\n", json_decode($career->job_description, true)) : '';
                @endphp

                <div class="row">
                    <!-- Job Info -->
                    <div class="form-group col-md-6">
                        <label>Job Title</label>
                        <input type="text" name="title" class="form-control" value="{{ $career->title }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Department</label>
                        <input type="text" name="department" class="form-control" value="{{ $career->department }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Employment Type</label>
                        <input type="text" name="employment_type" class="form-control" value="{{ $career->employment_type }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Experience</label>
                        <input type="text" name="experience" class="form-control" value="{{ $career->experience }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Salary</label>
                        <input type="text" name="salary" class="form-control" value="{{ $career->salary }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Location</label>
                        <input type="text" name="location" class="form-control" value="{{ $career->location }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Openings</label>
                        <input type="number" name="openings" class="form-control" value="{{ $career->openings }}" required>
                    </div>

                    <!-- Education (Single field, comma separated) -->
                    <div class="form-group col-12">
                        <label>Education Levels <small>(Comma Separated)</small></label>
                        <input type="text" name="education" class="form-control" value="{{ $education }}" placeholder="UG, PG, Diploma" required>
                    </div>

                    <!-- Skills -->
                    <div class="form-group col-12">
                        <label>Skills <small>(Comma Separated)</small></label>
                        <input type="text" name="skills" class="form-control" value="{{ $skills }}" placeholder="Figma, Photoshop, ..." required>
                    </div>

                    <!-- Job Description -->
                    <div class="form-group col-12">
                        <label>Job Description <small>(One per line)</small></label>
                        <textarea name="job_description" class="form-control" rows="5" required>{{ $job_description }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn--primary mt-3">{{ \App\CPU\translate('Update_Job') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/back-end/js/select2.min.js') }}"></script>
<script>
    $(".js-example-theme-single").select2({ theme: "classic" });
    $(".js-example-responsive").select2({ width: 'resolve' });
</script>
@endpush
