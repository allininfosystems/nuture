/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

/*jshint browser:true jquery:true*/
define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";
    
    $.widget('mage.popupSocials', {
        options: {
           SocialloginPopup: '#social_login_popup',
           CloseButtonPopup: '#sociallogin-close',
           ShowOtherSocialPopup: '#sociallogin-other-a-popup',
           ShowAllOtherSocial: '#sociallogin-other-button-popup'
        },
        _create: function () {
            $(this.options.ShowOtherSocialPopup).on('click', $.proxy(function () {
                
                    if($('#sociallogin-other-a-popup').hasClass('active')){
                       
                         $('#sociallogin-other-a-popup').removeClass('active');
                          $(this.options.ShowAllOtherSocial).hide();
                     }else{ 
                       
;                         $('#sociallogin-other-a-popup').addClass('active');
                         $(this.options.ShowAllOtherSocial).show();
                 }

            }, this));

            $(this.options.CloseButtonPopup).on('click', $.proxy(function () {
                $(this.options.SocialloginPopup).hide();
            }, this));
        }    
    });

    return $.mage.popupSocials;
});