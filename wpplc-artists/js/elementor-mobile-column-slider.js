// mobile slider
jQuery(function() {
    $services_slider = jQuery('.plcMobileSlider .elementor-row');

    settings = {
        autoplay: true,
        autoplaySpeed: 5000,
        infinite: true,
        speed: 500,
        arrows: false,
        dots: true,	};
    $services_slider.slick(settings);
});