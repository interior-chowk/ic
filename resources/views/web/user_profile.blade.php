@extends('layouts.back-end.common_seller_1')



@section('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        .tab-head {
            position: sticky;
            top: 110px;
            /* Adjust this value based on your header height */
            align-self: flex-start;
            z-index: 100;
            padding: 20px 0;
            margin-right: 30px;
            overflow-y: auto;
        }

        a {
            color: #000;
        }

        .modal {
            z-index: 1055 !important;
        }

        .modal-backdrop {
            z-index: 1050 !important;
        }
    </style>

    <main class="main myAccRespo">


        <div class="page-content pb-0">
            <div class="container cntRespPad">
                <div class="row">
                    <div class="col-lg-12 mb-2 mb-lg-0">

                        <div class="site-wrapper">
                            <section class="tabs-wrapper">
                                <div class="tabs-container">
                                    <div class="tabs-block">
                                        <div id="tabs-section" class="tabs">
                                            <ul class="tab-head">
                                                <li>
                                                    <a>
                                                        <div class="media">

                                                            @if ($user->gender == 'Male')
                                                                <img style="height:62px;width: 62px;object-fit: contain;"
                                                                    src="{{ asset('public/website/assets/images/male.webp') }}"
                                                                    class="mr-3 rounded-circle" alt="Profile Picture">
                                                            @elseif ($user->gender == 'Female')
                                                                <img style="height:62px;width: 62px;object-fit: contain;"
                                                                    src="{{ asset('public/website/assets/images/female.webp') }}"
                                                                    class="mr-3 rounded-circle" alt="Profile Picture">
                                                            @else
                                                                <img style="height:62px;width: 62px;object-fit: contain;"
                                                                    src="{{ asset('public/website/assets/images/guest.webp') }}"
                                                                    class="mr-3 rounded-circle" alt="Profile Picture">
                                                            @endif
                                                            <div class="media-body">
                                                                <p>Hello,</p>
                                                                <h6 class="mt-0">{{ $user->f_name ?? 'Guest' }}
                                                                    {{ $user->l_name ?? '' }}
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#tab-1" class="tab-link active"> <span
                                                            class="material-icons tab-icon">My Orders</span></a>
                                                </li>
                                                <li>
                                                    <a href="#tab-2" class="tab-link"> <span
                                                            class="material-icons tab-icon">Wallet</span></a>
                                                </li>
                                                <li>
                                                    <a href="#tab-3" class="tab-link"> <span
                                                            class="material-icons tab-icon">My Addresses</span></a>
                                                </li>
                                                <li>
                                                    <a href="#tab-4" class="tab-link"> <span
                                                            class="material-icons tab-icon">Personal Details</span></a>
                                                </li>
                                                <li>
                                                    <a href="#tab-5" class="tab-link"> <span
                                                            class="material-icons tab-icon">Wishlist</span></a>
                                                </li>
                                                <li>
                                                    <a href="#tab-6" class="tab-link"> <span
                                                            class="material-icons tab-icon">Cart</span></a>
                                                </li>
                                                <li>
                                                    <a href="#tab-7" class="tab-link"> <span
                                                            class="material-icons tab-icon">Refer & Earn</span></a>
                                                </li>
                                                {{-- <li>
                                                    <a href="{{ url('/logout') }}">
                                                        <span class="material-icons tab-icon">Log Out</span>
                                                    </a>
                                                </li> --}}
                                                <li>
                                                    <a href="{{ url('/logout') }}" onclick="return confirmLogout(event)">
                                                        <span class="material-icons tab-icon">Log Out</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <script>
                                                function confirmLogout(e) {
                                                    e.preventDefault();
                                                    const url = e.currentTarget.getAttribute('href');

                                                    Swal.fire({
                                                        title: 'Are you sure?',
                                                        text: "You will be logged out of your session.",
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#3085d6',
                                                        cancelButtonColor: '#d33',
                                                        confirmButtonText: 'Yes, log me out',
                                                        cancelButtonText: 'Cancel'
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            window.location.href = url;
                                                        }
                                                    });

                                                    return false;
                                                }
                                            </script>


                                            <section id="tab-1"
                                                class="tab-body entry-content active active-content ordSectionRespo">
                                                <h4 class="text-center mb-1">My Orders</h4>
                                                <p class="text-center">Track your open orders & view the summary of your
                                                    past orders</p>

                                                <div class="orders_div">

                                                    <select class="form-control w-auto" id="exampleFormControlSelect1">
                                                        <option>Last 6 Months</option>
                                                        <option>Last 12 Months</option>
                                                        <option>Last 24 Months</option>
                                                    </select>
                                                </div>

                                                @forelse($orders as $order)
                                                    @php
                                                        $images = json_decode($order->image, true);
                                                        $statuses = App\Model\ShiprocketCourier::where(
                                                            'order_id',
                                                            $order->id,
                                                        )->first();
                                                    @endphp
                                                    {{-- @dd($status); --}}
                                                    <div class="orderId-list">
                                                        <h6 style="color: #2E6CB2; margin-bottom: 0rem;"><a
                                                                href="{{ url('/get_order') }}?order_id={{ $order->id }}">Order
                                                                ID: {{ $order->id }}</a></h6>
                                                        <div class="p-4 rounded-lg"
                                                            style="background-color: #E3EEFF; margin-bottom: 10px;">
                                                            <div class="media">
                                                                <img 
                                                                {{-- src="{{ asset('storage/app/public/images/' . $images[0]) }}" --}}
                                                                src="{{ rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($images[0] ?? 'default.jpg', '/') }}"

                                                                    class="mr-3 rounded-lg" alt="Product Image">
                                                                <div class="media-body">
                                                                    <h5 class="mt-0 mb-0">
                                                                        @if (!empty($statuses) && ($statuses->status === 'NOT PICKED' || $statuses->status === 'BOOKED'))
                                                                            {{ 'Confirmed' }}
                                                                        @elseif(!empty($statuses))
                                                                            {{ ucfirst($statuses->status) }}
                                                                        @else
                                                                            {{ 'N/A' }}
                                                                        @endif
                                                                    </h5>

                                                                    <h6 class="mb-0 py-2">
                                                                        {{ \Carbon\Carbon::parse($order->created_at)->format('D d M, Y') }}
                                                                    </h6>
                                                                    {{-- @dd($order); --}}
                                                                    @if ($order->order_status === 'delivered')
                                                                        <div
                                                                            class="ratings-container d-inline-block bg-white p-1 rounded-lg">
                                                                            <label>You Rated: </label>
                                                                            <div class="ratings">
                                                                                <div class="ratings-val"
                                                                                    style="width: {{ rand(20, 100) }}%;">
                                                                                </div><!-- End .ratings-val -->
                                                                            </div><!-- End .ratings -->
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <img src="{{ asset('public/website/assets/images/no order yet.webp') }}"
                                                        alt="Empty Wishlist" class="img-fluid"
                                                        style="max-width: 35% !important;height: auto;margin: auto;">
                                                @endforelse
                                            </section>

                                            <section id="tab-2" class="tab-body entry-content walletRespo">
                                                <h4 class="text-center mb-1">InteriorChowk Wallet</h4>
                                                {{-- @dd($wallets); --}}
                                                @if (count($walletCustomer) == null || count($walletCustomer) == 0)
                                                    <div class="p-4 text-center">
                                                        <img src="{{ asset('public/website/assets/images/Empty Wallet.webp') }}"
                                                            alt="No Wallet" class="img-fluid"
                                                            style="max-width: 50% !important;height: auto;margin: auto;">
                                                    </div>
                                                @else
                                                    <div class="mt-2 walletDtlWrapper">
                                                        <div class="p-3 walletDtlInner">
                                                            <div class="text-left mr-5 totWalBalnc">
                                                                <h6 class="text-left mb-1">Total Wallet Balance</h6>
                                                                <h3 class="text-left mb-0" style="color: #2E6CB2;">

                                                                    <span>&#8377;</span>
                                                                    {{-- {{ number_format($wallets->first()->balance ?? 0, 2) }} --}}
                                                                    {{ number_format(optional($walletCustomer->last())->balance ?? 0, 2) }}

                                                                </h3>
                                                            </div>
                                                            <div class="text-center withdWalBalnc">
                                                                <h6 class="text-center mb-1">Withdrawable</h6>
                                                                <h3 class="text-center mb-0" style="color: #2E6CB2;">
                                                                    <span>&#8377;</span>
                                                                    {{ number_format(optional($walletCustomer->last())->credit ?? 0, 2) }}
                                                                </h3>
                                                                @php
                                                                    $walletss = $wallets
                                                                        ->where('transaction_type', 1)
                                                                        ->filter(function ($w) {
                                                                            return in_array($w->transaction_method, [
                                                                                'Ref. Earn bonus',
                                                                                'Ref. bonus',
                                                                            ]);
                                                                        })
                                                                        ->sum('transaction_amount');

                                                                    $deduct = $wallets
                                                                        ->where('transaction_type', 0)
                                                                        ->sum('transaction_amount');

                                                                    $nonWithdrawable = $walletss - $deduct;

                                                                    $walletsss = $wallets
                                                                        ->where('transaction_type', 1)
                                                                        ->filter(function ($w) {
                                                                            return !in_array($w->transaction_method, [
                                                                                'Ref. Earn bonus',
                                                                                'Ref. bonus',
                                                                            ]);
                                                                        })
                                                                        ->sum('transaction_amount');
                                                                @endphp
                                                                <!-- <button type="button" class="mt-1" data-balance="{{ number_format($wallets->first()->balance ?? 0, 2) }}"></button> -->
                                                                <button type="button"
                                                                    class="btn btnOrdCan btn-RetExcOrd btn-info  btnWalWith"
                                                                    data-toggle="modal" data-target="#cancellationModal">
                                                                    Withdraw
                                                                </button>
                                                            </div>
                                                            <div class="text-right nonWithdWalBalnc">
                                                                <h6 class="text-right mb-1">Non-Withdrawable</h6>
                                                                <h3 class="text-right mb-0" style="color: #2E6CB2;">

                                                                    <span>&#8377;</span>
                                                                    {{ number_format($nonWithdrawable && $nonWithdrawable > 0 ? $nonWithdrawable : 0, 2) }}
                                                                </h3>
                                                            </div>
                                                        </div>

                                                        <p class="mb-0 px-4 py-1">Recent Activity</p>
                                                        @php
                                                            $allTransactions = collect($wallets)->merge(
                                                                $walletCustomer,
                                                            );
                                                            $allTransactions = $allTransactions->sortByDesc(
                                                                'created_at',
                                                            );
                                                        @endphp
                                                        @forelse ($allTransactions as $transaction)
                                                            @if (is_object($transaction))
                                                                @php
                                                                    $amount =
                                                                        $transaction->transaction_amount ??
                                                                        ($transaction->credit ?? 0);
                                                                @endphp
                                                                @if ($amount > 0)
                                                                    <div class="media p-4"
                                                                        style="border-top:1px solid #2E6CB2;">
                                                                        <img src="{{ asset('public/website/new/assets/images/online-shopping_18662840.png') }}"
                                                                            class="mr-3" alt="Transaction">
                                                                        <div class="media-body">
                                                                            <p class="mb-0">
                                                                                @if ($transaction->transaction_type > 0 ?? $transaction->transaction_type == 'add_fund_by_admin')
                                                                                    {{ 'Credited' }}
                                                                                @else
                                                                                    {{ 'Debited' }}
                                                                                @endif
                                                                            </p>
                                                                            <h6 class="mt-0 mb-0">
                                                                                {{ \Carbon\Carbon::parse($transaction->created_at)->format('D, d M Y') }}
                                                                            </h6>
                                                                            <p class="mb-0" style="color: #2E6CB2;">
                                                                                Transaction
                                                                                ID:
                                                                                {{ $transaction->transaction_id ?? $transaction->transaction_method }}
                                                                            </p>
                                                                        </div>
                                                                        <div class="amount_div h4 mb-0 mt-2">

                                                                            @if ($transaction->transaction_type > 0 || $transaction->transaction_type == 'add_fund_by_admin')
                                                                                <span>&#43;</span>
                                                                            @else
                                                                                <span>&#45;</span>
                                                                            @endif
                                                                            <span>&#8377;</span>
                                                                            {{ number_format($amount, 2) }}
                                                                        </div>

                                                                    </div>
                                                                @endif
                                                            @endif
                                                        @empty
                                                            <div class="p-4 text-center">No transactions found.</div>
                                                        @endforelse


                                                        <div class="walletFaq">
                                                            <h6 class="faqHead"><strong>FAQ's</strong></h6>
                                                            <ul class="faq_list">
                                                                @if ($faqwallet->isEmpty())
                                                                    <li>No FAQs available at the moment.</li>
                                                                @else
                                                                    @foreach ($faqwallet as $value)
                                                                        <li>
                                                                            <p class="mb-0">
                                                                                <strong>{{ $value->question }}?</strong>
                                                                            </p>
                                                                            <p class="mb-0">{{ $value->answer }}</p>
                                                                        </li>
                                                                    @endforeach
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    </div>

                                                @endif
                                            </section>
                                            <div class="modal fade cancelModal" id="cancellationModal" tabindex="-1"
                                                role="dialog" aria-labelledby="cancellationModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body">
                                                            <div class="p-4 mb-2"
                                                                style="border:1px solid #2E6CB2; border-radius:15px; overflow:hidden; word-wrap:break-word;">
                                                                <!-- withdraw.blade.php -->

                                                                @if (!auth()->user()->razorpay_fund_account_id)
                                                                    <form method="POST"
                                                                        action="{{ route('razorpay.createFundAccount') }}">
                                                                        @csrf
                                                                        <input type="text" name="account_number"
                                                                            class="form-control"
                                                                            placeholder="Account Number" required>
                                                                        <input type="text" name="ifsc"
                                                                            class="form-control" placeholder="IFSC Code"
                                                                            required>
                                                                        <button type="submit">Save Bank Details</button>
                                                                    </form>
                                                                @else
                                                                    <form method="POST"
                                                                        action="{{ route('razorpay.withdraw') }}">
                                                                        @csrf
                                                                        <input type="text" name="amount"
                                                                            class="form-control"
                                                                            placeholder="Enter Amount" required>
                                                                        <button type="submit">Withdraw</button>
                                                                    </form>
                                                                @endif

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <section id="tab-3" class="tab-body entry-content">
                                                <style>
                                                    .add-address {
                                                        font-weight: bold;
                                                        margin-right: 5px;
                                                    }

                                                    #addAddressToggle {
                                                        cursor: pointer;
                                                    }
                                                </style>

                                                <div class="container mt-1 px-0">

                                                    <!-- Add Address Button -->
                                                    <div class="myAccAddWrapper">
                                                        <h6 id="addAddressToggle" class="mb-0">
                                                            <span class="add-address">&#43;</span> Add a new Address
                                                        </h6>
                                                    </div>

                                                    <!-- Hidden Form -->
                                                    {{-- <div id="addAddressFormContainer" class="addAddressFormContainer">
                                                        <form action="{{ route('address-store-1') }}" method="POST"
                                                            id="addAddressForm">
                                                            @csrf
                                                            <div class="container">
                                                                <!-- Full Name -->
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="contact_person_name"
                                                                            class="form-control" placeholder="Full Name"
                                                                            required>
                                                                    </div>
                                                                    <!-- Phone Number -->
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="phone"
                                                                            class="form-control"
                                                                            placeholder="10 Digit - Mobile No." required>
                                                                    </div>
                                                                </div>

                                                                <!-- Full Address -->
                                                                <div class="row">
                                                                    <div class="col-md-12 mb-3">
                                                                        <textarea name="address" class="form-control" rows="3" placeholder="Full Address" required></textarea>
                                                                    </div>
                                                                </div>

                                                                <!-- Pincode and Landmark -->
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="zip"
                                                                            class="form-control" id="pincode"
                                                                            placeholder="Pincode" required>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="landmark"
                                                                            class="form-control"
                                                                            placeholder="Landmark (Optional)">
                                                                    </div>
                                                                </div>

                                                                <!-- City and State -->
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="city"
                                                                            id="city" class="form-control"
                                                                            placeholder="City" required>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="state"
                                                                            id="state" class="form-control"
                                                                            placeholder="State" required>
                                                                    </div>
                                                                </div>

                                                                <!-- Address Type (Home, Work, Other) -->
                                                                <div class="row">
                                                                    <div class="col-md-12 mb-3">
                                                                        <label class="d-block mb-2">Address Type</label>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="address_type" value="Home"
                                                                                required>
                                                                            <label class="form-check-label">Home</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="address_type" value="Work"
                                                                                required>
                                                                            <label class="form-check-label">Work</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="address_type" value="Other"
                                                                                required>
                                                                            <label class="form-check-label">Other</label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Save / Cancel buttons -->
                                                                <div class="row">
                                                                    <div class="col-md-12 text-right">
                                                                        <button type="button" id="cancelButton"
                                                                            class="btn btn-secondary mr-2">Cancel</button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Save</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div> --}}

                                                    <div id="addAddressFormContainer" class="addAddressFormContainer">
                                                        <form action="{{ route('address-store-1') }}" method="POST"
                                                            id="addAddressForm" novalidate>
                                                            @csrf
                                                            <div class="container">

                                                                <!-- Full Name -->
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="contact_person_name"
                                                                            class="form-control" placeholder="Full Name"
                                                                            required>
                                                                        <div class="invalid-feedback">Please enter your
                                                                            full name.</div>
                                                                    </div>

                                                                    <!-- Phone Number -->
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="phone"
                                                                            class="form-control"
                                                                            placeholder="10 Digit - Mobile No." required>
                                                                        <div class="invalid-feedback">Please enter a valid
                                                                            10-digit mobile number.</div>
                                                                    </div>
                                                                </div>

                                                                <!-- Full Address -->
                                                                <div class="row">
                                                                    <div class="col-md-12 mb-3">
                                                                        <textarea name="address" class="form-control" rows="3" placeholder="Full Address" required></textarea>
                                                                        <div class="invalid-feedback">Please enter your
                                                                            full address.</div>
                                                                    </div>
                                                                </div>

                                                                <!-- Pincode and Landmark -->
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="zip"
                                                                            class="form-control" id="pincode"
                                                                            placeholder="Pincode" required>
                                                                        <div class="invalid-feedback">Please enter a valid
                                                                            6-digit pincode.</div>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="landmark"
                                                                            class="form-control"
                                                                            placeholder="Landmark (Optional)">
                                                                        <div class="invalid-feedback"></div>
                                                                    </div>
                                                                </div>

                                                                <!-- City and State -->
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="city"
                                                                            id="city" class="form-control"
                                                                            placeholder="City" required>
                                                                        <div class="invalid-feedback">Please enter your
                                                                            city.</div>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="state"
                                                                            id="state" class="form-control"
                                                                            placeholder="State" required>
                                                                        <div class="invalid-feedback">Please enter your
                                                                            state.</div>
                                                                    </div>
                                                                </div>

                                                                <!-- Address Type -->
                                                                <div class="row">
                                                                    <div class="col-md-12 mb-3">
                                                                        <label class="d-block mb-2">Address Type</label>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="address_type" value="Home"
                                                                                required>
                                                                            <label class="form-check-label">Home</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="address_type" value="Work"
                                                                                required>
                                                                            <label class="form-check-label">Work</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="address_type" value="Other"
                                                                                required>
                                                                            <label class="form-check-label">Other</label>
                                                                        </div>
                                                                        <div class="invalid-feedback d-block"
                                                                            id="addressTypeError" style="display:none;">
                                                                            Please select an address type.</div>
                                                                    </div>
                                                                </div>

                                                                <!-- Buttons -->
                                                                <div class="row">
                                                                    <div class="col-md-12 text-right">
                                                                        <button type="button" id="cancelButton"
                                                                            class="btn btn-secondary mr-2">Cancel</button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Save</button>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </form>
                                                    </div>




                                                </div>

                                                <script>
                                                    // When the user clicks on "Add a new Address"
                                                    document.getElementById('addAddressToggle').addEventListener('click', function() {
                                                        const formContainer = document.getElementById('addAddressFormContainer');
                                                        formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
                                                    });

                                                    // When the user clicks "Cancel"
                                                    document.getElementById('cancelButton').addEventListener('click', function() {
                                                        const formContainer = document.getElementById('addAddressFormContainer');
                                                        formContainer.style.display = 'none'; // Hide form on cancel
                                                    });
                                                </script>

                                                <h4 class="mb-1">My Addresses</h4>
                                                <div class="myDefAddWrapper">
                                                    @if ($addresses->isEmpty())
                                                        <div class="col-md-12">
                                                            <img src="{{ asset('public/website/assets/images/No saved address.webp') }}"
                                                                alt="Empty Wishlist" class="img-fluid"
                                                                style="max-width: 50% !important;height: auto;margin: auto;">
                                                        </div>
                                                    @else
                                                        @foreach ($addresses as $address)
                                                            <div
                                                                class="media p-4 {{ !$loop->first ? 'border-top: 1px solid #2E6CB2;' : '' }}">
                                                                <div class="media-body">
                                                                    <a href="#" class="badge"
                                                                        style="background-color: #2e6cb2; color: #ffffff;">
                                                                        {{ ucfirst($address->address_type) }}
                                                                    </a>
                                                                    <p class="mb-0" style="color: #2E6CB2;">
                                                                        <strong>{{ $address->contact_person_name }} -
                                                                            {{ $address->phone }}</strong>
                                                                    </p>
                                                                    <p class="mb-0">{{ $address->address }},
                                                                        {{ $address->city }}, {{ $address->state }} -
                                                                        {{ $address->zip }}
                                                                    </p>
                                                                </div>
                                                                <div class="amount_div h4 mb-0 mt-2">
                                                                    <button type="button" class="btnEdit mr-1"
                                                                        data-id="{{ $address->id }}"
                                                                        data-name="{{ $address->contact_person_name }}"
                                                                        data-phone="{{ $address->phone }}"
                                                                        data-address="{{ $address->address }}"
                                                                        data-city="{{ $address->city }}"
                                                                        data-state="{{ $address->state }}"
                                                                        data-zip="{{ $address->zip }}"
                                                                        data-address-type="{{ $address->address_type }}"
                                                                        data-landmark="{{ $address->landmark }}">
                                                                        Edit
                                                                    </button>
                                                                    <form action="{{ route('address-delete-1') }}"
                                                                        method="POST"
                                                                        id="removeAddressForm_{{ $address->id }}"
                                                                        style="display:inline;">
                                                                        @csrf
                                                                        <input type="hidden" name="address_id"
                                                                            value="{{ $address->id }}">
                                                                        <button type="button" class="btnRemove ml-1"
                                                                            onclick="confirmRemove({{ $address->id }})">Remove</button>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                            <!-- Hidden Edit Form for this address -->
                                                            <div id="editAddressForm_{{ $address->id }}"
                                                                class="editAddWrapper">
                                                                <form action="{{ route('address-update-1') }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="address_id"
                                                                        value="{{ $address->id }}">
                                                                    <!-- Full Name -->
                                                                    <div class="row">
                                                                        <div class="col-md-6 mb-3">
                                                                            <input type="text"
                                                                                name="contact_person_name"
                                                                                class="form-control contact_person_name"
                                                                                placeholder="Full Name"
                                                                                value="{{ $address->contact_person_name }}"
                                                                                required>
                                                                        </div>
                                                                        <!-- Phone Number -->
                                                                        <div class="col-md-6 mb-3">
                                                                            <input type="text" name="phone"
                                                                                class="form-control phone"
                                                                                placeholder="10 Digit - Mobile No."
                                                                                value="{{ $address->phone }}" required>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Full Address -->
                                                                    <div class="row">
                                                                        <div class="col-md-12 mb-3">
                                                                            <textarea name="address" class="form-control address" rows="3" placeholder="Full Address" required>{{ $address->address }}</textarea>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Pincode and Landmark -->
                                                                    <div class="row">
                                                                        <div class="col-md-6 mb-3">
                                                                            <input type="text" name="zip"
                                                                                class="form-control zip"
                                                                                placeholder="Pincode"
                                                                                value="{{ $address->zip }}" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <input type="text" name="landmark"
                                                                                class="form-control landmark"
                                                                                placeholder="Landmark (Optional)"
                                                                                value="{{ $address->landmark }}">
                                                                        </div>
                                                                    </div>

                                                                    <!-- City and State -->
                                                                    <div class="row">
                                                                        <div class="col-md-6 mb-3">
                                                                            <input type="text" name="city"
                                                                                class="form-control city"
                                                                                placeholder="City"
                                                                                value="{{ $address->city }}" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <input type="text" name="state"
                                                                                class="form-control state"
                                                                                placeholder="State"
                                                                                value="{{ $address->state }}" required>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Address Type (Home, Work, Other) -->
                                                                    <div class="row">
                                                                        <div class="col-md-12 mb-3">
                                                                            <label class="d-block mb-2">Address
                                                                                Type</label>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input"
                                                                                    type="radio" name="address_type"
                                                                                    value="Home"
                                                                                    {{ $address->address_type == 'Home' ? 'checked' : '' }}
                                                                                    required>
                                                                                <label
                                                                                    class="form-check-label">Home</label>
                                                                            </div>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input"
                                                                                    type="radio" name="address_type"
                                                                                    value="Work"
                                                                                    {{ $address->address_type == 'Work' ? 'checked' : '' }}
                                                                                    required>
                                                                                <label
                                                                                    class="form-check-label">Work</label>
                                                                            </div>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input"
                                                                                    type="radio" name="address_type"
                                                                                    value="Other"
                                                                                    {{ $address->address_type == 'Other' ? 'checked' : '' }}
                                                                                    required>
                                                                                <label
                                                                                    class="form-check-label">Other</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Save / Cancel buttons -->
                                                                    <div class="row">
                                                                        <div class="col-md-12 text-right">
                                                                            <button type="button"
                                                                                class="btn btn-secondary cancelButton">Cancel</button>
                                                                            <button type="submit"
                                                                                class="btn btn-primary">Save</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </section>



                                            <section id="tab-4" class="tab-body entry-content persInfoRespo">
                                                <h4 class="text-center mb-1">Personal Details</h4>
                                                <p class="text-center">
                                                    Hey there! Fill in your personal details for a personalized
                                                    InteriorChowk experience.
                                                </p>

                                                <div class="p-4 mb-2 persInfoFrm"
                                                    style="border:1px solid #2E6CB2; border-radius:15px; overflow:hidden; word-wrap:break-word;">
                                                    {{-- Success message --}}
                                                    @if (session('success'))
                                                        <div class="alert alert-success">
                                                            {{ session('success') }}
                                                        </div>
                                                    @endif

                                                    {{-- Personal Info Form --}}
                                                    <form id="personal-info-form" method="POST"
                                                        action="{{ route('user-update-1') }}">
                                                        @csrf

                                                        <h6 style="margin-bottom:10px;">Personal Information</h6>

                                                        <div class="container mt-2">
                                                            {{-- First / Last Name --}}
                                                            <div class="row">
                                                                <div class="col-12 col-sm-6 col-md-6">
                                                                    <input type="text" name="f_name"
                                                                        class="form-control" placeholder="First Name"
                                                                        value="{{ old('f_name', $user->f_name) }}"
                                                                        disabled>
                                                                </div>
                                                                <div class="col-12 col-sm-6 col-md-6">
                                                                    <input type="text" name="l_name"
                                                                        class="form-control" placeholder="Last Name"
                                                                        value="{{ old('l_name', $user->l_name) }}"
                                                                        disabled>
                                                                </div>
                                                            </div>

                                                            {{-- Gender --}}
                                                            <div class="row mt-3">
                                                                <div class="col-md-12">
                                                                    <label class="d-block mb-1 gnderLbl"><strong>Your
                                                                            Gender</strong></label>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="gender" id="gender_male"
                                                                            value="Male"
                                                                            {{ old('gender', $user->gender) === 'Male' ? 'checked' : '' }}
                                                                            disabled>
                                                                        <label class="form-check-label"
                                                                            for="gender_male">Male</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="gender" id="gender_female"
                                                                            value="Female"
                                                                            {{ old('gender', $user->gender) === 'Female' ? 'checked' : '' }}
                                                                            disabled>
                                                                        <label class="form-check-label"
                                                                            for="gender_female">Female</label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Email / Phone --}}
                                                            <div class="row mt-3">
                                                                <div class="col-md-6">
                                                                    <input type="email" name="email"
                                                                        class="form-control" placeholder="E-mail@mail.com"
                                                                        value="{{ old('email', $user->email) }}" disabled>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" name="phone"
                                                                        class="form-control"
                                                                        placeholder="10 digit - Mobile No."
                                                                        value="{{ old('phone', $user->phone) }}" disabled>
                                                                </div>
                                                            </div>

                                                            {{-- Buttons --}}
                                                            <div class="row mt-3">
                                                                <div class="col text-center">
                                                                    <button type="button" id="edit-btn"
                                                                        class="btn btn-primary px-5 mr-2">Edit</button>
                                                                    <button type="submit" id="save-btn"
                                                                        class="btn btn-info px-5 ml-2"
                                                                        style="display:none;">Save</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>

                                                    {{-- FAQ --}}
                                                    <div class="mt-5">
                                                        <h6 class="faqHead" style="margin-bottom:10px;">
                                                            <strong>FAQ’s</strong>
                                                        </h6>
                                                        <ul class="faq_list">
                                                            @if ($faqpersonaldetails->isEmpty())
                                                                <li>No FAQs available at the moment.</li>
                                                            @else
                                                                @foreach ($faqpersonaldetails as $value)
                                                                    <li>
                                                                        <p class="mb-0">
                                                                            <strong>{{ $value->question }}?</strong>
                                                                        </p>
                                                                        <p class="mb-0">{{ $value->answer }}</p>
                                                                    </li>
                                                                @endforeach
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </section>
                                            <section id="tab-5" class="tab-body entry-content wishListRespo">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-1">
                                                            <h4>My Wishlist</h4>
                                                        </div>
                                                        @if ($wishlists->isEmpty())
                                                            <div class="col-md-12">
                                                                <img src="https://interiorchowk.com/public/website/assets/images/Wishlistnew.png"
                                                                    alt="Empty Wishlist" class="img-fluid"
                                                                    style="width:50% !important;height:unset!important;margin-bottom:unset !important;margin:auto !important;">
                                                            </div>
                                                        @else
                                                            @foreach ($wishlists as $wishlist)
                                                                <div class="col-6 col-sm-6 col-md-3">
                                                                    <div class="product product-3">
                                                                        <figure class="product-media">
                                                                            @if ($wishlist->discount_type == 'percent' && $wishlist->discount > 0)
                                                                                <span
                                                                                    class="product-label label-new">{{ round($wishlist->discount, 0) }}%
                                                                                    off</span>
                                                                            @elseif($wishlist->discount_type == 'flat' && $wishlist->discount > 0)
                                                                                <span
                                                                                    class="product-label label-new">₹{{ number_format($wishlist->discount, 0) }}
                                                                                    off</span>
                                                                            @endif


                                                                            <!-- <span
                                                                                                                                                                                                                                                                                                                                                                                            class="product-label label-top">New</span> -->
                                                                            <a
                                                                                href="{{ url('product/' . $wishlist->slug) }}">
                                                                                <img 
                                                                                {{-- src="{{ asset('storage/app/public/images/' . $wishlist->thumbnail_image) }}" --}}
                                                                                src="{{ rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . ($wishlist->thumbnail_image ?? 'default.jpg') }}"
                                                                                    alt="{{ $wishlist->name }}"
                                                                                    class="product-image rounded-lg">
                                                                            </a>
                                                                        </figure>
                                                                        <div class="product-body">
                                                                            <div class="product-cat">
                                                                                <a
                                                                                    href="#">{{ $wishlist->category ?? 'Uncategorized' }}</a>
                                                                            </div>
                                                                            <h5 style="font-weight: 300; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;"
                                                                                class="mb-1"
                                                                                title="{{ $wishlist->name }}">
                                                                                <a
                                                                                    href="{{ url('product/' . $wishlist->slug) }}">{{ $wishlist->name }}</a>
                                                                            </h5>
                                                                            <h6 class="product-type mb-0">
                                                                                {{ $wishlist->variation }}
                                                                            </h6>
                                                                            <!-- <p class="mb-0" style="color:#FF7373;">{{ $wishlist->quantity }} Units left</p> -->
                                                                            @if ($wishlist->quantity == 0)
                                                                                <p class="text-red-500">Out of Stock</p>
                                                                            @elseif ($wishlist->quantity <= 10)
                                                                                <p class="mb-0" style="color:#FF7373;">
                                                                                    {{ $wishlist->quantity }} Units Left
                                                                                </p>
                                                                            @endif
                                                                            <div class="d-flex ml-auto">
                                                                                <div class="product-price">
                                                                                    ₹ {{ $wishlist->listed_price }}
                                                                                    @if ($wishlist->discount_percent)
                                                                                        <span class="price-cut">₹
                                                                                            {{ $wishlist->variant_mrp }}</span>
                                                                                    @endif
                                                                                </div>
                                                                                <!-- Add to Cart Button -->
                                                                                <button type="button"
                                                                                    class="btnAddToCart"
                                                                                    data-slug="{{ $wishlist->slug }}"
                                                                                    data-product_id="{{ $wishlist->product_id }}"
                                                                                    data-variant="{{ $wishlist->variation }}">
                                                                                    <i class="fa fa-shopping-cart"></i>
                                                                                </button>

                                                                                <!-- Delete Button -->
                                                                                <button type="button" class="btnDelete"
                                                                                    data-slug="{{ $wishlist->slug }}">
                                                                                    <i class="fa fa-trash-o"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    @if ($recently_viewed->isNotEmpty())
                                                        <div class="row mt-5 cartRecViewWrap">
                                                            <div class="col-md-12">
                                                                <div class="product-details-top">
                                                                    <div class="heading heading-flex mb-3">
                                                                        <div class="heading-left">
                                                                            <h2 class="title">Recently Viewed Items</h2>
                                                                        </div>
                                                                    </div>
                                                                    <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow"
                                                                        data-toggle="owl"
                                                                        data-owl-options='{
                                                                                "nav": false, 
                                                                                "dots": true,
                                                                                "margin": 20,
                                                                                "loop": false,
                                                                                "responsive": {
                                                                                    "0": {"items":2},
                                                                                    "480": {"items":2},
                                                                                    "768": {"items":3},
                                                                                    "992": {"items":4},
                                                                                    "1200": {"items":4, "nav": true, "dots": false}
                                                                                }
                                                                            }'>

                                                                        @foreach ($recently_viewed as $item)
                                                                            <div class="product product-7">
                                                                                <figure class="product-media">

                                                                                    @if ($item->discount_type == 'percent' && $item->discount > 0)
                                                                                        <span
                                                                                            class="product-label label-new">{{ round($item->discount, 0) }}%
                                                                                            off</span>
                                                                                    @elseif($item->discount_type == 'flat' && $item->discount > 0)
                                                                                        <span
                                                                                            class="product-label label-new">₹{{ number_format($item->discount, 0) }}
                                                                                            off</span>
                                                                                    @endif

                                                                                    <a
                                                                                        href="{{ url('product/' . $item->slug) }}">
                                                                                        <img 
                                                                                        {{-- src="{{ asset('storage/app/public/images/' . $item->thumbnail_image) }}" --}}
                                                                                        src="{{ rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . ($item->thumbnail_image ?? 'default.jpg') }}"
                                                                                            alt="{{ $item->name }}"
                                                                                            class="product-image"
                                                                                            style="max-width: 100%;">
                                                                                    </a>
                                                                                </figure>
                                                                                <div class="product-body">
                                                                                    <div class="product-cat">
                                                                                        <a
                                                                                            href="#">{{ $item->category ?? 'Uncategorized' }}</a>
                                                                                    </div>
                                                                                    <h3 class="product-title">
                                                                                        <a
                                                                                            href="{{ url('product/' . $item->slug) }}">{{ $item->name }}</a>
                                                                                    </h3>
                                                                                    <div class="product-price">
                                                                                        ₹ {{ $item->listed_price }}
                                                                                        @if ($item->discount)
                                                                                            <span class="price-cut">₹
                                                                                                {{ $item->variant_mrp }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                </div>
                                            </section>

                                            <section id="tab-6" class="tab-body entry-content cartRespo">
                                                <div class="container">
                                                    @php
                                                        $totalAmount = 0;
                                                        $totalDiscount = 0;
                                                        $deliveryCharges = 50;
                                                        $tatalamounts = 0;

                                                        foreach ($cart as $cartItem) {
                                                            if ($cartItem->is_selected == 1) {
                                                                $mrp = $cartItem->variant_mrp;
                                                                $price = $cartItem->listed_price;
                                                                $qty = $cartItem->cart_qty;

                                                                $totalAmount += $price * $qty;
                                                                $tatalamounts += $mrp * $qty;
                                                                $totalDiscount += ($mrp - $price) * $qty;
                                                            }
                                                        }

                                                        $finalAmount =
                                                            $totalAmount > 0 ? $totalAmount + $deliveryCharges : 0;
                                                        $totalSaving = $totalDiscount;

                                                        $user = auth()->user();
                                                    @endphp
                                                    @if ($cart->isNotEmpty())
                                                        <div class="row">
                                                            <div class="col-md-7">
                                                                <h4 class="mb-1">My Cart</h4>
                                                                @foreach ($cart as $cartItem)
                                                                    @php
                                                                        $images = json_decode($cartItem->image, true);
                                                                    @endphp
                                                                    <div class="media mt-4 cart-item"
                                                                        data-cart-id="{{ $cartItem->id }}">
                                                                        <div class="position-relative">
                                                                            <img style="height: 100px;"
                                                                                {{-- src="{{ asset('storage/app/public/images/' . $images[0]) }}" --}}
                                                                                src="{{ rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . ($images[0] ?? 'default.jpg') }}"
                                                                                class="mr-3"
                                                                                alt="{{ $cartItem->name }}">
                                                                            {{-- ✅ Checkbox --}}
                                                                            <input type="checkbox" class="select-checkbox"
                                                                                name="select[]"
                                                                                value="{{ $cartItem->id }}"
                                                                                {{ $cartItem->is_selected == 1 ? 'checked' : '' }}>
                                                                        </div>
                                                                        <div class="media-body">
                                                                            <a
                                                                                href="{{ url('product/' . $cartItem->slug) }}">
                                                                                <h5 style="font-weight: 300;"
                                                                                    class="mb-1 mt-0">
                                                                                    {{ strlen($cartItem->name) > 30 ? substr($cartItem->name, 0, 30) . '...' : $cartItem->name }}
                                                                                </h5>
                                                                            </a>
                                                                            @if ($cartItem->quantity == 0)
                                                                                <p class="text-red-500">Out of Stock</p>
                                                                            @elseif ($cartItem->quantity <= 10)
                                                                                <p class="mb-0" style="color:#FF7373;">
                                                                                    {{ $cartItem->quantity }} Units Left
                                                                                </p>
                                                                            @endif

                                                                            @if ($cartItem->color_name)
                                                                                <p class="mb-0">Color:
                                                                                    {{ $cartItem->color_name }}</p>
                                                                            @endif
                                                                            @if ($cartItem->sizes)
                                                                                <p class="mb-0">Size:
                                                                                    {{ $cartItem->sizes }}</p>
                                                                            @endif
                                                                            @if ($cartItem->variation)
                                                                                <p class="mb-0">Variation:
                                                                                    {{ $cartItem->variation }}</p>
                                                                            @endif

                                                                        </div>
                                                                        <div class="media-right">
                                                                            <div>
                                                                                <a href="javascript:void(0);"
                                                                                    class="delete-cart-item"
                                                                                    data-cart-id="{{ $cartItem->cart_id }}">
                                                                                    <i
                                                                                        class="fa fa-trash-o text-danger"></i>
                                                                                </a>
                                                                            </div>

                                                                            <div
                                                                                class="product-price justify-content-center">
                                                                                ₹
                                                                                {{ $cartItem->listed_price * $cartItem->cart_qty }}
                                                                                @if ($cartItem->discount > 0)
                                                                                    <span class="price-cut">₹
                                                                                        {{ $cartItem->variant_mrp }}</span>
                                                                                @endif
                                                                            </div>

                                                                            @if ($cartItem->discount > 0)
                                                                                <div>
                                                                                    <span
                                                                                        class="badge badge-pill badge-primary">
                                                                                        @if ($cartItem->discount_type == 'percent')
                                                                                            {{ round($cartItem->discount, 0) }}%
                                                                                            off
                                                                                        @else
                                                                                            ₹{{ number_format($cartItem->discount, 0) }}
                                                                                            off
                                                                                        @endif
                                                                                    </span>
                                                                                </div>
                                                                            @endif

                                                                            <div class="product-details-quantity">
                                                                                <input type="number"
                                                                                    class="form-control qty-input"
                                                                                    value="{{ $cartItem->cart_qty }}"
                                                                                    min="1"
                                                                                    max="{{ $cartItem->quantity }}"
                                                                                    step="1"
                                                                                    data-cart-id="{{ $cartItem->cart_id }}"
                                                                                    required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="col-md-5 cart-doc">
                                                                <div class="p-4 mb-2 text-login"
                                                                    style="border: 1px solid #2E6CB2;border-radius: 15px;">
                                                                    <div class="card border-0 mt-1">
                                                                        <img style="height: 200px; object-fit: cover; object-position: center; border-radius: 15px; max-width: 100%;"
                                                                            src="{{ asset('public/website/new/assets/images/products/product-1.webp') }}"
                                                                            class="card-img-top" alt="...">

                                                                        <div class="card-body p-0 mt-2">
                                                                            <h6>Total Bill Breakdown</h6>
                                                                            <p class="mb-0"><strong>MRP</strong>
                                                                                <span>₹
                                                                                    {{ number_format($tatalamounts, 2) }}</span>
                                                                            </p>
                                                                            <p class="mb-0"><strong>Final product
                                                                                    price</strong> <span>₹
                                                                                    {{ number_format($totalAmount, 2) }}</span>
                                                                            </p>
                                                                            @if ($totalAmount > 0)
                                                                                @if ($user->f_name != null)
                                                                                    <a href="{{ route('checkout') }}"
                                                                                        class="btn btn-info w-100 p-3">Proceed
                                                                                        to
                                                                                        Purchase</a>
                                                                                @else
                                                                                    <button type="button"
                                                                                        class="btn btn-info w-100 p-3"
                                                                                        data-toggle="modal"
                                                                                        data-target="#cancellationModal">
                                                                                        Proceed to Purchase
                                                                                    </button>
                                                                                @endif
                                                                                <a href="javascript:void(0);"
                                                                                    class="btn btn-primary rounded-pill w-100 mt-2">
                                                                                    You’re Saving ₹
                                                                                    {{ number_format($totalSaving, 2) }}
                                                                                    today!
                                                                                </a>
                                                                            @else
                                                                                <p class="text-danger mt-3 text-center">
                                                                                    ⚠️ Please select at least one
                                                                                    product to
                                                                                    proceed.</p>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>



                                                        </div>
                                                    @else
                                                        <div class="col-md-12">
                                                            <img src="https://interiorchowk.com/public/website/assets/images/Empty Cart.webp"
                                                                alt="Empty Wishlist" class="img-fluid"
                                                                style="max-width: 50% !important;height: auto;margin: auto;">
                                                        </div>
                                                    @endif

                                                    <div class="modal fade cancelModal" id="cancellationModal"
                                                        tabindex="-1" role="dialog"
                                                        aria-labelledby="cancellationModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-12 col-sm-12 col-md-12">
                                                                            <form id="personal-info-form" method="POST"
                                                                                action="{{ route('user-update-1') }}">
                                                                                @csrf

                                                                                <h6 style="margin-bottom:10px;">Personal
                                                                                    Information</h6>

                                                                                <div class="container mt-2">

                                                                                    <!-- First / Last Name -->
                                                                                    <div class="row">
                                                                                        <div class="col-md-6">
                                                                                            <input type="text"
                                                                                                name="f_name"
                                                                                                class="form-control"
                                                                                                placeholder="First Name"
                                                                                                value="{{ old('f_name', $user->f_name) }}"
                                                                                                pattern="^[A-Za-z]+$"
                                                                                                oninput="this.value = this.value.replace(/\s/g, '');"
                                                                                                title="First name cannot contain spaces or numbers"
                                                                                                required>
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <input type="text"
                                                                                                name="l_name"
                                                                                                class="form-control"
                                                                                                placeholder="Last Name"
                                                                                                value="{{ old('l_name', $user->l_name) }}"
                                                                                                pattern="^[A-Za-z]+$"
                                                                                                title="Last name cannot contain spaces or numbers"
                                                                                                required>
                                                                                        </div>
                                                                                    </div>

                                                                                    <!-- Email / Phone -->
                                                                                    <div class="row mt-3">
                                                                                        <div class="col-md-6">
                                                                                            <input type="email"
                                                                                                name="email"
                                                                                                class="form-control"
                                                                                                placeholder="E-mail@mail.com"
                                                                                                value="{{ old('email', $user->email) }}"
                                                                                                oninput="validateEmail(this)"
                                                                                                required>
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <input type="text"
                                                                                                name="phone"
                                                                                                class="form-control"
                                                                                                placeholder="10 digit - Mobile No."
                                                                                                value="{{ old('phone', $user->phone) }}"
                                                                                                pattern="^[6-9][0-9]{9}$"
                                                                                                maxlength="10"
                                                                                                title="Phone must be 10 digits starting with 6, 7, 8, or 9"
                                                                                                required>
                                                                                        </div>
                                                                                    </div>

                                                                                    <!-- Gender -->
                                                                                    <div class="row mt-3">
                                                                                        <div class="col-md-12">
                                                                                            <label
                                                                                                class="d-block mb-1"><strong>Your
                                                                                                    Gender</strong></label>
                                                                                            <div
                                                                                                class="form-check form-check-inline">
                                                                                                <input
                                                                                                    class="form-check-input"
                                                                                                    type="radio"
                                                                                                    name="gender"
                                                                                                    id="gender_male"
                                                                                                    value="Male"
                                                                                                    {{ old('gender', $user->gender) === 'Male' ? 'checked' : '' }}
                                                                                                    required>
                                                                                                <label
                                                                                                    class="form-check-label"
                                                                                                    for="gender_male">Male</label>
                                                                                            </div>
                                                                                            <div
                                                                                                class="form-check form-check-inline">
                                                                                                <input
                                                                                                    class="form-check-input"
                                                                                                    type="radio"
                                                                                                    name="gender"
                                                                                                    id="gender_female"
                                                                                                    value="Female"
                                                                                                    {{ old('gender', $user->gender) === 'Female' ? 'checked' : '' }}
                                                                                                    required>
                                                                                                <label
                                                                                                    class="form-check-label"
                                                                                                    for="gender_female">Female</label>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <!-- Buttons -->
                                                                                    <div class="row mt-4">
                                                                                        <div class="col text-center">
                                                                                            <button type="submit"
                                                                                                id="save-btn"
                                                                                                class="btn btn-info px-5 ml-2">Save</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </form>



                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- ✅ Checkbox selection AJAX --}}

                                                    <script>
                                                        function validateEmail(input) {
                                                            // Remove spaces automatically
                                                            input.value = input.value.replace(/\s/g, '');

                                                            const email = input.value.trim();
                                                            const errorSpan = document.getElementById("email-error");

                                                            // Simple regex for email format
                                                            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                                                            if (email === "") {
                                                                errorSpan.textContent = "";
                                                                input.classList.remove("is-invalid", "is-valid");
                                                            } else if (!regex.test(email)) {
                                                                errorSpan.textContent = "Please enter a valid email address.";
                                                                input.classList.add("is-invalid");
                                                                input.classList.remove("is-valid");
                                                            } else {
                                                                errorSpan.textContent = "";
                                                                input.classList.remove("is-invalid");
                                                                input.classList.add("is-valid");
                                                            }
                                                        }
                                                    </script>



                                                    <script>
                                                        document.querySelector("input[name='phone']").addEventListener("input", function(e) {
                                                            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
                                                        });


                                                        $(document).ready(function() {
                                                            $('.select-checkbox').on('change', function() {
                                                                let Id = $(this).val();
                                                                let selectedId = $(this).is(':checked') ? 1 : 0;

                                                                $.ajax({
                                                                    url: "{{ route('select-cart') }}",
                                                                    method: "POST",
                                                                    data: {
                                                                        _token: "{{ csrf_token() }}",
                                                                        select_id: selectedId,
                                                                        Id: Id
                                                                    },
                                                                    success: function(response) {
                                                                        location.reload(); // refresh after selection change
                                                                    }
                                                                });
                                                            });
                                                        });


                                                        // Quantity update
                                                        $(document).ready(function() {
                                                            $('.qty-input').on('change', function() {
                                                                const cartId = $(this).data('cart-id');
                                                                const newQty = $(this).val();

                                                                if (newQty < 1) {
                                                                    alert("Minimum quantity is 1");
                                                                    $(this).val(1);
                                                                    return;
                                                                }

                                                                $.ajax({
                                                                    url: "{{ route('cart.updateQuantity_1') }}",
                                                                    method: "POST",
                                                                    data: {
                                                                        _token: "{{ csrf_token() }}",
                                                                        cart_id: cartId,
                                                                        quantity: newQty
                                                                    },
                                                                    success: function() {
                                                                        location.reload();
                                                                    }
                                                                });
                                                            });
                                                        });


                                                        $(document).on('click', '.delete-cart-item', function() {
                                                            let cartId = $(this).data('cart-id');

                                                            if (!confirm("Are you sure you want to remove this item from the cart?")) return;

                                                            $.ajax({
                                                                url: "{{ route('cart.remove_1') }}",
                                                                type: "POST",
                                                                data: {
                                                                    _token: '{{ csrf_token() }}',
                                                                    cart_id: cartId
                                                                },
                                                                success: function(response) {
                                                                    alert(response.message);
                                                                    location.reload(); // Or use JS to remove the item from DOM dynamically
                                                                },
                                                                error: function() {
                                                                    alert("Something went wrong while removing the item.");
                                                                }
                                                            });
                                                        });
                                                    </script>









                                                    <!-- Personal Info Modal -->




                                                    @if ($recently_viewed->isNotEmpty())
                                                        <div class="row mt-5">
                                                            <div class="col-md-12">
                                                                <div class="product-details-top">
                                                                    <div class="heading heading-flex mb-3">
                                                                        <div class="heading-left">
                                                                            <h2 class="title">Recently Viewed Items</h2>
                                                                        </div>
                                                                    </div>
                                                                    <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow"
                                                                        data-toggle="owl"
                                                                        data-owl-options='{
                                "nav": false, 
                                "dots": true,
                                "margin": 20,
                                "loop": false,
                                "responsive": {
                                    "0": {"items":2},
                                    "480": {"items":2},
                                    "768": {"items":3},
                                    "992": {"items":4},
                                    "1200": {"items":4, "nav": true, "dots": false}
                                }
                            }'>
                                                                        @foreach ($recently_viewed as $recentItem)
                                                                            <div class="product product-7">
                                                                                <figure class="product-media">
                                                                                    @if ($recentItem->discount_type == 'percent' && $recentItem->discount > 0)
                                                                                        <span
                                                                                            class="product-label label-new">{{ round($recentItem->discount, 0) }}%
                                                                                            off</span>
                                                                                    @elseif($recentItem->discount_type == 'flat' && $recentItem->discount > 0)
                                                                                        <span
                                                                                            class="product-label label-new">₹{{ number_format($recentItem->discount, 0) }}
                                                                                            off</span>
                                                                                    @endif



                                                                                    <a
                                                                                        href="{{ url('product/' . $recentItem->slug) }}">
                                                                                        <img 
                                                                                        {{-- src="{{ asset('storage/app/public/images/' . $recentItem->thumbnail_image) }}" --}}
                                                                                        src="{{ rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . ($recentItem->thumbnail_image ?? 'default.jpg') }}"
                                                                                            alt="{{ $recentItem->name }}"
                                                                                            class="product-image"
                                                                                            style="max-width: 100%;">
                                                                                    </a>
                                                                                </figure>
                                                                                <div class="product-body">
                                                                                    <div class="product-cat">
                                                                                        <a
                                                                                            href="#">{{ $recentItem->category ?? 'Uncategorized' }}</a>
                                                                                    </div>
                                                                                    <h3 class="product-title">
                                                                                        <a
                                                                                            href="{{ url('product/' . $recentItem->slug) }}">{{ $recentItem->name }}</a>
                                                                                    </h3>
                                                                                    <div class="product-price">
                                                                                        ₹
                                                                                        {{ $recentItem->listed_price }}
                                                                                        @if ($recentItem->discount)
                                                                                            <span class="price-cut">₹
                                                                                                {{ $recentItem->variant_mrp }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </section>


                                            <section id="tab-7" class="tab-body entry-content refErnRespo">
                                                <h4 class="text-center mb-1">Refer & Earn</h4>
                                                <p class="text-center">Invite friends & earn 100 InteriorChowk points for
                                                    every friend.</p>
                                                <div class="p-4 mb-2 imgRefWrapper"
                                                    style="border: 1px solid #2E6CB2;border-radius: 15px;">
                                                    <div class="text-center imgErnWrap">
                                                        <img class="d-inline-block"
                                                            src="{{ asset('public/website/assets/images/referandearn.webp') }}"
                                                            style="width:50%" />
                                                        <div class="referBG">
                                                            <h4 class="ml-3" style="color: #ffffff;">Your Referral Code:
                                                                <span>{{ strtoupper($user->referral_code) }}</span>
                                                            </h4>
                                                            <span>
                                                                <img class="mr-4"
                                                                    src="{{ asset('public/website/new/assets/images/share icon.webp') }}"
                                                                    onclick="toggleSharePopup(event)"
                                                                    style="cursor: pointer;height:40px;width:40px;" />
                                                                <img class="mr-4"
                                                                    src="{{ asset('public/website/new/assets/images/copy icon.webp') }}"
                                                                    style="cursor: pointer;height:40px;width:40px;"
                                                                    onclick="copyReferralText()" alt="Copy" />
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <textarea id="referralText" style="display: none;">
@php
    $userss = empty($user->f_name) ? "I'm" : $user->f_name . "'s";
@endphp
Hey!

{{ $userss }}  giving you a special invite to explore InteriorChowk's stunning & premium décor collection! ✨

🎁 Use {{ $userss }} referral code: {{ $user->referral_code }} to:
✅ Get 100 Reward Points instantly
✅ Free Shipping
✅ Extra 10% OFF
✅ Gift Vouchers worth ₹250/-

Tap the link to get started/download app now: https://interiorchowk.com
</textarea>
                                                    <!-- JS for copying text -->
                                                    <script>
                                                        function copyReferralText() {
                                                            var textArea = document.getElementById("referralText");
                                                            textArea.style.display = "block"; // for selection
                                                            textArea.select();
                                                            textArea.setSelectionRange(0, 99999); // for mobile
                                                            document.execCommand("copy");
                                                            textArea.style.display = "none";
                                                            // alert("Referral message copied! 📋");
                                                        }
                                                    </script>
                                                    @php
                                                        $referrer = empty($user->f_name) ? "I'm" : $user->f_name . "'s";
                                                        $message =
                                                            $referrer .
                                                            " giving you a special invite to explore InteriorChowk's stunning & premium décor collection! ✨

                                                🎁 Use referral code: dwfjmBoi to:
                                                ✅ Get 100 Reward Points instantly
                                                ✅ Free Shipping
                                                ✅ Extra 10% OFF
                                                ✅ Gift Vouchers worth ₹250/-

                                                Tap here: " .
                                                            request()->fullUrl();
                                                    @endphp

                                                    <div id="sharePopup" class="share-popup">
                                                        <!-- Email -->
                                                        <a href="mailto:?subject=InteriorChowk Invite&body={{ urlencode($message) }}"
                                                            target="_blank">
                                                            <i class="fa fa-envelope"></i> Email
                                                        </a>

                                                        <!-- Twitter -->
                                                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($message) }}"
                                                            target="_blank">
                                                            <i class="bi bi-twitter-x"></i> Twitter
                                                        </a>

                                                        <!-- Pinterest -->
                                                        <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(request()->fullUrl()) }}&media={{ asset('path/to/product.jpg') }}&description={{ urlencode($message) }}"
                                                            target="_blank">
                                                            <i class="fa fa-pinterest"></i> Pinterest
                                                        </a>

                                                        <!-- Facebook -->
                                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}&quote={{ urlencode($message) }}"
                                                            target="_blank">
                                                            <i class="fa fa-facebook"></i> Facebook
                                                        </a>

                                                    </div>
                                                    <style>
                                                        .share-button {
                                                            position: absolute;
                                                            bottom: 300px;
                                                            right: 10px;
                                                            background: #ffffffcc;
                                                            border: none;
                                                            border-radius: 50%;
                                                            padding: 8px;
                                                            cursor: pointer;
                                                            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
                                                            z-index: 10;
                                                        }

                                                        .share-popup {
                                                            position: absolute;
                                                            bottom: 360px;
                                                            right: 10px;
                                                            background: white;
                                                            border: 1px solid #ddd;
                                                            border-radius: 8px;
                                                            padding: 10px;
                                                            display: none;
                                                            flex-direction: column;
                                                            gap: 10px;
                                                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                                                            z-index: 20;
                                                            width: 140px;
                                                        }

                                                        .share-popup a,
                                                        .share-popup button {
                                                            display: flex;
                                                            align-items: center;
                                                            gap: 8px;
                                                            font-size: 14px;
                                                            color: #333;
                                                            text-decoration: none;
                                                            border: none;
                                                            background: none;
                                                            cursor: pointer;
                                                        }

                                                        .share-popup a:hover,
                                                        .share-popup button:hover {
                                                            text-decoration: underline;
                                                        }
                                                    </style>
                                                    <script>
                                                        function toggleSharePopup(event) {
                                                            event.stopPropagation(); // Prevent global click from closing immediately
                                                            const popup = document.getElementById('sharePopup');
                                                            popup.style.display = popup.style.display === 'flex' ? 'none' : 'flex';
                                                        }

                                                        function copyLink() {
                                                            const dummy = document.createElement('input');
                                                            dummy.value = window.location.href;
                                                            document.body.appendChild(dummy);
                                                            dummy.select();
                                                            document.execCommand('copy');
                                                            document.body.removeChild(dummy);
                                                            alert('Link copied to clipboard!');
                                                        }

                                                        document.addEventListener('click', function(e) {
                                                            const popup = document.getElementById('sharePopup');
                                                            const isClickInside = e.target.closest('.share-button') || e.target.closest('#sharePopup');
                                                            if (!isClickInside) {
                                                                popup.style.display = 'none';
                                                            }
                                                        });
                                                    </script>
                                                    <div class="mt-5">
                                                        <h6 class="faqHead"><strong>FAQ's</strong></h6>
                                                        <ul class="faq_list">
                                                            @if ($faqreferandearn->isEmpty())
                                                                <li>No FAQs available at the moment.</li>
                                                            @else
                                                                @foreach ($faqreferandearn as $value)
                                                                    <li>
                                                                        <p class="mb-0">
                                                                            <strong>{{ $value->question }}?</strong>
                                                                        </p>
                                                                        <p class="mb-0">{{ $value->answer }}</p>
                                                                    </li>
                                                                @endforeach
                                                            @endif

                                                        </ul>
                                                    </div>
                                                </div>
                                            </section>

                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>

                    </div><!-- End .col-lg-6 -->

                </div><!-- End .row -->

            </div><!-- End .page-content -->
    </main><!-- End .main -->


    <style>
        .widget-title1 {
            color: #878787 !important;
            font-weight: 500;
            font-size: 12px !important;
            letter-spacing: -.01em;
            margin-top: 0;
            margin-bottom: 1.9rem;
        }

        .widget-list1 {
            line-height: 2;
            font-size: 13px;
            font-weight: 500;
            color: #fff;
            display: block;
            font-weight: 400;
            font-size: 12px;
        }

        .product-title {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            /* Limit to 1 line */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: normal;
            /* Ensure wrapping */
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            aspect-ratio: 1 / 1;
        }

        .btnAddToCart {
            width: 35px;
            height: 26px;
            line-height: 23px;
            border: 1px solid #ccc;
            font-size: 16px;
            margin-left: auto;
        }
    </style>


    <!-- Plugins JS File -->
    <script src="public/website/new/assets/js/jquery.min.js"></script>
    <script src="public/website/new/assets/js/bootstrap.bundle.min.js"></script>
    <script src="public/website/new/assets/js/jquery.hoverIntent.min.js"></script>
    <script src="public/website/new/assets/js/jquery.waypoints.min.js"></script>
    <!-- <script src="public/website/new/assets/js/superfish.min.js"></script> -->
    <script src="public/website/new/assets/js/owl.carousel.min.js"></script>
    <script src="public/website/new/assets/js/bootstrap-input-spinner.js"></script>
    <script src="public/website/new/assets/js/jqery.elevateZoom.min.js"></script>
    <script src="public/website/new/assets/js/bootstrap-input-spinner.js"></script>
    <script src="public/website/new/assets/js/jquery.magnific-popup.min.js"></script>
    <!-- Main JS File -->
    <!-- <script src="public/website/new/assets/js/main.js"></script> -->
    <script src="https://use.fontawesome.com/e9084ed560.js"></script>


    <script>
        //
        // Tabs Toggler
        //

        (function($) {
            // Variables
            const $tabLink = $('#tabs-section .tab-link');
            const $tabBody = $('#tabs-section .tab-body');
            let timerOpacity;

            // Toggle Class
            const init = () => {
                // Menu Click
                $tabLink.off('click').on('click', function(e) {
                    // Prevent Default
                    e.preventDefault();
                    e.stopPropagation();

                    // Clear Timers
                    window.clearTimeout(timerOpacity);

                    // Toggle Class Logic
                    // Remove Active Classes
                    $tabLink.removeClass('active ');
                    $tabBody.removeClass('active ');
                    $tabBody.removeClass('active-content');

                    // Add Active Classes
                    $(this).addClass('active');
                    $($(this).attr('href')).addClass('active');

                    // Opacity Transition Class
                    timerOpacity = setTimeout(() => {
                        $($(this).attr('href')).addClass('active-content');
                    }, 50);
                });
            };

            // Document Ready
            $(function() {
                init();
            });
        }(jQuery));
    </script>

    <script>
        function confirmRemove(id) {
            if (confirm('Are you sure you want to remove this address?')) {
                document.getElementById('removeAddressForm_' + id).submit();
            }
        }
    </script>


    <script>
        $(document).ready(function() {
            // Using class selector for multiple Add to Cart buttons
            $(".btnAddToCart").on("click", function() {
                const slug = $(this).data('slug'); // Get slug from data attribute
                const id = $(this).data('product_id'); // Get id from data attribute
                const variant = $(this).data('variant'); // Get variant from data attribute

                $.ajax({
                    url: "{{ route('cart.add_1') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        product: id,
                        variant: variant,
                    },
                    success: function(response) {
                        location.reload(); // refresh after add to cart
                        alert(response.message);
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            alert("You must be logged in to add to cart.");
                        } else {
                            alert("Something went wrong.");
                        }
                    }
                });
            });

            $(".btnDelete").on("click", function() {
                const slug = $(this).data('slug');

                if (!slug) {
                    alert('Product slug missing.');
                    return;
                }

                if (confirm('Are you sure you want to remove this item from wishlist?')) {
                    $.ajax({
                        url: "{{ route('delete-wishlist-1') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            slug: slug
                        },
                        success: function(response) {
                            alert(response.message);
                            // Optionally remove the wishlist item from DOM
                            // $(this).closest('.product').remove();  <-- careful with "this" inside success
                            location.reload(); // simple way: refresh after delete
                        },
                        error: function(xhr) {
                            if (xhr.status === 401) {
                                alert("You must be logged in to remove from wishlist.");
                            } else {
                                alert("Something went wrong while removing from wishlist.");
                            }
                        }
                    });
                }
            });
        });
    </script>
    <script>
        // Handle the Edit Button Click
        document.querySelectorAll('.btnEdit').forEach(button => {
            button.addEventListener('click', function() {
                const addressId = this.getAttribute('data-id');
                const contactPersonName = this.getAttribute('data-name');
                const phone = this.getAttribute('data-phone');
                const address = this.getAttribute('data-address');
                const city = this.getAttribute('data-city');
                const state = this.getAttribute('data-state');
                const zip = this.getAttribute('data-zip');
                const addressType = this.getAttribute('data-address-type');
                const landmark = this.getAttribute('data-landmark');

                // Populate the form fields
                document.querySelector(`#editAddressForm_${addressId} .contact_person_name`).value =
                    contactPersonName;
                document.querySelector(`#editAddressForm_${addressId} .phone`).value = phone;
                document.querySelector(`#editAddressForm_${addressId} .address`).value = address;
                document.querySelector(`#editAddressForm_${addressId} .city`).value = city;
                document.querySelector(`#editAddressForm_${addressId} .state`).value = state;
                document.querySelector(`#editAddressForm_${addressId} .zip`).value = zip;
                document.querySelector(`#editAddressForm_${addressId} .landmark`).value = landmark;

                // Set the correct address type radio button
                const addressTypeRadio = document.querySelector(
                    `#editAddressForm_${addressId} input[name="address_type"][value="${addressType}"]`);
                if (addressTypeRadio) addressTypeRadio.checked = true;

                // Show the form
                document.getElementById(`editAddressForm_${addressId}`).style.display = 'block';
            });
        });

        // Handle Cancel Button Click
        document.querySelectorAll('.cancelButton').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('div[id^="editAddressForm_"]');
                if (form) form.style.display = 'none';
            });
        });
    </script>

    <script>
        document.getElementById('edit-btn').addEventListener('click', function() {
            // Enable all inputs and radios
            document
                .querySelectorAll('#personal-info-form input')
                .forEach(el => el.disabled = false);

            // Swap buttons
            this.style.display = 'none';
            document.getElementById('save-btn').style.display = 'inline-block';
        });


        $(document).ready(function() {
            var pincode = $('#pincode');

            pincode.on('input', function() {
                var pincodes = pincode.val();
                console.log(pincodes);

                $.ajax({
                    url: '{{ route('seller.profile.pincode') }}', // Replace with your server-side route
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Laravel CSRF token for security
                    },
                    data: {
                        pincode: pincodes
                    },
                    success: function(response) {
                        console.log('Pincode response:', response);
                        $('#city').val(response.city);
                        $('#state').val(response.state);

                        // Handle the response as needed (e.g., update UI based on the response)
                    },
                    // error: function(xhr, status, error) {
                    //     console.error('Error:', error);
                    //     alert('An error occurred while processing the pincode.');
                    // }
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("addAddressForm");
            const submitButton = form.querySelector("button[type='submit']");
            const cancelButton = document.getElementById("cancelButton");

            form.addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent default form submit
                let valid = true;

                // Trim all text inputs
                form.querySelectorAll("input[type='text'], textarea").forEach(input => {
                    input.value = input.value.trim();
                });

                // Clear previous errors
                form.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
                document.getElementById("addressTypeError").style.display = "none";

                // --- VALIDATION ---
                const name = form.querySelector("[name='contact_person_name']");
                if (name.value === "") {
                    name.classList.add("is-invalid");
                    valid = false;
                }

                const phone = form.querySelector("[name='phone']");
                const phoneRegex = /^[0-9]{10}$/;
                if (!phoneRegex.test(phone.value)) {
                    phone.classList.add("is-invalid");
                    valid = false;
                }

                const address = form.querySelector("[name='address']");
                if (address.value === "") {
                    address.classList.add("is-invalid");
                    valid = false;
                }

                const zip = form.querySelector("[name='zip']");
                const pinRegex = /^[0-9]{6}$/;
                if (!pinRegex.test(zip.value)) {
                    zip.classList.add("is-invalid");
                    valid = false;
                }

                const city = form.querySelector("[name='city']");
                if (city.value === "") {
                    city.classList.add("is-invalid");
                    valid = false;
                }

                const state = form.querySelector("[name='state']");
                if (state.value === "") {
                    state.classList.add("is-invalid");
                    valid = false;
                }

                const addressType = form.querySelectorAll("input[name='address_type']:checked");
                const addressTypeError = document.getElementById("addressTypeError");
                if (addressType.length === 0) {
                    addressTypeError.style.display = "block";
                    valid = false;
                }

                // --- IF FORM IS VALID ---
                if (valid) {
                    // Submit the form normally
                    form.submit();
                }
                // else: errors are displayed below fields, page does not refresh
            });

            // Cancel button resets form
            cancelButton.addEventListener("click", () => {
                form.reset();
                form.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
                document.getElementById("addressTypeError").style.display = "none";
            });
        });


        $(document).ready(function() {
            var pincode = $('#pincode');

            pincode.on('input', function() {
                var pincodes = pincode.val();
                console.log(pincodes);

                $.ajax({
                    url: '{{ route('pincode') }}', // Replace with your server-side route
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Laravel CSRF token for security
                    },
                    data: {
                        pincode: pincodes
                    },
                    success: function(response) {
                        console.log('Pincode response:', response);
                        $('#city').val(response.city);
                        $('#state').val(response.state);
                    },
                });
            });
        });
    </script>

@endsection
