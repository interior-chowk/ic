@extends('layouts.back-end.common_seller_1')

@section('content')
<style>
a.btn.btn-primary.saving.mt-2 {
    width: 516px !important;
    margin-left: -16px !important;
    margin-bottom: -15px !important;
    border-radius: 2px 2px 14px 12px !important;
}
</style>
<div class="container featured mt-4 pb-2 myAccRespo">
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

    $finalAmount = $totalAmount > 0 ? $totalAmount + $deliveryCharges : 0;
    $totalSaving = $totalDiscount;

    $user = auth()->user();
    @endphp
    <style>
    <style>.pincode-section {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pincode-section input {
        width: 130px;
    }

    button.check {
        background: #2e6cb2;
        color: #fff;
        padding: 10px;
        border-radius: 7px;
        width: 88px;
        border: none;
        cursor: pointer;
    }

    .edt {
        margin-left: 10px;
        color: #555;
        font-weight: 600;
        white-space: nowrap;
    }

    form#edtForm {
        display: flex;
        gap: 12px;
    }
    </style>
    <form id="edtForm" style="width:355px; display:flex;margin-bottom:10px;">
        @csrf
        <input type="text" placeholder="Enter pincode to check delivery date" name="pincode" maxlength="6"
            class="form-control" required>
        @foreach ($cart as $item)
        <input type="hidden" name="cart_group_id[]" value="{{ $item->cart_group_id }}">
        @endforeach

        <button type="submit" class="check">Check</button>
    </form>
    <script>
    document.getElementById('edtForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        document.querySelectorAll('.delivery-date').forEach(el => {
            el.innerHTML = 'Checking delivery...';
        });

        fetch("{{ route('get_cart_edt') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {

                    document.querySelectorAll('.delivery-date').forEach(el => {
                        let groupId = el.getAttribute('data-group-id');

                        if (data.edt[groupId]) {
                            el.innerHTML = 'Delivered by ' + data.edt[groupId];
                        } else {
                            el.innerHTML = 'Delivery not available';
                        }
                    });
                }
            });

    });
    </script>
    <div class="row cartRespo">
        @if ($cart->isEmpty())
        <div class="col-md-12">
            <img src="{{ asset('public/website/assets/images/Empty Cart.webp') }}" alt="Empty Wishlist"
                class="img-fluid" style="max-width: 50% !important;height: auto;margin: auto;">
        </div>
        @else
        <div class="col-md-7">
            <h4 class="mb-1">My Cart</h4>

            @foreach ($cart as $cartItem)
            @php
            $images = json_decode($cartItem->image, true);
            @endphp
            <style>
            .cart-img {
                width: 90px;
                height: 90px;
                object-fit: cover;
                border-radius: 6px;
                background: #f5f5f5;
            }

            .cart-item .position-relative {
                width: 90px;
                flex-shrink: 0;
            }

            .select-checkbox {
                z-index: 2;
            }

            .cartRespo .select-checkbox {
                top: -85px;
            }
            </style>
            <div class="media mt-4 cart-item" data-cart-id="{{ $cartItem->id }}">
                <div class="position-relative">
                    <img class="cart-img" 
                    {{-- src="{{ asset('storage/app/public/images/' . $images[0]) }}"  --}}
                    src="{{ rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . ($images[0] ?? 'default.jpg') }}"
                    class="mr-3"
                        alt="{{ $cartItem->name }}">
                    {{-- ✅ Checkbox --}}
                    <input type="checkbox" class="select-checkbox" name="select[]" value="{{ $cartItem->id }}"
                        {{ $cartItem->is_selected == 1 ? 'checked' : '' }}>
                </div>
                <div class="media-body ml-4">
                    <a href="{{ url('product/' . $cartItem->slug) }}">
                        <h5 style="font-weight: 300;" class="mb-1 mt-0">
                            {{ strlen($cartItem->name) > 30 ? substr($cartItem->name, 0, 30) . '...' : $cartItem->name }}
                        </h5>
                    </a>

                    @if ($cartItem->quantity <= 0) <p class="text-red-500">Out of Stock</p>
                        @elseif ($cartItem->quantity <= 10) <p class="mb-0" style="color:#FF7373;">
                            {{ $cartItem->quantity }} Units Left</p>
                            @endif

                            @if ($cartItem->color_name)
                            <p class="mb-0">Color: {{ $cartItem->color_name }}</p>
                            @endif
                            @if ($cartItem->sizes)
                            <p class="mb-0">Size: {{ $cartItem->sizes }}</p>
                            @endif
                            @if ($cartItem->variation)
                            <p class="mb-0">Variation: {{ $cartItem->variation }}</p>
                            @endif

                </div>
                <div class="media-right">
                    <div>
                        <a href="javascript:void(0);" class="delete-cart-item" data-cart-id="{{ $cartItem->cart_id }}">
                            <i class="fa fa-trash-o text-danger"></i>
                        </a>
                    </div>

                    <div class="product-price justify-content-center">

                        ₹ <span id="product-price-updt">{{ $cartItem->listed_price * $cartItem->cart_qty }}</span>

                        @if ($cartItem->discount > 0)
                        <span class="price-cut">₹ {{ $cartItem->variant_mrp }}</span>
                        @endif
                    </div>

                    @if ($cartItem->discount > 0)
                    <div>
                        <span class="badge badge-pill badge-primary">
                            @if ($cartItem->discount_type == 'percent')
                            {{ round($cartItem->discount, 0) }}% off
                            @else
                            ₹{{ number_format($cartItem->discount, 0) }} off
                            @endif
                        </span>
                    </div>
                    @endif

                    <div class="product-details-quantity">
                        <input type="number" class="form-control qty-input" value="{{ $cartItem->cart_qty }}" min="0"
                            max="{{ $cartItem->quantity }}" step="1" data-cart-id="{{ $cartItem->cart_id }}"
                            onkeydown="return false" required>
                    </div>
                    <p class="delivery-date" data-group-id="{{ $cartItem->cart_group_id }}">
                </div>
            </div>
            @endforeach
            {{-- <a href="{{ route('web.mobCart') }}">
                <button type="button" class="btn btn-info rounded-pill w-100 mt-1">checkout page mobile ke liye</button>
            </a> --}}
        </div>

        <!-- Cart Summary -->
        <div class="col-md-5 cart-doc">
            <div class="p-4 mb-2 text-login" style="border: 1px solid #2E6CB2;border-radius: 15px;">
                <div class="card border-0 mt-1">
                    <img style="height: 200px; object-fit: cover; object-position: center; border-radius: 15px; max-width: 100%;"
                        src="{{ asset('public/website/new/assets/images/products/product-1.webp') }}"
                        class="card-img-top" alt="...">

                    <div class="card-body p-0 mt-2">
                        <h6>Total Bill Breakdown</h6>
                        <p class="mb-0"><strong>MRP</strong> <span>₹ {{ number_format($tatalamounts, 2) }}</span>
                        </p>
                        <p class="mb-0"><strong>Final product price</strong> <span>₹
                                {{ number_format($totalAmount, 2) }}</span></p>
                        {{-- <p class="mb-0"><strong>Delivery Charges</strong> <span>₹ {{ $totalAmount > 0 ? number_format($deliveryCharges, 2) : 0 }}</span>
                        </p>
                        <p class="mb-0"><strong>Total Payable</strong> <span>₹
                                {{ number_format($finalAmount, 2) }}</span></p> --}}

                        @if ($totalAmount > 0)
                        @if ($user->f_name != null)
                        <a href="{{ route('checkout') }}" class="btn btn-info rounded-pill w-100 mt-1">Proceed to
                            Purchase</a>
                        @else
                        <button type="button" class="btn btn-info rounded-pill w-100 mt-1" data-toggle="modal"
                            data-target="#cancellationModal">
                            Proceed to Purchase
                        </button>
                        @endif
                        <a href="javascript:void(0);" class="btn btn-primary saving w-100 mt-2">
                            You’re Saving ₹ {{ number_format($totalSaving, 2) }} today!
                        </a>
                        @else
                        <p class="text-danger mt-3 text-center">⚠️ Please select at least one product to
                            proceed.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="modal fade cancelModal" id="cancellationModal" tabindex="-1" role="dialog"
        aria-labelledby="cancellationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12">
                            <form id="personal-info-form" method="POST" action="{{ route('user-update-1') }}">
                                @csrf

                                <h6 style="margin-bottom:10px;">Personal Information</h6>

                                <div class="container mt-2">

                                    <!-- First / Last Name -->
                                    <div class="row">
                                        @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror

                                        <div class="col-md-6">
                                            <input type="text" name="f_name" class="form-control"
                                                placeholder="First Name" value="{{ old('f_name', $user->f_name) }}"
                                                pattern="^[A-Za-z]+$"
                                                oninput="this.value = this.value.replace(/\s/g, '');"
                                                title="First name cannot contain spaces or numbers" required>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="l_name" class="form-control"
                                                placeholder="Last Name" value="{{ old('l_name', $user->l_name) }}"
                                                pattern="^[A-Za-z]+$" title="Last name cannot contain spaces or numbers"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Email / Phone -->
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <input type="email" name="email" class="form-control"
                                                placeholder="E-mail@mail.com" value="{{ old('email', $user->email) }}"
                                                oninput="validateEmail(this)" required>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="phone" class="form-control"
                                                placeholder="10 digit - Mobile No."
                                                value="{{ old('phone', $user->phone) }}" pattern="^[6-9][0-9]{9}$"
                                                maxlength="10"
                                                title="Phone must be 10 digits starting with 6, 7, 8, or 9" required>
                                        </div>
                                    </div>

                                    <!-- Gender -->
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <label class="d-block mb-1"><strong>Your Gender</strong></label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender"
                                                    id="gender_male" value="Male"
                                                    {{ old('gender', $user->gender) === 'Male' ? 'checked' : '' }}
                                                    required>
                                                <label class="form-check-label" for="gender_male">Male</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender"
                                                    id="gender_female" value="Female"
                                                    {{ old('gender', $user->gender) === 'Female' ? 'checked' : '' }}
                                                    required>
                                                <label class="form-check-label" for="gender_female">Female</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="row mt-4">
                                        <div class="col text-center">
                                            <button type="submit" id="save-btn"
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

    <script>
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

        $('.qty-input').on('change', function() {
            const cartId = $(this).data('cart-id');
            let newQty = parseInt($(this).val());

            if (!newQty || newQty < 1) {
                toastr.error('Quantity must be at least 1.');
                $(this).val(1); // reset the input to 1
                newQty = 1;
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
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }

                }
            });
        });
    });

    $('.delete-cart-item').on('click', function(e) {
        e.preventDefault();
        const cartId = $(this).data('cart-id');

        $.ajax({
            url: "{{ route('cart.delete') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                cart_id: cartId
            },
            success: function() {
                location.reload();
                toastr.success('Item removed from the cart.');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function() {
                alert("Something went wrong while deleting the item.");
            }
        });
    })
    </script>
</div>
@endsection