define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'

], function (ko, Component, quote, priceUtils, totals) {
    'use strict';
    var show_hide_Shipping_Igstcharge_block = window.checkoutConfig.show_hide_Shipping_Igstcharge_block;
    var shipping_igst_label = window.checkoutConfig.shipping_igst_label;
    var custom_shipping_igst_amount = window.checkoutConfig.shipping_igst_charge;
	
    return Component.extend({

        totals: quote.getTotals(),
        canVisibleShippingIgstchargeBlock: show_hide_Shipping_Igstcharge_block,
        getShippingIgstFormattedPrice: ko.observable(priceUtils.formatPrice(custom_shipping_igst_amount, quote.getPriceFormat())),
        getShippingIgstLabel:ko.observable(shipping_igst_label),
        isShippingIgstDisplayed: function () {
			
            return this.getShippingIgstValue() != 0;
			//return canVisibleShippingIgstchargeBlock;
        },
        getShippingIgstValue: function() {
            var price = 0;
		
            if (this.totals() && totals.getSegment('shipping_igst_charge')) {
                price = totals.getSegment('shipping_igst_charge').value;
				
            }
           // return price;
		     return this.getShippingIgstFormattedPrice(priceUtils.formatPrice(price));
        }
    });
});
