/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'ko',
        'uiComponent',
        'Magento_Customer/js/customer-data'
    ],
    function (ko, Component, customerData, reward) {
        return Component.extend({

            initialize: function () {
                this._super();
                var self = this;
                var cartData = customerData.get('cart');
                cartData.subscribe(function (updatedCart) {
                    self.earnPoint(updatedCart.earnPoint);
                }, this);
            },

            earnPoint: ko.observable(minicartRewardpoints.earnPoint),

            enableReward:function(){
                return minicartRewardpoints.enableReward;
            },
            customerLogin: function(){
                if(minicartRewardpoints.customerLogin){
                    return minicartRewardpoints.customerLogin;
                }else{
                    return false;
                }
            },
            urlRedirectLogin:function(){
                if(minicartRewardpoints.urlRedirectLogin){
                    return minicartRewardpoints.urlRedirectLogin;
                }else{
                    return false;
                }
            },
            getImageHtml:function(){
                if(minicartRewardpoints.getImageHtml){
                    return minicartRewardpoints.getImageHtml;
                }else{
                    return false;
                }
            }

        })

    }
)
