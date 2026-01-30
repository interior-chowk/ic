<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\CPU\translate('invoice') }}</title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta charset="UTF-8">
    <style media="all">
        * {
            margin: 0;
            padding: 0;
            line-height: 1.3;
            font-family: sans-serif;
            color: #333542;
        }


        /* IE 6 */
        * html .footer {
            position: absolute;
            top: expression((0-(footer.offsetHeight)+(document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight)+(ignoreMe=document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop))+'px');
        }

        body {
            font-size: .75rem;
        }

        img {
            max-width: 100%;
        }

        .customers {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        table {
            width: 100%;
        }



        table thead th {
            padding: 6px;
            font-size: 11px;
            text-align: left;
            border: 1px solid black;
        }

        table tbody th,
        table tbody td {
            padding: 6px;
            font-size: 11px;
            border: 1px solid black;

        }

        table.fz-12 thead th {
            font-size: 12px;
        }

        table.fz-12 tbody th,
        table.fz-12 tbody td {
            font-size: 12px;
        }

        table.customers thead th {
            background-color: #0177CD;
            color: #fff;
        }

        table.customers tbody th,
        table.customers tbody td {
            background-color: #FAFCFF;
        }

        table.calc-table th {
            text-align: left;
        }

        table.calc-table td {
            text-align: right;
        }

        table.calc-table td.text-left {
            text-align: left;
        }

        .table-total {
            font-family: Arial, Helvetica, sans-serif;
        }


        .text-left {
            text-align: left !important;
        }

        .pb-2 {
            padding-bottom: 6px !important;
        }

        .pb-3 {
            padding-bottom: 14px !important;
        }

        .text-right {
            text-align: right;
        }

        table th.text-right {
            text-align: right !important;
        }

        .content-position {
            padding: 10px 10px;
        }

        .content-position-y {
            padding: 0px 0px;
        }

        .text-white {
            color: white !important;
        }

        .bs-0 {
            border-spacing: 0;
        }

        .text-center {
            text-align: center;
        }

        .mb-1 {
            margin-bottom: 4px !important;
        }

        .mb-2 {
            margin-bottom: 8px !important;
        }

        .mb-4 {
            margin-bottom: 24px !important;
        }

        .mb-30 {
            margin-bottom: 30px !important;
        }

        .px-10 {
            padding-left: 10px;
            padding-right: 10px;
        }

        .fz-14 {
            font-size: 14px;
        }

        .fz-12 {
            font-size: 12px;
        }

        .fz-10 {
            font-size: 10px;
        }

        .font-normal {
            font-weight: 400;
        }

        .border-dashed-top {
            border-top: 1px dashed #ddd;
        }

        .font-weight-bold {
            font-weight: 700;
        }

        .bg-light {
            background-color: #F7F7F7;
        }

        .py-30 {
            padding-top: 30px;
            padding-bottom: 30px;
        }

        .py-4 {
            padding-top: 24px;
            padding-bottom: 24px;
        }

        .d-flex {
            display: flex;
        }

        .gap-2 {
            gap: 8px;
        }

        .flex-wrap {
            flex-wrap: wrap;
        }

        .align-items-center {
            align-items: center;
        }

        .justify-content-center {
            justify-content: center;
        }

        a {
            color: rgba(0, 128, 245, 1);
        }

        .p-1 {
            padding: 4px !important;
        }

        .h2 {
            font-size: 1.5em;
            margin-block-start: 0.83em;
            margin-block-end: 0.83em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            font-weight: bold;
        }

        .h4 {
            margin-block-start: 1.33em;
            margin-block-end: 1.33em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            font-weight: bold;
        }
    </style>
</head>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<body>
    <div class="first">
        <table class="content-position ">
            <tr>
                <td class="p-0 text-left" style="font-size: 26px; ">
                    {{ \App\CPU\translate('Plan_Invoice') }}
                </td>
                <td style="text-align: center;">
                    <img height="70" src="{{ asset("storage/app/public/company/$company_web_logo") }}"
                        alt="">
                </td>
            </tr>
        </table>

    </div>
    <div class="">
        <section>
            <table class="content-position-y fz-12">
                <tr>
                    <td class="font-weight-bold ">
                        <table>
                            <tr>
                                <td>
                                    <span class="h2"
                                        style="margin: 0px;">{{ \App\CPU\translate('company_address') }} </span>
                                    <div class="h4 montserrat-normal-600">
                                        <p style=" margin-top: 6px; margin-bottom:0px;">
                                            {{ $company_name ? $company_name : '' }}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">
                                            {{ $company_phone ? $company_phone : '' }}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">
                                            {{ $company_email ? $company_email : '' }}</p>

                                    </div>

                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td>
                        <table>
                            <tr>
                                <td class="text-right">
                                    @if ($plan->provider)
                                        <span class="h2">{{ \App\CPU\translate('service_provider_address') }}
                                        </span>
                                        <div class="h4 montserrat-normal-600">
                                            <p style=" margin-top: 6px; margin-bottom:0px;">
                                                {{ $plan->provider ? $plan->provider['name'] : '' }}</p>
                                            <p style=" margin-top: 6px; margin-bottom:0px;">
                                                {{ $plan->provider ? $plan->provider['phone'] : '' }}</p>
                                            <span style="display:ruby-text">
                                                <span class="h3"
                                                    style="margin: 0px;">{{ \App\CPU\translate('permanent_address') }}
                                                    : </span>
                                                <p style=" margin-top: 6px; margin-bottom:0px;">
                                                    {{ $plan->provider ? $plan->provider['permanent_address'] : '' }}
                                                </p>
                                            </span>
                                            <span style="display:ruby-text">
                                                <span class="h3"
                                                    style="margin: 0px;">{{ \App\CPU\translate('current_address') }} :
                                                </span>
                                                <p style=" margin-top: 6px; margin-bottom:0px;">
                                                    {{ $plan->provider ? $plan->provider['current_address'] : '' }}</p>
                                            </span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </section>
    </div>

    <br>

    <div class="">
        <div class="content-position-y">
            <table class="customers bs-0">
                <thead>
                    <tr>
                        <th>{{ \App\CPU\translate('plan_name') }}</th>
                        <th>
                            {{ \App\CPU\translate('amount') }}
                        </th>
                        <th>
                            {{ \App\CPU\translate('GST_in_tax') }}
                        </th>
                        <th>
                            {{ \App\CPU\translate('unit_price') }}
                        </th>
                        <th class="text-right">
                            {{ \App\CPU\translate('total_amount') }}
                        </th>
                    </tr>
                </thead>
                @php
                    $subtotal = 0;
                    $total = 0;
                    $sub_total = 0;
                    $total_tax = 0;
                    $total_shipping_cost = 0;
                    $total_discount_on_product = 0;
                    $ext_discount = 0;
                    $sub_total_mrp = 0;
                    $all_coupons = 0;
                    $sub_total_price = 0;
                    $count = 1;
                @endphp
                <tbody>

                    @php $subtotal=($plan->membership->price) @endphp
                    @php $subtotalwithoutax=(($plan->membership->price*18)/100) @endphp
                    <tr>
                        <td>
                            {{ $plan->membership->plan_name }}
                            <br>
                            {{ \App\CPU\translate('Description') }} : {{ $plan->membership->plan_description }}
                        </td>
                        <td>{{ \App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($subtotal)) }}
                        </td>
                        <td>
                            {{ \App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency(($plan->membership->price * 18) / 100)) }}
                            [{{ 18 }}%]</td>

                        <td>{{ \App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($plan->membership->price - $subtotalwithoutax)) }}
                        </td>
                        <td class="text-right">
                            {{ \App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($subtotal)) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="">
        <div class="content-position-y">
            <table class="customers bs-0">
                <thead>
                    <tr>
                        <th>{{ \App\CPU\translate('Tax Type') }}</th>
                        <th>{{ \App\CPU\translate('Taxable Amount') }}</th>
                        <th>
                            {{ \App\CPU\translate('Rate') }}
                        </th>
                        <th>
                            {{ \App\CPU\translate('Tax Amount') }}
                        </th>
                        <th class="text-center" colspan="5">
                            {{ \App\CPU\translate('Invoice_Amount_in_words') }}
                        </th>

                    </tr>
                </thead>

                <tbody>
                    <tr>

                        <td style=" border-right: none; border-top: none;border-bottom: none;">GST </td>
                        <td style="border: none;">
                            {{ \App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($plan->membership->price)) }}
                        </td>

                        <td style="border: none;">
                            {{ \App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency(18)) }}%</td>
                        <td style="border: none;">{{ ($plan->membership->price * 18) / 100 }}</td>

                        <?php
                        $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                        $words = $formatter->format($subtotal);
                        ?>
                        <td colspan="5">{{ $words }} Only .</td>


                    </tr>

                    <tr>
                        <td style=" border-right: none; border-top: none;border-bottom: none;"></td>
                        <td style="border: none;"></td>
                        <td style="border: none;"></td>
                        <td style="border: none;"></td>

                        <td class="text-center" colspan="5"></td>
                    </tr>

                    <tr>
                        <td style=" border-right: none; border-top: none;border-bottom: none;"></td>
                        <td style="border: none;"></td>
                        <td style="border: none;"></td>
                        <td style="border: none;"></td>
                        <td class="text-center" colspan="5"></td>
                    </tr>

                    <tr>
                        <td style="border-right: none;"></td>
                        <td style="border-right: none; border-left: none;"><b>TOTAL TAX AMOUNT</b></td>
                        <td style="border-right: none;border-left: none;"></td>
                        <td style="border-right: none; border-left: none;">
                            {{ \App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency(($plan->membership->price * 18) / 100)) }}
                        </td>
                        <td class="text-right" colspan="5" style="border-bottom: none;"></td>
                    </tr>

                    <tr>
                        <td style="border-right: none;" colspan="4">
                            <p class="text-center"><b>Thank You ! For shopping with us.</b></p>
                            If you require any assistance or have feedback or suggestions about our app, You can connect
                            us at <a href="mail::to({{ $company_email }})">{{ $company_email }}</a> or 0120-6027176
                        </td>
                        <td class="text-center" colspan="5">
                            <br>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="customers bs-0">
                <thead>
                    <tr>
                        <td class="text-center" colspan="9" style="background-color: #0177CD;color: #fff;">
                            <b>Return: </b>While we at interiorchowk strive for flawless deliveries every time, in the
                            unlikely event that you need to return an item, please do so with the original brand box or
                            price tag, original packing, and invoice. Without these, it will be very difficult for us to
                            fulfill your request. Plese help us in supporting you. <i>Terms and Condition apply</i>
                        </td>

                    </tr>
                </thead>

            </table>

        </div>
    </div>

    <div class="row">
        <section>
            <table class="">
                <!--<tr>
                <th class="fz-12 font-normal pb-3">
                    {{ \App\CPU\translate('If_you_require_any_assistance_or_have_feedback_or_suggestions_about_our_site,_you') }} <br /> {{ \App\CPU\translate('can_email_us_at') }} <a href="mail::to({{ $company_email }})">{{ $company_email }}</a>
                </th>
            </tr>-->
                <tr>
                    <th class="content-position-y bg-light py-4">
                        <div class="d-flex justify-content-center gap-2">
                            <div class="mb-2">
                                <i class="fa fa-phone"></i>
                                {{ \App\CPU\translate('phone') }}
                                : {{ $company_phone }}
                            </div>
                            <div class="mb-2">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                {{ \App\CPU\translate('email') }}
                                : {{ $company_email }}
                            </div>
                        </div>
                        <div class="mb-2">
                            {{ url('/') }}
                        </div>
                        <div>
                            {{ \App\CPU\translate('All_copy_right_reserved_©_' . date('Y') . '_') . $company_name }}
                        </div>
                    </th>
                </tr>
            </table>
        </section>
    </div>

</body>

</html>
