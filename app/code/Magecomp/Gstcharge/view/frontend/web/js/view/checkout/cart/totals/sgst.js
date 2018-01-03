define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'

], function (ko, Component, quote, priceUtils, totals) {
    'use strict';
    var show_hide_sgst_blockConfig = window.checkoutConfig.show_hide_sgst_block;
    var sgst_label = window.checkoutConfig.sgst_label;
    var custom_sgst_amount = window.checkoutConfig.sgst_charge;

    return Component.extend({

        totals: quote.getTotals(),
        canVisibleSgstBlock: show_hide_sgst_blockConfig,
        getSgstFormattedPrice: ko.observable(priceUtils.formatPrice(custom_sgst_amount, quote.getPriceFormat())),
        getSgstLabel:ko.observable(sgst_label),
        isSgstDisplayed: function () {
			
            return this.getSgstValue() != 0;
			//return canVisibleSgstBlock;
        },
        getSgstValue: function() {
            var price = 0;

            if (this.totals() && totals.getSegment('sgst_charge')) {
                price = totals.getSegment('sgst_charge').value;
            }
            return this.getSgstFormattedPrice(priceUtils.formatPrice(price));
        }
    });
});
