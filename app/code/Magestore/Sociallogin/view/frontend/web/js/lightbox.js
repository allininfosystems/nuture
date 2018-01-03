/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

define([
    'prototype'
], function () {

    var Lightboxsocial = Class.create();
            Lightboxsocial.prototype={
                initialize : function(containerDiv) {
                    this.container = containerDiv;
                    if($('bg_fade') == null) {
                    var screen = new Element('div', {'id': 'bg_fade'});
                    document.body.insert({top:screen});
                    }
                    this._hideLayer(this.container);
                },
                open : function () {
                   
                    this._centerWindow(this.container);
                    this._fade('open', this.container);
                },
                close : function () {
                    this._fade('close', this.container);
                },
                _fade : function fadeBg(userAction,whichDiv){
                    if(userAction=='close'){
                        new Effect.Opacity('bg_fade',
                        {duration:.2,
                        from:0.4,
                        to:0,
                        afterFinish:this._makeInvisible,
                        afterUpdate:this._hideLayer(whichDiv)});
                        }else{
                        new Effect.Opacity('bg_fade',
                        {duration:.2,
                        from:0,
                        to:0.3,
                        beforeUpdate:this._makeVisible,
                        afterFinish:this._showLayer(whichDiv)});
                    }
                },
                _makeVisible : function makeVisible(){
                    $("bg_fade").style.visibility="visible";
                },
                _makeInvisible : function makeInvisible(){
                    $("bg_fade").style.visibility="hidden";
                },
                _showLayer : function showLayer(userAction){
                    $(userAction).style.display="block";
                },
                _hideLayer : function hideLayer(userAction){
                    $(userAction).style.display="none";
                },
                _centerWindow : function centerWindow(element) {
                    var windowHeight = parseFloat($(element).getHeight())/2;
                    var windowWidth = parseFloat($(element).getWidth())/2;
                    if(typeof window.innerHeight != 'undefined') {
                        $(element).style.top = Math.round(document.body.offsetTop + ((window.innerHeight - $(element).getHeight()))/2)+'px';
                        $(element).style.left = Math.round(document.body.offsetLeft + ((window.innerWidth - $(element).getWidth()))/2)+'px';
                        } else {
                        $(element).style.top = Math.round(document.body.offsetTop + ((document.documentElement.offsetHeight - $(element).getHeight()))/2)+'px';
                        $(element).style.left = Math.round(document.body.offsetLeft + ((document.documentElement.offsetWidth - $(element).getWidth()))/2)+'px';
                    }
                }
            }; 
        socialLogin = new Lightboxsocial('magestore-popup');    
         Event.observe(window, 'load', function () {
            Event.observe('bg_fade', 'click', function () {
                    socialLogin.close();
                }); 
            });
            Event.observe('sociallogin-close-popup', 'click', function () {
                socialLogin.close();
            }); 
            document.observe("dom:loaded", function() {
                Event.observe(window, 'resize', function () {       
                    socialLogin._centerWindow('magestore-popup');
                    socialLogin._centerWindow('magestore-popup_social');
                }); 
            }); 
            var links = document.links;
            for (i = 0; i < links.length; i++) {
                if (links[i].href.search('/customer/account/login/') != -1 && links[i].href.search('/customer/account/login/#') == -1) {
                    links[i].href = 'javascript:socialLogin.open();';
                    Event.observe(links[i], 'click', function () {
                        $('magestore-create-back').click();
                    $('magestore-login-social').style.display = "block";
                    if ($('sociallogin-other-a-popup')) 
                        $('sociallogin-other-a-popup').style.display = "block";
                    // $('magestore-popup').style.width="706px";
                    });
                }
                if (links[i].href.search('/wishlist/') != -1) {
                    links[i].href = 'javascript:socialLogin.open();';
                    Event.observe(links[i], 'click', function () {
                    $('magestore-login-social').style.display = "block";
                    if ($('sociallogin-other-a-popup')) $('sociallogin-other-a-popup').style.display = "block";
                    // $('magestore-popup').style.width="706px";
                    });
                }
                if (links[i].href.search('/customer/account/') != -1 && !links[i].down('span') && links[i].href.search('/customer/account/login/#') == -1) {
                    links[i].href = 'javascript:socialLogin.open();';
                    Event.observe(links[i], 'click', function () {
                     $('magestore-sociallogin-create-new-customer').click();
                    $('magestore-login-social').style.display = "block";
                    if ($('sociallogin-other-a-popup')) $('sociallogin-other-a-popup').style.display = "block";
                    // $('magestore-popup').style.width="706px";
                    });
                }
            }
            if(document.getElementById('product_comparison')){
            var links = document.links;
            for (i = 0; i < links.length; i++) {
                if (links[i].href.search('/wishlist/') != -1) {     
                    links[i].href = 'javascript:socialLogin.open();';
                }
            }
        }


});