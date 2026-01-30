@extends('layouts.back-end.common_seller_1')

@section('content')
    <div class="page-wrapper d-none d-md-block">
        <main class="main mt-3">
            <div class="page-content">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Privacy Policy</h5>
                                </div>
                                <div class="card-body">
                                    {!! $privacy_policy->value !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
