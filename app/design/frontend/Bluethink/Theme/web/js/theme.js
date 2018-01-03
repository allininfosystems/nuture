require(['jquery', 'jquery/ui', 'jquery.owl.carousel'], function($){

	$("a[href='#']").click(function(e){e.preventDefault();});
/*=====navigation border move for menu =====*/
	var menu = $(".navigation .border-1");
	var indicator = $('<span class="indicator"></span>');
	menu.append(indicator);
	
	if(menu.find(".menu-toggle > ul > li").hasClass("active")){
		position_indicator(menu.find(".menu-toggle > ul > li.active"));	
	}else{
		indicator.stop().animate({
		  left: '50%',
		  width: '0px'
		});		
	}
	setTimeout(function(){indicator.css("opacity", 1);}, 500);
	menu.find(".menu-toggle > ul > li").mouseenter(function(){
		position_indicator($(this));
	});
	menu.find(".menu-toggle > ul > li").mouseleave(function(){
		if(menu.find(".menu-toggle > ul > li").hasClass("active")){
			position_indicator(menu.find(".menu-toggle > ul > li.active"));	
		}else{
			indicator.stop().animate({
			  left: '50%',
			  width: '0px'
			});		
		}
	});	
	function position_indicator(ele){
		var menu_w = menu.offset().left;
		var left = ele.offset().left - menu_w;
		var width = ele.width();
		if(ele !== 'undefined'){
			//alert(menu_w);
			indicator.stop().animate({
			  left: left,
			  width: width
			});		
		}else{
			indicator.stop().animate({
			  left: '0',
			  width: '1px'
			});
		}
	}
/*=====navigation border move =====*/
	 $(document).ready(function(){
		$("#searchcont").click(function(){
			$("#searchcontainer").addClass('search-down');
			$("#searchContent").focus(); 
		});
		$('.close-search-btn').click(function(){
			$("#searchcontainer").removeClass('search-down');
		});
		
		/*======cart toggle===*/
		$('.cart-btn').click(function(){
			//$('.cart-box').toggleClass('cart-show');
			$('.cart-box').slideToggle();
		});
		$('.close-cart-toggle').click(function(){
			//$('.cart-box').removeClass('cart-show');
			$('.cart-box').slideUp();
		});
		$(document).on('click', function (e) {
			if(!$(e.target).is('.cart-btn b, .cart-btn') && !$(".cart-box").has(e.target).length){		
				//$('.cart-box').removeClass('cart-show');
				$('.cart-box').slideUp();
			}  
		});
		
		/*====product filter=====*/
		$('.filter a').click(function(){
			var tab_id = $(this).attr('data-tab');	
			$('.filter a').removeClass('current');
			$('.tab-content').removeClass('current');	
			$(this).addClass('current');
			$("#"+tab_id).addClass('current');
			$("current .item").addClass('is-animated');
			
			$(function () {
			  var $grid = $('.blog-list, .blog-list1, .blog-list2');
			  var $item = $('.blog-item');
			  grid = new Muuri({
				container: $grid.get(0),
				items: $item,
			  });
			});
		});

		
    });	


	/*========= back to top ===*/
	$(function(){
		var b = $('#back-to-top');
		//b.hide();
		b.on('click',function(e){
			e.preventDefault();
			$('html,body').animate({
				scrollTop: 0
			}, 700);
		});
		
		var $win = $(window);		
		$win.scroll(function () {	
			var footer_pos = 	($(".footer").offset().top - $(window).innerHeight()) - $(document).scrollTop();
			if ($win.scrollTop() > 300) {
				b.fadeIn();
				//console.log("fadding in")
			} 
			else {
				b.fadeOut();
			}
			
			if(footer_pos < 0){
				b.css("position","absolute");
			}else{
				b.css("position","fixed");
			}
		});
	});
	
	
	// Hide Header on on scroll down
	var didScroll;
	var lastScrollTop = 0;
	var delta = 500;
	var navbarHeight = $('.navigation').outerHeight();
	var sticky = $('.navigation');	
	$(window).scroll(function(event){
		didScroll = true;
	});
	
	setInterval(function() {
		if (didScroll) {
			hasScrolled();
			didScroll = false;
		}
	}, 250);
	
	function hasScrolled() {
		var st = $(this).scrollTop();		
		
		if(st <= 90) {
			sticky.removeClass('fixed');
		} else {			
			sticky.addClass('fixed');			
		}		
		if (st <= 90){				
			//sticky.addClass('fixed');
			sticky.removeClass('visible');
		}else{		
			sticky.addClass('visible');
		}
		
		lastScrollTop = st;
	} 
	
	$('.toggle-button').click(function(){
		$(this).toggleClass('open');
		$('.menu-toggle').slideToggle();
	});
	
	
	var filter = $(".filter_category");
	$(document).ready(function() {

		filter.find("li").click(function(){
			filter.find("li").find(".filter-menu").css({"opacity":0,"visibility":"hidden"});
			$(this).find(".filter-menu").css({"opacity":1,"visibility":"visible"});
		});
		
		$(document).on('click', function (e) {
			if(!$(".filter-menu").has(e.target).length && !$(".filter_category").has(e.target).length){		
				//$('.cart-box').removeClass('cart-show');
				$('.filter-menu').css({"opacity":0,"visibility":"hidden"});
			}  
		});			

    });
	
	jQuery('#brands').owlCarousel({
		loop:true,
		margin:23,
		nav:false,
		dots: true,
		autoplay:true,
		autoplayTimeout:1000,
    	autoplayHoverPause:true,
		responsive:{
			0:{ items:2  },
			400:{ items:2 },
			600:{ items:2 },
			1000:{ items:6 }
		}
	});
	
	//
	var didScroll;
	var lastScrollTop = 0;
	var delta = 5;
	var navbarHeight = jQuery('.nav-opens').outerHeight();

	jQuery(window).scroll(function(event){
		didScroll = true;
	});

	setInterval(function() {
		if (didScroll) {
			hasScrolled();
			didScroll = false;
		}
	}, 250);

	function hasScrolled() {
		var st = jQuery(this).scrollTop();
		

		if(Math.abs(lastScrollTop - st) <= delta)
			return;
		

		if (st > lastScrollTop && st > navbarHeight){
			// Scroll Down
		   jQuery('.nav-opens').removeClass('nav-down').addClass('nav-up');
		} else {
			// Scroll Up
			if(st + jQuery(window).height() < jQuery(document).height()) {
			   jQuery('.nav-opens').removeClass('nav-up').addClass('nav-down');
			}
		}
		
		lastScrollTop = st; 
	}
	
});




