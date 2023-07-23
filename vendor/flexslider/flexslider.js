// Can also be used with $(document).ready()
jQuery(window).load(function() {
    jQuery('.flexslider').flexslider({
        animation: "slide",
        touch: true,
        directionNav: false,
        smoothHeight: true,
        controlNav: SLIDER_OPTIONS.controlNav,
    });
});
