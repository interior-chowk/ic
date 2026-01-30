@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Claim Report'))

@section('content')

    <div class="content container-fluid">

        @php
            $totalbill = 0;
            session(['seller_earnings' => []]);
        @endphp

        <div class="card">
            <div class="table-responsive">
                <table class="table __table table-hover table-borderless table-align-middle w-100">
                    <thead>
                        <tr>
                            <th>Select All <br><input type="checkbox" id="select-all"></th>
                            <th>Vendor Code</th>
                            <th>Seller Name</th>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Net Payable to Seller</th>
                            <th>Claim Date</th>
                            <th>Invoice</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($orders as $order)
                            @php
                                $orderDetails = App\Model\Order::with('seller', 'details.product_all_status')
                                    ->where('id', $order->id)
                                    ->first();

                                $orderEarning = 0;
                            @endphp

                            @foreach ($orderDetails->details as $details)
                                @php
                                    $sku_product = DB::table('sku_product_new')
                                        ->where('product_id', $details->product_id)
                                        ->where('variation', $details->variant)
                                        ->first();

                                    $itemBase = $sku_product->listed_price * $details->qty;

                                    $commissionBase =
                                        (($sku_product->listed_price * 5) / 100 +
                                            ($sku_product->commission_fee * $sku_product->listed_percent) / 100) *
                                        $details->qty;

                                    $commission = $commissionBase + $commissionBase * 0.18;

                                    $itemEarning = $itemBase - $commission;

                                    $orderEarning += $itemEarning;
                                @endphp
                            @endforeach

                            @php
                                // ✅ APPLY DISCOUNT + SHIPPING ONCE PER ORDER
                                $orderEarning -= $order->shipping_cost_amt + $order->discount_amount;
                                $orderEarning = round($orderEarning, 2);

                                // ✅ GLOBAL TOTAL
                                $totalbill += $orderEarning;

                                // ✅ SESSION SELLER TOTAL
                                $sellerId = $order->seller->id;
                                $current = session('seller_earnings');
                                $current[$sellerId] = ($current[$sellerId] ?? 0) + $orderEarning;
                                session(['seller_earnings' => $current]);
                            @endphp

                            <tr>
                                <td>
                                    <input type="checkbox" class="row-check" value="{{ $order->id }}"
                                        data-amount="{{ $orderEarning }}">
                                </td>
                                <td>VN{{ $order->seller->id }}</td>
                                <td>{{ $order->seller->shop->name }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.details', ['id' => $order->id]) }}">
                                        {{ $order->id }}
                                    </a>
                                </td>
                                <td>{{ date('d-m-Y', strtotime($order->created_at)) }}</td>
                                <td class="row-amount">{{ number_format($orderEarning, 2) }}</td>
                                <td>{{ date('d-m-Y', strtotime($order->updated_at)) }}</td>
                                <td>IC/VN{{ $order->seller->id }}/{{ $order->id }}</td>
                            </tr>
                        @endforeach

                        @if ($orders->total() == 0)
                            <tr>
                                <td colspan="8" class="text-center p-4">No Data Found</td>
                            </tr>
                        @endif

                    </tbody>

                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><strong>Total:</strong></td>
                            <td><strong id="totalAmount">{{ number_format($totalbill, 2) }}</strong></td>
                            <td></td>
                            <td>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#retExcModal">
                                    Made Payment
                                </button>
                            </td>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </div>

    </div>


    {{-- ✅ MODAL --}}
    <div class="modal fade" id="retExcModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">

                    <form action="{{ route('admin.report.made_payment') }}" method="post">
                        @csrf

                        <input type="hidden" name="selected_ids" id="selected_ids">

                        <div class="form-group">
                            <label>Amount</label>
                            <input type="text" id="total_amount" name="total_amount" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-success">Save Payment</button>

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection


{{-- ✅ JS --}}
@push('script_2')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const checkboxes = document.querySelectorAll('.row-check');
            const selectAll = document.getElementById('select-all');
            const totalCell = document.getElementById('totalAmount');
            const selectedInput = document.getElementById('selected_ids');

            function updateTotal() {
                let total = 0;
                let selected = [];

                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        total += parseFloat(cb.dataset.amount) || 0;
                        selected.push(cb.value);
                    }
                });

                if (selected.length === 0) {
                    checkboxes.forEach(cb => {
                        total += parseFloat(cb.dataset.amount) || 0;
                    });
                }

                totalCell.textContent = total.toFixed(2);
                document.getElementById('total_amount').value = total.toFixed(2);
                selectedInput.value = selected.join(',');
            }

            checkboxes.forEach(cb => cb.addEventListener('change', updateTotal));

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateTotal();
            });

            updateTotal();
        });
    </script>
@endpush
