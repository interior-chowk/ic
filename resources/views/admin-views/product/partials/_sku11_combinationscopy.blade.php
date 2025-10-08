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
    display: inline-block; /* Make each image-container an inline block */
    margin-right: 10px; /* Add some spacing between images */
}

.image-preview {
    display: flex;
    flex-wrap: wrap; /* This will wrap images to the next line if space runs out */
    justify-content: flex-start; /* Align images to the left */
}

.remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
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


/* Popup styling */
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

.table td, .table th {
    padding: .1rem;
     vertical-align: 10px;
    border-top: .0625rem solid rgba(231, 234, 243, .7);
}


label {
    display: inline-block;
    /* margin-bottom: .5rem; */
}
    </style>

@php
   $commission = DB::table('sellers')->where('id', auth('seller')->id())->first();
@endphp
@if(count($combinations[0]) > 0)
	<table class="table table-bordered physical_product_show">
		<thead>
			<tr>
				<td class="text-center">
					<label for="" class="control-label">{{\App\CPU\translate('Variant')}}</label>
				</td>
				<td class="text-center">
					<label for="" class="control-label">{{\App\CPU\translate('Variant SKU')}}</label>
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
                @if($commission->commission_fee == 3)
                <td class="text-center">
				<label class="title-color">{{ \App\CPU\translate('Transfer Price') }}</label>
				</td>
                @endif
                <td class="text-center">
				<label class="title-color">{{ \App\CPU\translate('Commission fee') }}</label>
				</td>
				<td class="text-center">
				<label class="title-color">{{ \App\CPU\translate('total') }}
				{{ \App\CPU\translate('Stock') }}</label>
				</td>
				<!-- <td class="text-center">
					<label for="" class="control-label">{{\App\CPU\translate('Height')}}   (in cms)</label>
				</td>
				<td class="text-center">
					<label for="" class="control-label">{{\App\CPU\translate('Width')}}   (in cms)</label>
				</td> -->
			</tr>
		</thead>
		<tbody>
@endif
@php
$existingDatas = DB::table('products')
    ->where('id', $product_id)
    ->first(); // Only fetch the first record
    if($existingDatas){
        $variation = json_decode($existingDatas->variation,true);
        $types = array_column($variation, 'type');
    }

if ($existingDatas) {
    $choice_options = json_decode($existingDatas->choice_options, true); // JSON decode karna zaroori hai
    
    // Options ki value nikalna
    if (isset($choice_options[0]['options'])) {
        $options = $choice_options[0]['options']; // Options ko extract karna
    }
} else {
    echo "Product not found!";
}



$exist = []; // Initialize empty array for storing results

// Loop through options and fetch corresponding data
foreach ($types as $opt) {
    $cleanOpt = str_replace(' ', '', $opt);

    $exist[] = DB::table('sku_product_new')
        ->where('product_id', $product_id)
        ->where('variation', $opt) // $opt ko directly use kar rahe hain, no need for '->size'
        ->first(); // Only fetch the first record
}

@endphp

@foreach ($exist as $key => $data)

      @php
        $images = json_decode($data->image, true); // JSON ko array me convert kar rahe hain
    @endphp

 

<tr data-row-id="{{ $key }}">
    <td>
        <input type="hidden" name="sizes[]" value="{{$data->sizes}}">
        <label class="control-label"></label>
    </td>
    <td>
        <input type="text" name="skues[]" value="{{$data->sku}}" class="form-control">
    </td>
    <td>
        <input type="text" min="0" step="0.01" placeholder="Tax" name="taxes[]" value="{{$data->tax}}" class="form-control tax" id="tax_{{ $key }}">
        
    </td>
    <td>
        <div class="d-flex align-items-center">
            <input type="text" id="tax_gst_{{$key}}" name="tax_gst[]"  class="form-control ms-2 text-center" style="width: 40%; height: 10%;" value="{{$data->gst_percent}}" />
            <span class="fw-bold"> plus </span>
            <input type="text" id="var_tax_{{ $key }}" name="var_tax[]"  class="form-control me-2 text-center" style="width: 40%; height:10%;" value="{{$data->discount_percent}}" />
        </div>
        <input type="number" placeholder="Variant MRP" name="unit_prices[]" id="unit_price_{{ $key }}" value="{{$data->variant_mrp}}" class="form-control unit_price mt-2">
    </td>
  
    <td>
    <select class="form-control js-select2-custom discount_type" name="discount_types[]" id="discount_type_{{ $key }}">
        <option value="percent" {{ $data->discount_type == "percent" ? 'selected' : '' }}>Percent</option>
        <option value="flat" {{ $data->discount_type == "flat" ? 'selected' : '' }}>Flat</option>
    </select>
</td>

    <td>
        <input type="text" placeholder="Discount" name="discounts[]" value="{{$data->discount}}" id="discount_{{ $key }}" class="form-control discount">
    </td>
    <td>
        <div class="d-flex align-items-center">
            <input type="text" id="selling_tax_{{ $key }}" name="selling_taxs[]"  class="form-control me-2 text-center" style="width: 40%; height:10%;" value="{{$data->listed_percent}}" />
            <span class="fw-bold"> plus </span>
            <input type="text" id="tax1_gst_{{ $key }}" name="tax1_gst[]"  class="form-control me-2 text-center" style="width: 40%; height:10%;" value="{{$data->listed_gst_percent}}" />
        </div>
        <input type="text" id="selling_price_{{ $key }}" name="selling_prices[]" class="form-control selling_price mt-2" value="{{$data->listed_price}}" placeholder="Selling Price">
    </td>
    @if($data->transfer_price !== null)
    <td>
        <input type="number" name="transfer_price[]" value="{{ $data->transfer_price }}" id="transfer_price_{{ $key }}" class="form-control quant">
    </td>
@endif

    <td>
        <input type="number"  name="commission_fee[]" value="{{$data->commission_fee}}" id="commission_fee_{{ $key }}" class="form-control quant">
    </td>
    <td>
        <input type="number" id="quant_{{ $key }}" name="quant[]" value="{{$data->quantity}}" class="form-control quant">
    </td>
</tr>

<tr>
    <td colspan="5" style="text-align:center; padding-top: 20px;">Packaging dimensions* (in Cm)</td>
    <td>
        <label style="position: relative; left: 20px;">Length (in Cm)</label>
        <input type="text" name="lengths[]" value="{{$data->length}}" class="form-control">
    </td>
    <td>
        <label style="position: relative; left: 20px;">Breadth (in Cm)</label>
        <input type="text" name="breadths[]" value="{{$data->breadth}}" class="form-control">
    </td>
    <td>
        <label style="position: relative; left: 20px;">Height (in Cm)</label>
        <input type="text" name="heights[]" value="{{$data->height}}" class="form-control">
    </td>
    <td>
        <label style="position: relative; left: 20px;">Weight (in Kg)</label>
        <input type="text" name="weights[]" value="{{$data->weight}}" class="form-control">
    </td>
</tr>

<tr>
    <td colspan="2" style="text-align:center;">
        <p style="background-color: #ffffff; width: 50px; height: 50px; margin: 0 auto; border-radius: 50%; margin-top: 10px;"></p>
        <input type="text" name="color_names[]" placeholder="Write a color name" style="margin-top: 10px; text-align:center;" value="{{ $data->color_name }}">
    </td>
    <td colspan="5">
        <div id="dropArea_{{ $key }}" class="drop-area" style="border: 2px dashed #ccc; padding: 10px;">
            <p style="position: relative; top: 0;">
                &nbsp;&nbsp; Upload Images* ( Ratio 1:1 )
                <span class="ml-2" data-toggle="tooltip" data-placement="top" title="Checked image is thumbnail image">
                    <img class="info-img" src="/public/assets/back-end/img/info-circle.svg" alt="img">
                </span>
                <input type="file" id="imageInput_{{ $key }}" name="image_{{ $key }}[]" class="image-input" data-key="{{ $key }}" multiple accept="image/*" style="margin-top:10px;">
            </p>
        </div>

        <input type="hidden" id="thumbnail_input_{{ $key }}" name="thumbnail_image_{{ $key }}" value="{{ $data->thumbnail_image }}">
        <input type="hidden" id="image_order_{{ $key }}" name="image_order_{{ $key }}" value="">

        <div id="imagePreview_{{ $key }}" class="image-preview" style="margin-top:10px;">
            @if(is_array($images))
                @foreach ($images as $img)
                    <div class="image-container" style="display:inline-block; position:relative; margin:5px;" draggable="true">
                        <input type="hidden" name="old_image_{{ $key }}[]" value="{{ $img }}">
                        <img src="{{ asset('/storage/images/'.$img) }}" alt="Product Image" width="100" style="display:block;">
                        <input type="checkbox" class="image-checkbox" name="thumbnail_{{ $key }}" value="{{ $img }}"
                            @if($data->thumbnail_image == $img) checked @endif
                            style="position:absolute; top:5px; right:5px;">
                        <span class="remove-btn" style="position:absolute; top:5px; left:5px; cursor:pointer; font-weight:bold;">×</span>
                    </div>
                @endforeach
            @endif
        </div>
    </td>
</tr>

<!-- Popup Structure -->
<div id="popup_{{$key}}" class="popup" style="display:none;">
    <div class="popup-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <h3>Upload your image</h3>
    </div>
</div>
@endforeach



	</tbody>
</table>

<script>



document.addEventListener('input', function (event) {
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
                transferPriceInput.addEventListener('input', function () {
                    var transferPrice = parseFloat(transferPriceInput.value) || 0;
                    var commissionFee = transferPrice ? ((sellingTax - transferPrice) / sellingTax) * 100 : 0;
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
    const thumbnailInput = document.getElementById(`thumbnail_input_${key}`);
    const imageOrderInput = document.getElementById(`image_order_${key}`);

    let draggedElement = null;

    // Handle file input change
    input.addEventListener('change', (e) => handleFiles(e, imagePreview));

    // Drag and drop events on dropArea for file drop
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
        handleFiles({ target: { files } }, imagePreview);
    });

    // Make imagePreview sortable with drag and drop
    imagePreview.addEventListener('dragstart', (e) => {
        draggedElement = e.target.closest('.image-container');
        e.dataTransfer.effectAllowed = 'move';
    });

    imagePreview.addEventListener('dragover', (e) => {
        e.preventDefault();
        const target = e.target.closest('.image-container');
        if (!target || target === draggedElement) return;

        const bounding = target.getBoundingClientRect();
        const offset = bounding.y + bounding.height / 2;

        if (e.clientY - offset > 0) {
            target.after(draggedElement);
        } else {
            target.before(draggedElement);
        }
    });

    imagePreview.addEventListener('drop', (e) => {
        e.preventDefault();
        draggedElement = null;
        updateImageOrder();
    });

    // Handle new files to preview
    function handleFiles(event, previewContainer) {
        const files = event.target.files;

        Array.from(files).forEach((file) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = () => {
                    const imgContainer = document.createElement('div');
                    imgContainer.classList.add('image-container');
                    imgContainer.style = "display:inline-block; position:relative; margin:5px;";
                    imgContainer.setAttribute('draggable', true);

                    const img = document.createElement('img');
                    img.src = reader.result;
                    img.width = 100;
                    img.style = "display:block;";

                    // Hidden input for uploaded file name
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `uploaded_images_${key}[]`;
                    hiddenInput.value = file.name;

                    // Checkbox for thumbnail selection
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.classList.add('image-checkbox');
                    checkbox.name = `thumbnail_${key}`;
                    checkbox.value = file.name;
                    checkbox.style = "position:absolute; top:5px; right:5px;";

                    // Thumbnail checkbox logic
                    checkbox.addEventListener('change', () => {
                        const allCheckboxes = imagePreview.querySelectorAll('.image-checkbox');
                        allCheckboxes.forEach(cb => {
                            if (cb !== checkbox) {
                                cb.checked = false;
                                cb.disabled = checkbox.checked;
                            }
                        });

                        if (checkbox.checked) {
                            thumbnailInput.value = checkbox.value;
                        } else {
                            thumbnailInput.value = '';
                        }

                        if (![...allCheckboxes].some(cb => cb.checked)) {
                            allCheckboxes.forEach(cb => cb.disabled = false);
                        }
                    });

                    // Remove button
                    const removeBtn = document.createElement('span');
                    removeBtn.textContent = '×';
                    removeBtn.style = "position:absolute; top:5px; left:5px; cursor:pointer; font-weight:bold;";
                    removeBtn.classList.add('remove-btn');
                    removeBtn.addEventListener('click', () => {
                        imgContainer.remove();
                        updateImageOrder();
                    });

                    imgContainer.appendChild(removeBtn);
                    imgContainer.appendChild(img);
                    imgContainer.appendChild(checkbox);
                    imgContainer.appendChild(hiddenInput);
                    previewContainer.appendChild(imgContainer);

                    updateImageOrder();
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Update hidden input with current order of images (filenames or old image names)
    function updateImageOrder() {
        const imgs = imagePreview.querySelectorAll('.image-container');
        let order = [];

        imgs.forEach(container => {
            const checkbox = container.querySelector('input.image-checkbox');
            if (checkbox) order.push(checkbox.value);
        });

        imageOrderInput.value = JSON.stringify(order);
    }

    // Remove old images
    imagePreview.querySelectorAll('.remove-btn').forEach(removeBtn => {
        removeBtn.addEventListener('click', () => {
            const container = removeBtn.closest('.image-container');
            container.remove();
            updateImageOrder();
        });
    });

    // Initial call to update order on page load
    updateImageOrder();
});


</script>