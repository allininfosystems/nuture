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
    'jquery',
    "domReady!"
], function($){
    "use strict";

   return {

        auto: true,

        change: function(from, to, notPrsInt) {

            from = parseInt(from);
            if (!notPrsInt) {
                to = parseInt(to);
            }

            from = Math.round(from * 100) / 100;
            to = Math.round(to * 100) / 100;

            var priceSeparator = this.urlModel.isSeoFriendly ? '_' : '-';

            var $ranges = $('#narrow-by-list .item a[data-request="price"]');
            if ($ranges.length) {
                $ranges.removeClass('selected');
            }

            if (this.auto) {
                var url = window.location.href;
                var pValue = from + priceSeparator + to;
                var url = this.urlModel.getUrl('price', pValue);
                this.init.toolbarAction(url);
            } else {

                this.init.showFilterButton();
                this.init.removeSelected('price');

                var oldUrl = this.init.currentUrl;
                this.init.options.selected['price'] = [from + priceSeparator + to];
            }
        },

        changeRange: function (event) {;

            var init = this,
                $item = $(event.currentTarget);
            //Remove all prices from selected
            init.removeSelected('price');
            //Remove selected from all price ranges
            $('#narrow-by-list .item a[data-request="price"]').removeClass('selected')

            //Add current price to selected filters
            $item.addClass('selected');
            init.addSelected('price', $item.data('value'));

            var values = $item.data('value').split(init.options.isSeoFriendly ? '_' : '-');
            values = [parseInt(values[0]), parseInt(values[1])];

            if (!values[1]) {
                values[1] = $('#product-filter-init').data('max-price');
            }

            if (typeof jQuery.plumrocket.priceslider != 'undefined') {
                $('#slider-range').slider( "option", "values", values );
                $('#filter-price-amount-from').html(values[0]);
                $('#filter-price-amount-to').html(values[1]);
            }

            if (typeof jQuery.plumrocket.priceinput != 'undefined') {
                $('#filter-input-price-from').val(values[0]);
                $('#filter-input-price-to').val(values[1]);
            }
        }
    };
});
