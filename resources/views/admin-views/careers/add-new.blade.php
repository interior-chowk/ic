@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Add New Career'))

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/select2.min.css') }}" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ asset('/public/assets/back-end/img/add-new-employee.png') }}" alt="">
                {{ \App\CPU\translate('Add_New_Job_Post') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form method="POST" action="{{ route('admin.employee.career.create') }}">
                    @csrf

                    <div class="row">
                        <!-- Job Info -->
                        <div class="form-group col-md-6">
                            <label>Job Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Graphic Designer"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Department</label>
                            <input type="text" name="department" class="form-control" placeholder="Design" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Employment Type</label>
                            <input type="text" name="employment_type" class="form-control" placeholder="Full Time"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Experience</label>
                            <input type="text" name="experience" class="form-control" placeholder="3-5 years" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Salary</label>
                            <input type="text" name="salary" class="form-control" placeholder="₹ 7-9 LPA" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control" placeholder="Noida" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Openings</label>
                            <input type="number" name="openings" class="form-control" placeholder="1" required>
                        </div>

                        <!-- Education -->
                        <div class="form-group col-12">
                            <label>Education Levels <small>(Comma Separated)</small></label>
                            <input type="text" name="education" class="form-control" placeholder="UG, PG, Diploma"
                                required>
                        </div>

                        <!-- Skills -->
                        <div class="form-group col-12">
                            <label>Skills <small>(Comma Separated)</small></label>
                            <input type="text" name="skills" class="form-control"
                                placeholder="Photoshop, Figma, Illustrator" required>
                        </div>

                        <!-- Job Description -->
                        <div class="form-group col-12">
                            <label>Job Description <small>(One per line)</small></label>
                            <textarea name="job_description" class="form-control" rows="5"
                                placeholder="Write each responsibility on a new line" required></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success mt-3">{{ \App\CPU\translate('Post_Job') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('public/assets/back-end/js/select2.min.js') }}"></script>
    <script>
        $(".js-example-theme-single").select2({
            theme: "classic"
        });
        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>
@endpush
