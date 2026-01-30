@extends('layouts.back-end.common_seller_1')

@section('content')
    @push('style')
        <link rel="stylesheet" href="{{ asset('public/website/assets/css/career.css') }}">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact Section</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Inter', sans-serif;
                margin: 0;
                padding: 0;
                background: #fff;
            }

            .contact-section {
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 80px 20px;
                gap: 50px;
                flex-wrap: wrap;
            }

            .contact-info {
                max-width: 500px;
            }

            .contact-info h2 {
                font-size: 36px;
                margin-bottom: 15px;
            }

            .contact-info p {
                font-size: 16px;
                color: #555;
                margin-bottom: 30px;
            }

            .contact-info .info-item {
                display: flex;
                align-items: flex-start;
                margin-bottom: 20px;
                gap: 15px;
            }

            .contact-info .info-item i {
                font-size: 24px;
                background-color: #2e6cb2;
                color: #fff;
                padding: 15px;
                border-radius: 8px;
            }

            .contact-info .info-item div {
                display: flex;
                flex-direction: column;
            }

            .contact-info .info-item div span {
                font-weight: 600;
                margin-bottom: 5px;
            }

            .contact-form {
                background-color: #2e6cb2;
                padding: 40px;
                border-radius: 15px;
                width: 400px;
                position: relative;
            }

            .contact-form input,
            .contact-form select,
            .contact-form textarea {
                width: 100%;
                /* padding: 15px; */
                margin-bottom: 20px;
                border: none;
                border-radius: 8px;
                font-size: 14px;
            }

            .contact-form textarea {
                resize: none;
                height: 120px;
            }

            .contact-form button,
            .contact-form input[type="submit"] {
                width: 100%;
                /* padding: 15px; */
                background-color: #ed672f;
                border: none;
                border-radius: 8px;
                color: #fff;
                font-size: 16px;
                cursor: pointer;
                font-weight: 600;
            }

            .contact-form::after {
                content: '';
                position: absolute;
                top: -30px;
                right: -30px;
                width: 60px;
                height: 60px;
                background-color: #ed672f;
                border-radius: 50%;
            }

            .text-danger {
                color: #ffdddd !important;
            }


            @media (max-width: 900px) {
                .contact-section {
                    flex-direction: column;
                    align-items: center;
                }

                .contact-form {
                    width: 100%;
                    max-width: 400px;
                }
            }
        </style>
        <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @endpush

    <section class="contact-section">
        <div class="contact-info">
            <p style="color:#2e6cb2;font-weight:600;margin-bottom:10px;">Contact Us</p>
            <h2>Get In Touch With Us</h2>
            <p>Let us know how we can best serve you, use the contact form for email us. It's honour to support you in your
                shopping journey.</p>

            <div class="info-item">
                <i class="fas fa-phone"></i>
                <div>
                    <span>Phone Number</span>
                    <a href="tel:9950680690">9950680690</a>
                </div>
            </div>

            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <span>Email Address</span>
                    <a href="mailto:Customersupport@interiorchowk.com">Customersupport@interiorchowk.com </a>
                </div>
            </div>
        </div>

        <div class="contact-form">
            <form action="{{ route('callback-mail') }}" method="post" id="myForm">
                @csrf
                <input type="hidden" name="status_site" value="0">

                <div class="form-group">
                    <input type="text" name="name" placeholder="Name" class="form-control mb-2">
                    <span class="text-danger error-text name_error"></span>
                </div>

                <div class="form-group">
                    <input type="text" name="phone" placeholder="Phone No." class="form-control mb-2">
                    <span class="text-danger error-text phone_error"></span>
                </div>

                <div class="form-group">
                    <input type="email" name="email" placeholder="Email ID" class="form-control mb-2">
                    <span class="text-danger error-text email_error"></span>
                </div>

                <div class="form-group">
                    <select class="form-control mb-2" name="interested">
                        <option value="">I am:-</option>
                        <option>Customer</option>
                        <option>Seller</option>
                        <option>Architect</option>
                        <option>Interior designer</option>
                        <option>Contractor</option>
                        <option>Worker</option>
                    </select>
                    <span class="text-danger error-text interested_error"></span>
                </div>

                <div class="form-group">
                    <textarea name="message" placeholder="Message" class="form-control mb-2"></textarea>
                    <span class="text-danger error-text message_error"></span>
                </div>

                <input type="submit" class="btn btn-primary" value="Request Callback">
            </form>
        </div>

    </section>

    @push('script')
        <script>
            $(document).ready(function() {
                $('#myForm').submit(function(e) {
                    e.preventDefault(); // prevent default submission
                    $('.error-text').text(''); // clear previous errors

                    let valid = true;

                    let name = $('input[name="name"]').val().trim();
                    if (name === '') {
                        $('.name_error').text('Please enter your name.');
                        valid = false;
                    }

                    let phone = $('input[name="phone"]').val().trim();
                    let phoneRegex = /^[0-9+\- ]{7,15}$/;
                    if (phone === '') {
                        $('.phone_error').text('Please enter your phone number.');
                        valid = false;
                    } else if (!phoneRegex.test(phone)) {
                        $('.phone_error').text('Please enter a valid phone number.');
                        valid = false;
                    }

                    let email = $('input[name="email"]').val().trim();
                    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (email === '') {
                        $('.email_error').text('Please enter your email.');
                        valid = false;
                    } else if (!emailRegex.test(email)) {
                        $('.email_error').text('Please enter a valid email.');
                        valid = false;
                    }

                    let interested = $('select[name="interested"]').val();
                    if (interested === '') {
                        $('.interested_error').text('Please select your role.');
                        valid = false;
                    }

                    let message = $('textarea[name="message"]').val().trim();
                    if (message === '') {
                        $('.message_error').text('Please enter your message.');
                        valid = false;
                    }

                    if (valid) {
                        this.submit(); // submit the form if all validations pass
                    }
                });
            });
        </script>
    @endpush
@endsection
