require(['jquery', 'jquery/ui'], function($) {

    //navigation border move for menu
    var menu = $(".navigation");
    var indicator = $('<span class="indicator"></span>');
    menu.append(indicator);

    setTimeout(function() {
        indicator.css("opacity", 1);
    }, 500);
    menu.find("li").mouseenter(function() {
        indicator.css("display", "block");
        position_indicator($(this));
    });
    menu.find("li").mouseleave(function() {

        indicator.css("display", "none");
    });
    
	function position_indicator(ele) {
        var left = ele.offset().left - 0;
        var width = ele.width();
        indicator.stop().animate({
            left: left,
            width: width
        });
    }
	
    //navigation border move
     $(document).ready(function(){
		$("#searchcont").click(function(){
			$("#searchcontainer").addClass('search-down');
		});
		$('.close-search-btn').click(function(){
			$("#searchcontainer").removeClass('search-down');
		});
    
    
	//cart toggle
    $('.cart-btn').click(function() {
        $('.cart-box').toggleClass('cart-show');
    });
    $('.close-cart-toggle').click(function() {
        $('.cart-box').removeClass('cart-show');
    });


    //scroll effects
    $(window).scroll(function() {
        var sticky = $('.navigation'),
                scroll = $(window).scrollTop();
        if (scroll >= 33) {
            sticky.addClass('fixed visible');
        } else {
            sticky.removeClass('fixed visible');
        }
    });
	
    //product filter
    $('.filter a').click(function() {
        var tab_id = $(this).attr('data-tab');
        $('.filter a').removeClass('current');
        $('.tab-content').removeClass('current');
        $(this).addClass('current');
        $("#" + tab_id).addClass('current');
        $("current .item").addClass('is-animated');
    });
	
    //back to top
    $('#back-to-top').on('click', function(e) {
        e.preventDefault();
        $('html,body').animate({
            scrollTop: 0
        }, 700);
    });
    

    var filter = $(".filter_category");
    //$(document).ready(function() {
        
        filter.find("li").click(function() {
            filter.find("li").find(".filter-menu").css({"opacity": 0, "visibility": "hidden"});
            $(this).find(".filter-menu").css({"opacity": 1, "visibility": "visible"});
        });
        $("body").click(function(e) {
            var $target = $(e.target);
            if (!$target.parents().andSelf().is(filter)) {
                $('.filter-menu').css({"opacity": 0, "visibility": "hidden"});
            }
        });

    });


    //banner slider
    require(['jquery', 'jquery/ui', 'jquery.owl.carousel'], function($) {

        $('#owl-demo1').owlCarousel({
            animateOut: 'fadeOut',
            animateIn: 'fadeIn',
            loop: true,
            margin: 10,
            nav: true,
            mouseDrag: true,
            dots: false,
            responsive: {
                0: {items: 1},
                600: {items: 1},
                1000: {items: 1}
            }
        });
        $('#filter-1, #filter-2, #filter-3, #filter-4').owlCarousel({
            loop: true,
            margin: 35,
            nav: true,
            mouseDrag: true,
            dots: true,
            responsive: {
                0: {items: 1},
                400: {items: 1},
                600: {items: 2},
                1000: {items: 4}
            }
        });
        $('#dailydose').owlCarousel({
            loop: true,
            margin: 23,
            nav: true,
            dots: true,
            responsive: {
                0: {items: 1},
                400: {items: 1},
                600: {items: 3},
                1000: {items: 5}
            }
        });
        
    });
});