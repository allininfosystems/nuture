
define([
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils'

    ], function (ko, Component, quote, priceUtils) {
        'use strict';
        var show_hide_igst_shipblockConfig = window.checkoutConfig.show_hide_igst_shipblock;
        var igst_label = window.checkoutConfig.igst_label;         
        var custom_igst_amount = window.checkoutConfig.igst_charge;
       
        return Component.extend({
            defaults: {
                template: 'Magecomp_Gstcharge/checkout/shipping/custom-sgst'
            },
            canVisibleIgstBlock: show_hide_igst_shipblockConfig,
            getIgstFormattedPrice: ko.observable(priceUtils.formatPrice(custom_igst_amount, quote.getPriceFormat())),
            getIgstLabel:ko.observable(igst_label)
        });
    });
