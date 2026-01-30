@extends('layouts.back-end.app-seller')

@section('title', \App\CPU\translate('Warehouse'))
<style>
    /* Button styles */
    .btn--primary {
        padding: 10px 15px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .btn--primary i {
        margin-right: 5px;
    }

    /* Popup container */
    .popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    /* Popup content */
    .popup-content {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        width: 400px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        text-align: center;
    }

    .popup-content h3 {
        margin-bottom: 20px;
    }

    /* Input fields */
    .popup-content input[type="text"],
    .popup-content input[type="email"],
    .popup-content input[type="number"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    /* Close button */
    .close-btn {
        color: white;
        background-color: red;
        border: none;
        border-radius: 5px;
        padding: 8px 12px;
        cursor: pointer;
        margin-right: 10px;
    }

    /* Save button */
    .save-btn {
        color: white;
        background-color: green;
        border: none;
        border-radius: 5px;
        padding: 8px 12px;
        cursor: pointer;
    }

    /* Popup container */
    .popup {
        display: none;
        /* Initially hidden */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    /* Popup content */
    .popup-content {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 900px;
    }

    /* Flex container for the grid */
    .popup-content .form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        /* Space between fields */
        margin-bottom: 15px;
    }

    /* Each field will take up 1/3 of the 12-column grid */
    .popup-content .form-group {
        width: calc(100% / 4 - 15px);
        /* 4 fields per row on the first two rows */
        margin-bottom: 15px;
    }

    /* Adjust for the 3rd row with 3 fields per row */
    .popup-content .form-row:nth-child(3) .form-group {
        width: calc(100% / 3 - 15px);
        /* 3 fields per row for the 3rd row */
    }

    /* Labels styling */
    .popup-content .form-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    /* Inputs and selects styling */
    .popup-content .form-group input,
    .popup-content .form-group select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    /* Buttons styling */
    .popup-content .close-btn,
    .popup-content .save-btn {
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 4px;
        margin-top: 10px;
    }

    .popup-content .close-btn {
        background-color: #f44336;
        /* Red for cancel */
    }

    .popup-content .save-btn {
        background-color: #4CAF50;
        /* Green for save */
    }

    /* Responsiveness */
    @media (max-width: 768px) {
        .popup-content .form-group {
            width: 100%;
            /* Make fields full width on smaller screens */
        }
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid black;
    }

    th,
    td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }

    .tio-delete {
        color: red;
    }
</style>
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('public/assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="{{ asset('public/assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('/public/assets/back-end/img/my-bank-info.png') }}" alt="">
                {{ \App\CPU\translate('Warehouse') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-lg-5">
                                <form action="" method="GET">
                                    <!-- Search -->
                                    <div class="input-group input-group-merge input-group-custom">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{ \App\CPU\translate('Search by Product Name or SKU or HSN') }}"
                                            aria-label="Search orders" value="" required>
                                        <button type="submit"
                                            class="btn btn--primary">{{ \App\CPU\translate('search') }}</button>
                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div>
                            <!-- start filter  -->


                            <button class="btn  btn-primary" data-toggle="modal" data-target="#productFilterModal">
                                <span class="text">{{ \App\CPU\translate('Filter') }} <i class="tio-filter"></i></span>
                            </button>

                            <!-- End  filter  -->

                            <div class="col-lg-6 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                                <div style="display:none;">
                                    <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                                        <i class="tio-download-to"></i>

                                        <i class="tio-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a class="dropdown-item"
                                                href="{{ route('seller.product.bulk-export') }}">{{ \App\CPU\translate('excel') }}</a>
                                        </li>
                                        <div class="dropdown-divider"></div>
                                    </ul>
                                </div>
                                <button type="button" onclick="openPopup()" class="btn btn--primary"><i
                                        class="tio-add-circle"></i>{{ \App\CPU\translate('Add warehouse') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div id="output"></div> -->
            <div class="container">
                <div class="row">
                    <table style="width: 100%; border: 1px solid black;">
                        <tr>
                            <td>Warehouse id</th>
                            <th>Name</th>
                            <th>Title</th>
                            <th>City</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Pincode</th>
                            <th>Action</th>
                        </tr>
                        @foreach ($warehouse as $house)
                            <tr>
                                <td>{{ $house->id }}</td>
                                <td>{{ $house->name }}</td>
                                <td>{{ $house->title }}</td>
                                <td>{{ $house->city }}</td>
                                <td>{{ $house->contact }}</td>
                                <td>{{ $house->email }}</td>
                                <td>{{ $house->address }}</td>
                                <td>{{ $house->pincode }}</td>
                                <td><button type="button" class="delete-btn" data-id="{{ $house->id }}"><i
                                            class="tio-delete"></i></button></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>


            <!-- add popup -->
            <div class="popup" id="popup">
                <div class="popup-content">
                    <h3>Add Warehouse</h3>

                    <!-- First row with 4 fields -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="warehouse-name">Name</label>
                            <input type="text" id="warehouse-name" name="name" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <label for="warehouse-title">Warehouse Name</label>
                            <input type="text" id="warehouse-title" name="title" placeholder="warehouse name">
                        </div>
                        <div class="form-group">
                            <label for="warehouse-contact">Contact Number</label>
                            <input type="number" id="warehouse-contact" name="contact" placeholder="Contact Number">
                        </div>
                        <div class="form-group">
                            <label for="warehouse-email">Email</label>
                            <input type="email" id="warehouse-email" name="email" placeholder="Email">
                        </div>

                    </div>

                    <!-- Second row with 4 fields -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="warehouse-address">Address</label>
                            <input type="text" id="warehouse-address" name="address" placeholder="Address">
                        </div>


                    </div>

                    <!-- Third row with 3 fields -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="pincode">Pincode</label>
                            <input type="text" id="pincode" name="pincode" placeholder="Pincode">
                        </div>
                        <div class="form-group">
                            <label for="select1">City</label>
                            <input type="" name="city" id="city" disabled>
                        </div>
                        <div class="form-group">
                            <label for="select4">State</label>
                            <input type="" name="state" id="state" disabled>
                        </div>
                        <div class="form-group">
                            <label for="select5">Country</label>
                            <input type="" name="country" id="country" disabled>
                        </div>
                    </div>

                    <button class="close-btn" onclick="closePopup()">Cancel</button>
                    <button class="save-btn" id="save-warehouse-btn">Save</button>
                </div>
            </div>


            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
            <script>
                // Open the popup
                function openPopup() {
                    $('#popup').css('display', 'flex');
                }

                // Close the popup
                function closePopup() {
                    $('#popup').css('display', 'none');
                }

                // Save button logic
                $(document).ready(function() {
                    $('#save-warehouse-btn').on('click', function() {
                        // Collect input values
                        const name = $('#warehouse-name').val().trim();
                        const title = $('#warehouse-title').val().trim();
                        const contact = $('#warehouse-contact').val().trim();
                        const address = $('#warehouse-address').val().trim();
                        const email = $('#warehouse-email').val().trim();
                        const pincode = $('#pincode').val().trim();
                        const city = $('#city').val().trim();
                        const state = $('#state').val().trim();
                        // console.log(pincode);
                        // Validate fields
                        if (name && title && contact && address && email && pincode && city) {
                            // Disable button to prevent multiple clicks
                            $('#save-warehouse-btn').prop('disabled', true).text('Saving...');

                            // AJAX request
                            $.ajax({
                                url: '{{ route('seller.profile.save-warehouse') }}', // Replace with your server-side route
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Laravel CSRF token for security
                                },
                                data: {
                                    name: name,
                                    title: title,
                                    contact: contact,
                                    address: address,
                                    email: email,
                                    pincode: pincode,
                                    city: city,
                                    state: state,
                                },
                                success: function(response) {
                                    alert(response.message || 'Warehouse saved successfully!');
                                    closePopup();
                                    $('#popup input').val(''); // Clear inputs
                                    get(); // Refresh the warehouse list
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error:', error);
                                    alert('An error occurred while saving the warehouse.');
                                },
                                complete: function() {
                                    // Re-enable button after request completion
                                    $('#save-warehouse-btn').prop('disabled', false).text('Save');
                                }
                            });
                        } else {
                            alert('All fields are required!');
                        }
                    });
                });

                // Fetch and display warehouse data
                function get() {
                    $.ajax({
                        url: '{{ route('seller.profile.get_warehouse') }}', // Replace with your server-side route
                        type: 'GET',
                        success: function(response) {
                            console.log(response);
                            let result = '';
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(item) {
                                    result += '<div style="display:flex; background-color: white;">' +
                                        '<br><strong>Name:</strong> <p>' + item.name + '</p>' +
                                        '<br><strong>Title:</strong> <p>' + item.title + '</p>' +
                                        '<br><strong>Contact:</strong> <p>' + item.contact + '</p>' +
                                        '<br><strong>Address:</strong> <p>' + item.address + '</p>' +
                                        '<br><strong>Email:</strong> <p>' + item.email + '</p>' +
                                        '</div>';
                                });
                            } else {
                                result = '<p>No warehouses found.</p>';
                            }

                            // Display the result in the target container
                            $('#output').html(result);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            $('#output').html('<p>An error occurred while fetching data.</p>');
                        }
                    });
                }

                // Initial fetch of warehouse data
                get();

                // Pincode AJAX functionality
                $(document).ready(function() {
                    var pincode = $('#pincode');

                    pincode.on('input', function() {
                        var pincodes = pincode.val();
                        console.log(pincodes);

                        $.ajax({
                            url: '{{ route('seller.profile.pincode') }}', // Replace with your server-side route
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Laravel CSRF token for security
                            },
                            data: {
                                pincode: pincodes
                            },
                            success: function(response) {
                                console.log('Pincode response:', response);
                                $('#city').val(response.city);
                                $('#state').val(response.state);
                                $('#country').val(response.country);

                                // Handle the response as needed (e.g., update UI based on the response)
                            },
                            // error: function(xhr, status, error) {
                            //     console.error('Error:', error);
                            //     alert('An error occurred while processing the pincode.');
                            // }
                        });
                    });
                });



                $(document).ready(function() {
                    $('.delete-btn').on('click', function() {
                        var warehouseId = $(this).attr("data-id");

                        $.ajax({
                            url: '{{ route('seller.profile.delete_warehouse') }}',
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                warehouse_id: warehouseId
                            },
                            success: function(result) {
                                console.log(result);
                            },
                            error: function(xhr, status, error) {
                                console.error("Error:", error);
                            }
                        });
                    });
                });
            </script>

        @endsection

        @push('script')
        @endpush
