require([
    'jquery',
    'SmoothScroll',
    'owl.carousel/owl.carousel.min',
    'accordion'
],function(){

    jQuery(document).ready(function($) {

        jQuery('.page-loader').fadeOut('slow', function () {
            jQuery('.page-loader').remove();
        });

        var navbar = $("#js-navbar"),
            top;
        checkContainer();

        function checkContainer () {
            if(jQuery('.iwd_opc_payment_info').is(':visible')) {
                jQuery("#iwd_opc_review").appendTo("#summary-order-opc");
            } else {
                setTimeout(checkContainer, 6000);
            }
        }

        function miniCart() {
            jQuery(".minicart-wrapper .count-item-mini-cart").appendTo(".js-mini-shopcart .totals");
            jQuery(".header-2 .minicart-wrapper").appendTo("#js-navbar .mini-shopcart");
        }

        function clickMiniCart() {
            jQuery(".minicart-wrapper .showcart").appendTo(".js-mini-shopcart").after(jQuery(".js-mini-shopcart .totals"));
            jQuery('.js-mini-shopcart').click(function () {
                jQuery('.minicart-wrapper .mage-dropdown-dialog').toggle();
            });
        }

        if (jQuery('.minicart-wrapper .count-item-mini-cart').length > 0) {
            setTimeout(miniCart, 8000);
            setTimeout(clickMiniCart, 8000);
        }


        if (navbar) {
            top = navbar.offset().top;
            $(window).on("scroll", function (event) {
                var y = $(this).scrollTop();
                if (y >= top) {
                    navbar.addClass('fixed');
                } else {
                    navbar.removeClass('fixed');
                }
            });
        }

        /*Hamburger Menu*/

        jQuery('#toggle-icon').on('click', function(){
            jQuery(this).toggleClass("is-active");
            jQuery(".nav-menu").toggleClass('open');
        });


        var tooltip = $('[data-toggle-tooltip="tooltip"]');
        if (tooltip) {
            tooltip.tooltip();
        }

        // Navbar menu caret

        var btnCaret = $('.btn-caret');
        if (btnCaret) {
            btnCaret.on('click', function (e) {
                $(this).siblings('.drop-menu').slideToggle(200, 'linear');
                e.stopPropagation();
            });
        }

        /* Scroll Like Mac*/
        SmoothScroll({
            keyboardSupport: false,
            animationTime: 560, // [ms]
            stepSize: 100 // [px]
        });

        /* carousel */
        var owlSelector = $('.owl-carousel');

        if (owlSelector !== undefined) {
            owlSelector.each(function () {
                var option = {
                    items : 3,
                    margin : 0,
                    loop : false,
                    center : false,
                    mousedrag : true,
                    touchdrag : true,
                    pulldrag : true,
                    nav : false,
                    navtext : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
                    dots : false,
                    dotsdata : false,
                    autoplay : false,
                    smartspeed : 650,
                    animateout : null,
                    animatein : null,
                    xs :  1,
                    sm :  2,
                    md :  2,
                    lg :  3};

                for (var k in option) {
                    if (option.hasOwnProperty(k)) {
                        if ($(this).attr('data-carousel-' + k) != null) {
                            option[k] =  $(this).data('carousel-' + k);
                        }
                    }
                }


                $(this).owlCarousel({
                    margin: option.margin,
                    loop: option.loop,
                    center: option.center,
                    mouseDrag: option.mousedrag,
                    touchDrag: option.touchdrag,
                    pullDrag: option.pulldrag,
                    nav: option.nav,
                    navText: option.navtext,
                    dots: option.dots,
                    dotsData: option.dotsdata,
                    autoplay: option.autoplay,
                    smartSpeed: option.smartspeed,
                    animateIn: option.animatein,
                    animateOut: option.animateout,
                    responsive: {
                        // breakpoint from 0 up
                        0: {
                            items: option.xs
                        },
                        // breakpoint from 768 up
                        480: {
                            items: option.sm
                        },
                        // breakpoint from 768 up
                        768: {
                            items: option.md
                        },
                        992: {
                            items: option.lg
                        },
                        1200: {
                            items : option.items
                        }
                    }
                });

            });
        }

        /* accordion */
        var accordion_select = $('.accordion');

        if (accordion_select !== undefined) {
            accordion_select.each(function () {
                $(this).accordion({
                    "transitionSpeed": 400,
                    transitionEasing: 'ease-in-out'
                });
            });
        }

    });
});