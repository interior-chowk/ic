(function () {
  "use strict";

  /* ===================== DOM READY ===================== */
  document.addEventListener("DOMContentLoaded", function () {

    /* ---------- AOS SAFE INIT ---------- */
    if (typeof AOS !== "undefined") {
      AOS.init({
        once: true,
        duration: 800,
        offset: 120,
        easing: "ease-in-out"
      });
    }

    /* ---------- Loader ---------- */
    $(window).on("load", function () {
      $(".c-loder-w").hide();
    });

    /* ---------- Password Toggles ---------- */
    $(document).on("click", ".c-password-show", function () {
      $(this).toggleClass("active");
      const input = $("#pass_log_id");
      input.attr("type", input.attr("type") === "password" ? "text" : "password");
    });

    $(document).on("click", ".c-password-show-2", function () {
      $(this).toggleClass("active");
      const input = $(".pass_log_id_new");
      input.attr("type", input.attr("type") === "password" ? "text" : "password");
    });

    $(document).on("click", ".c-password-show-3", function () {
      $(this).toggleClass("active");
      const input = $(".pass_log_id_new-2");
      input.attr("type", input.attr("type") === "password" ? "text" : "password");
    });

    /* ---------- Dropdown ---------- */
    $(".c-drop-down-btn").on("click", function () {
      $(".c-drop-down-list").slideToggle("slow");
    });

    /* ---------- Custom Select ---------- */
    initCustomSelects();

    /* ---------- Pincode ---------- */
    const savedPin = localStorage.getItem("pincode");
    if (savedPin && /^\d{6}$/.test(savedPin)) {
      updatePincodeDisplay(savedPin);
    }

    /* ---------- OTP Auto Tab ---------- */
    $(document).on("keyup", ".otp-input", function () {
      const index = parseInt($(this).data("index"));
      if (this.value.length === 1 && index < $(".otp-input").length - 1) {
        $(".otp-input").eq(index + 1).focus();
      }
    });

    initOtpNavigation();

    /* ---------- Rotating Products ---------- */
    initRotatingProducts();

    /* ---------- Input Restrictions ---------- */
    $('input[name="full_name"], input[name="city"]').on("input", function () {
      this.value = this.value.replace(/[^a-zA-Z\s]/g, "");
    });

    $('input[name="phone"]').on("input", function () {
      this.value = this.value.replace(/\D/g, "").slice(0, 10);
    });

    /* ---------- jQuery Validation ---------- */
    if ($.validator) {
      registerValidators();
      initCareerForms();
    }

  });

  /* ===================== FUNCTIONS ===================== */

  function updatePincodeDisplay(pin) {
    const el = document.getElementById("pincodeDisplay");
    if (el) {
      el.innerHTML = `${pin} <i class="fa fa-chevron-down"></i>`;
    }
  }

  window.applyPincode = function () {
    const pin = document.getElementById("pincodeInput")?.value.trim();
    if (/^\d{6}$/.test(pin)) {
      localStorage.setItem("pincode", pin);
      updatePincodeDisplay(pin);
      $("#pincodeModal").modal("hide");
      location.reload();
    } else {
      alert("Please enter a valid 6-digit pincode.");
    }
  };

  window.getLocation = function () {
    if (!navigator.geolocation) return alert("Geolocation not supported.");

    navigator.geolocation.getCurrentPosition(
      pos => {
        const { latitude, longitude } = pos.coords;
        fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}`)
          .then(r => r.json())
          .then(data => {
            const pc = data.results
              .flatMap(r => r.address_components)
              .find(c => c.types.includes("postal_code"));

            if (pc && /^\d{6}$/.test(pc.long_name)) {
              localStorage.setItem("pincode", pc.long_name);
              updatePincodeDisplay(pc.long_name);
              $("#pincodeModal").modal("hide");
              location.reload();
            } else {
              alert("Unable to detect pincode.");
            }
          });
      },
      () => alert("Location access denied.")
    );
  };

  function initCustomSelects() {
    document.querySelectorAll(".custom-select").forEach(wrapper => {
      const select = wrapper.querySelector("select");
      if (!select) return;

      const selected = document.createElement("div");
      selected.className = "select-selected";
      selected.textContent = select.options[select.selectedIndex].textContent;
      wrapper.appendChild(selected);

      const list = document.createElement("div");
      list.className = "select-items select-hide";

      [...select.options].slice(1).forEach(opt => {
        const item = document.createElement("div");
        item.textContent = opt.textContent;
        item.addEventListener("click", () => {
          select.value = opt.value;
          selected.textContent = opt.textContent;
          list.querySelectorAll(".same-as-selected").forEach(i => i.classList.remove("same-as-selected"));
          item.classList.add("same-as-selected");
          list.classList.add("select-hide");
        });
        list.appendChild(item);
      });

      wrapper.appendChild(list);

      selected.addEventListener("click", e => {
        e.stopPropagation();
        closeAllSelects();
        list.classList.toggle("select-hide");
        selected.classList.toggle("select-arrow-active");
      });
    });

    document.addEventListener("click", closeAllSelects);
  }

  function closeAllSelects() {
    document.querySelectorAll(".select-items").forEach(el => el.classList.add("select-hide"));
    document.querySelectorAll(".select-selected").forEach(el => el.classList.remove("select-arrow-active"));
  }

  function initRotatingProducts() {
    const productEl = document.getElementById("rotating-product");
    if (!productEl) return;

    const productList = ["chair", "table", "lamp", "bedsheet", "sofa", "desk"];
    let index = 0;

    function update() {
      productEl.classList.add("slide-out-up");
      productEl.addEventListener("animationend", () => {
        productEl.textContent = productList[index];
        productEl.classList.remove("slide-out-up");
        productEl.classList.add("slide-in-up");
        index = (index + 1) % productList.length;
      }, { once: true });
    }

    setTimeout(() => {
      update();
      setInterval(update, 1500);
    }, 1500);
  }

  function initOtpNavigation() {
    document.querySelectorAll(".dot-nav").forEach(dot => {
      dot.addEventListener("click", function () {
        document.querySelectorAll(".slide-section").forEach(s =>
          s.classList.remove("active", "out-left")
        );
        document.getElementById(this.dataset.target)?.classList.add("active");
      });
    });
  }

  function registerValidators() {
    $.validator.addMethod("lettersOnly", v => /^[A-Za-z\s]+$/.test(v));
    $.validator.addMethod("validPhone", v => /^[6-9]\d{9}$/.test(v));
    $.validator.addMethod("validEmail", v => /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v));
    $.validator.addMethod("experienceFormat", v => /^\d{1,2}-\d{1,2}$/.test(v));
    $.validator.addMethod("filesize", (v, e, p) => !e.files[0] || e.files[0].size <= p);
  }

  function initCareerForms() {
    $(".careerForm").each(function () {
      $(this).validate({
        errorClass: "text-danger",
        submitHandler: form => form.submit()
      });
    });
  }

})();
