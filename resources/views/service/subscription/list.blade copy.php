@extends('layouts.back-end.app-service')
@section('title', \App\CPU\translate('subscription'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

    <link href="https://fonts.googleapis.com/css?family=Inter:400,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" /> --}}
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <style>
        .pricing-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #5a34e8, #7c5cff);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 15px;
        }

        strike {
            font-size: 12px;
        }

        .pricing-wrapper {
            padding: 40px 20px 60px;
            max-width: 1100px;
            width: 100%;
            margin: 0 auto;
            color: #000;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
        }

        .brand-name {
            font-size: 14px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            opacity: 0.8;
            text-align: center;
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 32px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 35px;
        }

        .plan-card {
            background: #f5f5f9;
            border-radius: 10px;
            padding: 30px 20px 26px;
            text-align: center;
            color: #111827;
            position: relative;
            height: 100%;
        }

        .plan-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .price {
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .price span {
            font-size: 18px;
            font-weight: 500;
        }

        .per-month {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 16px;
        }

        .select-btn {
            display: block;
            width: 100%;
            background: #111827;
            color: #fff;
            font-weight: 600;
            border-radius: 4px;
            padding: 10px 0;
            border: none;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 12px;
        }

        .features {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: left;
            font-size: 14px;
            color: #374151;
        }

        .features li {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tick {
            font-size: 12px;
            color: #10b981;
        }

        .cross {
            font-size: 12px;
            color: red;
        }

        .popular-ribbon {
            position: absolute;
            top: 0;
            right: 0;
            background: #fbbf24;
            color: #111827;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 0 10px 0 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .plan-standard {
            transform: translateY(-8px);
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.18);
            border-top: 4px solid #fbbf24;
        }

        @media (max-width: 991.98px) {
            .plan-standard {
                transform: none;
                margin-top: 20px;
                margin-bottom: 20px;
            }
        }

        .free-del {
            font-size: 20px;
            margin-bottom: 39px;
            margin-top: 25px;
        }
    </style>
    <div class="dashboard-container">
        <div class="dashboard-summary">
            <div class="card first-div">
                <div class="pricing-wrapper">
                    <div class="brand-name">Service</div>
                    <h2 class="section-title">Service Subscription Plans</h2>
                    <div class="container">
                        <div class="row g-4">
                            <!-- Basic Plan -->
                            @foreach ($memberships as $membership)
                                <div class="col-md-4">
                                    <div class="plan-card" data-price="{{ $membership->price }}"
                                        data-yearly-discount="{{ $membership->discount_on_yearly_plan }}">

                                        <div class="plan-name">{{ $membership->plan_name }}</div>
                                        @if ($membership->plan_name == 'Basic')
                                            <div class="free-del">
                                                Free
                                            </div>
                                        @else
                                            <div class="billing-toggle">
                                                <label>
                                                    <input type="radio" name="billing_{{ $membership->id }}"
                                                        value="yearly" checked onchange="changePrice(this)">
                                                    Yearly
                                                </label>

                                                <label>
                                                    <input type="radio" name="billing_{{ $membership->id }}"
                                                        value="quarterly" onchange="changePrice(this)">
                                                    Quarterly
                                                </label>
                                            </div>

                                            <div class="price">
                                                <strike class="old-price">₹{{ $membership->price }}</strike>
                                                <span class="amount">
                                                    ₹{{ $membership->price - $membership->discount_on_yearly_plan }}
                                                </span>
                                                <span class="duration">/year</span>
                                            </div>
                                        @endif


                                        @if ($membership->plan_name == 'Basic')
                                            <button class="select-btn">
                                                You are on basic plan
                                            </button>
                                        @else
                                            {{-- <button class="select-btn" data-plan-id="{{ $membership->id }}"
                                                data-billing="yearly" data-amount="{{ $membership->price * 100 }}">
                                                Select {{ $membership->plan_name }}
                                            </button> --}}
                                            <button class="select-btn" data-plan-id="{{ $membership->id }}"
                                                data-billing="yearly"
                                                data-amount="{{ ($membership->price - $membership->discount_on_yearly_plan) * 100 }}">
                                                Select {{ $membership->plan_name }}
                                            </button>
                                        @endif

                                        <ul class="features">
                                            <li>
                                                @if ($membership->logo == 1)
                                                    <span class="tick">✔</span> Logo visibility to customers
                                                @else
                                                    <span class="cross">X</span> Logo visibility to customers
                                                @endif
                                            </li>
                                            <li>
                                                @if ($membership->profile_image == 1)
                                                    <span class="tick">✔</span> Profile visibility to customers
                                                @else
                                                    <span class="cross">X</span> Profile visibility to customers
                                                @endif
                                            </li>
                                            <li>
                                                @if ($membership->contact_no_show == 1)
                                                    <span class="tick">✔</span> Contact Number visibility to customers
                                                @else
                                                    <span class="cross">X</span> Contact Number visibility to customers
                                                @endif
                                            </li>
                                            <li>
                                                @if ($membership->mail_id == 1)
                                                    <span class="tick">✔</span> Mail id visibility to customers
                                                @else
                                                    <span class="cross">X</span> Mail id visibility to customers
                                                @endif
                                            </li>
                                            <li>
                                                @if ($membership->whatapp_contact == 1)
                                                    <span class="tick">✔</span> Whatsapp number visibility to customers
                                                @else
                                                    <span class="cross">X</span> Whatsapp number visibility to customers
                                                @endif
                                            </li>
                                            <li>
                                                @if ($membership->social_media_link == 1)
                                                    <span class="tick">✔</span> Social media visibility to customers
                                                @else
                                                    <span class="cross">X</span> Social media visibility to customers
                                                @endif
                                            </li>
                                            <li>
                                                @if ($membership->website == 1)
                                                    <span class="tick">✔</span> Website visibility to customers
                                                @else
                                                    <span class="cross">X</span> Website visibility to customers
                                                @endif
                                            </li>
                                            <li>
                                                @if ($membership->free_2d_design == 0)
                                                    <span class="cross">X</span> Free 2D Design
                                                @else
                                                    <span class="tick">✔</span> {{ $membership->free_2d_design }} Free 2D
                                                    Design
                                                @endif
                                            </li>
                                            <li>
                                                @if ($membership->free_3d_design == 0)
                                                    <span class="cross">X</span> Free 3D Design
                                                @else
                                                    <span class="tick">✔</span> {{ $membership->free_3d_design }} Free 3D
                                                    Design
                                                @endif
                                            </li>
                                            <li>
                                                @if ($membership->advertisement == 1)
                                                    <span class="tick">✔</span> Free Advertisement
                                                @else
                                                    <span class="cross">X</span> Free Advertisement
                                                @endif
                                            </li>
                                            <li>
                                                @if ($membership->discount_on_delivery)
                                                    <span class="tick">✔</span> {{ $membership->discount_on_delivery }}%
                                                    Discount on delivery
                                                @endif
                                            </li>
                                        </ul>

                                    </div>
                                </div>
                            @endforeach
                            <script>
                                function changePrice(el) {
                                    const card = el.closest('.plan-card');
                                    const basePrice = parseFloat(card.dataset.price);
                                    const yearlyDiscount = parseFloat(card.dataset.yearlyDiscount);

                                    const amountEl = card.querySelector('.amount');
                                    const durationEl = card.querySelector('.duration');
                                    const button = card.querySelector('.select-btn');
                                    const oldPriceEl = card.querySelector('.old-price');

                                    if (el.value === 'quarterly') {
                                        let quarterlyPrice = (basePrice / 12) * 3;

                                        amountEl.innerText = '₹' + Math.round(quarterlyPrice);
                                        durationEl.innerText = '/quarter';

                                        oldPriceEl.style.display = 'none';

                                        button.dataset.billing = 'quarterly';
                                    } else {
                                        let yearlyPrice = basePrice - yearlyDiscount;

                                        amountEl.innerText = '₹' + yearlyPrice;
                                        durationEl.innerText = '/year';

                                        oldPriceEl.style.display = 'inline';

                                        button.dataset.billing = 'yearly';
                                    }
                                }
                            </script>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.querySelectorAll('.select-btn').forEach(btn => {
            btn.addEventListener('click', function() {

                let planId = this.dataset.planId;
                let amount = this.dataset.amount;

                fetch("{{ route('service.razorpay.create.order') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            plan_id: planId,
                            amount: amount
                        })
                    })
                    .then(res => res.json())
                    .then(order => {

                        let options = {
                            key: "{{ config('service.razorpay.key') }}",
                            amount: order.amount,
                            currency: "INR",
                            name: "Your Company Name",
                            description: "Membership Plan Payment",
                            order_id: order.razorpay_order_id,

                            handler: function(response) {
                                fetch("{{ route('service.razorpay.verify') }}", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify(response)
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('service.payment.success') }}";
                                });
                            }
                        };

                        let rzp = new Razorpay(options);
                        rzp.open();
                    });
            });
        });
    </script>

@endsection
