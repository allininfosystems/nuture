define([
    'jquery'
],function ($) {
var activeSlideNo = 0; 		// keep track of the current slide nb
var lastSlideNo = 0;  // number of slides
var onlyonetime=0;
function slide(direction){
	//if( !slide ) return false;
    if ($('.inner-moment-slide').is(':animated')){ return; }            //do not animate it an animation is already in motion
    if ( direction > 0 && activeSlideNo == 0 ) { enableScroll(); return; }    //do not animate backwards if at beginning
    if ( direction < 0 && activeSlideNo == lastSlideNo) { enableScroll(); return; }    //do not animate forward if at the end         
    (direction > 0) ? slide_up(): slide_down();
}

function slide_up(){
    activeSlideNo -= 1;						              //keep track of the current slide nb
    $('.inner-moment-slide').stop().animate(                //animate!
                {'margin-top': "+=" + $('.moment-slide').height()}, 1000);
}
function slide_down(){
    activeSlideNo += 1;							            //keep track of the current slide nb
    $('.inner-moment-slide').stop().animate(                //animate!
                {'margin-top': "-=" + $('.moment-slide').height()}, 1000);
}
$(document).ready(function() {
	lastSlideNo = $('.inner-moment-slide').children().length - 1;	
});
//on resize, recalibrate margin to point to desired (current) slide
$(window).resize(function() {
    $('.inner-moment-slide').css({ marginLeft : -1 * activeSlideNo * $('.slide').width()});    
    /*$('.holder_demonstr').css({ marginLeft : -1 * activeSlideNo * $('.slide').width() -3}); */            
});

var keys = {37: 1, 38: 1, 39: 1, 40: 1};
function preventDefault(e) {
  e = e || window.event;
  if (e.preventDefault)
      e.preventDefault();
  e.returnValue = false;  
}
function preventDefaultForScrollKeys(e) {
    if (keys[e.keyCode]) {
        preventDefault(e);
        return false;
    }
}
function disableScroll() {
  if (window.addEventListener) // older FF
      window.addEventListener('DOMMouseScroll', preventDefault, false);
	  window.onwheel = preventDefault; // modern standard
	  window.onmousewheel = document.onmousewheel = preventDefault; // older browsers, IE
	  window.ontouchmove  = preventDefault; // mobile
	  document.onkeydown  = preventDefaultForScrollKeys;
}

function enableScroll() {
    if (window.removeEventListener)
        window.removeEventListener('DOMMouseScroll', preventDefault, false);
		window.onmousewheel = document.onmousewheel = null; 
		window.onwheel = null; 
		window.ontouchmove = null;  
		document.onkeydown = null;  
}

$(window).on('scroll resize',function(){
	var $window = $(window);
	var window_height = $window.height();
	var window_top_position = $window.scrollTop();
	var window_bottom_position = (window_top_position + window_height);
	
	var $element = $(".moment-slide");
	var element_height = $element.outerHeight();
	var element_top_position = $element.offset().top;
	var element_bottom_position = (element_top_position + element_height);	
	//console.log(element_top_position  + (element_height/2)+ ' -- ' + window_bottom_position);

	if ((element_bottom_position - (element_height) >= window_top_position) && (element_top_position + (element_height) <= window_bottom_position)) {
		disableScroll();
		$('body').on('mousewheel', function(event) {
			slide(event.deltaY);
			//console.log(event.deltaY);
			//$('.moment-slide').css("background","#f00");
		});
		
	} else{
		$('body').on('mousewheel', function(event) {
			slide(event.deltaY);
			//$('.moment-slide').css("background","#f0f");			
		});
		enableScroll();		
	}
});
});