define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'

], function (ko, Component, quote, priceUtils, totals) {
    'use strict';
    var show_hide_Gstcharge_blockConfig = window.checkoutConfig.show_hide_Gstcharge_block;
    var cgst_label = window.checkoutConfig.cgst_label;
    var custom_fee_amount = window.checkoutConfig.cgst_charge;
	
    return Component.extend({

        totals: quote.getTotals(),
        canVisibleGstchargeBlock: show_hide_Gstcharge_blockConfig,
        getCgstFormattedPrice: ko.observable(priceUtils.formatPrice(custom_fee_amount, quote.getPriceFormat())),
        getCgstLabel:ko.observable(cgst_label),
        isCgstDisplayed: function () {
			
            return this.getCgstValue() != 0;
			//return canVisibleGstchargeBlock;
        },
        getCgstValue: function() {
            var price = 0;
		
            if (this.totals() && totals.getSegment('cgst_charge')) {
                price = totals.getSegment('cgst_charge').value;
            }
           // return price;
		     return this.getCgstFormattedPrice(priceUtils.formatPrice(price));
        }
    });
});
