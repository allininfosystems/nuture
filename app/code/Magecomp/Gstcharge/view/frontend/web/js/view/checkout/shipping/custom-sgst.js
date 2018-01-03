
define([
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils'

    ], function (ko, Component, quote, priceUtils) {
        'use strict';
        var show_hide_Gstcharge_blockConfig = window.checkoutConfig.show_hide_sgst_shipblock;
        var sgst_label = window.checkoutConfig.sgst_label;         
        var custom_fee_amount = window.checkoutConfig.sgst_charge;
        
        return Component.extend({
            defaults: {
                template: 'Magecomp_Gstcharge/checkout/shipping/custom-sgst'
            },
            canVisibleGstchargeBlock: show_hide_Gstcharge_blockConfig,
            getSgstFormattedPrice: ko.observable(priceUtils.formatPrice(custom_fee_amount, quote.getPriceFormat())),
            getSgstLabel:ko.observable(sgst_label)
        });
    });
