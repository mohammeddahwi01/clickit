jQuery(document).ready(function() {
// SLIDER CUSTOMER REVIEWS
var swiper = new Swiper('.customer-reviews-swiper', {
  slidesPerView: 2,
  spaceBetween: 0,
  slidesPerGroup: 2,
  loop: true,
  loopFillGroupWithBlank: true,
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
  breakpoints: {
    768: {
      slidesPerView: 1,
      slidesPerGroup: 1,
    }
  }
});
// SLIDER OTHER PRODUCTS
var swiper = new Swiper('.other-products-swiper', {
  slidesPerView: 4,
  spaceBetween: 30,
  loop: true,
  loopFillGroupWithBlank: true,    
  freeMode: true,
  navigation: {
    nextEl: '.swiper-2-button-next',
    prevEl: '.swiper-2-button-prev',
  },    
  breakpoints: {
    1400: {
      slidesPerView: 3,
    },
      992: {
      slidesPerView: 2,
    },
    768: {
      slidesPerView: 1,
    }  
  }
});
});
