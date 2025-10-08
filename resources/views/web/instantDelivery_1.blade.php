@extends('layouts.back-end.common_seller_1')



@section('content')



<main class="main">





<div class="page-content pb-0">



    <div class="banner-head">
<center>
        <img style="object-fit: cover; max-width: 100%;" src="https://interiorchowk.com/storage/banner/2025-04-01-67eb7a9796458.mp4"

        alt="" />
</center>
    </div>



    <div class="container">

    <div class="row mt-3 mb-3">

        <div class="col-lg-10">

            <div class="cities-ui">

                <img style="border-radius: 15px 0 0 15px;" src="{{ asset('website/new/assets/images/Group-256.png') }}" alt="" />

                <ul class="cities-list" style="cursor: pointer;">

                    <li onclick="goToCity('ahmedabad')"><img src="{{ asset('website/new/assets/images/ahmedabaad.png') }}" alt="" /><span>Ahmedabad</span></li>

                    <li onclick="goToCity('bengaluru')"><img src="{{ asset('website/new/assets/images/bengaluru.png') }}" alt="" /><span>Bengaluru</span></li>

                    <li onclick="goToCity('chandigarh')"><img src="{{ asset('website/new/assets/images/chandigarh.png') }}" alt="" /><span>Chandigarh</span></li>

                    <li onclick="goToCity('chennai')"><img src="{{ asset('website/new/assets/images/chennai.png') }}" alt="" /><span>Chennai</span></li>

                    <li onclick="goToCity('coimbatore')"><img src="{{ asset('website/new/assets/images/coimbatore.png') }}" alt="" /><span>Coimbatore</span></li>

                    <li onclick="goToCity('delhi')"><img src="{{ asset('website/new/assets/images/delhi.png') }}" alt="" /><span>Delhi</span></li>

                    <li onclick="goToCity('hyderabad')"><img src="{{ asset('website/new/assets/images/hyderabaad.png') }}" alt="" /><span>Hyderabad</span></li>

                    <li onclick="goToCity('goa')"><img src="{{ asset('website/new/assets/images/goa.png') }}" alt="" /><span>Goa</span></li>

                </ul>

            </div>

        </div>



        <div class="col-lg-2">

            <div class="card" style="background-color: #EBF5FF; border: 1px solid #2E6CB2; border-radius: 15px;">

                <div class="card-body text-login">

                    <p style="font-size: 11px; font-weight: 600;">Enter Pin-code manually.</p>

                    <div class="form-group d-flex mt-0 mb-0">

                        <input type="text" class="form-control mr-2" id="pincodeInput2" placeholder="Enter your Pin Code" style="flex: 1;">

                        <button onclick="goToPincode()" class="btn btn-primary">Go</button>

                    </div>



                    <p class="card-text mb-0 text-center">OR</p>

                    <button onclick="getLocation()" class="btn btn-primary btn-block mt-0">Use My Location <i class="fa fa-location-arrow ml-2 mr-0" aria-hidden="true"></i></button>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- Scripts --}}
<script>
    function goToCity(citySlug) {
        const cityPincodes = {
            'ahmedabad': '380001',
            'bengaluru': '560001',
            'chandigarh': '160017',
            'chennai': '600001',
            'coimbatore': '641001',
            'delhi': '110001',
            'hyderabad': '500001',
            'goa': '403001'
        };

        const pincode = cityPincodes[citySlug.toLowerCase()];
        if (pincode) {
            window.location.href = "{{ url('/instant_2') }}/" + pincode;
            // OR if named route
            // window.location.href = "{{ route('instant-delivery-products', '') }}/" + pincode;
        } else {
            alert('Pincode not found for this city');
        }
    }
</script>

<script>

    // const cityPincodes = {

    //     'ahmedabad': '380001',

    //     'bengaluru': '560001',

    //     'chandigarh': '160017',

    //     'chennai': '600001',

    //     'coimbatore': '641001',

    //     'delhi': '110001',

    //     'hyderabad': '500001',

    //     'goa': '403001'

    // };



    // function goToCity(citySlug) {

    //     const pincode = cityPincodes[citySlug.toLowerCase()];

    //     if (pincode) {

    //         window.location.href = `/instant_2/${pincode}`;

    //     } else {

    //         alert('Pincode not found for this city');

    //     }

    // }



    function goToPincode() {

        const pin = document.getElementById('pincodeInput2').value;

        if (pin.match(/^\d{6}$/)) {

            window.location.href = `/instant_2/${pin}`;

        } else {

            alert('Please enter a valid 6-digit pincode.');

        }

    }



    function getLocation() {

        if (navigator.geolocation) {

            navigator.geolocation.getCurrentPosition(function(position) {

                const lat = position.coords.latitude;

                const lng = position.coords.longitude;



                const apiKey = "{{ env('GOOGLE_MAPS_API_KEY') }}";

                const url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${apiKey}`;



                fetch(url)

                    .then(res => res.json())

                    .then(data => {

                        // flatten every address_components array into one big list

                        const allComponents = data.results

                        .flatMap(r => r.address_components);



                        // now find the first component whose types include "postal_code"

                        const postalComponent = allComponents

                        .find(c => c.types.includes('postal_code'));



                        if (postalComponent) {

                        window.location.href = `/ic/instant_2/${postalComponent.long_name}`;

                        } else {

                        alert('Could not detect pincode from location.');

                        }

                    })

                    .catch(err => {

                        console.error(err);

                        alert('Failed to fetch location info.');

                    });



            }, function(error) {

                alert('Location access denied.');

            });

        } else {

            alert("Geolocation is not supported by this browser.");

        }

    }

</script>





        <div class="row mb-4">

            <div class="col-md-12">

                <div class="p-4" style="border: 1px solid #2E6CB2;border-radius: 15px;">

                    <h6>Related Videos</h6>

                    <div class="row">

                        <div class="col-md-9">

                            <iframe width="100%" height="500" src="https://www.youtube.com/embed/SpsiQwxOrKw?si=tyONcOf4DKPjxLBd" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

                            <h5 class="mt-1">Waise Ye Ghar Kisse Banwayaa? | InteriorChowk | Home decor products | Download InteriorChowk app now</h5>

                        </div>

                        <div class="col-md-3">

                            <iframe width="100%" height="160" src="https://www.youtube.com/embed/SpsiQwxOrKw?si=tyONcOf4DKPjxLBd" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

                            <iframe width="100%" height="160" src="https://www.youtube.com/embed/SpsiQwxOrKw?si=tyONcOf4DKPjxLBd" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

                            <iframe width="100%" height="160" src="https://www.youtube.com/embed/SpsiQwxOrKw?si=tyONcOf4DKPjxLBd" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

                        </div>

                    </div>

                </div>

            </div>

        </div>



    </div><!-- End .page-content -->

</main><!-- End .main -->





@endsection

