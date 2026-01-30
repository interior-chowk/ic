@extends('layouts.back-end.common_seller_1')

@section('content')
    <style>
        .city-tag {
            background: #e9ecef;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tick-icon {
            position: absolute;
            right: 21px;
            top: 25%;
            transform: translateY(-50%);
            display: none;
            font-size: 18px;
        }


        .city-tag .remove {
            cursor: pointer;
            font-weight: bold;
        }

        .service-tag {
            background: #e9ecef;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .service-tag .remove-service {
            cursor: pointer;
            font-weight: bold;
        }

        .text-danger {
            font-size: 15px;
            margin-left: -148px;
        }
    </style>
    <style>
        /* Main container styling */
        #aadharCaptchafields {
            display: flex;
            padding: 0px 0px 0px 20px;
            gap: 14px;
        }

        /* Captcha image and refresh button container */
        #aadharCaptchafields .col-4,
        #aadharCaptchafields .col-md-3 {
            display: flex;
            align-items: center;
            /* justify-content: center; */
        }

        /* Captcha image styling */
        #captcha-image {
            width: 80px;
            max-width: 150px;
            height: auto;
            border-radius: 8px;
            border: 1px solid #ced4da;
            object-fit: cover;
        }

        /* Refresh button styling */
        #refresh-captcha {
            background-color: #17a2b8;
            color: #fff;
            border: none;
            padding: 10px 7px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #refresh-captcha i {
            font-size: 18px;
        }

        #refresh-captcha:hover {
            background-color: #138496;
        }

        /* OTP and other inputs */
        #aadharCaptchafields input.form-control,
        #aadharCaptchafields textarea.form-control {
            width: 135px;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }

        #aadharCaptchafields input.form-control:focus,
        #aadharCaptchafields textarea.form-control:focus {
            border-color: #17a2b8;
            box-shadow: 0 0 8px rgba(23, 162, 184, 0.3);
            outline: none;
        }

        /* OTP field styling
                                                                                                    #otpField {
                                                                                                        letter-spacing: 5px;
                                                                                                        text-align: center;
                                                                                                        max-width: 150px;
                                                                                                        margin: 0 auto;
                                                                                                    } */

        /* Buttons styling */
        #getOtpBtn,
        #verifyAadhaarOtpBtn {
            background: linear-gradient(135deg, #28a745, #218838);
            color: #fff;
            border: none;
            /* padding: 12px 20px; */
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #getOtpBtn:hover,
        #verifyAadhaarOtpBtn:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
        }

        /* Full width inputs */
        #fullNameField {
            width: 100%;
        }

        /* Responsive layout */
        @media (max-width: 768px) {
            #aadharCaptchafields {
                flex-direction: column;
                gap: 12px;
                padding: 20px;
            }

            #otpField {
                margin-top: 10px;
            }
        }

        /* Optional: subtle hover effect for inputs */
        #aadharCaptchafields input.form-control:hover,
        #aadharCaptchafields textarea.form-control:hover {
            border-color: #80bdff;
        }
    </style>

    <div class="page-wrapper">
        <main class="main">
            <div class="page-content pb-0 position-relative">
                <div class="serBgImg"></div>
                <div class="oval"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-12">

                            <form id="msform" class="msform" method="POST" action="{{ route('service.register') }}"
                                enctype="multipart/form-data">
                                @csrf

                                <ul id="progressbar">
                                    <li class="active"><span>Signup Details</span></li>
                                    <li><span>Personal/business Details</span></li>
                                    <li><span>Contact Information</span></li>
                                </ul>

                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                                        <div class="serviceRegImgWrapper">
                                            <img src="{{ asset('public/website/new/assets/images/service-form.png') }}"
                                                class="img-fluid" alt="service-form">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                                        <fieldset class="serRegFormWrap firstformset">
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <h2 class="fs-title">I am,</h2>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <select class="form-control form-select" name="role"
                                                        aria-label="Default select example">
                                                        <option selected>Select Profession<sup>*</sup></option>
                                                        <option value="5">Interior Designer</option>
                                                        <option value="4">Architect</option>
                                                        <option value="3">Contractor</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12 position-relative"
                                                    style="height:60px;">
                                                    <input type="text" name="phone" id="mobileNumber"
                                                        class="form-control number-only" placeholder="Mobile Number*"
                                                        maxlength="10" />
                                                    <i class="fa fa-check-circle text-success tick-icon"
                                                        id="mobileTick"></i>
                                                    <small class="text-danger" id="mobileError"></small>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-5">
                                                    <input type="text" name="referral_code" class="form-control"
                                                        placeholder="Referal Code" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-7 position-relative">

                                                    <input type="text" id="otp" name="otp"
                                                        class="number-only form-control" placeholder="Enter OTP">
                                                    <i class="fa fa-check-circle text-success tick-icon" id="otpTick"></i>
                                                    <p class="text-right">
                                                        <button type="button" id="resendOtp" class="btn btn-link" disabled
                                                            style="display:none">
                                                            Resend OTP
                                                        </button>
                                                        <span class="otpTimer">01:00</span>
                                                    </p>

                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <input type="email" name="email" id="email" class="form-control"
                                                        placeholder="Email*" />
                                                    <small class="text-danger" id="emailError"></small>

                                                </div>
                                            </div>
                                            <input type="button" name="previous" class="previous back-button"
                                                value="Go Back" disabled />
                                            <input type="button" name="verify" id="verifyBtn" class="next action-button"
                                                value="Verify" disabled />
                                        </fieldset>

                                        <fieldset class="serRegFormWrap">
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <select class="form-control form-select" name="business_type"
                                                        id="businessType" aria-label="Default select example">
                                                        <option selected>Business Type<sup>*</sup></option>
                                                        <option value="1">Proprietorship</option>
                                                        <option value="2">Partnership</option>
                                                        <option value="3">LLP</option>
                                                        <option value="4">Private Limited</option>
                                                        <option value="5">Limited</option>
                                                        <option value="6">Individual</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-6">
                                                    <input type="text" name="gstin" class="form-control"
                                                        placeholder="GSTIN" id="gstinField" required />
                                                    <input type="hidden" name="state" id="stateField">
                                                    <input type="hidden" name="city" id="cityField">
                                                    <input type="hidden" name="pincode" id="pincodeField">
                                                    <input type="hidden" name="address" id="addressField">
                                                </div>

                                                <div class="col-12 col-sm-12 col-md-6">
                                                    <input type="text" name="pan" class="form-control"
                                                        placeholder="PAN" id="panField" required />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <input type="text" name="business_name" class="form-control"
                                                        placeholder="Business Name" id="businessNameField" required />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-6">
                                                    <input type="text" name="aadhaar_number" id="aadhaarField"
                                                        class="form-control number-only" placeholder="Aadhaar Card Number"
                                                        maxlength="12">
                                                </div>

                                                <div id="aadharCaptchafields" class="row g-3">
                                                    <div class="">
                                                        <img src="data:image/png;base64,{{ $captcha }}"
                                                            id="captcha-image" class="img-fluid">
                                                    </div>
                                                    <div class="">
                                                        <button type="button" id="refresh-captcha" class="btn-light">
                                                            <i class="fas fa-sync"></i>
                                                        </button>
                                                    </div>
                                                    <div class="">
                                                        <input type="text" name="captcha" id="aadhaarCaptcha"
                                                            class="form-control" placeholder="Enter Captcha">
                                                        <input type="hidden" name="sessionId"
                                                            value="{{ $session_id }}" id="aadhaarSessionId">
                                                    </div>

                                                    <div class="">
                                                        <input type="text" id="otpField"
                                                            class="form-control number-only" placeholder="Enter OTP"
                                                            maxlength="6">
                                                    </div>
                                                    <input type="hidden" name="aadhaar_gender" id="aadhaarGender">
                                                    <input type="hidden" name="aadhaar_verified" id="aadhaarVerified">
                                                    <input type="hidden" name="pincode" id="aadhaarPincode">
                                                    <input type="hidden" name="state" id="aadhaarState">
                                                    <input type="hidden" name="father" id="aadhaarFatherName">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <input type="text" name="name" class="form-control"
                                                        placeholder="Full Name as per Aadhaar" id="fullNameField" required
                                                        readonly>
                                                </div>
                                                <div class="col-12 col-md-6" id="dobField">
                                                    <input type="text" id="aadhaarDob" name="dob"
                                                        class="form-control number-only" placeholder="Enter DOB"
                                                        maxlength="6" readonly>
                                                </div>
                                                <div class="col-12 col-md-12" id="aadharaddressField">
                                                    <textarea id="aadhaaraddress" name="address" class="form-control" placeholder="Enter Address" readonly></textarea>
                                                </div>



                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group position-relative"
                                                        style="margin-bottom:unset !important;">
                                                        <div class="form-control d-flex justify-content-between align-items-center"
                                                            id="citySelectBox" style="cursor:pointer;">
                                                            <span id="cityPlaceholder">Select Cities / Areas of
                                                                Working</span>
                                                            <span>▼</span>
                                                        </div>

                                                        <div class="border rounded mt-1 p-2 bg-white position-absolute w-100 d-none"
                                                            id="cityDropdown"
                                                            style="max-height:200px; overflow-y:auto;text-align:justify; z-index:999;">

                                                            @php $cities = DB::table('cities')->get(); @endphp

                                                            @foreach ($cities as $city)
                                                                <div class="form-check" style="width: 48%; float: left;">
                                                                    <input class="form-check-input city-checkbox"
                                                                        type="checkbox" name="city[]"
                                                                        value="{{ $city->name }}"
                                                                        data-name="{{ $city->name }}"
                                                                        id="city_{{ $city->id }}">

                                                                    <label class="form-check-label"
                                                                        for="city_{{ $city->id }}">
                                                                        {{ $city->name }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="mt-2 d-flex flex-wrap gap-2" id="selectedCities">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-12 col-md-6">
                                                    <div class="form-control d-flex justify-content-between align-items-center"
                                                        id="serviceSelectBox" style="cursor:pointer;">
                                                        <span id="servicePlaceholder">Services Provided</span>
                                                        <span>▼</span>
                                                    </div>
                                                    <div class="border rounded mt-1 p-2 bg-white position-absolute w-100 d-none"
                                                        id="serviceDropdown"
                                                        style="max-height:200px; overflow-y:auto;text-align:justify; z-index:999;">

                                                        @php
                                                            $services = DB::table('service_categories')->get();
                                                        @endphp
                                                        @foreach ($services as $service)
                                                            <div class="form-check" style="width: 48%; float: left;">
                                                                <input class="form-check-input service-checkbox"
                                                                    type="checkbox" name="serviceTypeId[]"
                                                                    value="{{ $service->id }}"
                                                                    data-name="{{ $service->name }}"
                                                                    id="service_{{ $service->id }}">

                                                                <label class="form-check-label"
                                                                    for="service_{{ $service->id }}">
                                                                    {{ $service->name }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="mt-2 d-flex flex-wrap gap-2" id="selectedservices">
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-12 col-md-6">
                                                    <select class="form-control form-select" name="total_project_done"
                                                        aria-label="Default select example">
                                                        <option selected>No. of Projects Done</option>
                                                        <option value="0-10">0-10</option>
                                                        <option value="11-30">11-30</option>
                                                        <option value="31-50">31-50</option>
                                                        <option value="50+">50+</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-6">
                                                    <select class="form-control form-select" name="working_since"
                                                        aria-label="Working Since">
                                                        <option value="">Working Since</option>

                                                        @for ($year = 1970; $year <= date('Y'); $year++)
                                                            <option value="{{ $year }}">{{ $year }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-6">
                                                    <select class="form-control form-select" name="team_strength"
                                                        aria-label="Default select example">
                                                        <option selected>Team Strength</option>
                                                        <option value="0-10">0-10</option>
                                                        <option value="11-20">11-20</option>
                                                        <option value="21-30">21-30</option>
                                                        <option value="31-40">31-40</option>
                                                        <option value="41-50">41-50</option>
                                                        <option value="50+">50+</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <textarea name="about_company" rows="4" class="form-control" placeholder="About Company"></textarea>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <textarea name="achievements" rows="4" class="form-control" placeholder="Achievements"></textarea>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-5">
                                                    <div class="profile-wrapper">
                                                        <img id="profilePreview" src="https://via.placeholder.com/150"
                                                            class="profilePreview" alt="">
                                                        <label class="upload-btn">
                                                            <img src="{{ asset('public/website/new/assets/images/add.png') }}"
                                                                class="img-fluid selLogo" alt="logo">
                                                            <input type="file" id="profileInput" name="profile_image"
                                                                accept="image/*">
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-7">
                                                    <div class="ser-reg-banner-wrapper">
                                                        <img id="banPreview" src="https://via.placeholder.com/150"
                                                            class="banPreview" alt="">

                                                        <label class="upload-btn">
                                                            <img src="{{ asset('public/website/new/assets/images/add.png') }}"
                                                                class="img-fluid selLogo" alt="logo">
                                                            <input type="file" id="banInput" name="banner_image"
                                                                accept="image/*">
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="button" name="previous" class="previous back-button"
                                                value="Go Back" />
                                            <input type="button" name="next" class="next action-button"
                                                value="Next" />
                                        </fieldset>

                                        <fieldset class="serRegFormWrap">
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <input type="text" name="whatsapp" class="form-control"
                                                        placeholder="Whatsapp (Optional)" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <input type="text" name="website" class="form-control"
                                                        placeholder="Website (Optional)" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <input type="text" name="insta_link" class="form-control"
                                                        placeholder="Instagram Link (Optional)" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <input type="email" name="facebook_link" class="form-control"
                                                        placeholder="Facebook Link (Optional)" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <input type="email" name="youtube_link" class="form-control"
                                                        placeholder="Youtube Link (Optional)" />
                                                </div>
                                            </div>
                                            <input type="button" name="previous" class="previous back-button"
                                                value="Go Back" />
                                            <button type="submit" class="submit action-button">Submit</button>
                                        </fieldset>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                    {{-- <div class="mobmsform mt-3">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-12">
                                <ul class="stepper">
                                    <li class="step active" data-step="1">
                                        <span class="circle">1</span>
                                        <span class="label">Signup Details</span>
                                    </li>
                                    <div class="step-content active" data-step="1">
                                        <div class="serRegFormWrap">
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <h2 class="fs-title">I am,</h2>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="select_profession container">
                                                        <label>
                                                            <input type="radio" name="professionSel" class="d-none"
                                                                checked>
                                                            <span class="text-center d-block py-3">Architect</span>
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="professionSel" class="d-none">
                                                            <span class="text-center d-block py-3">Interior
                                                                Designer</span>
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="professionSel" class="d-none">
                                                            <span class="text-center d-block py-3">Contractor</span>
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="professionSel" class="d-none">
                                                            <span class="text-center d-block py-3">Worker</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <span class="inpLabel">Mobile Number</span>
                                                    <input type="text" name="mobileNumber" class="form-control"
                                                        placeholder="" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <span class="inpLabel">Enter OTP</span>
                                                    <input type="text" name="otp" class="form-control optInput"
                                                        placeholder="" />
                                                    <p class="text-right">
                                                        <a href="">Rsend OTP ? <span>00:00</span></a>
                                                    </p>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <span class="inpLabel">Referal Code (optional)</span>
                                                    <input type="text" name="refCode" class="form-control"
                                                        placeholder="" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <span class="inpLabel">Email Address</span>
                                                    <input type="email" name="email" class="form-control"
                                                        placeholder="" />
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn action-button next">Next</button>
                                    </div>

                                    <li class="step" data-step="2">
                                        <span class="circle">2</span>
                                        <span class="label">Personal Details</span>
                                    </li>
                                    <div class="step-content" data-step="2">
                                        <div class="serRegFormWrap">
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="profile-wrapper">
                                                        <img id="profilePreview" src="https://via.placeholder.com/150"
                                                            class="profilePreview" alt="">

                                                        <label class="upload-btn">
                                                            <img src="{{ asset('public/website/new/assets/images/add.png') }}"
                                                                class="img-fluid selLogo" alt="logo">
                                                            <input type="file" id="profileInput" accept="image/*">
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="ser-reg-banner-wrapper">
                                                        <img id="banPreview" src="https://via.placeholder.com/150"
                                                            class="banPreview" alt="">

                                                        <label class="upload-btn">
                                                            <img src="{{ asset('public/website/new/assets/images/add.png') }}"
                                                                class="img-fluid selLogo" alt="logo">
                                                            <input type="file" id="banInput" accept="image/*">
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <span class="inpLabel">Full Name</span>
                                                    <input type="text" name="fullName" class="form-control"
                                                        placeholder="" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="calendar-main">
                                                        <div class="calendar-drop-main form-control"
                                                            id="calendar-backdrop">
                                                            <span class="calendar-drop-text" id="selected-date-text">Date
                                                                of birth as per Aadhaar
                                                                Card</span>
                                                            <img src="{{ asset('public/website/new/assets/images/calender.png') }}"
                                                                class="img-fluid calImg" alt="calender">
                                                        </div>
                                                        <div class="calendar-wrapper" id="calendar-wrapper">
                                                            <h4 class="mb-0 text-left pt-1 px-4">Select Date</h4>
                                                            <div class="calendar-header">
                                                                <div class="calendar-select-main">
                                                                    <div class="calendar-select-wrapper">
                                                                        <div class="calendar-select">
                                                                            <div id="selected-month"
                                                                                onclick="toggleDropdownMonth()">
                                                                                Select month
                                                                            </div>
                                                                            <div class="calendar-select-list-wrap"
                                                                                id="month-select"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="calendar-select-wrapper">
                                                                        <div class="calendar-select">
                                                                            <div id="selected-year"
                                                                                onclick="toggleDropdownYear()">
                                                                                Select year
                                                                            </div>
                                                                            <div class="calendar-select-list-wrap"
                                                                                id="year-select"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <img src="assets/images/calender.png"
                                                                    class="img-fluid calInsideImg" alt="calender">
                                                            </div>
                                                            <div class="calendar">
                                                                <ul class="weeks">
                                                                    <li>Su</li>
                                                                    <li>Mo</li>
                                                                    <li>Tu</li>
                                                                    <li>We</li>
                                                                    <li>th</li>
                                                                    <li>fr</li>
                                                                    <li>sa</li>
                                                                </ul>
                                                                <ul class="days"></ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <span class="inpLabel">Aadhaar Card Number</span>
                                                    <input type="text" name="aadhaarNo" class="form-control"
                                                        placeholder="" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="upload-btn">
                                                        <input type="file" id="aadhaarFront">
                                                        <label class="c-file-input__label" for="aadhaarFront">
                                                            <img src="{{ asset('public/website/new/assets/images/add-photo.png') }}"
                                                                class="img-fluid selLogo mr-2" alt="logo">Attachment
                                                            of
                                                            Aadhaar Front Photo
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="upload-btn">
                                                        <input type="file" id="aadhaarBack">
                                                        <label class="c-file-input__label" for="aadhaarBack">
                                                            <img src="{{ asset('public/website/new/assets/images/add-photo.png') }}"
                                                                class="img-fluid selLogo mr-2" alt="logo">Attachment
                                                            of
                                                            Aadhaar Back Photo
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <span class="inpLabel">Current / Present Address</span>
                                                    <input type="text" name="curPreAddr" class="form-control"
                                                        placeholder="" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <span class="inpLabel">Permanent Address</span>
                                                    <input type="text" name="permaAddr" class="form-control"
                                                        placeholder="" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group custForm-group">
                                                        <input type="checkbox" id="radioSel">
                                                        <label for="radioSel">Same as Current Address?</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn back-button prev">Previous</button>
                                        <button class="btn action-button next">Next</button>
                                    </div>

                                    <li class="step" data-step="3">
                                        <span class="circle">3</span>
                                        <span class="label">Bussiness Details</span>
                                    </li>
                                    <div class="step-content" data-step="3">
                                        <div class="serRegFormWrap">
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <span class="inpLabel">Bussiness Name (Visible on Profile)</span>
                                                    <input type="text" name="bussinessName" class="form-control"
                                                        placeholder="" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <select class="form-control form-select"
                                                        aria-label="Default select example">
                                                        <option selected>Select service provided by you</option>
                                                        <option value="1">Service No 1</option>
                                                        <option value="2">Service No 2</option>
                                                        <option value="3">Service No 3</option>
                                                        <option value="3">Service No 4 </option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <select class="form-control form-select"
                                                        aria-label="Default select example">
                                                        <option selected>Select no. of projects done</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4 </option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <span class="inpLabel">Working Since</span>
                                                    <input type="text" name="workSince" class="form-control"
                                                        placeholder="" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <span class="inpLabel">Team Strength</span>
                                                    <input type="text" name="teamNumber" class="form-control"
                                                        placeholder="" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <span class="inpLabel">Description</span>
                                                    <input type="text" name="workSince" class="form-control"
                                                        placeholder="" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <span class="inpLabel">Achievments</span>
                                                    <input type="text" name="workSince" class="form-control"
                                                        placeholder="" />
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn back-button prev">Previous</button>
                                        <button class="btn action-button next">Next</button>
                                    </div>

                                    <li class="step" data-step="4">
                                        <span class="circle">4</span>
                                        <span class="label">Area of working</span>
                                    </li>
                                    <div class="step-content" data-step="4">
                                        <div class="serRegFormWrap">
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <select class="form-control form-select"
                                                        aria-label="Default select example">
                                                        <option selected>Select Cities</option>
                                                        <option value="1">Delhi</option>
                                                        <option value="2">Noida</option>
                                                        <option value="3">Ghaziabad</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn back-button prev">Previous</button>
                                        <button class="btn action-button next">Next</button>
                                    </div>

                                    <li class="step" data-step="5">
                                        <span class="circle">5</span>
                                        <span class="label">Contact information</span>
                                    </li>
                                    <div class="step-content" data-step="5">
                                        <div class="serRegFormWrap">
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <input type="text" name="whatsapp" class="form-control"
                                                        placeholder="Whatsapp (Optional)" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <input type="text" name="Website" class="form-control"
                                                        placeholder="Website (Optional)" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <input type="text" name="instagramLink" class="form-control"
                                                        placeholder="Instagram Link (Optional)" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <input type="email" name="facebookLink" class="form-control"
                                                        placeholder="Facebook Link (Optional)" />
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <input type="email" name="youtubeLink" class="form-control"
                                                        placeholder="Youtube Link (Optional)" />
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn back-button prev">Previous</button>
                                        <button class="btn action-button">Continue</button>
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div> --}}

                </div>
            </div>
        </main>

    </div>
    <script>
        $(function() {
            var current_fs, next_fs, previous_fs;
            var left, opacity, scale;
            var animating;

            $(".next").click(function() {
                if (animating) return false;
                animating = true;

                current_fs = $(this).parent();
                next_fs = $(this).parent().next();
                $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
                next_fs.show();
                current_fs.animate({
                    opacity: 0
                }, {
                    step: function(now, mx) {
                        scale = 1 - (1 - now) * 0.2;
                        left = (now * 50) + "%";
                        opacity = 1 - now;
                        current_fs.css({
                            'transform': 'scale(' + scale + ')'
                        });
                        next_fs.css({
                            'left': left,
                            'opacity': opacity
                        });
                    },
                    duration: 500,
                    complete: function() {
                        current_fs.hide();
                        animating = false;
                    },
                    easing: 'swing'
                });
            });

            $(".previous").click(function() {
                if (animating) return false;
                animating = true;
                current_fs = $(this).parent();
                previous_fs = $(this).parent().prev();
                $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");
                previous_fs.show();
                current_fs.animate({
                    opacity: 0
                }, {
                    step: function(now, mx) {
                        scale = 0.8 + (1 - now) * 0.2;
                        left = ((1 - now) * 50) + "%";
                        opacity = 1 - now;
                        current_fs.css({
                            'left': left
                        });
                        previous_fs.css({
                            'transform': 'scale(' + scale + ')',
                            'opacity': opacity
                        });
                    },
                    duration: 500,
                    complete: function() {
                        current_fs.hide();
                        animating = false;
                    },
                    easing: 'swing'
                });
            });

            $(".submit").click(function() {
                $("#msform").submit();
            });
        });
    </script>
    <script>
        const monthSelect = document.getElementById("month-select");
        const yearSelect = document.getElementById("year-select");
        const daysTag = document.querySelector(".days");

        let date = new Date(),
            currentYear = date.getFullYear(),
            currentMonth = date.getMonth();
        let selectedDateObj = null;

        const months = [
            "January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
            "November", "December"
        ];

        for (let i = 0; i < months.length; i++) {
            let option = document.createElement("div");
            option.classList += " calendar-select-list";
            option.id = `option-${i}`;
            option.value = i;
            if (currentMonth === i) {
                option.classList += " month active";
            }
            option.addEventListener("click", function() {
                toggleDropdownMonth(i, months[i]);
            });
            option.textContent = months[i];
            monthSelect.appendChild(option);
        }

        for (let i = currentYear - 50; i <= currentYear + 50; i++) {
            let option = document.createElement("div");
            option.classList += " calendar-select-list";
            option.value = i;
            option.id = `option-${i}`;
            if (currentYear === i) {
                option.classList += " year active";
            }
            option.textContent = i;
            option.addEventListener("click", function() {
                toggleDropdownYear(i);
            });
            yearSelect.appendChild(option);
        }
        const backdrop = document.getElementById("calendar-backdrop");
        const calendarWrapper = document.getElementById("calendar-wrapper");
        backdrop.addEventListener("click", (e) => {
            e.stopPropagation();
            calendarWrapper.classList.add("show-calender");
        });

        document.addEventListener("click", (e) => {
            if (!calendarWrapper.contains(e.target) && !backdrop.contains(e.target)) {
                calendarWrapper.classList.remove("show-calender");
                monthSelect.classList.remove("show-dropdown");
                yearSelect.classList.remove("show-dropdown");
            }
        });

        monthSelect.value = currentMonth;
        yearSelect.value = currentYear;
        const toggleCalender = () => {
            const div = document.getElementById("calendar-wrapper");
            div.classList.toggle("show-calender");
        };
        const toggleDropdownMonth = (index, month) => {
            const div = document.getElementById("month-select");
            const year_div = document.getElementById("year-select");
            year_div.classList.remove("show-dropdown");
            if (month) {
                currentMonth = index;
                document
                    .querySelectorAll(".calendar-select-list.month.active")
                    .forEach((el) => {
                        el.classList.remove("active");
                    });
                const div_option = document.getElementById(`option-${index}`);
                div_option.classList += " month active";
                renderCalendar();
            }
            div.classList.toggle("show-dropdown");
        };

        const toggleDropdownYear = (year) => {
            const div = document.getElementById("year-select");
            const month_div = document.getElementById("month-select");
            month_div.classList.remove("show-dropdown");
            if (year) {
                currentYear = year;
                document
                    .querySelectorAll(".calendar-select-list.year.active")
                    .forEach((el) => {
                        el.classList.remove("active");
                    });
                const div_option = document.getElementById(`option-${year}`);
                div_option.classList += " year active";
                renderCalendar();
            }
            div.classList.toggle("show-dropdown");
        };
        const renderCalendar = () => {
            let firstDayOfMonth = new Date(currentYear, currentMonth, 1).getDay();
            let lastDateOfMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            let lastDayOfMonth = new Date(
                currentYear,
                currentMonth,
                lastDateOfMonth
            ).getDay();
            const dropdown_month = document.getElementById("selected-month");
            dropdown_month.innerText = months[currentMonth];
            const dropdown_year = document.getElementById("selected-year");
            dropdown_year.innerText = currentYear;
            let lastDateOfLastMonth = new Date(currentYear, currentMonth, 0).getDate();
            let liDayTag = "";

            for (let i = firstDayOfMonth; i > 0; i--) {
                liDayTag += `<li class="inactive">${lastDateOfLastMonth - i + 1}</li>`;
            }

            for (let i = 1; i <= lastDateOfMonth; i++) {
                let isToday = "";
                if (selectedDateObj) {
                    if (
                        i === selectedDateObj.getDate() &&
                        currentMonth === selectedDateObj.getMonth() &&
                        currentYear === selectedDateObj.getFullYear()
                    ) {
                        isToday = 'class="active"';
                    }
                } else {
                    if (
                        i === date.getDate() &&
                        currentMonth === new Date().getMonth() &&
                        currentYear === new Date().getFullYear()
                    ) {
                        isToday = 'class="active"';
                    }
                }
                liDayTag += `<li ${isToday} onclick="selectDate(${i})">${i}</li>`;
            }

            for (let i = lastDayOfMonth; i < 6; i++) {
                liDayTag += `<li class="inactive">${i - lastDayOfMonth + 1}</li>`;
            }
            daysTag.innerHTML = liDayTag;
        };

        const selectDate = (day) => {
            const dd = String(day).padStart(2, '0');
            const mm = String(currentMonth + 1).padStart(2, '0');
            const yyyy = currentYear;
            document.getElementById("selected-date-text").innerText =
                `${dd}/${mm}/${yyyy}`;
            const formattedDob = `${yyyy}-${mm}-${dd}`;
            document.getElementById("dob").value = formattedDob;
            selectedDateObj = new Date(yyyy, currentMonth, day);
            renderCalendar();
            document.getElementById("calendar-wrapper")
                .classList.remove("show-calender");
            monthSelect.classList.remove("show-dropdown");
            yearSelect.classList.remove("show-dropdown");
        };

        renderCalendar(currentMonth, currentYear);
        monthSelect.addEventListener("change", (e) => {
            currentMonth = parseInt(e.target.value);
            renderCalendar();
        });
        yearSelect.addEventListener("change", (e) => {
            currentYear = parseInt(e.target.value);
            renderCalendar();
        });
    </script>
    <script>
        $("#profileInput").on("change", function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $("#profilePreview").attr("src", e.target.result);
                    $("#removeImage").removeClass("d-none");
                };
                reader.readAsDataURL(file);
            }
        });

        $("#removeImage").on("click", function() {
            $("#profilePreview").attr("src", "https://via.placeholder.com/150");
            $("#profileInput").val("");
            $(this).addClass("d-none");
        });

        $("#banInput").on("change", function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $("#banPreview").attr("src", e.target.result);
                    $("#removeImage").removeClass("d-none");
                };
                reader.readAsDataURL(file);
            }
        });

        $("#removeImage").on("click", function() {
            $("#banPreview").attr("src", "https://via.placeholder.com/150");
            $("#banInput").val("");
            $(this).addClass("d-none");
        });

        $("#aadhaarFront").on("change", function(e) {

            var fileName = "";
            var scope = $(this).attr("id");

            if (this.files && this.files.length > 1) {
                fileName = this.files.length + " files selected";
            } else if (e.target.value) {
                fileName = e.target.value.split('\\').pop();
            }

            if (fileName) {
                $(".c-file-input__label[for='" + scope + "']").html(fileName);
            }
        });

        $("#aadhaarBack").on("change", function(e) {

            var fileName = "";
            var scope = $(this).attr("id");

            if (this.files && this.files.length > 1) {
                fileName = this.files.length + " files selected";
            } else if (e.target.value) {
                fileName = e.target.value.split('\\').pop();
            }

            if (fileName) {
                $(".c-file-input__label[for='" + scope + "']").html(fileName);
            }
        });
    </script>
    <script>
        $(function() {
            let currentStep = 1;

            function showStep(step) {
                currentStep = step;

                $('.step').each(function() {
                    const s = $(this).data('step');
                    $(this).removeClass('active done');

                    if (s < step) $(this).addClass('done');
                    else if (s === step) $(this).addClass('active');
                });

                $('.step-content').removeClass('active');
                $('.step-content[data-step="' + step + '"]').addClass('active');
            }

            $('.next').click(function() {
                showStep(currentStep + 1);
            });

            $('.prev').click(function() {
                showStep(currentStep - 1);
            });

            $('.step').click(function() {
                showStep($(this).data('step'));
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    {{-- OTP Verification Script --}}
    <script>
        let otpSent = false;
        let otpVerified = false;
        let timerInterval = null;
        let timeLeft = 60;
        $('#mobileNumber').on('input', function() {
            let mobile = $(this).val().replace(/\D/g, '');
            $(this).val(mobile);
            if (mobile.length !== 10) {
                $('#mobileError').text('Enter valid 10 digit mobile number');
                resetOtp();
                return;
            }
            $('#mobileError').text('');
            if (!otpSent) {
                otpSent = true;
                sendOtpAjax(mobile);
                startOtpTimer();
            }
        });

        function sendOtpAjax(mobile) {
            $.post("{{ route('send_otp') }}", {
                mobile: mobile,
                _token: "{{ csrf_token() }}"
            }, function(response) {
                if (response.status === 'already_customer') {
                    Swal.fire({
                        title: 'Already Registered',
                        text: 'You are already registered. Register as service provider?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            sendOtpForce(mobile);
                            startOtpTimer();
                            $('#mobileTick').show();
                        }
                    });
                    return;
                }
                if (response.status === 'otp_sent') {
                    Swal.fire('Success', 'OTP sent successfully', 'success');
                    $('#mobileTick').show();
                }
            }, 'json');
        }

        function sendOtpForce(mobile) {
            $.post("{{ route('send_otp') }}", {
                mobile: mobile,
                _token: "{{ csrf_token() }}"
            }, function() {
                Swal.fire('Success', 'OTP sent successfully', 'success');
            }, 'json');
        }

        function startOtpTimer() {
            clearInterval(timerInterval);
            timeLeft = 60;
            $('.otpTimer').text("01:00");
            $('#resendOtp').hide().prop('disabled', true);
            timerInterval = setInterval(() => {
                timeLeft--;
                let sec = String(timeLeft).padStart(2, '0');
                $('.otpTimer').text(`00:${sec}`);
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    $('.otpTimer').text("00:00");
                    $('#resendOtp').show().prop('disabled', false);
                }
            }, 1000);
        }

        $('#resendOtp').on('click', function() {
            let mobile = $('#mobileNumber').val();
            otpSent = false;
            sendOtpAjax(mobile);
            startOtpTimer();
        });

        $('#otp').on('input', function() {
            let otp = $(this).val();
            if (otp.length === 4) {
                $.post("{{ route('verify_otp') }}", {
                    mobile: $('#mobileNumber').val(),
                    otp: otp,
                    _token: "{{ csrf_token() }}"
                }, function(response) {
                    console.log(response);
                    if (response.status === 'otp_verified') {
                        otpVerified = true;
                        Swal.fire('Verified', 'OTP verified successfully', 'success');
                        $('#otpTick').show();
                        checkAllValid();
                    } else {
                        otpVerified = false;
                        Swal.fire('Error', 'Invalid OTP', 'error');
                    }
                }, 'json');
            }
        });

        function checkAllValid() {
            $('#verifyBtn').prop('disabled', !($('#mobileNumber').val().length === 10 && otpVerified));
        }

        function resetOtp() {
            otpSent = false;
            otpVerified = false;
            clearInterval(timerInterval);
            $('.otpTimer').text("01:00");
            $('#resendOtp').hide().prop('disabled', true);
            $('#verifyBtn').prop('disabled', true);

            $('#mobileTick').hide();
            $('#otpTick').hide();
        }
    </script>

    {{-- City Selection Script --}}
    <script>
        $(document).ready(function() {
            $('#citySelectBox').on('click', function(e) {
                e.stopPropagation();
                $('#cityDropdown').toggleClass('d-none');
            });
            $('.city-checkbox').on('change', function() {
                let cityId = $(this).val();
                let cityName = $(this).data('name');
                if ($(this).is(':checked')) {
                    if ($('#tag_' + cityId).length === 0) {
                        $('#selectedCities').append(
                            `<div class="city-tag" id="tag_${cityId}">
                        ${cityName}
                        <span class="remove" data-id="${cityId}">✕</span>
                    </div>`
                        );
                    }
                } else {
                    $('#tag_' + cityId).remove();
                }
                updatePlaceholder();
            });

            $(document).on('click', '.remove', function() {
                let cityId = $(this).data('id');
                $('#city_' + cityId).prop('checked', false);
                $('#tag_' + cityId).remove();
                updatePlaceholder();
            });

            function updatePlaceholder() {
                if ($('#selectedCities .city-tag').length > 0) {
                    $('#cityPlaceholder').text('Select Cities / Areas of Working');
                } else {
                    $('#cityPlaceholder').text('Select Cities / Areas of Working');
                }
            }

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#citySelectBox, #cityDropdown').length) {
                    $('#cityDropdown').addClass('d-none');
                }
            });
        });
    </script>

    {{-- Services Selection Script --}}
    <script>
        $(document).ready(function() {
            $('#serviceSelectBox').on('click', function(e) {
                e.stopPropagation();
                $('#serviceDropdown').toggleClass('d-none');
            });
            $('.service-checkbox').on('change', function() {
                let serviceId = $(this).val();
                let serviceName = $(this).data('name');
                if ($(this).is(':checked')) {
                    if ($('#service_tag_' + serviceId).length === 0) {
                        $('#selectedservices').append(
                            `<div class="service-tag" id="service_tag_${serviceId}">
                                ${serviceName}
                                <span class="remove-service" data-id="${serviceId}">✕</span>
                            </div>`
                        );
                    }
                } else {
                    $('#service_tag_' + serviceId).remove();
                }
                updateServicePlaceholder();
            });
            $(document).on('click', '.remove-service', function() {
                let serviceId = $(this).data('id');
                $('#service_' + serviceId).prop('checked', false);
                $('#service_tag_' + serviceId).remove();
                updateServicePlaceholder();
            });

            function updateServicePlaceholder() {
                if ($('#selectedservices .service-tag').length > 0) {
                    $('#servicePlaceholder').text('Services Provided');
                } else {
                    $('#servicePlaceholder').text('Services Provided');
                }
            }
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#serviceSelectBox, #serviceDropdown').length) {
                    $('#serviceDropdown').addClass('d-none');
                }
            });
        });
    </script>

    {{-- Business Type Selection Script --}}
    <script>
        $(document).ready(function() {
            function toggleFields() {
                let type = $("#businessType").val();
                if (type === "6") {
                    $("#gstinField").closest(".col-12").slideUp();
                    $("#businessNameField").closest(".col-12").slideUp();
                    $("#gstinField, #businessNameField").prop("required", false);
                    $("#aadhaarField").closest(".col-12, .col-md-7").slideDown();
                    $("#fullNameField").closest(".col-12").slideDown();
                    $("#aadharaddressField").closest(".col-12").slideDown();
                    $("#dobField").closest(".col-12").slideDown();
                    $("#dobField, #aadharCaptchafields").slideDown();
                    $("#aadhaarField, #aadhaarCaptcha, #aadhaarDob, #fullNameField,#dobField,#aadharaddressField")
                        .prop("required", true);
                } else if (type !== "") {
                    $("#gstinField").closest(".col-12").slideDown();
                    $("#businessNameField").closest(".col-12").slideDown();
                    $("#gstinField, #businessNameField").prop("required", true);
                    $("#aadhaarField").closest(".col-12, .col-md-7").slideUp();
                    $("#fullNameField").closest(".col-12").slideUp();
                    $("#aadharaddressField").closest(".col-12").slideUp();
                    $("#dobField").closest(".col-12").slideUp();
                    $("#dobField, #aadharCaptchafields").slideUp();
                    $("#aadhaarField, #aadhaarCaptcha, #aadhaarDob, #fullNameField,#dobField,#aadharaddressField")
                        .prop("required", false);
                }
            }
            toggleFields();
            $("#businessType").on("change", toggleFields);
        });
    </script>

    {{-- Pan Field from gst Script --}}
    <script>
        document.getElementById('gstinField').addEventListener('input', function() {
            let gstin = this.value.toUpperCase().trim();
            this.value = gstin;
            if (gstin.length === 15) {
                let pan = gstin.substr(2, 10);
                document.getElementById('panField').value = pan;
            } else {
                document.getElementById('panField').value = '';
            }
        });
    </script>


    {{-- GST Verify Script --}}
    <script>
        const $gst = $('#gstinField');
        const $shopName = $('#businessNameField');
        let gstCalling = false;

        const gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;

        function setVerifyStatus($el, status) {
            $el.removeClass('is-valid is-invalid');
            if (status === 'success') $el.addClass('is-valid');
            if (status === 'error') $el.addClass('is-invalid');
        }

        function showSwal(type, title, text) {
            Swal.fire({
                icon: type,
                title: title,
                text: text,
                confirmButtonColor: '#3085d6'
            });
        }

        function verifyGST() {
            if (gstCalling) return;

            const gstVal = $gst.val().trim().toUpperCase();
            $gst.val(gstVal);

            if (gstVal.length !== 15) return;

            if (!gstRegex.test(gstVal)) {
                setVerifyStatus($gst, 'error');
                showSwal('error', 'Invalid GSTIN', 'Please enter a valid GST number.');
                return;
            }

            gstCalling = true;

            Swal.fire({
                title: 'Verifying GST',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('verifygst') }}",
                type: "POST",
                data: {
                    gst: gstVal,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    gstCalling = false;
                    Swal.close();

                    if (!res.success) {
                        setVerifyStatus($gst, 'error');
                        showSwal('error', 'Verification Failed', res.message || 'GST verification failed.');
                        return;
                    }

                    const d = res.data || {};

                    if (d.business_name) {
                        $shopName.val(d.business_name);
                        setVerifyStatus($shopName, 'success');
                    }

                    $('#stateField').val(d.state || '');
                    $('#cityField').val(d.city || '');
                    $('#pincodeField').val(d.pincode || '');
                    $('#aadhaarAddress').val(d.address || '');
                    $('#dobField').val(d.dateOfBirth || '');

                    setVerifyStatus($gst, 'success');

                    showSwal('success', 'GST Verified', 'Business details fetched successfully.');
                },
                error: function() {
                    gstCalling = false;
                    Swal.close();
                    setVerifyStatus($gst, 'error');
                    showSwal('error', 'Server Error', 'Unable to verify GST. Please try again.');
                }
            });
        }

        $gst.on('keyup blur', function() {
            if ($(this).val().length === 15) {
                verifyGST();
            }
        });
    </script>


    {{-- Captcha Refresh Script --}}
    <script>
        let AadhaarotpSent = false;
        let AadharotpVerified = false;
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const refreshBtn = document.getElementById('refresh-captcha');
            const captchaImg = document.getElementById('captcha-image');
            const sessionIdInput = document.getElementById('aadhaarSessionId');

            refreshBtn.addEventListener('click', function() {

                const sessionId = sessionIdInput.value;

                if (!sessionId) {
                    Swal.fire('Error', 'Session ID missing', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Refreshing Captcha',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch("{{ route('aadhaar.reload-captcha') }}?session_id=" + sessionId)
                    .then(res => res.json())
                    .then(data => {
                        Swal.close();

                        if (data.success && data.captcha) {
                            captchaImg.src = 'data:image/png;base64,' + data.captcha;
                            document.getElementById('aadhaarCaptcha').value = '';
                            AadhaarotpSent = false;

                            Swal.fire('Updated', 'Captcha refreshed', 'success');
                        } else {
                            Swal.fire('Failed', 'Captcha reload failed', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.close();
                        Swal.fire('Error', 'API not reachable', 'error');
                    });
            });

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const captchaInput = document.getElementById('aadhaarCaptcha');
            const aadhaarInput = document.getElementById('aadhaarField');

            captchaInput.addEventListener('input', function() {

                const captcha = this.value.trim(); // CASE SENSITIVE
                const aadhaar = aadhaarInput.value.trim();
                const sessionId = document.getElementById('aadhaarSessionId').value;

                if (captcha.length < 5) return;
                if (AadhaarotpSent) return;

                if (!sessionId) {
                    Swal.fire('Error', 'Session expired. Refresh captcha.', 'error');
                    return;
                }

                if (!/^\d{12}$/.test(aadhaar)) {
                    Swal.fire('Invalid Aadhaar', 'Enter 12 digit Aadhaar number', 'warning');
                    return;
                }

                AadhaarotpSent = true;

                Swal.fire({
                    title: 'Sending OTP',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch("{{ route('aadhaar.generate-otp') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            aadhaar_number: aadhaar,
                            captcha: captcha,
                            session_id: sessionId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.close();

                        if (data.success) {
                            Swal.fire('OTP Sent', 'OTP sent to registered mobile', 'success');
                        } else {
                            AadhaarotpSent = false;
                            Swal.fire('Error', data.message || 'Invalid captcha', 'error');
                        }
                    })
                    .catch(() => {
                        AadhaarotpSent = false;
                        Swal.close();
                        Swal.fire('Error', 'OTP API failed', 'error');
                    });
            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const otpInput = document.getElementById('otpField');
            let AadharotpVerified = false; // 🔒 prevent duplicate calls

            function showSwal(type, title, text) {
                Swal.fire({
                    icon: type,
                    title: title,
                    text: text,
                    confirmButtonColor: '#3085d6'
                });
            }

            otpInput.addEventListener('input', function() {

                if (AadharotpVerified) return; // ⛔ stop repeat verification

                const otp = this.value.trim();
                const sessionId = document.getElementById('aadhaarSessionId').value;

                if (!/^\d{6}$/.test(otp)) return;

                if (!sessionId) {
                    showSwal('error', 'Session Expired', 'Please refresh captcha and try again.');
                    return;
                }

                AadharotpVerified = true; // 🔒 lock immediately

                Swal.fire({
                    title: 'Verifying Aadhaar',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch("{{ route('aadhaar.verify-otp') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            otp: otp,
                            session_id: sessionId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.close();

                        if (data.success) {
                            let d = data.data || {};
                            let dAddr = d.address || {};
                            let aadharadd =
                                `${dAddr.careOf ?? ''}, ${dAddr.house ?? ''}, ${dAddr.street ?? ''}, ${dAddr.locality ?? ''}, ${dAddr.vtc ?? ''}, ${dAddr.postOffice ?? ''}, ${dAddr.landmark ?? ''}`;
                            aadharadd = aadharadd.replace(/(,\s*)+/g, ', ').replace(/^, |, $/g, '')
                                .trim();

                            document.getElementById('fullNameField').value = d.name ?? '';
                            document.getElementById('aadhaarDob').value = d.dateOfBirth ?? '';
                            document.getElementById('aadhaarGender').value = d.gender ?? '';
                            document.getElementById('aadhaarState').value = d.address.state ?? '';
                            document.getElementById('aadhaarFatherName').value = d.address.careOf ?? '';
                            document.getElementById('aadhaaraddress').value = aadharadd ?? '';
                            document.getElementById('aadhaarPincode').value = d.address.pin ?? '';
                            document.getElementById('aadhaarVerified').value = 1;

                            document.getElementById('dobField').classList.remove('d-none');

                            Swal.fire({
                                icon: 'success',
                                title: 'Aadhaar Verified',
                                text: 'Your Aadhaar has been verified successfully.',
                                confirmButtonColor: '#28a745'
                            });
                        } else {
                            showSwal('error', 'Verification Failed', data.message || 'Invalid OTP.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.close();
                        showSwal('error', 'Error', 'Something went wrong. Please try again.');
                    });
            });
        });
    </script>


    {{-- Number Validation --}}
    <script>
        $(document).on('input', '.number-only', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
@endsection
