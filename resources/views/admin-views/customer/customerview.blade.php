@extends('layouts.back-end.app')
@section('title', 'View Customer Message')

@push('css_or_js')
    <style>
        .contact-details-card {
            border: 1px solid #e7eaf3;
            border-radius: 8px;
            background: #fff;
            padding: 20px;
        }

        .contact-details-card h5 {
            font-weight: 600;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #eaeaea;
            padding: 8px 0;
        }

        .info-row strong {
            width: 35%;
        }

        .message-box {
            background: #f9f9f9;
            border-radius: 6px;
            padding: 15px;
            white-space: pre-line;
            border: 1px solid #e0e0e0;
            color: #000 !important;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2 text-capitalize">
                <img width="25" src="{{ asset('/public/assets/back-end/img/message.png') }}" alt="">
                Customer Message Details
            </h2>
            <a href="{{ route('admin.customer.customerslist') }}" class="btn btn--primary">
                <i class="tio-arrow-back"></i> Back to List
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="contact-details-card shadow-sm">
                    <h5 class="mb-4 text-primary">
                        <i class="tio-user"></i> Customer Information
                    </h5>

                    <div class="info-row">
                        <strong>Name:</strong>
                        <span>{{ $contact->name ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Email:</strong>
                        <span>{{ $contact->email ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Mobile Number:</strong>
                        <span>{{ $contact->mobile_number ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Business Name:</strong>
                        <span>{{ $contact->business_name ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Type:</strong>
                        <span>{{ $contact->type ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Subject:</strong>
                        <span>{{ $contact->subject ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Seen:</strong>
                        <span>
                            @if ($contact->seen)
                                <span class="badge badge-success">Seen</span>
                            @else
                                <span class="badge badge-warning">Unseen</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <strong>Created At:</strong>
                        <span>{{ $contact->created_at ? $contact->created_at->format('d M Y, h:i A') : '-' }}</span>
                    </div>

                    <hr>

                    <h5 class="text-primary mt-4 mb-2"><i class="tio-email"></i> Message</h5>
                    <div class="message-box mb-3">
                        {{ $contact->message ?? 'No message provided.' }}
                    </div>

                    @if ($contact->feedback)
                        <h5 class="text-primary mt-4 mb-2"><i class="tio-feedback"></i> Feedback</h5>
                        <div class="message-box mb-3">
                            {{ $contact->feedback }}
                        </div>
                    @endif

                    @if ($contact->reply)
                        <h5 class="text-primary mt-4 mb-2"><i class="tio-reply"></i> Admin Reply</h5>
                        <div class="message-box">
                            {{ $contact->reply }}
                        </div>
                    @endif

                    <div class="mt-4 text-right">
                        <a href="{{ route('admin.customer.customerslist') }}" class="btn btn-secondary">
                            <i class="tio-arrow-back"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
