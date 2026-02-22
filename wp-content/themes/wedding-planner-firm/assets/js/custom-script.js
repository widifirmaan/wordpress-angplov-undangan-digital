jQuery(document).ready(function ($) {

  $(window).scroll(function () {
    if ($(this).scrollTop() > 100) {
      $(".back-to-top a").fadeIn();
    } else {
      $(".back-to-top a").fadeOut();
    }
  });

  $(".back-to-top a").click(function () {
    $("html, body").animate({ scrollTop: 0 }, 800);
    return false;
  });

  //projects-swiper
  var wedding_planner_firm_project_Slider = new Swiper(".projects-slider", {
    breakpoints: {
      0: {
        slidesPerView: 1,
        centeredSlides: false,
      },
      768: {
        slidesPerView: 2,
        centeredSlides: false,
      },
      992: {
        slidesPerView: 3,
        centeredSlides: true,
      }
    },
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },
    spaceBetween: 30,
    loop: true,
    navigation: {
      nextEl: ".projects-swiper-button-next",
      prevEl: ".projects-swiper-button-prev",
    },
  });

  // /*slick slider*/
  jQuery('.slider-for').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    dots: true,
    fade: true,
    asNavFor: '.slider-nav',
    infinite: true,
    loop: true,
  });
  jQuery('.slider-nav').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    asNavFor: '.slider-for',
    dots: false,
    centerMode: true,
    arrows: false,
    focusOnSelect: true,
    infinite: true,
    loop: true,
  });
});

// Sub-heading
jQuery(document).ready(function() {
  jQuery(".about-section .about-text").each(function() {
    var t = jQuery(this).text().trim();
    var splitT = t.split(" ");
    var newText = "";
    var targetWordIndex = 10; // 0-based index → 11th word

    for (var i = 0; i < splitT.length; i++) {
      if (i === targetWordIndex) {
        newText += "<span class='highlight-word'>" + splitT[i] + "</span> ";
      } else {
        newText += splitT[i] + " ";
      }
    }
    jQuery(this).html(newText.trim());
  });
});
