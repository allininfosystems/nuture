define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'

], function (ko, Component, quote, priceUtils, totals) {
    'use strict';
    var show_hide_Shipping_Cgstcharge_block = window.checkoutConfig.show_hide_Shipping_Cgstcharge_block;
    var shipping_cgst_label = window.checkoutConfig.shipping_cgst_label;
    var custom_fee_amount = window.checkoutConfig.shipping_cgst_charge;
	
    return Component.extend({

        totals: quote.getTotals(),
        canVisibleShippingCgstchargeBlock: show_hide_Shipping_Cgstcharge_block,
        getShippingCgstFormattedPrice: ko.observable(priceUtils.formatPrice(custom_fee_amount, quote.getPriceFormat())),
        getShippingCgstLabel:ko.observable(shipping_cgst_label),
        isShippingCgstDisplayed: function () {
			
            return this.getShippingCgstValue() != 0;
		   //return canVisibleShippingCgstchargeBlock;
        },
        getShippingCgstValue: function() {
            var price = 0;
			
            if (this.totals() && totals.getSegment('shipping_cgst_charge')) {
                price = totals.getSegment('shipping_cgst_charge').value;
				
            }
           // return price;
		     return this.getShippingCgstFormattedPrice(priceUtils.formatPrice(price));
        }
    });
});
