jQuery(function($) {
    //$('.plcArtSldHoverBg').hide();
    $('.plcArtSldHoverBg').css({opacity:0});
    $('.plcArtSliderPortrait').on('mouseover', function () {
        ///jQuery(this).next('.plcArtSldHoverBg').toggle(1000);
        //$(this).find('.plcArtSldHoverBg').css({opacity:1});
        $(this).find('.plcArtSldHoverBg').animate({opacity: 1}, 'slow', function() {
        });
    });

    $('.plcArtSliderPortrait').on('mouseout', function () {
        console.log('out');
        $(this).find('.plcArtSldHoverBg').animate({opacity: 0}, 'slow', function() {

        });
    });
});