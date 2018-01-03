
define([
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils'

    ], function (ko, Component, quote, priceUtils) {
        'use strict';
        var show_hide_Gstcharge_blockConfig = window.checkoutConfig.show_hide_Gstcharge_shipblock;
        var cgst_label = window.checkoutConfig.cgst_label;         
        var custom_fee_amount = window.checkoutConfig.cgst_charge;
        
        return Component.extend({
            defaults: {
                template: 'Magecomp_Gstcharge/checkout/shipping/custom-cgst'
            },
            canVisibleGstchargeBlock: show_hide_Gstcharge_blockConfig,
            getCgstFormattedPrice: ko.observable(priceUtils.formatPrice(custom_fee_amount, quote.getPriceFormat())),
            getCgstLabel:ko.observable(cgst_label)
        });
    });
