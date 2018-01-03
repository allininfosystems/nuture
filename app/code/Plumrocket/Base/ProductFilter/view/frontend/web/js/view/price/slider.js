/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Product Filter v3.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    "jquery",
    'Plumrocket_ProductFilter/js/model/price',
    'jquery/ui',
    "domReady!"
], function($, price){
    "use strict";

    $.widget('plumrocket.priceslider', {

        initedElement: $('#product-filter-init'),

        _create: function() {
            var self = this;

            if (!this.initedElement.data('max-price')) {
                this.initedElement.data('max-price', self.options.max)
                this.initedElement.data('min-price', self.options.min)
            }

            try {
                $(this.element).slider({
                    range: true,
                    slide: function (event, ui) {
                        self.changeInputValue(ui.values[ 0 ], ui.values[ 1 ]);
                    },
                    stop: function (event, ui) {
                        price.change(ui.values[ 0 ], ui.values[ 1 ] + 0.01, true);
                    }
                });
            } catch(err) {}

            var values = [];
            if (this.options.request.min) {
                values.push(parseInt(this.options.request.min));
            } else {
                values.push(parseInt(self.options.min));
            }

            if (this.options.request.max) {
                values.push(parseInt(this.options.request.max));
            } else {
                values.push(parseInt(self.options.max));
            }

            $(this.element).slider( "option", "values", values );

            $(this.element).slider( "option", "min", this.initedElement.data('min-price'));
            $(this.element).slider( "option", "max", this.initedElement.data('max-price'));
        },

        changeRangeHtml: function(from, to) {
            $(this.options.amountFrom).html(from);
            $(this.options.amountTo).html(to);
        },

        changeInputValue: function(from, to) {
            // price.change(from,to);
            this.changeRangeHtml(from, to);
            if ($('div[data-filter-type="input"]').length) {
                $(this.options.inputFrom).val(from);
                $(this.options.inputTo).val(to);
            }
        }
    });

    return $.plumrocket.priceslider;
});
