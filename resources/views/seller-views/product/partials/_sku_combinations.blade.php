<style>
    #imagePreview {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding: 10px;
    }

    .image-container {
        position: relative;
        width: 100px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 2px solid #ccc;
        border-radius: 8px;
        cursor: grab;
    }

    #dropArea {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
        transition: background-color 0.3s;
    }

    #dropArea.dragover {
        background-color: #f0f0f0;
    }

    .image-container {
        display: inline-block;
        margin-right: 10px;
    }

    .image-preview {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: #1f1a1a;
        color: white;
        font-size: 16px;
        border-radius: 50%;
        padding: 5px;
        cursor: pointer;
        z-index: 10;
    }

    .image-container {
        position: relative;
        display: inline-block;
        margin: 10px;
    }

    .image-container img {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }

    .popup {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
    }

    .popup-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        width: 300px;
        text-align: center;
    }

    .popup-content h3 {
        margin-bottom: 20px;
    }

    .popup .close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 25px;
        cursor: pointer;
    }

    .table td,
    .table th {
        padding: .1rem;
        vertical-align: 10px;
        border-top: .0625rem solid rgba(231, 234, 243, .7);
    }


    label {
        display: inline-block;
    }
</style>

@php
    $commission = DB::table('sellers')
        ->where('id', auth('seller')->id())
        ->first();
    $sub_cat = DB::table('categories')->where('id', $sub_category)->first();

@endphp
{{-- @dd($combinations); --}}
@if (count($combinations[0]) > 0)
    <table class="table table-bordered physical_product_show">
        <thead>
            <tr>
                <td class="text-center">
                    <label for="" class="control-label">{{ \App\CPU\translate('Variant') }}</label>
                </td>
                <td class="text-center">
                    <label for="" class="control-label">{{ \App\CPU\translate('Variant SKU') }}</label>
                </td>
                <td class="text-center">
                    <label class="title-color">{{ \App\CPU\translate('Tax Included') }}</label>
                    <label class="badge badge-soft-info">{{ \App\CPU\translate('Percent') }} ( % )</label>
                </td>
                <td class="text-center">
                    <label for="" class="control-label">{{ \App\CPU\translate('Variant MRP') }}
                    </label>
                </td>



                <td class="text-center">
                    <label class="title-color">{{ \App\CPU\translate('discount_type') }}</label>

                </td>
                <td class="text-center">
                    <label class="title-color">{{ \App\CPU\translate('Discount') }}</label>

                </td>

                <td class="text-center">
                    <label for="" class="control-label">
                        {{ \App\CPU\translate('Listed price after discount') }}<br>

                        <!-- <small>(Listed price after discount)</small> -->
                    </label>

                </td>
                @if ($commission->commission_fee == 3)
                    <td class="text-center">
                        <label class="title-color">{{ \App\CPU\translate('Transfer Price') }}</label>
                    </td>
                @endif
                <td class="text-center">
                    <label class="title-color">{{ \App\CPU\translate('fee & Commission') }} in % </label>
                </td>
                <td class="text-center">
                    <label class="title-color">{{ \App\CPU\translate('total') }}
                        {{ \App\CPU\translate('Stock') }}</label>
                </td>
            </tr>
        </thead>
        <tbody>
@endif
@foreach ($combinations as $key => $combination)
    @php
        $sku = '';
        foreach (explode(' ', $product_name) as $value) {
            $sku .= substr($value, 0, 1);
        }

        $str = '';
        foreach ($combination as $index => $item) {
            if ($index > 0) {
                $str .= '-' . str_replace(' ', '', $item);
                $sku .= '-' . str_replace(' ', '', $item);
            } else {
                if ($colors_active == 1) {
                    $color = \App\Model\Color::where('code', $item)->first();
                    $color_name = $color->name ?? 'Unknown';
                    $str .= $color_name;
                    $sku .= '-' . $color_name;
                } else {
                    $str .= str_replace(' ', '', $item);
                    $sku .= '-' . str_replace(' ', '', $item);
                }
            }
        }
    @endphp
    {{-- @dd($str); --}}
    @if (strlen($str) > 0)
        <tr data-row-id="{{ $key }}">
            <td>
                <input type="hidden" name="sizes[]" value="{{ $str }}">
                <label class="control-label">{{ $str }}</label>
            </td>
            <td>
                <input type="text" name="skues[]" value="{{ $sku }}" class="form-control">
            </td>
            <td>
                <input type="number" min="0" step="0.01" placeholder="{{ \App\CPU\translate('Tax') }}"
                    name="taxes[]" value="{{ old('tax') }}" class="form-control tax" id="tax_{{ $key }}">
                <!-- <input name="tax_type" value="percent" class="d-none"> -->
            </td>
            <td>
                <!-- Variant Tax and GST Tax Fields with "+" Symbol -->
                <div class="d-flex align-items-center">
                    <input type="text" id="tax_gst_{{ $key }}" name="tax_gst[]"
                        class="form-control ms-2 text-center" style="width: 40%; height: 10%;" />
                    <span class="fw-bold"> plus </span> <!-- Plus Symbol -->
                    <input type="text" id="var_tax_{{ $key }}" name="var_tax[]"
                        class="form-control me-2 text-center" style="width: 40%; height:10%;" />

                </div>

                <!-- Variant MRP Field (Below) -->
                <input type="number" placeholder="Variant MRP" name="unit_prices[]"
                    id="unit_price_{{ $key }}" value="" class="form-control unit_price mt-2">
            </td>

            <td>
                <select class="form-control js-select2-custom" name="discount_types[]"
                    id="discount_type_{{ $key }}">
                    <option value="flat">{{ \App\CPU\translate('Flat') }}</option>
                    <option value="percent">{{ \App\CPU\translate('Percent') }}</option>
                </select>
            </td>
            <td>
                <input type="text" placeholder="{{ \App\CPU\translate('Discount') }}" name="discounts[]"
                    value="{{ old('discount') }}" id="discount_{{ $key }}" class="form-control discount">
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <input type="text" id="selling_tax_{{ $key }}" name="selling_taxs[]"
                        class="form-control me-2 text-center" style="width: 40%; height:10%;" />
                    <span class="fw-bold"> plus </span> <!-- Plus Symbol -->
                    <input type="text" id="tax1_gst_{{ $key }}" name="tax1_gst[]"
                        class="form-control me-2 text-center" style="width: 40%; height:10%;" />
                </div>
                <input type="text" id="selling_price_{{ $key }}" name="selling_prices[]"
                    class="form-control selling_price mt-2" value="" plcaeholder="Selling Price">
            </td>
            @if ($commission->commission_fee == 3)
                <td>
                    <input type="text" placeholder="{{ \App\CPU\translate('Transfer Price') }}" value=""
                        name="transfer_price[]" class="form-control" id="tp_{{ $key }}">
                </td>


                <td>
                    <input type="text" placeholder="{{ \App\CPU\translate('Commission Fee') }}" value=""
                        name="commission_fee[]" class="form-control commission_fee"
                        id="commission_fee_{{ $key }}" readonly>
                </td>
            @endif
            @if ($commission->commission_fee == 2)
                <td>
                    <input type="number" placeholder="{{ \App\CPU\translate('Commission Fee') }}"
                        value="{{ $commission->fee }}" name="commission_fee[]" class="form-control commission_fee"
                        id="commission_fee_{{ $key }}" readonly>
                </td>
            @endif
            @if ($commission->commission_fee == 1)
                <td>
                    <input type="number" placeholder="{{ \App\CPU\translate('Commission Fee') }}"
                        value="{{ $sub_cat->commission }}" name="commission_fee[]"
                        class="form-control commission_fee" id="commission_fee_{{ $key }}" readonly>
                </td>
            @endif
            <td>
                <input type="number" id="quant_{{ $key }}" name="quant[]" value=""
                    class="form-control quant">
            </td>
        </tr>


        <tr>
            <td colspan="5" style="text-align:center; padding-top: 20px;">Packaging dimensions* (in Cm) </td>
            <td>
                <label style="position: relative;
    left: 20px;">Length (in Cm)</label>

                <input type="text" name="lengths[]" value="" class="form-control">
            </td>
            <td>
                <label style="position: relative;
    left: 20px;">{{ \App\CPU\translate('Breadth') }} (in Cm)</label>
                <input type="text" name="breadths[]" value="" class="form-control">
            </td>
            <td>
                <label style="position: relative;
    left: 20px;">{{ \App\CPU\translate('Height') }} (in Cm)</label>
                <input type="text" name="heights[]" value="" class="form-control">
            </td>
            <td>
                <label style="position: relative;
    left: 20px;">{{ \App\CPU\translate('Weight') }} (in Kg)</label>
                <input type="text" name="weights[]" value="" class="form-control" required>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="text-align:center;">

                <p
                    style="background-color: {{ $color->code ?? '#ffffff' }}; width: 50px; height: 50px; margin: 0 auto; border-radius: 50%; margin-top: 10px;">
                </p>
                <input type="text" name="color_names[]" placeholder="write a color name"
                    style="margin-top: 10px; text-align:center;">
            </td>
            <td colspan="5">
                <div id="dropArea_{{ $key }}" class="drop-area" style="position:relative;">
                    <p style="position: relative; top: -66px;">
                        &nbsp;&nbsp; Upload Images* ( Ratio 1:1 )
                        <span class="ml-2" data-toggle="tooltip" data-placement="top"
                            title="{{ \App\CPU\translate('Checked image is thumbnail image') }}">
                            <img class="info-img" src="{{ asset('/public/assets/back-end/img/info-circle.svg') }}"
                                alt="img">
                        </span>
                        <input type="file" id="imageInput_{{ $key }}"
                            name="image_{{ $key }}[]" class="image-input" data-key="{{ $key }}"
                            multiple accept="image/*" style="position:relative; z-index:10;">
                    </p>
                </div>
                <input type="hidden" id="thumbnail_input_{{ $key }}"
                    name="thumbnail_image_{{ $key }}" required>

                <div id="imagePreview_{{ $key }}" class="image-preview" name="imagePreview"
                    style="display:flex; gap: 10px; flex-wrap: wrap;"></div>
            </td>

        </tr>

        <!-- Popup Structure -->
        <div id="popup_{{ $key }}" class="popup" style="display:none;">
            <div class="popup-content">
                <span class="close" onclick="closePopup({{ $key }})">&times;</span>
                <h3>Upload your image</h3>

            </div>
        </div>
    @endif
@endforeach

</tbody>
</table>

<script>
    $(document).ready(function() {
        $('.store_image_{{ $key }}').on('click', function(e) {
            e.preventDefault(); // Prevent default action

            // Fetch the key from button's data attribute
            let key = $(this).data('key');
            console.log("hello");
            // Send AJAX GET request to fetch all images
            $.ajax({
                url: '{{ route('seller.product.image_get') }}', // Replace with your endpoint to fetch images
                type: 'GET',
                // data: {
                //     key: key, // Send the key if required for filtering
                // },
                success: function(response) {
                    console.log('Images fetched:', response);

                    // Display images in popup
                    let imagesContainer = $('#uploadedImages_' + key);
                    imagesContainer.html(''); // Clear previous content
                    response.images.forEach(function(image) {
                        imagesContainer.append(
                            `<img src="${image.url}" alt="Uploaded Image">`);
                    });

                    // Open the popup
                    $('#popup_' + key).css('display', 'flex');
                },
                error: function(xhr, status, error) {
                    console.log('Error fetching images:', error);
                    alert('Failed to fetch images. Please try again.');
                }
            });
        });
    });

    function openPopup(event, key) {
        event.preventDefault();
        var popup = document.getElementById('popup_' + key);
        popup.style.display = 'flex';
    }

    function closePopup(key) {
        var popup = document.getElementById('popup_' + key);
        popup.style.display = 'none';
    }

    function storePopupImage(key) {
        var fileInput = document.getElementById('popupImageInput_' + key);
        var files = fileInput.files;

        if (files.length > 0) {
            // Here you can implement your logic for storing the image,
            // for example, uploading it to the server using AJAX
            alert('Image uploaded successfully!');
            closePopup(key); // Close the popup after image is stored
        } else {
            alert('Please select an image!');
        }
    }

    update_qty();

    function update_qty() {
        var total_qty = 0;
        var qty_elements = $('input[name^="qty_"]');
        for (var i = 0; i < qty_elements.length; i++) {
            total_qty += parseInt(qty_elements.eq(i).val());
        }
        if (qty_elements.length > 0) {

            $('input[name="current_stock"]').attr("readonly", true);
            $('input[name="current_stock"]').val(total_qty);
        } else {
            $('input[name="current_stock"]').attr("readonly", false);
        }
    }
    $('input[name^="qty_"]').on('keyup', function() {
        var total_qty = 0;
        var qty_elements = $('input[name^="qty_"]');
        for (var i = 0; i < qty_elements.length; i++) {
            total_qty += parseInt(qty_elements.eq(i).val());
        }
        $('input[name="current_stock"]').val(total_qty);
    });


    document.addEventListener('input', function(event) {
        if (event.target.matches('.unit_price, .quant, .discount, .discount_type, .tax')) {
            var row = event.target.closest('tr'); // Get the current row

            if (row) {
                var rowId = row.getAttribute('data-row-id'); // Get row ID dynamically

                // Get the input elements for the current row
                var unitPriceInput = row.querySelector(`#unit_price_${rowId}`);
                var quantityInput = row.querySelector(`#quant_${rowId}`);
                var discountInput = row.querySelector(`#discount_${rowId}`);
                var discountTypeInput = row.querySelector(`#discount_type_${rowId}`);
                var taxInput = row.querySelector(`#tax_${rowId}`);
                var sellingPriceInput = row.querySelector(`#selling_price_${rowId}`);
                var sellingTaxInput = row.querySelector(`#selling_tax_${rowId}`);
                var gstTaxInput = row.querySelector(`#tax1_gst_${rowId}`);
                var transferPriceInput = row.querySelector(`#tp_${rowId}`);
                var commissionFeeInput = row.querySelector(`#commission_fee_${rowId}`);
                var gstTaxInput1 = row.querySelector(`#tax_gst_${rowId}`);
                var varTaxInput = row.querySelector(`#var_tax_${rowId}`);

                // Get input values
                var unitPrice = parseFloat(unitPriceInput?.value) || 0;
                var quantity = parseFloat(quantityInput?.value) || 1;
                var discount = parseFloat(discountInput?.value) || 0;
                var discountType = discountTypeInput?.value || "none";
                var tax = parseFloat(taxInput?.value) || 0;

                // Calculate the selling price before tax
                var sellingPrice = unitPrice * 1;
                if (discountType === "percent") {
                    sellingPrice -= (sellingPrice * (discount / 100));
                } else if (discountType === "flat") {
                    sellingPrice -= discount;
                }
                sellingPrice = Math.max(sellingPrice, 0); // Ensure price is non-negative

                // First calculation - Variant Tax Calculation
                var taxMultiplier = (tax + 100) / 100;
                var selling = unitPrice / taxMultiplier;
                var gst = (selling * tax) / 100;

                // Second calculation - Selling Price Tax Calculation
                var sellingTax = sellingPrice / taxMultiplier;
                var gstTax = (sellingPrice * tax) / (tax + 100);

                // Update input fields with calculated values
                sellingPriceInput.value = sellingPrice.toFixed(2);
                sellingTaxInput.value = sellingTax.toFixed(2);
                gstTaxInput.value = gstTax.toFixed(2);
                varTaxInput.value = gst.toFixed(2);
                gstTaxInput1.value = selling.toFixed(2);

                // Handle transfer price and commission fee
                if (transferPriceInput) {
                    transferPriceInput.addEventListener('input', function() {
                        var transferPrice = parseFloat(transferPriceInput.value) || 0;
                        var commissionFee = transferPrice ? ((sellingTax - transferPrice) /
                            sellingTax) * 100 : 0;
                        commissionFeeInput.value = commissionFee.toFixed(2);
                    });
                }
            }
        }
    });

    document.querySelectorAll('[id^="imageInput_"]').forEach((input) => {
        const key = input.dataset.key;
        const dropArea = document.getElementById(`dropArea_${key}`);
        const imagePreview = document.getElementById(`imagePreview_${key}`);
        let draggedElement = null;

        // Handle file input change
        input.addEventListener('change', (e) => handleFiles(e, imagePreview));

        // Drag and drop events
        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('dragover');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('dragover');
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            handleFiles({
                target: {
                    files
                }
            }, imagePreview);
        });

        function handleFiles(event, previewContainer) {
            const files = event.target.files;

            Array.from(files).forEach((file) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = () => {
                        const imgContainer = document.createElement('div');
                        imgContainer.classList.add('image-container');

                        const img = document.createElement('img');
                        img.src = reader.result;
                        img.draggable = true;

                        // Hidden input for storing file reference
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `uploaded_images_${key}[]`;
                        hiddenInput.value = file.name; // Store filename

                        // Checkbox for thumbnail selection
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.classList.add('image-checkbox');
                        checkbox.name = `thumbnail_${key}`; // Unique name per input group

                        // Handle thumbnail selection
                        checkbox.addEventListener('change', () => {
                            const allCheckboxes = document.querySelectorAll(
                                `#imagePreview_${key} .image-checkbox`);

                            allCheckboxes.forEach((cb) => {
                                if (cb !== checkbox) {
                                    cb.checked = false;
                                    cb.disabled = checkbox
                                        .checked; // Disable others
                                }
                            });

                            // Ensure thumbnail input is updated
                            const thumbnailInput = document.getElementById(
                                `thumbnail_input_${key}`);
                            if (checkbox.checked) {
                                thumbnailInput.value = file
                                    .name; // Save selected file as thumbnail
                            } else {
                                thumbnailInput.value = ""; // Clear selection
                            }

                            // Enable all if no checkbox is checked
                            if (![...allCheckboxes].some(cb => cb.checked)) {
                                allCheckboxes.forEach(cb => cb.disabled = false);
                            }
                        });

                        // Remove button
                        const removeBtn = document.createElement('span');
                        removeBtn.classList.add('remove-btn');
                        removeBtn.textContent = '×';

                        // Remove image
                        removeBtn.addEventListener('click', () => {
                            imgContainer.remove();
                            updateImageOrder(key);
                        });

                        // Drag events
                        imgContainer.addEventListener('dragstart', (e) => {
                            draggedElement = imgContainer;
                            e.dataTransfer.effectAllowed = 'move';
                        });

                        imgContainer.addEventListener('dragover', (e) => {
                            e.preventDefault();
                        });

                        imgContainer.addEventListener('drop', (e) => {
                            e.preventDefault();
                            if (draggedElement && draggedElement !== imgContainer) {
                                const draggedIndex = Array.from(previewContainer.children)
                                    .indexOf(draggedElement);
                                const targetIndex = Array.from(previewContainer.children)
                                    .indexOf(imgContainer);

                                if (draggedIndex < targetIndex) {
                                    previewContainer.insertBefore(draggedElement,
                                        imgContainer.nextSibling);
                                } else {
                                    previewContainer.insertBefore(draggedElement,
                                        imgContainer);
                                }
                                updateImageOrder(key);
                            }
                            draggedElement = null;
                        });

                        // Append elements
                        imgContainer.appendChild(removeBtn);
                        imgContainer.appendChild(img);
                        imgContainer.appendChild(checkbox);
                        imgContainer.appendChild(hiddenInput);
                        previewContainer.appendChild(imgContainer);

                        updateImageOrder(key);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        function updateImageOrder(key) {
            const previewContainer = document.getElementById(`imagePreview_${key}`);
            const imageNames = [];

            previewContainer.querySelectorAll('input[type="hidden"][name^="uploaded_images_"]').forEach(
                input => {
                    imageNames.push(input.value);
                });

            // Hidden input to send image order
            let imageOrderInput = document.getElementById(`image_order_${key}`);
            if (!imageOrderInput) {
                imageOrderInput = document.createElement('input');
                imageOrderInput.type = 'hidden';
                imageOrderInput.id = `image_order_${key}`;
                imageOrderInput.name = `image_order_${key}`;
                previewContainer.appendChild(imageOrderInput);
            }

            imageOrderInput.value = JSON.stringify(imageNames);
        }
    });
</script>
