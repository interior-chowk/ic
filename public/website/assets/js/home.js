document.addEventListener("DOMContentLoaded", function () {
  if (
    typeof jQuery !== "undefined" &&
    typeof $.fn.owlCarousel !== "undefined"
  ) {
    $(".section2").owlCarousel({
      nav: false,
      dots: false,
      loop: true,
      autoplay: true,
      autoplayTimeout: 4000,
      autoplaySpeed: 2000,
      responsive: {
        0: {
          items: 1,
        },
        480: {
          items: 1,
        },
        768: {
          items: 1,
        },
        992: {
          items: 1,
        },
        1200: {
          items: 1,
          nav: false,
        },
      },
    });
  } else {
    console.warn("jQuery or OwlCarousel not loaded.");
  }
});

document.addEventListener("DOMContentLoaded", function () {
  if (
    typeof jQuery !== "undefined" &&
    typeof $.fn.owlCarousel !== "undefined"
  ) {
    $(".section3").owlCarousel({
      nav: false,
      dots: false,
      loop: true,
      autoplay: true,
      autoplayTimeout: 4000,
      autoplaySpeed: 2000,
      responsive: {
        0: {
          items: 1,
        },
        480: {
          items: 1,
        },
        768: {
          items: 1,
        },
        992: {
          items: 1,
        },
        1200: {
          items: 1,
          nav: false,
        },
      },
    });
  } else {
    console.warn("jQuery or OwlCarousel not loaded.");
  }
});

//second section
document.addEventListener("DOMContentLoaded", function () {
  if (
    typeof jQuery !== "undefined" &&
    typeof $.fn.owlCarousel !== "undefined"
  ) {
    $(".category-carousel").owlCarousel({
      nav: false,
      dots: false,
      margin: 25,
      loop: false,
      autoplay: false,
      autoplayTimeout: 2000,
      autoplaySpeed: 2000,
      autoplayHoverPause: true,
      slideTransition: "linear",
      responsive: {
        0: {
          items: 4,
        },
        480: {
          items: 4,
        },
        768: {
          items: 4,
        },
        992: {
          items: 4,
        },
        1200: {
          items: 8,
          nav: true,
        },
      },
    });
  } else {
    console.warn("jQuery or OwlCarousel not loaded.");
  }
});

document.addEventListener("DOMContentLoaded", function () {
  if (
    typeof jQuery !== "undefined" &&
    typeof $.fn.owlCarousel !== "undefined"
  ) {
    $(".section4-carousel").owlCarousel({
      nav: false,
      dots: false,
      margin: 25,
      loop: false,
      autoplay: false,
      autoplayTimeout: 2000,
      autoplaySpeed: 2000,
      autoplayHoverPause: true,
      slideTransition: "linear",
      responsive: {
        0: {
          items: 4,
        },
        480: {
          items: 4,
        },
        768: {
          items: 4,
        },
        992: {
          items: 4,
        },
        1200: {
          items: 8,
          nav: true,
        },
      },
    });
  } else {
    console.warn("jQuery or OwlCarousel not loaded for section 4 banner.");
  }
});

document.addEventListener("DOMContentLoaded", function () {
  if (
    typeof jQuery !== "undefined" &&
    typeof $.fn.owlCarousel !== "undefined"
  ) {
    $(".recently-viewed-carousel").owlCarousel({
      nav: false,
      dots: false,
      margin: 20,
      loop: false,
      responsive: {
        0: { items: 2 },
        480: { items: 3 },
        768: { items: 4 },
        992: { items: 6 },
        1200: { items: 6, nav: false, dots: false },
      },
    });
  } else {
    console.warn(
      "jQuery or OwlCarousel not loaded for recently viewed carousel."
    );
  }
});

document.addEventListener("DOMContentLoaded", function () {
  if (
    typeof jQuery !== "undefined" &&
    typeof $.fn.owlCarousel !== "undefined"
  ) {
    $(".related-products-carousel").owlCarousel({
      nav: false,
      dots: false,
      margin: 20,
      loop: false,
      responsive: {
        0: { items: 2 },
        480: { items: 3 },
        768: { items: 4 },
        992: { items: 6 },
        1200: { items: 6, nav: false, dots: false },
      },
    });
  } else {
    console.warn("jQuery or OwlCarousel not found.");
  }
});

document.addEventListener("DOMContentLoaded", function () {
  if (
    typeof jQuery !== "undefined" &&
    typeof $.fn.owlCarousel !== "undefined"
  ) {
    $(".more-items-carousel").owlCarousel({
      nav: false,
      dots: false,
      margin: 20,
      loop: false,
      responsive: {
        0: { items: 2 },
        480: { items: 3 },
        768: { items: 4 },
        992: { items: 6 },
        1200: { items: 6, nav: false, dots: false },
      },
    });
  } else {
    console.warn("jQuery or OwlCarousel is not loaded.");
  }
});

document.addEventListener("DOMContentLoaded", function () {
  if (
    typeof jQuery !== "undefined" &&
    typeof $.fn.owlCarousel !== "undefined"
  ) {
    $(".wishlist-carousel").owlCarousel({
      nav: false,
      dots: true,
      margin: 20,
      loop: false,
      responsive: {
        0: { items: 1 },
        480: { items: 2 },
        768: { items: 3 },
        992: { items: 3 },
        1200: { items: 3, nav: true, dots: false },
      },
    });
  } else {
    console.warn("OwlCarousel or jQuery not found.");
  }
});

$(document).ready(function () {
  $(".btnAddToCart").on("click", function () {
    const slug = $(this).data("slug");
    $.ajax({
      url: "{{ route('cart.add_1') }}",
      type: "POST",
      data: {
        _token: "{{ csrf_token() }}",
        product: {
          slug: slug,
        },
      },
      success: function (response) {
        alert(response.message); // Success
      },
      error: function (xhr) {
        if (xhr.status === 401) {
          alert("You must be logged in to add to cart.");
        } else {
          alert("Something went wrong. Please try again.");
        }
      },
    });
  });

  // Add to Wishlist Button
  $(".btnAddToWishlist").on("click", function () {
    const slug = $(this).data("slug");

    $.ajax({
      url: "{{ route('store-wishlist-1') }}", // fixed to your correct route
      type: "POST",
      data: {
        _token: "{{ csrf_token() }}",
        slug: slug,
      },
      success: function (response) {
        alert(response.message);
      },
      error: function (xhr) {
        if (xhr.status === 401) {
          alert("You must be logged in to add to wishlist.");
        } else {
          alert("Something went wrong while adding to wishlist.");
        }
      },
    });
  });
});

$(document).ready(function () {
  $("#top-products-carousel").owlCarousel({
    nav: false,
    dots: false,
    margin: 20,
    loop: false,
    responsive: {
      0: { items: 2 },
      480: { items: 3 },
      768: { items: 4 },
      992: { items: 6 },
      1200: { items: 6, nav: false, dots: false },
    },
  });
});

// Initialize Owl Carousel for Top Brands
$(document).ready(function () {
  $("#top-brands-carousel").owlCarousel({
    nav: false,
    dots: false,
    margin: 20,
    loop: true,
    autoplay: true,
    autoplayTimeout: 3000,
    autoplaySpeed: 3000,
    autoplayHoverPause: true,
    slideTransition: "linear",
    responsive: {
      0: { items: 4 },
      480: { items: 4 },
      768: { items: 6 },
      992: { items: 6, nav: false, dots: false },
    },
  });
});

// Optional: Fade in banner on scroll
$(document).ready(function () {
  const banner = $(".architect-banner");
  $(window).on("scroll", function () {
    if (
      $(window).scrollTop() + $(window).height() >
      banner.offset().top + 100
    ) {
      banner.addClass("fade-in");
    }
  });
});

// Initialize Owl Carousel for Architects
$(document).ready(function () {
  $("#architects-carousel").owlCarousel({
    nav: false,
    dots: true,
    margin: 20,
    loop: false,
    responsive: {
      0: { items: 2 },
      480: { items: 3 },
      768: { items: 3 },
      992: { items: 4 },
      1200: { items: 5, nav: true, dots: false },
    },
  });
});

// Owl Carousel: Top Interior Designers
$(document).ready(function () {
  $("#interior-designers-carousel").owlCarousel({
    nav: false,
    dots: false,
    margin: 20,
    loop: false,
    responsive: {
      0: { items: 2 },
      480: { items: 3 },
      768: { items: 3 },
      992: { items: 4 },
      1200: { items: 5, nav: false, dots: false },
    },
  });
});

// Owl Carousel: Contractors
$(document).ready(function () {
  $("#contractor-carousel").owlCarousel({
    nav: false,
    dots: false,
    margin: 20,
    loop: false,
    responsive: {
      0: { items: 2 },
      480: { items: 3 },
      768: { items: 4 },
      992: { items: 6 },
      1200: { items: 6, nav: false, dots: false },
    },
  });
});

$(document).ready(function () {
  $("#deals-carousel").owlCarousel({
    nav: false,
    dots: true,
    margin: 20,
    loop: false,
    responsive: {
      0: { items: 2 },
      480: { items: 3 },
      768: { items: 4 },
      992: { items: 6 },
      1200: { items: 6, nav: false, dots: false },
    },
  });
});

$(document).ready(function () {
  $("#creator-carousel-1, #creator-carousel-2").owlCarousel({
    nav: false,
    dots: true,
    margin: 20,
    loop: false,
    responsive: {
      0: { items: 2 },
      480: { items: 2 },
      768: { items: 3 },
      992: { items: 4 },
      1200: {
        items: 4,
        nav: true,
        dots: false,
      },
    },
  });
});

$(document).ready(function () {
  $("#luxe-carousel").owlCarousel({
    nav: false,
    dots: true,
    margin: 20,
    loop: false,
    responsive: {
      0: { items: 2 },
      480: { items: 2 },
      768: { items: 3 },
      992: {
        items: 5,
        nav: true,
        dots: false,
      },
    },
  });
});

// tips.js
$(document).ready(function () {
  $(".tips-carousel-1").owlCarousel({
    nav: false,
    dots: true,
    margin: 20,
    loop: false,
    responsive: {
      0: { items: 2 },
      480: { items: 2 },
      768: { items: 3 },
      1200: {
        items: 3,
        nav: true,
        dots: false,
      },
    },
  });

  $(".tips-carousel-2").owlCarousel({
    nav: false,
    dots: true,
    margin: 20,
    loop: false,
    responsive: {
      0: { items: 2 },
      480: { items: 2 },
      768: { items: 3 },
      992: { items: 4 },
      1200: {
        items: 4,
        nav: true,
        dots: false,
      },
    },
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const videos = document.querySelectorAll(".custom-banner-video");
  videos.forEach((video) => {
    video.addEventListener("error", () => {
      console.warn("Video failed to load:", video);
    });
  });
});

// featured.js
$(document).ready(function () {
  $(".featured-carousel").owlCarousel({
    nav: false,
    dots: true,
    margin: 20,
    loop: false,
    responsive: {
      0: { items: 2 },
      480: { items: 2 },
      768: { items: 3 },
      992: {
        items: 6,
        nav: true,
        dots: false,
      },
    },
  });

  $(".featured-carousel-alt").owlCarousel({
    nav: false,
    dots: true,
    margin: 20,
    loop: false,
    responsive: {
      0: { items: 1 },
      480: { items: 2 },
      768: { items: 3 },
      992: {
        items: 3,
        dots: false,
      },
    },
  });
});

// happy-customers.js
$(document).ready(function () {
  $(".happy-customer-carousel").owlCarousel({
    nav: false,
    dots: false,
    margin: 20,
    loop: true,
    autoplay: true,
    autoplayTimeout: 4000,
    slideBy: 4,
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 2,
      },
      992: {
        items: 3,
      },
      1280: {
        items: 4,
      },
    },
  });
});

// blog-section.js
$(document).ready(function () {
  $(".blog-carousel").owlCarousel({
    nav: false,
    dots: true,
    margin: 20,
    loop: false,
    autoplay: false,
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 2,
      },
      992: {
        items: 3,
      },
      1280: {
        items: 4,
        nav: true,
        dots: false,
      },
    },
  });
});

// Optional toggle logic
document.addEventListener("DOMContentLoaded", function () {
  const exploreLinks = document.querySelectorAll(".cta-heading a");
  exploreLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      alert("Navigating to: " + link.href);
    });
  });
});
