@extends('layouts.back-end.common_seller_1')

@section('content')
    <main class="main">
        <div class="page-content pb-0">

            <div class="container">

                {{-- ================= City Selector ================= --}}
                <div class="row mt-3 mb-3">

                    <div class="col-lg-10">
                        <div class="cities-ui">
                            <img style="border-radius:15px 0 0 15px"
                                src="{{ asset('public/website/new/assets/images/Group-256.png') }}">

                            <ul class="cities-list" style="cursor:pointer">
                                @php
                                    $cities = [
                                        'ahmedabaad' => 'Ahmedabad',
                                        'bengaluru' => 'Bengaluru',
                                        'chandigarh' => 'Chandigarh',
                                        'chennai' => 'Chennai',
                                        'coimbatore' => 'Coimbatore',
                                        'delhi' => 'Delhi',
                                        'hyderabaad' => 'Hyderabad',
                                        'goa' => 'Goa',
                                    ];
                                @endphp

                                @foreach ($cities as $slug => $name)
                                    <li onclick="goToCity('{{ $slug }}')">
                                        <img src="{{ asset('public/website/new/assets/images/' . $slug . '.png') }}">
                                        <span>{{ $name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- ================= Pincode Card ================= --}}
                    <div class="col-lg-2">
                        <div class="card" style="background:#EBF5FF;border:1px solid #2E6CB2;border-radius:15px">
                            <div class="card-body text-login">

                                <p style="font-size:11px;font-weight:600">Enter Pin-code manually.</p>

                                <div class="form-group d-flex mb-0">
                                    <input type="text" id="pincodeInput2" class="form-control mr-2"
                                        placeholder="Enter Pin Code">
                                    <button onclick="goToPincode()" class="btn btn-primary">Go</button>
                                </div>

                                <p class="text-center mb-0">OR</p>

                                <button onclick="getLocation()" class="btn btn-primary btn-block">
                                    Use My Location <i class="fa fa-location-arrow ml-2"></i>
                                </button>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ================= Related Videos ================= --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="p-4" style="border:1px solid #2E6CB2;border-radius:15px">
                        <h6>Related Videos</h6>

                        <div class="row">
                            {{-- MAIN VIDEO --}}
                            <div class="col-md-9">
                                <div class="yt-placeholder" data-video="SpsiQwxOrKw" style="height:500px">
                                    <img src="https://img.youtube.com/vi/SpsiQwxOrKw/hqdefault.jpg">
                                    <button class="yt-play">▶</button>
                                </div>

                                <h5 class="mt-1">
                                    Waise Ye Ghar Kisse Banwayaa? | InteriorChowk
                                </h5>
                            </div>

                            {{-- SIDE VIDEOS --}}
                            <div class="col-md-3">
                                @for ($i = 0; $i < 3; $i++)
                                    <div class="yt-placeholder small" data-video="SpsiQwxOrKw">
                                        <img src="https://img.youtube.com/vi/SpsiQwxOrKw/mqdefault.jpg">
                                        <button class="yt-play">▶</button>
                                    </div>
                                @endfor
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </main>

    {{-- ================= YouTube Lazy Load ================= --}}
    <script>
        document.querySelectorAll('.yt-placeholder').forEach(el => {
            el.addEventListener('click', function() {
                const id = this.dataset.video;
                this.innerHTML = `
            <iframe width="100%" height="100%"
                src="https://www.youtube-nocookie.com/embed/${id}?autoplay=1"
                frameborder="0"
                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        `;
            });
        });
    </script>

    {{-- ================= Existing Scripts ================= --}}
    <script>
        const cityPincodes = {
            ahmedabad: '380001',
            bengaluru: '560001',
            chandigarh: '160017',
            chennai: '600001',
            coimbatore: '641001',
            delhi: '110001',
            hyderabad: '500001',
            goa: '403001'
        };

        function goToCity(city) {
            if (cityPincodes[city]) {
                window.location.href = "{{ url('/instant_2') }}/" + cityPincodes[city];
            } else alert('Pincode not found');
        }

        function goToPincode() {
            const pin = document.getElementById('pincodeInput2').value;
            /^\d{6}$/.test(pin) ?
                window.location.href = `/instant_2/${pin}` :
                alert('Enter valid 6-digit pincode');
        }

        function getLocation() {
            if (!navigator.geolocation) return alert('Geolocation not supported');

            navigator.geolocation.getCurrentPosition(pos => {
                const {
                    latitude,
                    longitude
                } = pos.coords;
                fetch(
                        `https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&key={{ env('GOOGLE_MAPS_API_KEY') }}`)
                    .then(r => r.json())
                    .then(d => {
                        const postal = d.results.flatMap(r => r.address_components)
                            .find(c => c.types.includes('postal_code'));
                        postal
                            ?
                            window.location.href = `/instant_2/${postal.long_name}` :
                            alert('Pincode not found');
                    });
            }, () => alert('Location denied'));
        }
    </script>

    {{-- ================= Styles ================= --}}
    <style>
        .yt-placeholder {
            position: relative;
            cursor: pointer;
            overflow: hidden;
            border-radius: 10px;
        }

        .yt-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .yt-play {
            position: absolute;
            inset: 0;
            margin: auto;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: none;
            background: rgba(0, 0, 0, .6);
            color: #fff;
            font-size: 28px;
        }

        .yt-placeholder.small {
            height: 160px;
            margin-bottom: 10px;
        }
    </style>
@endsection
