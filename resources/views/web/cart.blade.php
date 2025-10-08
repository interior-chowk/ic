@extends('layouts.back-end.common_seller_1')

@section('content')

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

        <div class="row cartRespo">
            @if ($cart->isEmpty())
                <div class="col-md-12">
                    <img src="{{ asset('website/assets/images/Empty Cart.webp') }}" alt="Empty Wishlist" class="img-fluid"
                        style="max-width: 50%;height: auto;margin: auto;">
                </div>
            @else
                <div class="col-md-7">
                    <h4 class="mb-1">My Cart</h4>

                    @foreach ($cart as $cartItem)
                        @php
                            $images = json_decode($cartItem->image, true);
                        @endphp
                        <div class="media mt-4 cart-item" data-cart-id="{{ $cartItem->id }}">
                            <div class="position-relative">
                                <img style="height: 100px;" src="{{ asset('storage/images/' . $images[0]) }}" class="mr-3"
                                    alt="{{ $cartItem->name }}">
                                {{-- ✅ Checkbox --}}
                                <input type="checkbox" class="select-checkbox" name="select[]" value="{{ $cartItem->id }}"
                                    {{ $cartItem->is_selected == 1 ? 'checked' : '' }}>
                            </div>
                            <div class="media-body">
                                <a href="{{ url('product/' . $cartItem->slug) }}">
                                    <h5 style="font-weight: 300;" class="mb-1 mt-0">
                                        {{ strlen($cartItem->name) > 30 ? substr($cartItem->name, 0, 30) . '...' : $cartItem->name }}
                                    </h5>
                                </a>

                                @if ($cartItem->quantity <= 0)
                                    <p class="text-red-500">Out of Stock</p>
                                @elseif ($cartItem->quantity <= 10)
                                    <p class="mb-0" style="color:#FF7373;">{{ $cartItem->quantity }} Units Left</p>
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
                                    <a href="javascript:void(0);" class="delete-cart-item"
                                        data-cart-id="{{ $cartItem->cart_id }}">
                                        <i class="fa fa-trash-o text-danger"></i>
                                    </a>
                                </div>

                                <div class="product-price justify-content-center" >
                                   
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
                                    <input type="number" class="form-control qty-input" value="{{ $cartItem->cart_qty }}"
                                        min="0" max="{{ $cartItem->quantity }}" step="1"
                                        data-cart-id="{{ $cartItem->cart_id }}" onkeydown="return false" required>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Cart Summary -->
                <div class="col-md-5 cart-doc">
                    <div class="p-4 mb-2 text-login" style="border: 1px solid #2E6CB2;border-radius: 15px;">
                        <div class="card border-0 mt-1">
                            <img style="height: 200px; object-fit: cover; object-position: center; border-radius: 15px; max-width: 100%;"
                                src="{{ asset('website/new/assets/images/products/product-1.webp') }}" class="card-img-top"
                                alt="...">

                            <div class="card-body p-0 mt-2">
                                <h6>Total Bill Breakdown</h6>
                                <p class="mb-0"><strong>MRP</strong> <span>₹ {{ number_format($tatalamounts, 2) }}</span>
                                </p>
                                <p class="mb-0"><strong>Final product price</strong> <span>₹
                                        {{ number_format($totalAmount, 2) }}</span></p>
                                {{-- <p class="mb-0"><strong>Delivery Charges</strong> <span>₹ {{ $totalAmount > 0 ? number_format($deliveryCharges, 2) : 0 }}</span></p>
                                <p class="mb-0"><strong>Total Payable</strong> <span>₹ {{ number_format($finalAmount, 2) }}</span></p> --}}

                                @if ($totalAmount > 0)
                                    @if ($user->f_name != null)
                                        <a href="{{ route('checkout') }}" class="btn btn-info w-100 p-3">Proceed to
                                            Purchase</a>
                                    @else
                                        <button type="button" class="btn btn-info w-100 p-3" data-toggle="modal"
                                            data-target="#cancellationModal">
                                            Proceed to Purchase
                                        </button>
                                    @endif
                                    <a href="javascript:void(0);" class="btn btn-primary rounded-pill w-100 mt-2">
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

        {{-- ✅ Checkbox selection AJAX --}}
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

                // Quantity update
             $('.qty-input').on('change', function() {
                    const cartId = $(this).data('cart-id');
                    let newQty = parseInt($(this).val());
                
                    // If invalid or less than 1 → reset to 1
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
                          toastr.success('Item removed from the cart.');
                        setTimeout(function () {
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
