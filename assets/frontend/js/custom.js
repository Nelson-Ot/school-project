(function($) {
    "use strict";

    // Main Slider Carousel
    if ($('.main-slider-carousel').length) {
        $(".owl-carousel").on('translate.owl.carousel', function() {
            $(".slider-text span").removeClass('animated fadeInDown').css('opacity', '0');
            $(".slider-text h1").removeClass('animated fadeInUp').css('opacity', '0');
            $(".slider-text p").removeClass('animated fadeInDown').css('opacity', '0');
            $(".slider-text a").removeClass('animated fadeInUp').css('opacity', '0');
        });

        $('.owl-carousel').on('translated.owl.carousel', function() {
            $(".slider-text span").addClass('animated fadeInDown').css('opacity', "1");
            $(".slider-text h1").addClass('animated fadeInUp').css('opacity', "1");
            $(".slider-text p").addClass('animated fadeInDown').css('opacity', "1");
            $(".slider-text a").addClass('animated fadeInUp').css('opacity', "1")
        });

        $('.main-slider-carousel').owlCarousel({
            loop: true,
            margin: 0,
            nav: true,
            animateOut: 'slideOutDown',
            animateIn: 'fadeInLeft',
            active: true,
            smartSpeed: 1000,
            autoplay: 5000,
            dots: false,
            navText: ['<span class="fas fa-arrow-left"></span>', '<span class="fas fa-arrow-right"></span>'],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                1200: {
                    items: 1
                }
            }
        });
    }

    // Testimonial Slider Carousel
    if ($('.testimonial-carousel').length) {
        $('.testimonial-carousel').owlCarousel({
            dots: true,
            loop: true,
            margin: 30,
            nav: false,
            navText: [
                '<i class="fas fa-chevron-left"></i>',
                '<i class="fas fa-chevron-right"></i>'
            ],
            autoplayHoverPause: false,
            autoplay: 6000,
            smartSpeed: 1000,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                800: {
                    items: 1
                },
                1024: {
                    items: 1
                },
                1100: {
                    items: 2
                },
                1200: {
                    items: 2
                }
            }
        });
    }

    // Remove # From Url
    $('a[href="#"]').on('click', function(e) {
        e.preventDefault();
    });

    // Accordion
    if ($('#accordion-faqs').length) {
        var $active = $('#accordion-faqs .collapse.show').prev().addClass('active');
        $active.find("a").append("<span class=\"fa fa-minus float-end\"></span>");
        $('#accordion-faqs .card-header')
            .not($active)
            .find('a')
            .prepend("<span class=\"fa fa-plus float-end\"></span>");

        $('#accordion-faqs').on('show.bs.collapse', function(e) {
            $('#accordion-faqs .card-header.active')
                .removeClass('active')
                .find('.fa')
                .toggleClass('fa-plus fa-minus');
            $(e.target)
                .prev()
                .addClass('active')
                .find('.fa')
                .toggleClass('fa-plus fa-minus');
        });

        $('#accordion-faqs').on('hide.bs.collapse', function(e) {
            $(e.target)
                .prev()
                .removeClass("active")
                .find(".fa")
                .removeClass("fa-minus")
                .addClass("fa-plus");
        });
    }

    // Teachers Filters
    if ($('#doctors-grid').length) {
        var $grid = $('#doctors-grid');
        $grid.shuffle({
            itemSelector: '.doctors-grid', // the selector for the items in the grid
            speed: 500 // Transition/animation speed (milliseconds)
        });
        /* reshuffle when user clicks a filter item */
        $('#doctors-filter li a').click(function(e) {
            // set active class
            $('#doctors-filter li a').removeClass('active');
            $(this).addClass('active');
            // get group name from clicked item
            var groupName = $(this).attr('data-group');
            // reshuffle grid
            $grid.shuffle('shuffle', groupName);
        });
    }

    // TABS
    $('.nav-tabs a').click(function(e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // Header Sticky
    $(window).on('scroll', function() {
        if ($(window).width() > 991) {
            var header = document.getElementById('strickyMenu');
            var sticky = header.offsetTop;
            if (window.pageYOffset > sticky) {
                $('.stricky').removeClass('fadeIn animated');
                $('.stricky').addClass('stricky-fixed fadeInDown animated');
            } else {
                $('.stricky').removeClass('stricky-fixed fadeInDown animated');
                $('.stricky').addClass('slideIn animated');
            }
        }
    });

    // Scroll To Top
    $(window).on('scroll', function() {
        if (window.pageYOffset > 300) {
            $('.back-to-top').addClass('show');
        } else {
            $('.back-to-top').removeClass('show');
        }
        counter();
    });

    $('.back-to-top').on('click', function(e) {
        e.preventDefault();
        $('html,body').animate({
            scrollTop: 0
        }, 700);
    });

    $(window).on('load', function() {
        // Preloader Fadeout Onload
        $(".loader-container").addClass('loader-fadeout');
    });

    $('#activateSchool').on('change', function(e) {
        var branch_id = $(this).val();
        $.ajax({
            url: base_url + 'home/get_branch_url',
            type: 'POST',
            data: {
                branch_id: branch_id
            },
            dataType: "json",
            success: function(data) {
                window.location.href = data.url_alias;
            }
        });
    });

    // Gallery Magnific Popup
    if ($('.gallery-grid').length) {
        $('.gallery-grid').magnificPopup({
            delegate: 'a.zoom',
            type: 'image',
            gallery: {
                enabled: true
            }
        });

        $('.gallery-grid .popup-video').magnificPopup({
            disableOn: 700,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: true,
            fixedContentPos: false,
            gallery: {
                enabled: true
            }
        });
    }

    $("form.frm-submit-data").each(function(i, el)
    {
        var $this = $(el);
        $this.on('submit', function(e){
            e.preventDefault();
            var btn = $this.find('[type="submit"]');
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: "json",
                contentType: false,
                processData: false,
                cache: false,
                beforeSend: function () {
                    btn.button('loading');
                },
                success: function (data) {
                    $('.error').html("");
                    if (data.status == "fail") {
                        console.log(data.error);
                        $.each(data.error, function (index, value) {
                            $this.find("[name='" + index + "']").parents('.form-group').find('.error').html(value);
                        });
                        btn.button('reset');
                    } else {
                        if (data.url) {
                            window.location.href = data.url;
                        } else if (data.status == "access_denied") {
                            window.location.href = base_url + "dashboard";
                        } else {
                            location.reload(true);
                        }
                    }
                },
                error: function () {
                    btn.button('reset');
                }
            });
        });
    });

    // marquee new
    const $marquee = $('[data-marquee]');
    if ($marquee.length) {
        $marquee.marquee({
            direction: 'left',
            delayBeforeStart: 0,
            duration: 20000,
            pauseOnHover: true,
            startVisible: true,
        });
    }
})(window.jQuery);

// Datepicker
(function($) {
    'use strict';
    if ($.isFunction($.fn['datepicker'])) {
        $(function() {
            $('[data-plugin-datepicker]').each(function() {
                $(this).datepicker({
                    format: "yyyy-mm-dd",
                    orientation: "bottom",
                    autoclose: true,
                    todayHighlight: true
                });
            });
        });
    }
}).apply(this, [jQuery]);

// Select2
(function($) {
    'use strict';
    if ($.isFunction($.fn['select2'])) {
        $(function() {
            $('[data-plugin-selectTwo]').each(function() {
                $(this).select2({
                    width: "100%"
                });
            });
        });
    }
}).apply(this, [jQuery]);

function getSectionByClass(class_id) {
    $.ajax({
        url: base_url + 'ajax/getSectionByClass',
        type: 'POST',
        data: {
            class_id: class_id
        },
        success: function(response) {
            $('#section_id').html(response);
        }
    });
}

// Home Page Counter Widget
function counter() {
    if ($('.counter').length !== 0) {
        var oTop = $('.counter').offset().top - window.innerHeight;
        if ($(window).scrollTop() > oTop) {
            $('.counter').each(function() {
                var $this = $(this),
                    countTo = $this.attr('data-count');
                $({
                    countNum: $this.text()
                }).animate({
                    countNum: countTo
                }, {
                    duration: 1000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $this.text(this.countNum);
                    }
                });
            });
        }
    }
}

// Loading button plugin (removed from BS5)
$(document).ready(function() {
    $.fn.button = function(action) {
        if (action === 'loading' && this.data('loading-text')) {
            this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true);
        }
        if (action === 'reset' && this.data('original-text')) {
            this.html(this.data('original-text')).prop('disabled', false);
        }
    };
});

$(".whatsapp-button").on( "click", function() {
    $('.whatsapp-popup').toggleClass('open');
});

$(".whatsapp-agent").on( "click", function() {
    go_to_whatsapp($(this).attr('data-number'));
});

function go_to_whatsapp(number, text = ""){
    var WhatsAppUrl = 'https://web.whatsapp.com/send';
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        WhatsAppUrl = 'https://api.whatsapp.com/send'; 
    }
    var url = WhatsAppUrl+'?phone='+number;
    if (text !== "") {
        url += '&text='+text;
    }
    var win = window.open(url, '_blank');
    win.focus();
}

function changeCustomUploader(photoElem) {
    var fileName = $(photoElem).val().split('\\').pop();
    $(photoElem).siblings('label').addClass("selected").html(ellipsis(fileName));
}

function ellipsis(str, length, ending) {
    if (length == null) {
      length = 40;
    }
    if (ending == null) {
      ending = '...';
    }
    if (str.length > length) {
      return str.substring(0, length - ending.length) + ending;
    } else {
      return str;
    }
};