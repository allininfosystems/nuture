define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'

], function (ko, Component, quote, priceUtils, totals) {
    'use strict';
    var show_hide_Shipping_Sgstcharge_block = window.checkoutConfig.show_hide_Shipping_Sgstcharge_block;
    var shipping_sgst_label = window.checkoutConfig.shipping_sgst_label;
    var custom_shipping_sgst_amount = window.checkoutConfig.shipping_sgst_charge;
	
    return Component.extend({ 

        totals: quote.getTotals(),
        canVisibleShippingSgstchargeBlock: show_hide_Shipping_Sgstcharge_block,
        getShippingSgstFormattedPrice: ko.observable(priceUtils.formatPrice(custom_shipping_sgst_amount, quote.getPriceFormat())),
        getShippingSgstLabel:ko.observable(shipping_sgst_label),
        isShippingSgstDisplayed: function () {
			
            return this.getShippingSgstValue() != 0;
			//return canVisibleShippingSgstchargeBlock;
        },
        getShippingSgstValue: function() {
            var price = 0;
			
            if (this.totals() && totals.getSegment('shipping_sgst_charge')) {
                price = totals.getSegment('shipping_sgst_charge').value;
				
            }
           // return price;
		     return this.getShippingSgstFormattedPrice(priceUtils.formatPrice(price));
        }
    });
});
