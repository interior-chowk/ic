AOS.init();
$(window).on('load', function () {

  $('.c-loder-w ').hide();

});
$(document).on('click', '.c-password-show', function () {

  $(this).toggleClass("active");

  var input = $("#pass_log_id");

  input.attr('type') === 'password' ? input.attr('type', 'text') : input.attr('type', 'password')

});

$(document).on('click', '.c-password-show-2', function () {

  $(this).toggleClass("active");

  var input = $(".pass_log_id_new");

  input.attr('type') === 'password' ? input.attr('type', 'text') : input.attr('type', 'password')

});

$(document).on('click', '.c-password-show-3', function () {

  $(this).toggleClass("active");

  var input = $(".pass_log_id_new-2");

  input.attr('type') === 'password' ? input.attr('type', 'text') : input.attr('type', 'password')

});

$(document).ready(function () {

  $(".c-drop-down-btn").click(function () {

    $(".c-drop-down-list").slideToggle("slow");

  });

});

var x, i, j, l, ll, selElmnt, a, b, c;

x = document.getElementsByClassName("custom-select");

l = x.length;

for (i = 0; i < l; i++) {

  selElmnt = x[i].getElementsByTagName("select")[0];

  ll = selElmnt.length;

  a = document.createElement("DIV");

  a.setAttribute("class", "select-selected");

  a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;

  x[i].appendChild(a);

  b = document.createElement("DIV");

  b.setAttribute("class", "select-items select-hide");

  for (j = 1; j < ll; j++) {

    c = document.createElement("DIV");

    c.innerHTML = selElmnt.options[j].innerHTML;

    c.addEventListener("click", function (e) {

      var y, i, k, s, h, sl, yl;

      s = this.parentNode.parentNode.getElementsByTagName("select")[0];

      sl = s.length;

      h = this.parentNode.previousSibling;

      for (i = 0; i < sl; i++) {

        if (s.options[i].innerHTML == this.innerHTML) {

          s.selectedIndex = i;

          h.innerHTML = this.innerHTML;

          y = this.parentNode.getElementsByClassName("same-as-selected");

          yl = y.length;

          for (k = 0; k < yl; k++) {

            y[k].removeAttribute("class");

          }

          this.setAttribute("class", "same-as-selected");

          break;

        }

      }

      h.click();

    });

    b.appendChild(c);

  }

  x[i].appendChild(b);

  a.addEventListener("click", function (e) {

    e.stopPropagation();

    closeAllSelect(this);

    this.nextSibling.classList.toggle("select-hide");

    this.classList.toggle("select-arrow-active");

  });

}

function closeAllSelect(elmnt) {

  var x, y, i, xl, yl, arrNo = [];

  x = document.getElementsByClassName("select-items");

  y = document.getElementsByClassName("select-selected");

  xl = x.length;

  yl = y.length;

  for (i = 0; i < yl; i++) {

    if (elmnt == y[i]) {

      arrNo.push(i)

    } else {

      y[i].classList.remove("select-arrow-active");

    }

  }

  for (i = 0; i < xl; i++) {

    if (arrNo.indexOf(i)) {

      x[i].classList.add("select-hide");

    }

  }

}
document.addEventListener("click", closeAllSelect);

// On load: if we’ve stored a pincode, show that instead of 000999
document.addEventListener('DOMContentLoaded', () => {
  const saved = localStorage.getItem('pincode');
  if (saved && /^\d{6}$/.test(saved)) {
    updateDisplay(saved);
  }
});

// Utility: change the span’s innerHTML
function updateDisplay(pin) {
  document.getElementById('pincodeDisplay').innerHTML =
    `${pin} <i class="fa fa-chevron-down" aria-hidden="true"></i>`;
}

// Called when user clicks “Apply”
function applyPincode() {
  const pin = document.getElementById('pincodeInput').value.trim();
  if (/^\d{6}$/.test(pin)) {
    localStorage.setItem('pincode', pin);
    updateDisplay(pin);
    $('#pincodeModal').modal('hide');
    location.reload();
  } else {
    alert('Please enter a valid 6‑digit pincode.');
  }
}

// Called when user clicks “Locate”
function getLocation() {
  if (!navigator.geolocation) {
    return alert("Geolocation is not supported by this browser.");
  }

  navigator.geolocation.getCurrentPosition(
    pos => {
      const {
        latitude: lat,
        longitude: lng
      } = pos.coords;
      const apiKey = 'AIzaSyBjSaMOYIsNMmMcqZI6iyd9bjREm0oBhjY';
      const url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${apiKey}`;

      fetch(url)
        .then(r => r.json())
        .then(data => {
          const comps = data.results.flatMap(r => r.address_components);
          const pc = comps.find(c => c.types.includes('postal_code'));

          if (pc && /^\d{6}$/.test(pc.long_name)) {
            localStorage.setItem('pincode', pc.long_name);
            updateDisplay(pc.long_name);
            $('#pincodeModal').modal('hide');
            location.reload();
          } else {
            alert('Could not detect a valid 6‑digit pincode from your location.');
          }
        })
        .catch(() => alert('Failed to fetch location info.'));
    },
    () => alert('Location access denied.')
  );
}

$(document).on('click', '.delete-cart-items', function () {
  let cartId = $(this).data('cart-id');

  if (!confirm("Are you sure you want to remove this item from the cart?")) return;

  $.ajax({
    url: "{{ route('cart.remove_1') }}", // Your delete cart route
    type: "POST",
    data: {
      _token: '{{ csrf_token() }}',
      cart_id: cartId
    },
    success: function (response) {
      alert(response.message);
      location.reload();
    },
    error: function () {
      alert("Something went wrong while removing the item.");
    }
  });
});


// ✅ OTP Auto Tab Input
$(document).on('keyup', '.otp-input', function () {
  let index = parseInt($(this).data('index'));
  if ($(this).val().length === 1 && index < 3) {
    $('.otp-input').eq(index + 1).focus();
  }
});

// ✅ Verify OTP Submit


// OTP Section Slide Logic
document.getElementById("sendOtpBtn").addEventListener("click", function () {
  const login = document.getElementById("loginSection");
  const otp = document.getElementById("otpSection");

  login.classList.remove("active");
  login.classList.add("out-left");

  otp.classList.add("active");

  // Dot change
  document.querySelectorAll(".dot-nav").forEach(dot => dot.style.background = "#aaa");
  document.querySelector(".dot-nav[data-target='otpSection']").style.background = "#000";
});

// Dot Navigation Logic
document.querySelectorAll(".dot-nav").forEach(dot => {
  dot.addEventListener("click", function () {
    const targetId = this.getAttribute("data-target");
    const allSections = document.querySelectorAll(".slide-section");

    allSections.forEach(section => {
      section.classList.remove("active", "out-left");
    });

    document.getElementById(targetId).classList.add("active");

    // Dot indicator
    document.querySelectorAll(".dot-nav").forEach(d => d.style.background = "#aaa");
    this.style.background = "#000";
  });
});

// OTP Navigation Logic
document.addEventListener("DOMContentLoaded", function () {
  const otpInputs = document.querySelectorAll('.otp-input');

  otpInputs.forEach((input, index) => {
    input.addEventListener('input', function () {
      if (this.value.length === 1 && index < otpInputs.length - 1) {
        otpInputs[index + 1].focus();
      }
    });

    input.addEventListener('keydown', function (e) {
      if (e.key === 'Backspace' && this.value === '' && index > 0) {
        otpInputs[index - 1].focus();
      } else if (e.key === 'ArrowLeft' && index > 0) {
        otpInputs[index - 1].focus();
      } else if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
        otpInputs[index + 1].focus();
      }
    });
  });
});




const products = ['chair', 'table', 'lamp', 'bedsheet', 'sofa', 'desk'];
let currentIndex = 0;
const productElement = document.getElementById('rotating-product');

function updateProduct() {
  productElement.classList.add('slide-out-up');

  productElement.addEventListener('animationend', () => {
    productElement.textContent = products[currentIndex];
    productElement.classList.remove('slide-out-up');
    productElement.classList.add('slide-in-up');

    productElement.addEventListener('animationend', () => {
      productElement.classList.remove('slide-in-up');
    }, {
      once: true
    });

    currentIndex = (currentIndex + 1) % products.length;
  }, {
    once: true
  });
}

// Initial update after first load
setTimeout(() => {
  updateProduct();
  // Set up regular updates every 2 seconds
  setInterval(updateProduct, 1500);
}, 1500);


$(document).ready(function () {

  // --- Input Restrictions ---

  // Letters only for name/city
  $('input[name="full_name"], input[name="city"]').on('input', function () {
    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
  });

  // Digits only for phone, max 10 digits
  $('input[name="phone"]').on('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
  });

  // --- jQuery Validation ---
  $.validator.addMethod("lettersOnly", function (value, element) {
    return this.optional(element) || /^[A-Za-z\s]+$/.test(value);
  }, "Only letters and spaces allowed.");

  $.validator.addMethod("validPhone", function (value, element) {
    return this.optional(element) || /^[6-9]\d{9}$/.test(value);
  }, "Enter a valid 10-digit Indian phone number.");

  $.validator.addMethod("validEmail", function (value, element) {
    return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/.test(
      value);
  }, "Enter a valid email address.");

  $.validator.addMethod("experienceFormat", function (value, element) {
    return this.optional(element) || /^\d{1,2}-\d{1,2}$/.test(value);
  }, "Format: years-months (e.g. 2-6).");

  $.validator.addMethod("filesize", function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param);
  }, "File must not exceed 100KB.");

  // Apply validation to each career form
  $('.careerForm').each(function () {
    $(this).validate({
      rules: {
        full_name: {
          required: true,
          minlength: 3,
          maxlength: 50,
          lettersOnly: true
        },
        city: {
          required: true,
          minlength: 2,
          maxlength: 50,
          lettersOnly: true
        },
        phone: {
          required: true,
          validPhone: true
        },
        email: {
          required: true,
          validEmail: true
        },
        experience: {
          experienceFormat: true
        },
        resume: {
          required: true,
          extension: "jpg|jpeg|png|pdf|doc|docx|webp",
          filesize: 100000
        }
      },
      errorElement: 'small',
      errorClass: 'text-danger',
      errorPlacement: function (error, element) {
        error.insertAfter(element);
      },
      highlight: function (element) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function (element) {
        $(element).removeClass('is-invalid');
      },
      submitHandler: function (form) {
        form.submit();
      }
    });
  });

});
