@extends('layouts.back-end.app-seller')

@section('title', \App\CPU\translate('Dashboard'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

    <link href="https://fonts.googleapis.com/css?family=Inter:400,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">

    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <style>
        body {
            background: #f7f7fa;
            font-family: 'Inter', Arial, sans-serif;
            margin: 0;
            color: #35373e;
        }

        .dashboard-container {
            margin: 0 auto;
            max-width: 1200px;
            padding: 24px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .tab-group {
            display: flex;
            gap: 8px;
        }

        .tab {
            background: #ede8ee;
            border: none;
            padding: 6px 20px;
            border-radius: 6px;
            font-weight: 600;
            color: #6d6377;
            cursor: pointer;
        }

        .tab.active {
            background: #cbb2c8;
            color: #35373e;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .dates-btn,
        .add-btn {
            background: #fff;
            border: 1px solid #efefef;
            padding: 7px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .add-btn {
            background: #974171;
            color: #fff;
            border: none;
        }

        .dashboard-summary {
            display: flex;
            gap: 16px;
            margin-bottom: 18px;
        }

        .card {
            background: #fff;
            box-shadow: 0 2px 4px #e4e4e8;
            border-radius: 10px;
            flex: 1;
            padding: 22px 18px;
            min-width: 150px;
            min-height: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            position: relative;
        }

        .card.small-card {
            min-width: 100px;
            padding: 15px 12px;
            font-size: 13px;
        }

        .card-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .card-value {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .card-mini-value {
            font-size: 18px;
            font-weight: 600;
        }

        .card-desc.green {
            color: #3bb272;
            font-size: 12px;
        }

        /* Chart cards merged styling */
        .charts-container {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            margin: 14px 0 0 0;
        }

        .card.chart-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 7px #ececec;
            padding: 22px 18px;
            min-width: 320px;
            flex: 1;
            max-width: 430px;
            margin-bottom: 18px;
        }

        .card-subtitle {
            font-size: 13px;
            color: #908a9c;
            margin-bottom: 3px;
        }

        /* Gauge chart */
        .gauge-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: auto;
            position: relative;
        }

        .gauge-label {
            font-size: 21px;
            font-weight: 700;
            position: absolute;
            top: 85px;
            left: 50%;
            transform: translateX(-50%);
            color: #4E4352;
        }

        .gauge-percent.green {
            font-size: 14px;
            color: #55b374;
            margin-top: 120px;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .gauge-desc {
            text-align: center;
            color: #868b94;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .gauge-footer {
            display: flex;
            justify-content: space-between;
            color: #5A5666;
            font-size: 14px;
            margin: 8px 0 4px 0;
            width: 100%;
        }

        .gauge-footer div {
            text-align: center;
            width: 33%;
        }

        .gauge-footer .green {
            color: #55b374;
            font-weight: 600;
        }

        .gauge-footer .red {
            color: #c14848;
            font-weight: 600;
        }

        .linechart-container,
        .areachart-container {
            width: 100%;
            position: relative;
        }

        .linechart-container svg,
        .areachart-container svg {
            width: 100%;
            height: auto;
            display: block;
            margin-bottom: 2px;
        }

        .x-axis-labels {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #b3b5c1;
            margin-top: 2px;
            padding-left: 2px;
        }

        .legend {
            margin-bottom: 8px;
        }

        .legend span {
            font-size: 13px;
            color: #676784;
            margin-right: 18px;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .dot.rev {
            background: #6C54A3;
        }

        .dot.sal {
            background: #49A86A;
        }


        .dashboard-main-row {
            display: flex;
            gap: 18px;
            margin-bottom: 22px;
        }

        .product-table-section {
            background: #fff;
            flex: 2;
            border-radius: 10px;
            padding: 20px 16px;
            box-shadow: 0 2px 4px #e4e4e8;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .product-table th,
        .product-table td {
            padding: 8px 12px;
            text-align: left;
        }

        .product-table th {
            background: #f7edf7;
            color: #974171;
            font-weight: 600;
        }

        .product-table tbody tr {
            border-bottom: 1px solid #efefef;
        }

        .product-table td img {
            vertical-align: middle;
            margin-right: 6px;
            border-radius: 5px;
        }

        .status {
            display: inline-block;
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 7px;
        }

        .status.low-stock {
            background: #faeded;
            color: #d65788;
        }

        .status.published {
            background: #ebf9ef;
            color: #3bb272;
        }

        .status.processing {
            background: #faeded;
            color: #d65788;
        }

        .status.shipped {
            background: #e9f1fb;
            color: #3b77b2;
        }

        .status.delivered {
            background: #ebf9ef;
            color: #3bb272;
        }

        .status.cancelled {
            background: #f7ecea;
            color: #f36840;
        }

        .table-pagination {
            margin-top: 7px;
            display: flex;
            gap: 7px;
        }

        .table-pagination span {
            background: #ede8ee;
            border-radius: 4px;
            padding: 3px 9px;
            font-size: 14px;
            cursor: pointer;
        }

        .table-pagination span.active {
            background: #cbb2c8;
            color: #fff;
        }

        .side-stats-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .customers-orders-card,
        .best-cat-card {
            background: #fff;
            border-radius: 10px;
            padding: 18px 13px;
            box-shadow: 0 2px 4px #e4e4e8;
            width: 460px;
        }

        .customers-orders-card img {
            width: 100%;
            border-radius: 8px;
            margin-top: 10px;
        }

        .best-cat-card .card-title {
            margin-bottom: 12px;
        }

        .category-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .category-list li {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .cat-name {
            width: 90px;
            font-weight: 500;
        }

        .cat-bar {
            height: 11px;
            border-radius: 5px;
            flex-grow: 1;
            margin-left: 12px;
            background: #eee;
        }

        .cat-bar.sneaker {
            background: linear-gradient(90deg, #974171 70%, #efe7f0 30%);
        }

        .cat-bar.sweatshirt {
            background: linear-gradient(90deg, #47b0ca 54%, #efefef 46%);
        }

        .cat-bar.bag {
            background: linear-gradient(90deg, #947e47 50%, #efefef 50%);
        }

        .cat-bar.tshirt {
            background: linear-gradient(90deg, #efa142 40%, #efefef 60%);
        }

        .orders-section {
            background: #fff;
            border-radius: 10px;
            padding: 20px 16px;
            box-shadow: 0 2px 4px #e4e4e8;
            width: -webkit-fill-available;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .orders-table th,
        .orders-table td {
            padding: 7px 10px;
            text-align: left;
        }

        .orders-table th {
            background: #f7edf7;
            color: #974171;
            font-weight: 600;
        }

        .orders-table tbody tr {
            border-bottom: 1px solid #efefef;
        }

        .orders-table .email {
            font-size: 11px;
            color: #8f8d96;
        }

        .action-btn {
            background: #ede8ee;
            border: none;
            border-radius: 5px;
            padding: 4px 9px;
            cursor: pointer;
            font-size: 16px;
        }

        .first-div {
            min-width: 235px;
        }

        .small-cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            text-align: -webkit-center;
            gap: 5px;
        }

        .small-cards-grid .card {
            height: 65px;
        }

        .card.dashboard-header {
            border: none;
            background: #f7f7fa;
            box-shadow: none;
            padding: 0px;
            justify-content: unset;
        }

        ul#dateTabs .nav-item {
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 2px 4px #e4e4e8;
        }

        .small-card-title {
            width: -webkit-fill-available;
        }
    </style>
    <div class="dashboard-container">

        <div class="card dashboard-header">
            <h4 class="mb-3">{{ \App\CPU\translate('Filter_Data') }}</h4>

            <ul class="nav nav-pills mb-3" id="dateTabs">
                <li class="nav-item">
                    <a class="nav-link {{ $date_type == 'this_year' ? 'active' : '' }}" href="javascript:void(0)"
                        onclick="setDateType('this_year')">
                        {{ \App\CPU\translate('This_Year') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $date_type == 'this_month' ? 'active' : '' }}" href="javascript:void(0)"
                        onclick="setDateType('this_month')">
                        {{ \App\CPU\translate('This_Month') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $date_type == 'this_week' ? 'active' : '' }}" href="javascript:void(0)"
                        onclick="setDateType('this_week')">
                        {{ \App\CPU\translate('This_Week') }}
                    </a>
                </li>

                <li class="nav-item">
                    <form action="#" id="form-data" method="GET" class="w-100">
                        <input type="hidden" name="date_type" id="date_type" value="{{ $date_type }}">
                        <input type="hidden" name="from" id="from_date" value="{{ $from }}">
                        <input type="hidden" name="to" id="to_date" value="{{ $to }}">

                        <div class="row gx-2 gy-3 align-items-center">
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <input type="text" name="date_range" id="date_range"
                                        value="{{ request('date_range') }}" class="form-control __form-control"
                                        placeholder="Select Date Range" readonly>
                                    <label>{{ \App\CPU\translate('Select Date Range') }}</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </li>
            </ul>
        </div>

        <div class="dashboard-summary">
            <div class="card first-div">
                <div class="card-title" title="The total sale amount includes the base amount, GST and shipping costs.">
                    Total Sales</div>
                <div class="card-value" title="The total sale amount includes the base amount, GST and shipping costs.">
                    ₹{{ $totalIncome ?? 0 }}</div>
                <div class="card-desc green"
                    title="The order has been successfully delivered. The amount may change if an order is returned.">Order
                    Delivered = ₹{{ $deliveredIncome ?? 0 }}</div>
                <div class="card-desc green"
                    title="All orders that are currently in the pickup to delivery process, but have not yet been delivered.">
                    Order in Progress = ₹{{ max(0, $totalIncome - $deliveredIncome) }}</div>
            </div>
            <div class="card first-div">
                <div class="card-title"
                    title="Net Receivable amount after deduction of Comm, Fee, Shipping Charges, Coupon etc.">Total Earnings
                </div>
                <div class="card-value"
                    title="Net Receivable amount after deduction of Comm, Fee, Shipping Charges, Coupon etc.">
                    ₹{{ $totalearning[auth('seller')->id()] ?? 0 }}
                </div>
                <div class="card-desc green"
                    title="The order has been successfully delivered. The amount may change if an order is returned.">
                    {{-- Order Delivered = ₹{{ max(0, ($totalearning[auth('seller')->id()] ?? 0) - ($earning ?? 0)) }} --}}
                    Order Delivered = ₹{{ number_format($paidTotalEarning, 2) ?? 0 }}
                </div>
                <div class="card-desc green"
                    title="All orders that are currently in the pickup to delivery process, but have not yet been delivered.">
                    Order in Progress =
                    ₹{{ ($totalearning[auth('seller')->id()] ?? 0) - ($paidTotalEarning ?? 0) }}

                </div>
            </div>
            <div class="card first-div">
                <div class="card-title" title="Total amount received in bank account.">Total Collection</div>
                <div class="card-value" title="Total amount received in bank account.">
                    ₹{{ number_format($paidCollectionEarning, 2) ?? 0 }}
                </div>
                <div class="card-desc green"
                    title="All orders that have successfully been delivered and have met their return deadline. But not yet received in bank.">
                    Unpaid Due =
                    ₹{{ number_format($dueTotalEarning, 2) ?? 0 }}
                </div>
                <div class="card-desc green"
                    title="All orders that have not been delivered or have not met their return deadline.">Future due =
                    ₹{{ number_format(($totalearning[auth('seller')->id()] ?? 0) - ($paidTotalEarning ?? 0), 2) }}
                </div>
            </div>

            <script>
                function formatNumber(num) {
                    if (num >= 1000000) {
                        return (num / 1000000).toFixed(1) + 'M';
                    } else if (num >= 1000) {
                        return (num / 1000).toFixed(1) + 'K';
                    }
                    return num;
                }

                document.addEventListener("DOMContentLoaded", function() {
                    let productViews = {{ $productCount }};
                    let wishlistItems = {{ $wishlistCount }};
                    let totalCustomers = {{ $totalCustomers }};
                    let cartItems = {{ $cartCount }};

                    document.getElementById('productViewCount').innerText = formatNumber(productViews);
                    document.getElementById('wishlistCount').innerText = formatNumber(wishlistItems);
                    document.getElementById('totalCustomers').innerText = formatNumber(totalCustomers);
                    document.getElementById('cartCount').innerText = formatNumber(cartItems);
                });
            </script>

            <div class="small-cards-grid">
                <div class="card small-card">
                    <div class="small-card-title">Product Views<br>
                        <span class="card-mini-value" id="productViewCount">0</span>
                    </div>

                </div>
                <div class="card small-card">
                    <div class="small-card-title">Wishlist<br>
                        <span class="card-mini-value" id="wishlistCount">0</span>
                    </div>
                </div>
                <div class="card small-card">
                    <div class="small-card-title">Total Customer<br>
                        <span class="card-mini-value" id="totalCustomers">0</span>
                    </div>
                </div>
                <div class="card small-card">
                    <div class="small-card-title">Cart<br>
                        <span class="card-mini-value" id="cartCount">0</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="charts-container">
            <style>
                button#set-target-btn {
                    padding: 12px;
                    border-radius: 25px;
                    background: #073b74;
                    color: #fff;
                    border: none;
                    cursor: pointer;
                }
            </style>

            <div class="card chart-card" style="max-width:300px !important;">
                <div class="card-title">Sales Progress</div>
                <div class="card-subtitle">This Quarter</div>

                <div id="target-section">
                    <button id="set-target-btn">Set Target</button>

                    <div id="target-form" style="display:none; margin-top:10px;">
                        <input type="number" id="target-input" class="form-control mb-2"
                            placeholder="Enter target (₹)" />
                        <button id="save-target-btn" class="btn btn-sm btn-primary w-100">Save</button>
                    </div>
                </div>

                <div class="gauge-container" id="gauge-container" style="display:none;">
                    <svg viewBox="0 0 120 60" width="250" height="160">
                        <path d="M10,55 A50,45 0 1,1 110,55" stroke="#ECECEC" stroke-width="10" fill="none" />
                        <path id="gauge-progress" d="M10,55 A50,45 0 1,1 110,55" stroke="#6B394A" stroke-width="10"
                            fill="none" stroke-dasharray="0,282" />
                    </svg>
                    <div class="gauge-label" id="gauge-label">0%</div>
                </div>

                <div class="gauge-footer" id="gauge-footer" style="display:none;">
                    <div><span>Target</span><br><span class="red" id="footer-target">₹0</span></div>
                    <div><span>Revenue</span><br><span class="green" id="footer-revenue">₹0</span></div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    // SALES value (correct)
                    const totalIncome = @json($totalIncome ?? 0);

                    const setBtn = document.getElementById('set-target-btn');
                    const targetForm = document.getElementById('target-form');
                    const saveBtn = document.getElementById('save-target-btn');
                    const targetInput = document.getElementById('target-input');

                    const gaugeContainer = document.getElementById('gauge-container');
                    const gaugeProgress = document.getElementById('gauge-progress');
                    const gaugeLabel = document.getElementById('gauge-label');

                    const gaugeFooter = document.getElementById('gauge-footer');
                    const footerTarget = document.getElementById('footer-target');
                    const footerRevenue = document.getElementById('footer-revenue');

                    // ✅ Use localStorage (persistent)
                    let sellerTarget = localStorage.getItem('sellerTarget');

                    // Load saved target
                    if (sellerTarget) {
                        sellerTarget = Number(sellerTarget);
                        targetInput.value = sellerTarget;

                        setBtn.style.display = 'none';
                        targetForm.style.display = 'block';
                        saveBtn.innerText = 'Update';

                        showGauge(sellerTarget);
                    }

                    setBtn.addEventListener('click', () => {
                        targetForm.style.display = 'block';
                        setBtn.style.display = 'none';
                    });

                    saveBtn.addEventListener('click', () => {
                        const value = Number(targetInput.value);

                        if (!value || value <= 0) {
                            alert('Please enter a valid target amount');
                            return;
                        }

                        localStorage.setItem('sellerTarget', value);
                        sellerTarget = value;

                        saveBtn.innerText = 'Update';
                        showGauge(value);
                    });

                    function showGauge(target) {
                        gaugeContainer.style.display = 'block';
                        gaugeFooter.style.display = 'flex';

                        footerTarget.innerText = `₹${target.toLocaleString()}`;
                        footerRevenue.innerText = `₹${totalIncome.toLocaleString()}`;

                        let percent = (totalIncome / target) * 100;
                        percent = percent > 100 ? 100 : percent;

                        gaugeLabel.innerText = percent.toFixed(1) + '%';

                        const pathLength = 282;
                        const filled = (percent / 100) * pathLength;

                        gaugeProgress.style.strokeDasharray = `${filled}, ${pathLength}`;
                    }

                });
            </script>









            <div class="card chart-card" style="max-width:800px !important;">
                <div class="card-title">Statistics</div>
                <div class="card-subtitle">Income and Sales</div>

                <div class="legend">
                    <span><span class="dot rev"></span>Revenue</span>
                    <span><span class="dot sal"></span>Sales</span>
                </div>

                @php
                    $salesValue = round($totalIncome, 2);
                    $revenueValue = round($totalearning[auth('seller')->id()] ?? 0, 2);

                    $currentMonth = (int) now()->format('n'); // 1–12

                    $maxValue = max($salesValue, $revenueValue);
                    $maxValue = $maxValue < 100 ? 100 : $maxValue;

                    $salesPoints = [];
                    $revenuePoints = [];
                    $pointsData = [];

                    $svgWidth = 400;
                    $svgHeight = 100;
                    $xGap = $svgWidth / 11;

                    foreach (range(1, 12) as $index => $month) {
                        $x = $index * $xGap;

                        $monthSales = $month == $currentMonth ? $salesValue : 0;
                        $monthRevenue = $month == $currentMonth ? $revenueValue : 0;

                        $salesY = $svgHeight - ($monthSales / $maxValue) * 120;
                        $revenueY = $svgHeight - ($monthRevenue / $maxValue) * 120;

                        $salesPoints[] = round($x, 2) . ',' . round($salesY, 2);
                        $revenuePoints[] = round($x, 2) . ',' . round($revenueY, 2);

                        $pointsData[] = [
                            'month' => $month,
                            'sales' => $monthSales,
                            'revenue' => $monthRevenue,
                            'x' => round($x, 2),
                            'salesY' => round($salesY, 2),
                            'revenueY' => round($revenueY, 2),
                        ];
                    }

                    $yRanges = [
                        0,
                        round($maxValue * 0.25),
                        round($maxValue * 0.5),
                        round($maxValue * 0.75),
                        round($maxValue),
                    ];
                @endphp
                <div class="areachart-container chart-wrapper">
                    <div class="y-axis">
                        @foreach (array_reverse($yRanges) as $val)
                            <span>₹{{ number_format($val) }}</span>
                        @endforeach
                    </div>

                    <div class="chart-area" style="position:relative;">
                        <svg viewBox="0 0 400 120" width="100%" height="120">

                            <!-- Revenue -->
                            <polyline fill="none" stroke="#6C54A3" stroke-width="2"
                                points="{{ implode(' ', $revenuePoints) }}" />

                            <!-- Sales -->
                            <polyline fill="none" stroke="#49A86A" stroke-width="2"
                                points="{{ implode(' ', $salesPoints) }}" />

                            @foreach ($pointsData as $p)
                                <circle class="chart-dot sales-dot" cx="{{ $p['x'] }}" cy="{{ $p['salesY'] }}"
                                    r="4" data-month="{{ $p['month'] }}" data-value="{{ $p['sales'] }}"
                                    data-type="Sales" />

                                <circle class="chart-dot revenue-dot" cx="{{ $p['x'] }}"
                                    cy="{{ $p['revenueY'] }}" r="4" data-month="{{ $p['month'] }}"
                                    data-value="{{ $p['revenue'] }}" data-type="Revenue" />
                            @endforeach

                        </svg>

                        <div class="chart-tooltip" id="chartTooltip"></div>

                        <div class="x-axis-labels">
                            <span class="{{ $currentMonth == 1 ? 'active' : '' }}">JAN</span>
                            <span class="{{ $currentMonth == 2 ? 'active' : '' }}">FEB</span>
                            <span class="{{ $currentMonth == 3 ? 'active' : '' }}">MAR</span>
                            <span class="{{ $currentMonth == 4 ? 'active' : '' }}">APR</span>
                            <span class="{{ $currentMonth == 5 ? 'active' : '' }}">MAY</span>
                            <span class="{{ $currentMonth == 6 ? 'active' : '' }}">JUN</span>
                            <span class="{{ $currentMonth == 7 ? 'active' : '' }}">JUL</span>
                            <span class="{{ $currentMonth == 8 ? 'active' : '' }}">AUG</span>
                            <span class="{{ $currentMonth == 9 ? 'active' : '' }}">SEP</span>
                            <span class="{{ $currentMonth == 10 ? 'active' : '' }}">OCT</span>
                            <span class="{{ $currentMonth == 11 ? 'active' : '' }}">NOV</span>
                            <span class="{{ $currentMonth == 12 ? 'active' : '' }}">DEC</span>
                        </div>
                    </div>
                </div>
                <style>
                    .chart-wrapper {
                        display: flex;
                    }

                    .y-axis {
                        height: 175px;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        font-size: 11px;
                        margin-right: 8px;
                        color: #555;
                    }

                    .chart-dot {
                        cursor: pointer;
                    }

                    .sales-dot {
                        fill: #49A86A;
                    }

                    .revenue-dot {
                        fill: #6C54A3;
                    }

                    .x-axis-labels span {
                        font-size: 11px;
                        color: #999;
                    }

                    .x-axis-labels span.active {
                        color: #fff;
                        background: #2563eb;
                        padding: 2px 6px;
                        border-radius: 4px;
                    }

                    .chart-tooltip {
                        position: absolute;
                        background: #222;
                        color: #fff;
                        padding: 6px 10px;
                        border-radius: 6px;
                        font-size: 11px;
                        pointer-events: none;
                        opacity: 0;
                        transition: 0.2s;
                    }

                    .chart-area {
                        width: 100%;
                        overflow: visible;
                    }

                    svg {
                        overflow: visible;
                    }
                </style>
                <script>
                    const tooltip = document.getElementById('chartTooltip');
                    document.querySelectorAll('.chart-dot').forEach(dot => {
                        dot.addEventListener('mouseenter', function(e) {

                            const value = this.dataset.value;
                            const type = this.dataset.type;
                            const month = this.dataset.month;

                            const monthNames = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT',
                                'NOV', 'DEC'
                            ];

                            tooltip.innerHTML = `
                                <strong>${type}</strong><br>
                                ₹${Number(value).toLocaleString()}<br>
                                ${monthNames[month - 1]}
                            `;

                            tooltip.style.opacity = 1;
                        });

                        dot.addEventListener('mousemove', function(e) {
                            tooltip.style.left = e.pageX + 10 + 'px';
                            tooltip.style.top = e.pageY - 20 + 'px';
                        });

                        dot.addEventListener('mouseleave', function() {
                            tooltip.style.opacity = 0;
                        });
                    });
                </script>

            </div>






            <!-- End CHARTS HTML -->


            <div class="dashboard-main-row">
                <div class="product-table-section">
                    <div class="section-title">Best Selling Product</div>
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Sales</th>
                                <th>Amount</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bestSellingProducts as $product)
                                <tr>
                                    <td>
                                        <img src="{{ Storage::url('app/public/images/' . $product->thumbnail_image) }}"
                                            alt="Product Image" width="40" height="40">

                                        {{ \Illuminate\Support\Str::words($product->name, 8, '...') }}
                                    </td>
                                    <td>{{ $product->total_quantity }}</td>
                                    <td>₹{{ $product->total_amount }}</td>
                                    <td>₹{{ $product->listed_price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="side-stats-section">
                    @php
                        $max = max($monthlyData) ?: 1;
                        $points = [];
                        foreach ($monthlyData as $index => $value) {
                            $x = $index * 25;
                            $y = 90 - ($value / $max) * 80;
                            $points[] = $x . ',' . $y;
                        }
                    @endphp

                    <div class="customers-orders-card">
                        <div class="card-title">Overall Customers Orders</div>

                        <div class="linechart-container">
                            <svg viewBox="0 0 300 100" width="300" height="100">
                                <polyline fill="none" stroke="#6B394A" stroke-width="5"
                                    points="{{ implode(' ', $points) }}" />
                            </svg>

                            <div class="x-axis-labels">
                                <span>JAN</span><span>FEB</span><span>MAR</span><span>APR</span>
                                <span>MAY</span><span>JUN</span><span>JUL</span><span>AUG</span>
                                <span>SEP</span><span>OCT</span><span>NOV</span><span>DEC</span>
                            </div>
                        </div>
                    </div>

                    <div class="best-cat-card">
                        <div class="card-title">Best Selling Products Categories</div>

                        <ul class="category-list">
                            @foreach ($bestSellingCategories as $cat)
                                @php
                                    $percentage = ($cat->total_qty / $maxCategorySale) * 100;
                                @endphp
                                <li>
                                    <span class="cat-name">{{ $cat->category_name }}</span>
                                    <span class="cat-bar" style="width: {{ $percentage }}%"></span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="orders-section">
                <div class="section-title">Recent Orders</div>

                <div class="table-responsive">
                    <table id="datatable"
                        style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 __table">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ \App\CPU\translate('SL') }}</th>
                                <th>{{ \App\CPU\translate('Order_ID') }}</th>
                                <th>{{ \App\CPU\translate('Order_Date') }}</th>
                                <th>{{ \App\CPU\translate('customer_info') }}</th>
                                <th>{{ \App\CPU\translate('Order_Status') }}</th>
                                <th class="text-center">{{ \App\CPU\translate('Shipping Status') }}</th>
                                <th>{{ \App\CPU\translate('pickup_date') }}</th>
                                <th class="text-center">{{ \App\CPU\translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $k => $transaction)
                                @if ($transaction->order)
                                    @php
                                        $order = $transaction->order;
                                    @endphp
                                    <tr>
                                        <td>{{ $transactions->firstItem() + $k }}</td>

                                        <td>
                                            <a class="title-color hover-c1"
                                                href="{{ route('seller.orders.details', $order->id) }}">
                                                {{ $order->id }}
                                            </a>
                                        </td>

                                        <td>
                                            <div>{{ date('d M Y', strtotime($order->created_at)) }}</div>
                                            <div>{{ date('h:i A', strtotime($order->created_at)) }}</div>
                                        </td>

                                        <td>
                                            @if ($order->customer_id == 0)
                                                <strong>Walking Customer</strong>
                                            @else
                                                <div>{{ $order->customer->f_name ?? '' }}
                                                    {{ $order->customer->l_name ?? '' }}</div>
                                                <a class="d-block title-color"
                                                    href="tel:{{ $order->customer->phone ?? '' }}">
                                                    {{ $order->customer->phone ?? '' }}
                                                </a>
                                            @endif
                                        </td>

                                        <td class="text-capitalize">
                                            @if ($order->order_status == 'pending')
                                                <label class="badge badge-soft-primary">{{ $order->order_status }}</label>
                                            @elseif($order->order_status == 'processing' || $order->order_status == 'out_for_delivery')
                                                <label class="badge badge-soft-warning">
                                                    {{ str_replace('_', ' ', $order->order_status) }}
                                                </label>
                                            @elseif($order->order_status == 'delivered' || $order->order_status == 'confirmed')
                                                <label class="badge badge-soft-success">{{ $order->order_status }}</label>
                                            @elseif($order->order_status == 'returned')
                                                <label class="badge badge-soft-danger">{{ $order->order_status }}</label>
                                            @else
                                                <label class="badge badge-soft-danger">{{ $order->order_status }}</label>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @php
                                                $ship = DB::table('shiprocket_couriers')
                                                    ->where('order_id', $order->id)
                                                    ->first();
                                            @endphp

                                            @if (isset($ship->status))
                                                <span class="badge badge-soft-dark">{{ $ship->status }}</span>
                                            @else
                                                <span class="badge badge-soft-dark">Shipping not created</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($order->pickup_date)
                                                {{ date('Y-m-d h:i A', strtotime($order->pickup_date)) }}
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-center gap-2">

                                                {{-- VIEW --}}
                                                <a class="btn btn-outline--primary btn-sm square-btn" title="View"
                                                    href="{{ route('seller.orders.details', $order->id) }}">
                                                    <i class="tio-invisible"></i>
                                                </a>

                                                {{-- INVOICE --}}
                                                <a class="btn btn-outline-info btn-sm square-btn" target="_blank"
                                                    href="{{ route('seller.orders.generate-invoice', $order->id) }}">
                                                    <i class="tio-file"></i>
                                                </a>

                                                {{-- LABEL --}}
                                                <a class="btn btn-outline-success btn-sm square-btn"
                                                    href="javascript:void(0);"
                                                    onclick="downloadLabel('{{ optional($ship)->awb_code }}')">
                                                    <i class="tio-download"></i>
                                                </a>

                                            </div>
                                        </td>

                                    </tr>
                                @endif
                            @endforeach
                        </tbody>

                        <script>
                            function downloadLabel(awb) {
                                let url = "{{ route('label', ['awb' => ':awb']) }}".replace(':awb', awb);
                                window.location.href = url;
                            }
                        </script>
                    </table>
                    @if (count($transactions) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ asset('public/assets/back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description">
                            <p class="mb-0">{{ \App\CPU\translate('No_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const dateTypeInput = document.getElementById('date_type');
                const fromInput = document.getElementById('from_date');
                const toInput = document.getElementById('to_date');
                const form = document.getElementById('form-data');

                $('#date_range').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        format: 'YYYY-MM-DD',
                        cancelLabel: 'Clear'
                    }
                });

                $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format(
                        'YYYY-MM-DD'));
                    fromInput.value = picker.startDate.format('YYYY-MM-DD');
                    toInput.value = picker.endDate.format('YYYY-MM-DD');
                    dateTypeInput.value = 'custom_date';
                    form.submit(); // auto-submit
                });

                $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    fromInput.value = '';
                    toInput.value = '';
                    dateTypeInput.value = '';
                    form.submit(); // optional: submit empty filter
                });

            });

            function setDateType(type) {
                const today = new Date();
                let start, end;

                if (type === 'this_year') {
                    start = new Date(today.getFullYear(), 0, 1);
                    end = new Date(today.getFullYear(), 11, 31);
                }

                if (type === 'this_month') {
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                }

                if (type === 'this_week') {
                    const day = today.getDay();
                    start = new Date(today);
                    start.setDate(today.getDate() - day);
                    end = new Date(start);
                    end.setDate(start.getDate() + 6);
                }

                if (type === 'custom_date') {
                    document.getElementById('date_type').value = 'custom_date';
                    return;
                }

                function format(d) {
                    return d.toISOString().split('T')[0];
                }

                document.getElementById('from_date').value = format(start);
                document.getElementById('to_date').value = format(end);
                document.getElementById('date_type').value = type;
                document.getElementById('form-data').submit();
            }
        </script>
    @endsection
