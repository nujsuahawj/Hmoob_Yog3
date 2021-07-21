(function ($) {
    'use strict';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    window.botbleCookieNewsletter = (() => {

        const COOKIE_VALUE = 1;
        const COOKIE_NAME = 'botble_cookie_newsletter';
        const COOKIE_DOMAIN = $('div[data-session-domain]').data('session-domain');
        const COOKIE_MODAL = $('#newsletter-modal');

        function newsletterWithCookies(expirationInDays) {
            setCookie(COOKIE_NAME, COOKIE_VALUE, expirationInDays);
        }

        function cookieExists(name) {
            return document.cookie.split('; ').indexOf(name + '=' + COOKIE_VALUE) !== -1;
        }

        function hideCookieDialog() {
            COOKIE_MODAL.modal('hide', {}, 500);
        }

        function setCookie(name, value, expirationInDays) {
            const date = new Date();
            date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));
            document.cookie = name + '=' + value
                + ';expires=' + date.toUTCString()
                + ';domain=' + COOKIE_DOMAIN
                + ';path=/';
        }

        if (!cookieExists(COOKIE_NAME)) {
            setTimeout(() => {
                COOKIE_MODAL.modal('show', {}, 500);
            }, 5000);
        }

        COOKIE_MODAL.on('hidden.bs.modal', () => {
            if (!cookieExists(COOKIE_NAME) && $('#dont_show_again').is(':checked')) {
                newsletterWithCookies(3);
            } else {
                newsletterWithCookies(1/24);
            }
        });

        return {
            newsletterWithCookies: newsletterWithCookies,
            hideCookieDialog: hideCookieDialog
        };
    })();

    $(document).ready(function () {
        var showError = message => {
            $('.newsletter-error-message').html(message).show();
        }

        var showSuccess = message => {
            $('.newsletter-success-message').html(message).show();
        }

        var handleError = data => {
            if (typeof (data.errors) !== 'undefined' && data.errors.length) {
                handleValidationError(data.errors);
            } else if (typeof (data.responseJSON) !== 'undefined') {
                if (typeof (data.responseJSON.errors) !== 'undefined') {
                    if (data.status === 422) {
                        handleValidationError(data.responseJSON.errors);
                    }
                } else if (typeof (data.responseJSON.message) !== 'undefined') {
                    showError(data.responseJSON.message);
                } else {
                    $.each(data.responseJSON, (index, el) => {
                        $.each(el, (key, item) => {
                            showError(item);
                        });
                    });
                }
            } else {
                showError(data.statusText);
            }
        }

        var handleValidationError = errors => {
            let message = '';
            $.each(errors, (index, item) => {
                if (message !== '') {
                    message += '<br />';
                }
                message += item;
            });
            showError(message);
        }

        $(document).on('click', '.newsletter-form button[type=submit]', function (event) {
            event.preventDefault();
            event.stopPropagation();

            let _self = $(this);

            _self.addClass('button-loading');
            $('.newsletter-success-message').html('').hide();
            $('.newsletter-error-message').html('').hide();

            $.ajax({
                type: 'POST',
                cache: false,
                url: _self.closest('form').prop('action'),
                data: new FormData(_self.closest('form')[0]),
                contentType: false,
                processData: false,
                success: res => {
                    _self.removeClass('button-loading');

                    if (typeof refreshRecaptcha !== 'undefined') {
                        refreshRecaptcha();
                    }

                    if (!res.error) {
                        window.botbleCookieNewsletter.newsletterWithCookies(30);
                        _self.closest('form').find('input[type=email]').val('');
                        showSuccess(res.message);
                        setTimeout(() => {
                            _self.closest('.modal-body').find('button[data-dismiss="modal"]').trigger('click');
                        }, 2000);
                    } else {
                        showError(res.message);
                    }
                },
                error: res => {
                    if (typeof refreshRecaptcha !== 'undefined') {
                        refreshRecaptcha();
                    }
                    _self.removeClass('button-loading');
                    handleError(res);
                }
            });
        });
    });

    $(document).ready(function () {
        $(document).on('change', '.switch-currency', function () {
            $(this).closest('form').submit();
        });

        $(document).on('change', '.product-filter-item', function () {
            $(this).closest('form').submit();
        });

        $(document).on('click', '.js-add-favorite-button', function (event) {
            event.preventDefault();
            let _self = $(this);

            _self.addClass('button-loading');

            $.ajax({
                url: _self.prop('href'),
                method: 'POST',
                success: res => {

                    if (res.error) {
                        _self.removeClass('button-loading');
                        return false;
                    }

                    $('.btn-wishlist span').text(res.data.count);

                    _self.removeClass('button-loading').removeClass('js-add-favorite-button').addClass('js-remove-favorite-button active');
                },
                error: () => {
                    _self.removeClass('button-loading');
                }
            });
        });


        $(document).on('click', '.js-remove-favorite-button', function (event) {
            event.preventDefault();
            let _self = $(this);

            _self.addClass('button-loading');

            $.ajax({
                url: _self.prop('href'),
                method: 'DELETE',
                success: res => {

                    if (res.error) {
                        _self.removeClass('button-loading');
                        return false;
                    }

                    $('.btn-wishlist span').text(res.data.count);

                    _self.closest('tr').remove();
                    _self.removeClass('button-loading').removeClass('js-remove-favorite-button active').addClass('js-add-favorite-button');
                },
                error: () => {
                    _self.removeClass('button-loading');
                }
            });
        });

        $(document).on('click', '.add-to-cart-button', function (event) {
            event.preventDefault();
            let _self = $(this);

            _self.prop('disabled', true).addClass('button-loading');

            $.ajax({
                url: _self.prop('href'),
                method: 'POST',
                data: {
                    id: _self.data('id')
                },
                dataType: 'json',
                success: res => {
                    _self.prop('disabled', false).removeClass('button-loading').addClass('active');

                    if (res.error) {
                        return false;
                    }

                    $.ajax({
                        url: '/ajax/cart',
                        method: 'GET',
                        success: response => {
                            if (!response.error) {
                                $('.cart_box').html(response.data.html);
                                $('.btn-shopping-cart span').text(response.data.count);
                            }
                        }
                    });
                },
                error: () => {
                    _self.prop('disabled', false).removeClass('button-loading');
                }
            });
        });

        $(document).on('click', '.remove-cart-button', function (event) {
            event.preventDefault();

            $('.confirm-remove-item-cart').data('url', $(this).prop('href'));
            $('#remove-item-modal').modal('show');
        });


        $(document).on('click', '.confirm-remove-item-cart', function (event) {
            event.preventDefault();
            let _self = $(this);

            _self.prop('disabled', true).addClass('button-loading');

            $.ajax({
                url: _self.data('url'),
                method: 'GET',
                success: res => {
                    _self.prop('disabled', false).removeClass('button-loading');

                    if (res.error) {
                        return false;
                    }

                    _self.closest('.modal').modal('hide');

                    if ($('.section--shopping-cart').length) {
                        $('.section--shopping-cart').load(window.location.href + ' .section--shopping-cart > *');
                    }

                    $.ajax({
                        url: '/ajax/cart',
                        method: 'GET',
                        success: response => {
                            if (!response.error) {
                                $('.cart_box').html(response.data.html);
                                $('.btn-shopping-cart span').text(response.data.count);
                            }
                        }
                    });
                },
                error: () => {
                    _self.prop('disabled', false).removeClass('button-loading');
                }
            });
        });

        window.onBeforeChangeSwatches = function () {
            $('.add-to-cart-form button[type=submit]').prop('disabled', true).addClass('btn-disabled');
        }

        window.onChangeSwatchesSuccess = function (res) {
            $('.add-to-cart-form .error-message').hide();
            $('.add-to-cart-form .success-message').hide();
            if (res.error) {
                $('.add-to-cart-form button[type=submit]').prop('disabled', true).addClass('btn-disabled');
                $('.number-items-available').html('<span class="text-danger">(0 products available)</span>').show();
                $('#hidden-product-id').val('');
            } else {
                $('.add-to-cart-form').find('.error-message').hide();
                $('.product_price .product-sale-price-text').text(res.data.display_sale_price);
                if (res.data.sale_price !== res.data.price) {
                    $('.product_price .product-price-text').text(res.data.display_price).show();
                    $('.product_price .on_sale .on_sale_percentage_text').text(res.data.sale_percentage).show();
                    $('.product_price .show').hide();
                } else {
                    $('.product_price .product-price-text').text(res.data.sale_percentage).hide();
                    $('.product_price .on_sale .on_sale_percentage_text').text(res.data.sale_percentage).hide();
                    $('.product_price .on_sale').hide();
                }

                $('#hidden-product-id').val(res.data.id);
                $('.add-to-cart-form button[type=submit]').prop('disabled', false).removeClass('btn-disabled');
                if (res.data.with_storehouse_management && res.data.quantity) {
                    $('.number-items-available').html('<span class="text-success">(' + res.data.quantity + ' products available)</span>').show();
                } else {
                    $('.number-items-available').html('<span class="text-success">(> 10 products available)</span>').show();
                }

                let thumbHtml = '';
                res.data.image_with_sizes.thumb.forEach(function (item, index) {
                    thumbHtml += '<div class="item"><a href="#" class="product_gallery_item ' + (index === 0 ? 'active' : '') + '" data-image="' + res.data.image_with_sizes.origin[index] +'" data-zoom-image="' + res.data.image_with_sizes.origin[index] + '"><img src="' + item + '" alt="image"/></a></div>'
                });

                let slider = $('.slick_slider');

                slider.slick('unslick');

                slider.html(thumbHtml);

                slider.slick({
                    arrows: slider.data("arrows"),
                    dots: slider.data("dots"),
                    infinite: slider.data("infinite"),
                    centerMode: slider.data("center-mode"),
                    vertical: slider.data("vertical"),
                    fade: slider.data("fade"),
                    cssEase: slider.data("css-ease"),
                    autoplay: slider.data("autoplay"),
                    verticalSwiping: slider.data("vertical-swiping"),
                    autoplaySpeed: slider.data("autoplay-speed"),
                    speed: slider.data("speed"),
                    pauseOnHover: slider.data("pause-on-hover"),
                    draggable: slider.data("draggable"),
                    slidesToShow: slider.data("slides-to-show"),
                    slidesToScroll: slider.data("slides-to-scroll"),
                    asNavFor: slider.data("as-nav-for"),
                    focusOnSelect: slider.data("focus-on-select"),
                    responsive: slider.data("responsive")
                });

                $(window).trigger('resize');

                let image = $('#product_img');
                image.prop('src', res.data.image_with_sizes.origin[0]).data('zoom-image', res.data.image_with_sizes.origin[0]);

                let zoomActive = false;

                zoomActive = !zoomActive;
                if (zoomActive) {
                    if (image.length > 0) {
                        image.elevateZoom({
                            cursor: 'crosshair',
                            easing: true,
                            gallery: 'pr_item_gallery',
                            zoomType: 'inner',
                            galleryActiveClass: 'active'
                        });
                    }
                } else {
                    $.removeData(image, 'elevateZoom');//remove zoom instance from image
                    $('.zoomContainer:last-child').remove();// remove zoom container from DOM
                }
            }
        };

        let handleError = function (data, form) {
            if (typeof (data.errors) !== 'undefined' && !_.isArray(data.errors)) {
                handleValidationError(data.errors, form);
            } else if (typeof (data.responseJSON) !== 'undefined') {
                if (typeof (data.responseJSON.errors) !== 'undefined' && data.status === 422) {
                    handleValidationError(data.responseJSON.errors, form);
                } else if (typeof (data.responseJSON.message) !== 'undefined') {
                    $(form).find('.error-message').html(data.responseJSON.message).show();
                } else {
                    let message = '';
                    $.each(data.responseJSON, (index, el) => {
                        $.each(el, (key, item) => {
                            message += item + '<br />';
                        });
                    });

                    $(form).find('.error-message').html(message).show();
                }
            } else {
                $(form).find('.error-message').html(data.statusText).show();
            }
        };

        let handleValidationError = function (errors, form) {
            let message = '';
            $.each(errors, (index, item) => {
                message += item + '<br />';
            });

            $(form).find('.success-message').html('').hide();
            $(form).find('.error-message').html('').hide();

            $(form).find('.error-message').html(message).show();
        };

        $(document).on('click', '.add-to-cart-form button[type=submit]', function (event) {
            event.preventDefault();
            event.stopPropagation();

            let _self = $(this);

            if (!$('#hidden-product-id').val()) {
                _self.prop('disabled', true).addClass('btn-disabled');
                return;
            }

            _self.prop('disabled', true).addClass('btn-disabled').addClass('button-loading');

            _self.closest('form').find('.error-message').hide();
            _self.closest('form').find('.success-message').hide();

            $.ajax({
                type: 'POST',
                cache: false,
                url: _self.closest('form').prop('action'),
                data: new FormData(_self.closest('form')[0]),
                contentType: false,
                processData: false,
                success: res => {
                    _self.prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');

                    if (res.error) {
                        _self.closest('form').find('.error-message').html(res.message).show();
                        return false;
                    }

                    _self.closest('form').find('.success-message').html(res.message).show();

                    $.ajax({
                        url: '/ajax/cart',
                        method: 'GET',
                        success: function (response) {
                            if (!response.error) {
                                $('.cart_box').html(response.data.html);
                                $('.btn-shopping-cart span').text(response.data.count);
                            }
                        }
                    });
                },
                error: res => {
                    _self.prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
                    handleError(res, _self.closest('form'));
                }
            });
        });

        $(document).on('change', '.submit-form-on-change', function () {
            $(this).closest('form').submit();
        });

        $(document).on('click', '.form-review-product button[type=submit]', function (event) {
            event.preventDefault();
            event.stopPropagation();
            $(this).prop('disabled', true).addClass('btn-disabled').addClass('button-loading');

            $.ajax({
                type: 'POST',
                cache: false,
                url: $(this).closest('form').prop('action'),
                data: new FormData($(this).closest('form')[0]),
                contentType: false,
                processData: false,
                success: res => {
                    $(this).closest('form').find('.success-message').html('').hide();
                    $(this).closest('form').find('.error-message').html('').hide();

                    if (!res.error) {
                        $(this).closest('form').find('select').val(0);
                        $(this).closest('form').find('textarea').val('');

                        $(this).closest('form').find('.success-message').html(res.message).show();

                        $.ajax({
                            url: '/ajax/reviews/' + $(this).closest('form').find('input[name=product_id]').val(),
                            method: 'GET',
                            success: function (response) {
                                if (!response.error) {
                                    $('#list-reviews').html(response.data.html);

                                    $(document).find('select.rating').each(function () {
                                        let readOnly = $(this).attr('data-read-only') === 'true';
                                        $(this).barrating({
                                            theme: 'fontawesome-stars',
                                            readonly: readOnly,
                                            emptyValue: '0'
                                        });
                                    });
                                }
                            }
                        });

                        setTimeout(function () {
                            $(this).closest('form').find('.success-message').html('').hide();
                        }, 5000);
                    } else {
                        $(this).closest('form').find('.error-message').html(res.message).show();

                        setTimeout(function () {
                            $(this).closest('form').find('.error-message').html('').hide();
                        }, 5000);
                    }

                    $(this).prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
                },
                error: res => {
                    $(this).prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
                    handleError(res, $(this).closest('form'));
                }
            });
        });

    });

})(jQuery);

/*===================================
Author       : Bestwebcreator.
Template Name: Shopwise - eCommerce Bootstrap 4 HTML Template
Version      : 1.0
===================================*/

/*===================================*
PAGE JS
*===================================*/

(function ($) {
    'use strict';

    /*===================================*
    01. LOADING JS
    /*===================================*/
    $(window).on('load', function () {
        setTimeout(function () {
            $(".preloader").delay(700).fadeOut(700).addClass('loaded');
        }, 800);
    });

    /*===================================*
    02. BACKGROUND IMAGE JS
    *===================================*/
    /*data image src*/
    $(".background_bg").each(function () {
        var attr = $(this).attr('data-img-src');
        if (typeof attr !== typeof undefined && attr !== false) {
            $(this).css('background-image', 'url(' + attr + ')');
        }
    });

    /*===================================*
    03. ANIMATION JS
    *===================================*/
    $(function () {

        function ckScrollInit(items, trigger) {
            items.each(function () {
                var ckElement = $(this),
                    AnimationClass = ckElement.attr('data-animation'),
                    AnimationDelay = ckElement.attr('data-animation-delay');

                ckElement.css({
                    '-webkit-animation-delay': AnimationDelay,
                    '-moz-animation-delay': AnimationDelay,
                    'animation-delay': AnimationDelay,
                    opacity: 0
                });

                var ckTrigger = (trigger) ? trigger : ckElement;

                ckTrigger.waypoint(function () {
                    ckElement.addClass("animated").css("opacity", "1");
                    ckElement.addClass('animated').addClass(AnimationClass);
                }, {
                    triggerOnce: true,
                    offset: '90%',
                });
            });
        }

        ckScrollInit($('.animation'));
        ckScrollInit($('.staggered-animation'), $('.staggered-animation-wrap'));

    });

    /*===================================*
    04. MENU JS
    *===================================*/
    //Main navigation scroll spy for shadow
    $(window).on('scroll', function () {
        var scroll = $(window).scrollTop();

        if (scroll >= 150) {
            $('header.fixed-top').addClass('nav-fixed');
        } else {
            $('header.fixed-top').removeClass('nav-fixed');
        }

    });

    // Show Hide dropdown-menu Main navigation
    $(document).on('ready', function () {
        $('.dropdown-menu a.dropdown-toggler').on('click', function () {
            if (!$(this).next().hasClass('show')) {
                $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
            }
            var $subMenu = $(this).next(".dropdown-menu");
            $subMenu.toggleClass('show');

            $(this).parent("li").toggleClass('show');

            $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function () {
                $('.dropdown-menu .show').removeClass("show");
            });

            return false;
        });
    });

    //Hide Navbar Dropdown After Click On Links
    var navBar = $('.header_wrap');
    var navbarLinks = navBar.find(".navbar-collapse ul li a.page-scroll");

    $.each(navbarLinks, function () {

        var navbarLink = $(this);

        navbarLink.on('click', function () {
            navBar.find(".navbar-collapse").collapse('hide');
            $("header").removeClass("active");
        });

    });

    // Main navigation Active Class Add Remove
    $('.navbar-toggler').on('click', function () {
        $("header").toggleClass("active");
        if ($('.search-overlay').hasClass('open')) {
            $(".search-overlay").removeClass('open');
            $(".search_trigger").removeClass('open');
        }
    });

    $(document).on('ready', function () {
        if ($('.header_wrap').hasClass("fixed-top") && !$('.header_wrap').hasClass("transparent_header") && !$('.header_wrap').hasClass("no-sticky")) {
            $(".header_wrap").before('<div class="header_sticky_bar d-none"></div>');
        }
    });

    $(window).on('scroll', function () {
        var scroll = $(window).scrollTop();

        if (scroll >= 150) {
            $('.header_sticky_bar').removeClass('d-none');
            $('header.no-sticky').removeClass('nav-fixed');

        } else {
            $('.header_sticky_bar').addClass('d-none');
        }

    });

    var setHeight = function () {
        var height_header = $(".header_wrap").height();
        $('.header_sticky_bar').css({'height': height_header});
    };

    $(window).on('load', function () {
        setHeight();
    });

    $(window).on('resize', function () {
        setHeight();
    });

    $('.sidetoggle').on('click', function () {
        $(this).addClass('open');
        $('body').addClass('sidetoggle_active');
        $('.sidebar_menu').addClass('active');
        $("body").append('<div id="header-overlay" class="header-overlay"></div>');
    });

    $(document).on('click', '#header-overlay, .sidemenu_close', function () {
        $('.sidetoggle').removeClass('open');
        $('body').removeClass('sidetoggle_active');
        $('.sidebar_menu').removeClass('active');
        $('#header-overlay').fadeOut('3000', function () {
            $('#header-overlay').remove();
        });
        return false;
    });

    $(".categories_btn").on('click', function () {
        $('.side_navbar_toggler').attr('aria-expanded', 'false');
        $('#navbarSidetoggle').removeClass('show');
    });

    $(".side_navbar_toggler").on('click', function () {
        $('.categories_btn').attr('aria-expanded', 'false');
        $('#navCatContent').removeClass('show');
    });

    $(".pr_search_trigger").on('click', function () {
        $(this).toggleClass('show');
        $('.product_search_form').toggleClass('show');
    });

    var rclass = true;

    $("html").on('click', function () {
        if (rclass) {
            $('.categories_btn').addClass('collapsed');
            $('.categories_btn,.side_navbar_toggler').attr('aria-expanded', 'false');
            $('#navCatContent,#navbarSidetoggle').removeClass('show');
        }
        rclass = true;
    });

    $(".categories_btn,#navCatContent,#navbarSidetoggle .navbar-nav,.side_navbar_toggler").on('click', function () {
        rclass = false;
    });

    /*===================================*
    05. SMOOTH SCROLLING JS
    *===================================*/
    // Select all links with hashes

    var topheaderHeight = $(".top-header").innerHeight();
    var mainheaderHeight = $(".header_wrap").innerHeight();
    var headerHeight = mainheaderHeight - topheaderHeight - 20;
    $('a.page-scroll[href*="#"]:not([href="#"])').on('click', function () {
        $('a.page-scroll.active').removeClass('active');
        $(this).closest('.page-scroll').addClass('active');
        // On-page links
        if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
            // Figure out element to scroll to
            var target = $(this.hash),
                speed = $(this).data("speed") || 800;
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');

            // Does a scroll target exist?
            if (target.length) {
                // Only prevent default if animation is actually gonna happen
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - headerHeight
                }, speed);
            }
        }
    });
    $(window).on('scroll', function () {
        var lastId,
            // All list items
            menuItems = $(".header_wrap").find("a.page-scroll"),
            topMenuHeight = $(".header_wrap").innerHeight() + 20,
            // Anchors corresponding to menu items
            scrollItems = menuItems.map(function () {
                var items = $($(this).attr("href"));
                if (items.length) {
                    return items;
                }
            });
        var fromTop = $(this).scrollTop() + topMenuHeight;

        // Get id of current scroll item
        var cur = scrollItems.map(function () {
            if ($(this).offset().top < fromTop)
                return this;
        });
        // Get the id of the current element
        cur = cur[cur.length - 1];
        var id = cur && cur.length ? cur[0].id : "";

        if (lastId !== id) {
            lastId = id;
            // Set/remove active class
            menuItems.closest('.page-scroll').removeClass("active").end().filter("[href='#" + id + "']").closest('.page-scroll').addClass("active");
        }

    });

    $('.more_slide_open').slideUp();
    $('.more_categories').on('click', function () {
        $(this).toggleClass('show');
        $('.more_slide_open').slideToggle();
    });

    /*===================================*
    06. SEARCH JS
    *===================================*/

    $(".close-search").on("click", function () {
        $(".search_wrap,.search_overlay").removeClass('open');
        $("body").removeClass('search_open');
    });

    var removeClass = true;
    $(".search_wrap").after('<div class="search_overlay"></div>');
    $(".search_trigger").on('click', function () {
        $(".search_wrap,.search_overlay").toggleClass('open');
        $("body").toggleClass('search_open');
        removeClass = false;
        if ($('.navbar-collapse').hasClass('show')) {
            $(".navbar-collapse").removeClass('show');
            $(".navbar-toggler").addClass('collapsed');
            $(".navbar-toggler").attr("aria-expanded", false);
        }
    });
    $(".search_wrap form").on('click', function () {
        removeClass = false;
    });
    $("html").on('click', function () {
        if (removeClass) {
            $("body").removeClass('open');
            $(".search_wrap,.search_overlay").removeClass('open');
            $("body").removeClass('search_open');
        }
        removeClass = true;
    });

    /*===================================*
    07. SCROLLUP JS
    *===================================*/
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 150) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        }
    });

    $(".scrollup").on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, 600);
        return false;
    });

    /*===================================*
    09. MASONRY JS
    *===================================*/
    $(window).on("load", function () {
        var $grid_selectors = $(".grid_container");
        if ($grid_selectors.length) {
            var filter_selectors = ".grid_filter > li > a";
            if ($grid_selectors.length > 0) {
                $grid_selectors.imagesLoaded(function () {
                    if ($grid_selectors.hasClass("masonry")) {
                        $grid_selectors.isotope({
                            itemSelector: '.grid_item',
                            percentPosition: true,
                            layoutMode: "masonry",
                            masonry: {
                                columnWidth: '.grid-sizer'
                            },
                        });
                    } else {
                        $grid_selectors.isotope({
                            itemSelector: '.grid_item',
                            percentPosition: true,
                            layoutMode: "fitRows",
                        });
                    }
                });
            }

            //isotope filter
            $(document).on("click", filter_selectors, function () {
                $(filter_selectors).removeClass("current");
                $(this).addClass("current");
                var dfselector = $(this).data('filter');
                if ($grid_selectors.hasClass("masonry")) {
                    $grid_selectors.isotope({
                        itemSelector: '.grid_item',
                        layoutMode: "masonry",
                        masonry: {
                            columnWidth: '.grid_item'
                        },
                        filter: dfselector
                    });
                } else {
                    $grid_selectors.isotope({
                        itemSelector: '.grid_item',
                        layoutMode: "fitRows",
                        filter: dfselector
                    });
                }
                return false;
            });

            $('.portfolio_filter').on('change', function () {
                $grid_selectors.isotope({
                    filter: this.value
                });
            });

            $(window).on("resize", function () {
                setTimeout(function () {
                    $grid_selectors.find('.grid_item').removeClass('animation').removeClass('animated'); // avoid problem to filter after window resize
                    $grid_selectors.isotope('layout');
                }, 300);
            });
        }
    });

    $('.link_container').each(function () {
        $(this).magnificPopup({
            delegate: '.image_popup',
            type: 'image',
            mainClass: 'mfp-zoom-in',
            removalDelay: 500,
            gallery: {
                enabled: true
            }
        });
    });

    /*===================================*
    10. SLIDER JS
    *===================================*/
    function carousel_slider() {
        $('.carousel_slider').each(function () {
            var $carousel = $(this);
            $carousel.owlCarousel({
                dots: $carousel.data("dots"),
                loop: $carousel.data("loop"),
                items: $carousel.data("items"),
                margin: $carousel.data("margin"),
                mouseDrag: $carousel.data("mouse-drag"),
                touchDrag: $carousel.data("touch-drag"),
                autoHeight: $carousel.data("autoheight"),
                center: $carousel.data("center"),
                nav: $carousel.data("nav"),
                rewind: $carousel.data("rewind"),
                navText: ['<i class="ion-ios-arrow-left"></i>', '<i class="ion-ios-arrow-right"></i>'],
                autoplay: $carousel.data("autoplay"),
                animateIn: $carousel.data("animate-in"),
                animateOut: $carousel.data("animate-out"),
                autoplayTimeout: $carousel.data("autoplay-timeout"),
                smartSpeed: $carousel.data("smart-speed"),
                responsive: $carousel.data("responsive")
            });
        });
    }

    function slick_slider() {
        $('.slick_slider').each(function () {
            var $slick_carousel = $(this);
            $slick_carousel.slick({
                arrows: $slick_carousel.data("arrows"),
                dots: $slick_carousel.data("dots"),
                infinite: $slick_carousel.data("infinite"),
                centerMode: $slick_carousel.data("center-mode"),
                vertical: $slick_carousel.data("vertical"),
                fade: $slick_carousel.data("fade"),
                cssEase: $slick_carousel.data("css-ease"),
                autoplay: $slick_carousel.data("autoplay"),
                verticalSwiping: $slick_carousel.data("vertical-swiping"),
                autoplaySpeed: $slick_carousel.data("autoplay-speed"),
                speed: $slick_carousel.data("speed"),
                pauseOnHover: $slick_carousel.data("pause-on-hover"),
                draggable: $slick_carousel.data("draggable"),
                slidesToShow: $slick_carousel.data("slides-to-show"),
                slidesToScroll: $slick_carousel.data("slides-to-scroll"),
                asNavFor: $slick_carousel.data("as-nav-for"),
                focusOnSelect: $slick_carousel.data("focus-on-select"),
                responsive: $slick_carousel.data("responsive")
            });
        });
    }


    $(document).ready(function () {
        carousel_slider();
        slick_slider();
    });
    /*===================================*
    11. CONTACT FORM JS
    *===================================*/
    $("#submitButton").on("click", function (event) {
        event.preventDefault();
        var mydata = $("form").serialize();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "contact.php",
            data: mydata,
            success: function (data) {
                if (data.type === "error") {
                    $("#alert-msg").removeClass("alert, alert-success");
                    $("#alert-msg").addClass("alert, alert-danger");
                } else {
                    $("#alert-msg").addClass("alert, alert-success");
                    $("#alert-msg").removeClass("alert, alert-danger");
                    $("#first-name").val("Enter Name");
                    $("#email").val("Enter Email");
                    $("#phone").val("Enter Phone Number");
                    $("#subject").val("Enter Subject");
                    $("#description").val("Enter Message");

                }
                $("#alert-msg").html(data.msg);
                $("#alert-msg").show();
            },
            error: function (xhr, textStatus) {
                alert(textStatus);
            }
        });
    });

    /*===================================*
    12. POPUP JS
    *===================================*/
    $('.content-popup').magnificPopup({
        type: 'inline',
        preloader: true,
        mainClass: 'mfp-zoom-in',
    });

    $('.image_gallery').each(function () { // the containers for all your galleries
        $(this).magnificPopup({
            delegate: 'a', // the selector for gallery item
            type: 'image',
            gallery: {
                enabled: true,
            },
        });
    });

    $('.popup-ajax').magnificPopup({
        type: 'ajax',
        callbacks: {
            ajaxContentAdded: function () {
                carousel_slider();
                slick_slider();
            }
        }
    });

    $('.video_popup, .iframe_popup').magnificPopup({
        type: 'iframe',
        removalDelay: 160,
        mainClass: 'mfp-zoom-in',
        preloader: false,
        fixedContentPos: false
    });

    /*===================================*
    13. Select dropdowns
    *===================================*/

    if ($('select').length) {
        // Traverse through all dropdowns
        $.each($('select'), function (i, val) {
            var $el = $(val);

            if ($el.val() === "") {
                $el.addClass('first_null');
            }

            if (!$el.val()) {
                $el.addClass('not_chosen');
            }

            $el.on('change', function () {
                if (!$el.val())
                    $el.addClass('not_chosen');
                else
                    $el.removeClass('not_chosen');
            });

        });
    }

    /*==============================================================
    14. FIT VIDEO JS
    ==============================================================*/
    if ($(".fit-videos").length > 0) {
        $(".fit-videos").fitVids({
            customSelector: "iframe[src^='https://w.soundcloud.com']"
        });
    }

    /*==============================================================
    15. DROPDOWN JS
    ==============================================================*/
    if ($(".custome_select").length > 0) {
        $(document).on('ready', function () {
            $(".custome_select").msDropdown();
        });
    }


    /*===================================*
    17. COUNTDOWN JS
    *===================================*/
    $('.countdown_time').each(function () {
        var endTime = $(this).data('time');
        $(this).countdown(endTime, function (tm) {
            $(this).html(tm.strftime('<div class="countdown_box"><div class="countdown-wrap"><span class="countdown days">%D </span><span class="cd_text">Days</span></div></div><div class="countdown_box"><div class="countdown-wrap"><span class="countdown hours">%H</span><span class="cd_text">Hours</span></div></div><div class="countdown_box"><div class="countdown-wrap"><span class="countdown minutes">%M</span><span class="cd_text">Minutes</span></div></div><div class="countdown_box"><div class="countdown-wrap"><span class="countdown seconds">%S</span><span class="cd_text">Seconds</span></div></div>'));
        });
    });

    /*===================================*
    18. List Grid JS
    *===================================*/
    $(document).on('click', '.shorting_icon', function () {
        if ($(this).hasClass('grid')) {
            $('.shop_container').removeClass('list').addClass('grid');
            $(this).addClass('active').siblings().removeClass('active');
        } else if ($(this).hasClass('list')) {
            $('.shop_container').removeClass('grid').addClass('list');
            $(this).addClass('active').siblings().removeClass('active');
        }
        $(".shop_container").append('<div class="loading_pr"><div class="mfp-preloader"></div></div>');
        setTimeout(function () {
            $('.loading_pr').remove();
        }, 500);
    });

    /*===================================*
    19. TOOLTIP JS
    *===================================*/
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({
            trigger: 'hover',
        });
    });
    $(function () {
        $('[data-toggle="popover"]').popover();
    });

    /*===================================*
    20. PRODUCT COLOR JS
    *===================================*/
    $('.product_color_switch span').each(function () {
        var get_color = $(this).attr('data-color');
        $(this).css("background-color", get_color);
    });

    $('.product_color_switch span,.product_size_switch span').on("click", function () {
        $(this).siblings(this).removeClass('active').end().addClass('active');
    });

    /*===================================*
    21. QUICK VIEW POPUP + ZOOM IMAGE + PRODUCT SLIDER JS
    *===================================*/

    var zoomProductImage = function () {
        var image = $('#product_img');
        var zoomActive = false;

        zoomActive = !zoomActive;
        if (zoomActive) {
            if ($(image).length > 0) {
                $(image).elevateZoom({
                    cursor: 'crosshair',
                    easing: true,
                    gallery: 'pr_item_gallery',
                    zoomType: 'inner',
                    galleryActiveClass: 'active'
                });
            }
        } else {
            $.removeData(image, 'elevateZoom');//remove zoom instance from image
            $('.zoomContainer:last-child').remove();// remove zoom container from DOM
        }
    }

    $.magnificPopup.defaults.callbacks = {
        open: function () {
            $('body').addClass('zoom_image');
        },
        close: function () {
            // Wait until overflow:hidden has been removed from the html tag
            setTimeout(function () {
                $('body').removeClass('zoom_image').removeClass('zoom_gallery_image');
                $('.zoomContainer:last-child').remove();// remove zoom container from DOM
                $('.zoomContainer').slice(1).remove();
            }, 100);
        }
    };

    // Set up gallery on click
    var galleryZoom = $('#pr_item_gallery');
    galleryZoom.magnificPopup({
        delegate: 'a',
        type: 'image',
        gallery: {
            enabled: true
        },
        callbacks: {
            elementParse: function (item) {
                item.src = item.el.attr('data-zoom-image');
            }
        }
    });

    // Zoom image when click on icon
    $('.product_img_zoom').on('click', function () {
        var actual = $('#pr_item_gallery a').attr('data-zoom-image');
        $('body').addClass('zoom_gallery_image');
        $('#pr_item_gallery .item').each(function () {
            if (actual == $(this).find('.product_gallery_item').attr('data-zoom-image')) {
                return galleryZoom.magnificPopup('open', $(this).index());
            }
        });
    });

    $('.plus').on('click', function () {
        if ($(this).prev().val()) {
            $(this).prev().val(+$(this).prev().val() + 1);
        }
    });
    $('.minus').on('click', function () {
        if ($(this).next().val() > 1) {
            if ($(this).next().val() > 1) $(this).next().val(+$(this).next().val() - 1);
        }
    });

    /*===================================*
   22. PRICE FILTER JS
   *===================================*/
    $(document).ready(function () {
        function number_format(number, decimals, dec_point, thousands_sep) {
            let n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                toFixedFix = function (n, prec) {
                    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                    let k = Math.pow(10, prec);
                    return Math.round(n * k) / k;
                },
                s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');

            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }

            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        var $filter_selector = $('#price_filter');
        if ($filter_selector.length) {
            var a = $filter_selector.data('min-value');
            var b = $filter_selector.data('max-value');
            var c = $filter_selector.data('price-sign');
            var currentExchangeRate = parseFloat($('div[data-current-exchange-rate]').data('current-exchange-rate'));
            var isPrefixSymbol = $('div[data-is-prefix-symbol]').data('is-prefix-symbol');
            $filter_selector.slider({
                range: true,
                min: $filter_selector.data('min'),
                max: $filter_selector.data('max'),
                values: [a, b],
                slide: function (event, ui) {
                    var from = number_format(ui.values[0] * currentExchangeRate);
                    var to = number_format(ui.values[1] * currentExchangeRate);

                    if (isPrefixSymbol == '1') {
                        from = c + from;
                        to = c + to;
                    } else {
                        from = from + c;
                        to = to + c;
                    }

                    $('#flt_price').html(from + ' - ' + to);
                    $('#price_first').val(ui.values[0]);
                    $('#price_second').val(ui.values[1]);
                },
                stop: function () {
                    $('#price_filter').closest('form').submit();
                }
            });
            var from = number_format($filter_selector.slider('values', 0) * currentExchangeRate);
            var to = number_format($filter_selector.slider('values', 1) * currentExchangeRate);

            if (isPrefixSymbol == '1') {
                from = c + from;
                to = c + to;
            } else {
                from = from + c;
                to = to + c;
            }
            $('#flt_price').html(from + ' - ' + to);
        }
    });

    /*===================================*
    23. RATING STAR JS
    *===================================*/
    $(document).ready(function () {
        $('.star_rating span').on('click', function () {
            var onStar = parseFloat($(this).data('value'), 10); // The star currently selected
            var stars = $(this).parent().children('.star_rating span');
            for (var i = 0; i < stars.length; i++) {
                $(stars[i]).removeClass('selected');
            }
            for (i = 0; i < onStar; i++) {
                $(stars[i]).addClass('selected');
            }

            $(this).closest('form').find('input[name=star]').val(onStar);
        });
    });

    $(document).ready(function () {
        zoomProductImage();
    });

})(jQuery);
