
define([
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils'

    ], function (ko, Component, quote, priceUtils) {
        'use strict';
        var show_hide_ShippingGstcharge_blockConfig = window.checkoutConfig.show_hide_Gstcharge_shipblock;
        var cgst_label = window.checkoutConfig.cgst_label;         
        var custom_fee_amount = window.checkoutConfig.shipping_cgst_charge;
        
        return Component.extend({
            defaults: {
                template: 'Magecomp_Gstcharge/checkout/shipping/shipping-custom-cgst'
            },
            canVisibleShippnigGstchargeBlock: show_hide_ShippingGstcharge_blockConfig,
            getShippingCgstFormattedPrice: ko.observable(priceUtils.formatPrice(custom_fee_amount, quote.getPriceFormat())),
            getShippingCgstLabel:ko.observable(cgst_label)
        });
    });
