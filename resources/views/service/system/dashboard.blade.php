@extends('layouts.back-end.app-service')
@section('title', \App\CPU\translate('Dashboard'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="dashboard-container">
        <div class="dashboard-summary">
            <div class="card first-div">
                Dashboard Service
            </div>
        </div>
    </div>
@endsection
