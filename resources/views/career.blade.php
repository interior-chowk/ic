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
                    <div class="row">
                        <div class="col-12">
                            <div class="banner-head">
                                <img src="{{ asset('public/website/assets/images/banners/career.webp') }}" alt="banner-3" />
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
                                                        <div class="jobDetail mr-4"><i class="fa fa-suitcase"></i>
                                                            <span>{{ $career->experience }}</span>
                                                        </div>
                                                        <div class="jobDetail"><i class="fa fa-inr"></i>
                                                            <span>{{ $career->salary }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="jobDescWrapper">
                                                        <div class="jobDetail border-0"><i class="fa fa-map-marker"></i>
                                                            <span>{{ $career->location }}</span>
                                                        </div>
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
                                                            <span
                                                                class="jobDescData">{{ $career->created_at->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Middle Column -->
                                            <div class="col-sm-3 borRight">
                                                <div class="jobLeftWrapper px-1">
                                                    <div class="jobHead">
                                                        <h5>Education:</h5>
                                                    </div>
                                                    @foreach (json_decode($career->education) as $education)
                                                        <div class="jobDescWrapper">
                                                            <span class="jobDescHead education">{{ $education }}</span>
                                                        </div>
                                                    @endforeach
                                                    <hr>
                                                    <div class="mt-2">
                                                        <div class="jobHead">
                                                            <h5>Skills Required:</h5>
                                                        </div>
                                                        <div class="skills">
                                                            @foreach (json_decode($career->skills) as $skill)
                                                                <span class="mr-1"><i
                                                                        class="fa fa-star-o mr-1"></i>{{ $skill }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Right Column -->
                                            <div class="col-sm-6">
                                                <div class="jobLeftWrapper pl-2">
                                                    <div class="jobHead">
                                                        <h5>Job Description:</h5>
                                                    </div>
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
                                            <button type="button" class="btn btnOrdCan btnOrdCan2 mt-2 mr-4 mb-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#carrierApplyModal-{{ $career->id }}">
                                                Apply Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade carrierAppModal" id="carrierApplyModal-{{ $career->id }}"
                                tabindex="-1" role="dialog" aria-labelledby="carrierApplyModalLabel-{{ $career->id }}"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">

                                            <div class="row">
                                                <div class="col-12">
                                                    <h4 id="carrierApplyModalLabel-{{ $career->id }}"
                                                        style="text-align:justify;">Applying for
                                                        {{ $career->title }}</h4>
                                                    <button type="button" class="close modalCloseBtn"
                                                        data-bs-dismiss="modal" aria-label="Close"
                                                        style="text-align: right;margin:-18px 20px 0px;font-size:xx-large;"><!-- Close button -->
                                                        <span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="col-12">
                                                    <div class="applyForm">
                                                        <form method="POST"
                                                            action="{{ route('career.apply', $career->id) }}"
                                                            enctype="multipart/form-data" novalidate>
                                                            @csrf
                                                            <input type="hidden" name="career_id"
                                                                value="{{ $career->id }}">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Full Name</label>
                                                                    <input type="text" name="full_name"
                                                                        class="form-control" required
                                                                        pattern="^[A-Za-z\s]{3,50}$"
                                                                        title="Only letters and spaces, 3–50 characters">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">City</label>
                                                                    <input type="text" name="city"
                                                                        class="form-control" required
                                                                        pattern="^[A-Za-z\s]{2,50}$"
                                                                        title="Only letters and spaces">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Phone Number</label>
                                                                    <input type="tel" name="phone"
                                                                        class="form-control" required
                                                                        pattern="^[6-9]\d{9}$"
                                                                        title="Enter a valid 10-digit Indian phone number">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Email</label>
                                                                    <input type="email" name="email"
                                                                        class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Experience
                                                                        <span>(years-months)</span></label>
                                                                    <input type="text" name="experience"
                                                                        class="form-control" pattern="^\d{1,2}-\d{1,2}$"
                                                                        title="Format: years-months (e.g. 2-6)">
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Portfolio Links</label>
                                                                    <textarea name="portfolio_links" class="form-control" maxlength="500" placeholder="Paste links separated by commas"></textarea>
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Upload Resume/Image (Max:
                                                                        100KB)</label>
                                                                    <input type="file" name="resume"
                                                                        class="form-control"
                                                                        accept=".jpg,.jpeg,.png,.webp,.doc,.docx,.pdf"
                                                                        required>
                                                                </div>

                                                                @php($recaptcha = \App\CPU\Helpers::get_business_settings('recaptcha'))
                                                                @if (isset($recaptcha) && $recaptcha['status'] == 1)
                                                                    <div id="recaptcha_element_{{ $career->id }}"
                                                                        class="w-100 mt-2" data-type="image"></div>
                                                                @else
                                                                    <div class="row mt-2">
                                                                        <div class="col-6 pr-0">
                                                                            <input type="text" class="form-control"
                                                                                name="default_captcha_value"
                                                                                placeholder="{{ \App\CPU\translate('Enter captcha value') }}"
                                                                                autocomplete="off">
                                                                        </div>
                                                                        <div class="col-6 input-icons">
                                                                            <a onclick="re_captcha();"
                                                                                class="d-flex align-items-center">
                                                                                <img src="{{ URL('/admin/auth/code/captcha/1') }}"
                                                                                    class="rounded __h-40"
                                                                                    id="default_recaptcha_id">
                                                                                <i
                                                                                    class="tio-refresh position-relative cursor-pointer p-2"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                <div class="col-12 d-flex justify-content-between mt-3">
                                                                    <button type="reset"
                                                                        class="btn btn-secondary">Clear</button>
                                                                    <button type="submit"
                                                                        class="btn btn-primary">Submit</button>
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
            var onloadCallback = function() {
                @foreach ($careers as $career)
                    var widgetId = grecaptcha.render('recaptcha_element_{{ $career->id }}', {
                        'sitekey': '{{ $recaptcha['site_key'] }}'
                    });
                    document.getElementById('recaptcha_element_{{ $career->id }}')
                        .setAttribute('data-widget-id', widgetId);
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

    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('form').forEach(form => {
                    const nameInput = form.querySelector('input[name="full_name"]');
                    const phoneInput = form.querySelector('input[name="phone"]');
                    const emailInput = form.querySelector('input[name="email"]');
                    const fileInput = form.querySelector('input[name="resume"]');

                    // Helper to show error message
                    function showError(input, message) {
                        let errorElem = input.nextElementSibling;
                        if (!errorElem || !errorElem.classList.contains('text-danger')) {
                            errorElem = document.createElement('div');
                            errorElem.className = 'text-danger mt-1';
                            input.parentNode.appendChild(errorElem);
                        }
                        errorElem.innerText = message;
                        input.classList.add('is-invalid');
                    }

                    // Helper to clear error
                    function clearError(input) {
                        let errorElem = input.nextElementSibling;
                        if (errorElem && errorElem.classList.contains('text-danger')) {
                            errorElem.innerText = '';
                        }
                        input.classList.remove('is-invalid');
                    }

                    // Real-time keyboard restrictions
                    if (nameInput) {
                        nameInput.addEventListener('keypress', (e) => {
                            if (!/[a-zA-Z\s]/.test(e.key)) e.preventDefault();
                        });
                    }

                    if (phoneInput) {
                        phoneInput.addEventListener('keypress', (e) => {
                            if (!/[0-9]/.test(e.key) || phoneInput.value.length >= 10) e
                                .preventDefault();
                        });
                    }

                    // On form submit
                    form.addEventListener('submit', (e) => {
                        let valid = true;

                        // Full Name validation
                        if (nameInput) {
                            clearError(nameInput);
                            if (!/^[A-Za-z\s]{3,50}$/.test(nameInput.value)) {
                                showError(nameInput, 'Full Name must be 3-50 letters only.');
                                valid = false;
                            }
                        }

                        // Phone validation
                        if (phoneInput) {
                            clearError(phoneInput);
                            if (!/^[6-9]\d{9}$/.test(phoneInput.value)) {
                                showError(phoneInput, 'Phone must start with 6-9 and be 10 digits.');
                                valid = false;
                            }
                        }

                        // Email validation
                        if (emailInput) {
                            clearError(emailInput);
                            if (!/^\S+@\S+\.\S+$/.test(emailInput.value)) {
                                showError(emailInput, 'Please enter a valid email.');
                                valid = false;
                            }
                        }

                        // File validation
                        if (fileInput && fileInput.files.length > 0) {
                            clearError(fileInput);
                            const allowedTypes = [
                                'image/jpeg', 'image/jpg', 'image/png', 'image/webp',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/pdf'
                            ];
                            const file = fileInput.files[0];
                            if (!allowedTypes.includes(file.type)) {
                                showError(fileInput,
                                    'Only jpeg, jpg, png, webp, doc, docx, pdf files are allowed.');
                                valid = false;
                            } else if (file.size > 100 * 1024) {
                                showError(fileInput, 'File size must be less than 100KB.');
                                valid = false;
                            }
                        }

                        // reCAPTCHA validation
                        const recaptchaElement = form.querySelector('[id^="recaptcha_element_"]');
                        if (recaptchaElement && typeof grecaptcha !== 'undefined') {
                            const widgetId = recaptchaElement.getAttribute('data-widget-id');
                            const response = grecaptcha.getResponse(widgetId);
                            if (!response) {
                                alert("Please complete the reCAPTCHA.");
                                valid = false;
                            }
                        }

                        if (!valid) {
                            e.preventDefault();
                            e.stopPropagation();
                            form.classList.add('was-validated');
                        }
                    });
                });
            });
        </script>
    @endpush

@endsection
