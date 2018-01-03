define(
    [
		'ko',
        'Nuluv_Freeshipamt/js/view/checkout/summary/freeshipamt',
		'Magento_Checkout/js/model/quote',
		'Magento_Catalog/js/price-utils',
		'Magento_Checkout/js/model/totals'
    ],
    function (ko, Component,quote,priceUtils, totals) {
        'use strict';

		var custom_fee_amount = 0;

		if (totals.getSegment('freeshipamt'))
		{
			custom_fee_amount=totals.getSegment('freeshipamt').value
		}

		var freeshipamt_label = window.checkoutConfig.freeshipamt_label;

        return Component.extend({

			getFormattedPrice: ko.observable(priceUtils.formatPrice(custom_fee_amount, quote.getPriceFormat())),
			getFeeLabelFreeshipamt:ko.observable(freeshipamt_label),
            isDisplayed: function () {
                return this.isFullMode() && this.getPureValue() != 0;
            }
        });
    }
);