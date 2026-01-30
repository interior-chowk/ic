@extends('layouts.back-end.app-seller')

@section('title', \App\CPU\translate('Policy'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('public/assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="{{ asset('public/assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{ \App\CPU\translate('Seller Guide & Terms') }}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <h5 class="mb-3">{{ \App\CPU\translate('Seller Guide') }}</h5>
                <a href="{{ env('CLOUDFLARE_R2_PUBLIC_URL') . '/policies/Seller’s Guide 2025.pdf' }}"
                    class="btn btn-primary mb-3" target="_blank">{{ \App\CPU\translate('View Seller Guide') }}</a>
            </div>
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <h5 class="mb-3">{{ \App\CPU\translate('Policies') }}</h5>

                {{-- Select Box --}}
                <select id="policySelect" class="form-control mb-2">
                    <option value="">Select Policy</option>
                    <option value="1">Term and conditions</option>
                    <option value="2">Fee and Commission policy</option>
                    <option value="3">Pricing policy</option>
                    <option value="4">Packaging guidelines</option>
                    <option value="5">Content Policy</option>
                    <option value="6">Return & refund policy</option>
                </select>

                {{-- View Policy Button --}}
                <a id="viewPolicyBtn" href="#" target="_blank" class="btn btn-primary mb-3" style="display: none;">
                    {{ \App\CPU\translate('View Policy') }}
                </a>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const policySelect = document.getElementById('policySelect');
                    const viewBtn = document.getElementById('viewPolicyBtn');

                    // Define PDF URLs for each policy
                    const pdfLinks = {
                        1: "{{ env('CLOUDFLARE_R2_PUBLIC_URL') . '/policies/1Term and conditions.pdf' }}",
                        2: "{{ env('CLOUDFLARE_R2_PUBLIC_URL') . '/policies/2 Fee and Commission policy.pdf' }}",
                        3: "{{ env('CLOUDFLARE_R2_PUBLIC_URL') . '/policies/3 Pricing policy.pdf' }}",
                        4: "{{ env('CLOUDFLARE_R2_PUBLIC_URL') . '/policies/4 Packaging guidelines.pdf' }}",
                        5: "{{ env('CLOUDFLARE_R2_PUBLIC_URL') . '/policies/5 Content Policy.pdf' }}",
                        6: "{{ env('CLOUDFLARE_R2_PUBLIC_URL') . '/policies/6 Return & refund policy.pdf' }}"
                    };

                    // Show/hide button and set link
                    policySelect.addEventListener('change', function() {
                        const selected = this.value;
                        if (selected && pdfLinks[selected]) {
                            viewBtn.href = pdfLinks[selected];
                            viewBtn.style.display = 'inline-block';
                        } else {
                            viewBtn.style.display = 'none';
                            viewBtn.href = '#';
                        }
                    });
                });
            </script>

        </div>
    </div>
@endsection
