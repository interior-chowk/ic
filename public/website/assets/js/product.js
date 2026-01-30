 document.addEventListener('DOMContentLoaded', () => {
    const productId = document.getElementById('product_id')?.value;
    if (productId) {
        console.log("Current Product ID:", productId);
    }

    const activeItem = document.querySelector('.breadcrumb-item.active');
    if (activeItem) {
        activeItem.style.fontStyle = 'italic';
    }
});

document.addEventListener('DOMContentLoaded', function () {
    // Share popup toggle
    window.toggleSharePopup = function (event) {
        event.stopPropagation();
        const popup = document.getElementById('sharePopup');
        popup.style.display = popup.style.display === 'flex' ? 'none' : 'flex';
    };

    // Copy share link
    window.copyLink = function () {
        const dummy = document.createElement('input');
        dummy.value = window.location.href;
        document.body.appendChild(dummy);
        dummy.select();
        document.execCommand('copy');
        document.body.removeChild(dummy);
        alert('Link copied to clipboard!');
    };

    document.addEventListener('click', function (e) {
        const popup = document.getElementById('sharePopup');
        const isClickInside = e.target.closest('.share-button') || e.target.closest('#sharePopup');
        if (!isClickInside) {
            popup.style.display = 'none';
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const productId = document.getElementById('id_s')?.value;
    if (productId) {
        console.log("Viewing Product ID:", productId);
    }
});

$(document).ready(function () {
    $("#cart").on("click", function () {
        const product_id = $('#id_s').val();
        const variant = $('input[name="selected_size"]').val();

        $.ajax({
            url: "{{ route('cart.add_1') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                product: product_id,
                variant: variant
            },
            success: function (response) {
                alert(response.message);
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    alert("You must be logged in to add to cart.");
                } else {
                    alert("Something went wrong.");
                }
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    // Example values, replace with AJAX or backend-rendered data as needed
    const listedPrice = 999;
    const mrp = 1299;
    const discount = mrp > listedPrice ? Math.round(((mrp - listedPrice) / mrp) * 100) : 0;

    // Set Listed Price
    const priceEl = document.getElementById('listed-price');
    if (priceEl) {
        priceEl.textContent = `₹ ${listedPrice.toLocaleString()}`;
    }

    // Set MRP and Discount
    const discountEl = document.getElementById('mrp-discount');
    if (discountEl && discount > 0) {
        discountEl.innerHTML = `
            <span style="text-decoration: line-through;">₹ ${mrp.toLocaleString()}</span>
            <span style="color: green; font-weight: 500;"> &nbsp;(${discount}% OFF)</span>
        `;
    }
});

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('btn')?.addEventListener('click', () => {
        alert("Proceeding to buy now...");
    });

    document.getElementById('cart')?.addEventListener('click', () => {
        alert("Item added to cart!");
    });
});

function initThumbnailClick() {
    document.querySelectorAll('.product-gallery-item').forEach(function (thumb) {
        thumb.addEventListener('mouseenter', function (e) {
            e.preventDefault();
            const imgSrc = this.dataset.image;
            const zoomImg = this.dataset['zoom-image'];
            const mainImg = document.getElementById('product-zoom');
            mainImg.src = imgSrc;
            mainImg.setAttribute('data-zoom-image', zoomImg);
            document.querySelectorAll('.product-gallery-item').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

$(document).ready(function () {
    $("#btn-add-to-wishlist").on("click", function (e) {
        e.preventDefault();
        const slug = $(this).data('id');
        const $icon = $(this).find('i');
        $.ajax({
            url: "{{ route('store-wishlist-1') }}", // Your correct route
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                slug: slug
            },
            success: function (response) {
                if (response.success) {
                    // Change icon to filled heart and make it red
                    $icon.removeClass('fa-heart-o').addClass('fa-heart').css('color', 'red');
                    alert(response.message);
                } else {
                    alert(response.message || "Already in wishlist.");
                }
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    alert("Please login to add to wishlist.");
                } else {
                    alert("Something went wrong.");
                }
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const colorItems = document.querySelectorAll(".color-item");
    const selectedColor = document.getElementById("colors");

    colorItems.forEach(item => {
        item.addEventListener("click", function () {
            // Remove active class from all
            colorItems.forEach(ci => ci.classList.remove("active"));

            // Add active class to clicked
            this.classList.add("active");

            // Set selected color text
            const color = this.getAttribute("data-color");
            selectedColor.textContent = color;
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const instantDeliveryEl = document.getElementById('instantDelivery');
    if (!instantDeliveryEl) return;

    const warehousePin = instantDeliveryEl.getAttribute('data-warehouse-pincode');
    const userPin = localStorage.getItem('pincode');

    if (userPin && /^\d{6}$/.test(userPin) && userPin === warehousePin) {
        instantDeliveryEl.classList.remove('d-none');
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const instantEl = document.getElementById('instantDelivery');
    if (!instantEl) return;

    const warehousePin = instantEl.dataset.warehousePincode;
    const userPin = localStorage.getItem('pincode');

    if (userPin && /^\d{6}$/.test(userPin) && userPin === warehousePin) {
        instantEl.classList.remove('d-none');
    }
});

document.addEventListener("DOMContentLoaded", function () {
    $('#related-products-carousel').owlCarousel({
        nav: false,
        dots: true,
        margin: 20,
        loop: false,
        responsive: {
            0:   { items: 1 },
            480: { items: 2 },
            768: { items: 3 },
            992: { items: 4 },
            1200:{ items: 6, nav: true, dots: false }
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    $('#service-provider-carousel').owlCarousel({
        nav: false,
        dots: true,
        margin: 20,
        loop: false,
        responsive: {
            0: { items: 1 },
            480: { items: 2 },
            768: { items: 3 },
            992: { items: 4 },
            1200: {
                items: 5,
                nav: true,
                dots: false
            }
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const title = document.querySelector('.video-banner-title');
    title.style.opacity = 0;
    setTimeout(() => {
        title.style.transition = 'opacity 1s ease-in-out';
        title.style.opacity = 1;
    }, 300);
});

document.addEventListener('DOMContentLoaded', function () {
    const colorItems = document.querySelectorAll('.color-item');
    colorItems.forEach(function (item) {
        item.addEventListener('click', function () {
            document.querySelectorAll('.color-item').forEach(el => el.classList.remove('selected'));
            this.classList.add('selected');
            const selectedColor = this.getAttribute('data-color');
            const id = document.getElementById('id_s').value;
            fetch(`{{ route('variation') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    color: selectedColor,
                    id: id
                })
            })
                .then(res => res.json())
                .then(response => {
                    let html = '';
                    let colorNames = new Set();
                    let colorToId = {};
                    response.variant.forEach(item => {
                        const color = item.color_name;
                        colorNames.add(color);
                        if (!colorToId[color]) {
                            colorToId[color] = item.id;
                        }
                        html += `
                        <div class="col-md-2">
                            <div class="card variant-box"
                                 data-listed_price="${item.listed_price}"
                                 data-variant_mrp="${item.variant_mrp}"
                                 data-discount_type="${item.discount_type || ''}"
                                 data-discount="${item.discount || 0}"
                                 data-sizes="${item.sizes}"
                                 data-variation="${item.variation}" 
                                   data-thumbnail="${item.thumbnail_image}"
                                      data-images='${JSON.stringify(item.image)}'>
                                <div class="card-header text-center p-1 font-weight-bold mb-0" style="background-color: #FFECE2;">
                                    ${item.sizes}
                                </div>
                                <div class="card-body p-2">
                                    <p class="card-text">₹ ${Number(item.listed_price).toLocaleString()}</p>
                                    ${item.discount != 0
                                ? `<p><span class="price-cut ml-0">₹ ${Number(item.variant_mrp).toLocaleString()}</span></p>`
                                : ''
                            }
                                </div>
                            </div>
                        </div>
                    `;
                    });
                    document.getElementById('colors').innerText = Array.from(colorNames).join(', ');
                    const uniqueIds = Object.values(colorToId).join(',');
                    const wishlistBtn = document.getElementById('btn-add-to-wishlist');
                    wishlistBtn.setAttribute('data-id', uniqueIds);
                    document.getElementById('variant-list').innerHTML = html;
                    document.querySelectorAll('.variant-box').forEach(function (box) {
                        box.addEventListener('click', function () {
                            document.querySelectorAll('.variant-box').forEach(el => el.classList.remove('selected'));
                            this.classList.add('selected');
                            const listedPrice = this.dataset.listed_price;
                            const variantMrp = this.dataset.variant_mrp;
                            const discountType = this.dataset.discount_type;
                            const discount = this.dataset.discount;
                            const sizes = this.dataset.sizes;
                            const vari_ant = this.dataset.variation;
                            thumbnail = this.dataset.thumbnail;
                            images = JSON.parse(this.dataset.images || '[]');
                            basePath = "{{ asset('storage/app/public/images/') }}/";
                            mainImg = document.getElementById('product-zoom');
                            mainImg.src = basePath + thumbnail;
                            mainImg.setAttribute('data-zoom-image', basePath + thumbnail);
                            images = JSON.parse(images);
                            gallery = document.getElementById('product-zoom-gallery');
                            galleryHTML = '';
                            images.forEach((img, index) => {
                                galleryHTML += `
                                                                <a class="product-gallery-item ${index === 0 ? 'active' : ''}" href="#"
                                                                data-image="${basePath + img}"
                                                                data-zoom-image="${basePath + img}">
                                                                    <img src="${basePath + img}" alt="Product side" style="width: 100px; height: 100px;">
                                                                </a>
                                                            `;
                            });

                            gallery.innerHTML = galleryHTML;
                            initThumbnailClick();
                            document.querySelector('.product-price').innerHTML = `₹ ${Number(listedPrice).toLocaleString()}`;

                            let discountHTML = '';
                            if (discountType === 'percent' && discount > 0) {
                                discountHTML = `<span class="badge badge-danger" style="background-color: #E26526;">
                                ${Math.round(discount)}% off
                            </span>`;
                            } else if (discountType === 'flat' && discount > 0) {
                                discountHTML = `<span class="badge badge-danger" style="background-color: #E26526;">
                                ₹${Number(discount).toLocaleString()} off
                            </span>`;
                            }
                            if (discount > 0) {
                                document.querySelector('.product-price.mt-1.mb-1').innerHTML = `
                            ${discountHTML}
                            <span class="price-cut">₹ ${Number(variantMrp).toLocaleString()}</span>
                        `;
                            }
                            document.querySelector('.size').innerHTML = `
                            <input type="hidden" name="selected_size" value="${vari_ant}">
                        `;
                        });
                    });
                    const firstVariant = document.querySelector('.variant-box');
                    if (firstVariant) {
                        firstVariant.click();
                    }
                })
                .catch(err => console.error("AJAX error:", err));
        });
    });
    if (colorItems.length > 0) {
        colorItems[0].click();
    }
});