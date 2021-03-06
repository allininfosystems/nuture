 require(['jquery', 'jquery/ui'], function($){ 

/*=====navigation border move for menu =====*/
	var menu = $(".navigation");
	var indicator = $('<span class="indicator"></span>');
	menu.append(indicator);
//	position_indicator(menu.find("li.active"));
	setTimeout(function(){indicator.css("opacity", 1);}, 500);
	menu.find("li").mouseenter(function(){
        indicator.css("display", "block");
		position_indicator($(this));
	});
	menu.find("li").mouseleave(function(){
//		position_indicator(menu.find("li.active"));
        indicator.css("display", "none");
	});	
	function position_indicator(ele){
		var left = ele.offset().left - 0;
		var width = ele.width();
		indicator.stop().animate({
		  left: left,
		  width: width
		});		
	}
/*=====navigation border move =====*/
	$('.search-btn').click(function(){
		$('.search-bar').addClass('search-down');
	});
	$('.close-search-btn').click(function(){
		$('.search-bar').removeClass('search-down');
	});
	/*======cart toggle===*/
	$('.cart-btn').click(function(){
		$('.cart-box').toggleClass('cart-show');
	});
	$('.close-cart-toggle').click(function(){
		$('.cart-box').removeClass('cart-show');
	});
	
	
	/*========scroll effects========*/
	$(window).scroll(function() {
		var sticky = $('.navigation'),
		scroll = $(window).scrollTop();	
		if (scroll >= 33){
			sticky.addClass('fixed');
		}else {
			sticky.removeClass('fixed');
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
	});
	/*=========back to top===*/
	$('#back-to-top').on('click',function(e){
		e.preventDefault();
		$('html,body').animate({
			scrollTop: 0
		}, 700);
	});
	/*=============scroll effects===================*/
	  /*$('.inner-moment-slide').scroll(function () {   	
		var offsetTop = $(".inner-moment-slide").offset().top;
		console.log($(this).scrollTop());
		
		if ($(this).scrollTop() >= 1) {
		  $('.hone').hide('slow');
		  $('.htwo').show('slow');
		} else {
		  $('.htwo').hide('slow');
		  $('.hone').show('slow');
		}
	  });*/
	/*=============menu toggle effects=============*/
//	$('.toggle-button').click(function(){
//		$(this).toggleClass('open');
//		$('.menu-toggle').slideToggle();
//	});
	/*=============dynamic fit position=============*/
	// tooltip javascript
	/*function tooltip(ele){
		$(ele).click(function () {                    
			var elementHeight = $(this).height();  //get the height position of the current object
			var offsetWidth = 10;
			var offsetHeight = 10;
			var toolTipWidth = $("#toolTipContainer").width();  //Get the tool tip container width
			var toolTipHeight = $("#toolTipContainer").height();  //Get the tool tip container height
			var documentWidth = $(document).width();  //Get the document width
			var documentHeight = $(document).height();  //Get the document height                    
			var top = $(this).offset().top; //Set top and bottom position of the tool tip
			if (top + toolTipHeight > documentHeight) {
				// and show up in the correct place
				top = documentHeight - toolTipHeight - offsetHeight - (2 * elementHeight);
			}
			//set  the left and right position of the tool tip
			var left = $(this).offset().left + offsetWidth;
			if (left + toolTipWidth > documentWidth) {
				left = documentWidth - toolTipWidth - (2 * offsetWidth);
			}
			$('#toolTipContainer').css({ 'top': top, 'left': left });
			$('#toolTipContainer').show();
		});
		$("body").click(function(e){
			var $target = $(e.target);
			if (!$(e.target).parents().andSelf().is('a.show-tooltips, #toolTipContainer')) {
				$('#toolTipContainer').hide();
			}
		});				
	}
	// call tooltip function
	$(document).ready(function () {
		tooltip(".filter_category");
	});*/
	
	var filter = $(".filter_category");
	$(document).ready(function() {
        filter.find("li").each(function(){
            /*var ele = $(this);
			var left = ele.offset().left;
			var top = ele.offset().top + 30;
			var width = ele.width();
			$(this).find(".filter-menu").stop().css({
				"left": left,"top": top
			});
			$(window).resize(function(){
				$(this).find(".filter-menu").stop().css({
					"left": left,"top": top
				});
			});*/
        });
		filter.find("li").click(function(){
			filter.find("li").find(".filter-menu").css({"opacity":0,"visibility":"hidden"});
			$(this).find(".filter-menu").css({"opacity":1,"visibility":"visible"});
		});
		$("body").click(function(e){
			var $target = $(e.target);
			if (!$target.parents().andSelf().is(filter)) {
				$('.filter-menu').css({"opacity":0,"visibility":"hidden"});
			}
		});				

    });
	
	
	/*========banner slider=====*/
 require(['jquery', 'jquery/ui'], function($){ 

	$('#owl-demo1').owlCarousel({
		animateOut: 'fadeOut',
		animateIn: 'fadeIn',
		loop:true,
		margin:10,
		nav:true,
		mouseDrag: true,
		dots: false,
		responsive:{
			0:{ items:1 },
			600:{ items:1 },
			1000:{ items:1 }
		}
	});
	$('#filter-1, #filter-2, #filter-3, #filter-4').owlCarousel({
		loop:true,
		margin:35,
		nav:true,
		mouseDrag: true,
		dots: true,
		responsive:{
			 0:{ items:1 },
			400:{ items:1 },
			600:{ items:2 },
			1000:{ items:4 }
		}
	});
	$('#dailydose').owlCarousel({
		loop:true,
		margin:23,
		nav:true,
		dots: true,
		responsive:{
			0:{ items:1 },
			400:{ items:1 },
			600:{ items:3 },
			1000:{ items:5 }
		}
	});
	$('#brands').owlCarousel({
		loop:true,
		margin:23,
		nav:false,
		dots: false,
		responsive:{
			0:{ items:2  },
			400:{ items:2 },
			600:{ items:2 },
			1000:{ items:6 }
		}
	});
 });	
});