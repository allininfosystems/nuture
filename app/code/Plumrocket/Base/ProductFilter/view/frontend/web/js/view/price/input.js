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
    'Plumrocket_ProductFilter/js/model/price'
], function($, price){
    "use strict";

    $.widget('plumrocket.priceinput', {

        _create: function() {
            $('input', this.element).on('change', $.proxy(this.changeRange, this));
        },

        changeRange: function(event) {


            var from = $('input[data-range-role="from"]', this.element).val(),
                to = $('input[data-range-role="to"]', this.element).val();

            if ($( this.options.slider ).length) {

                if (from > $( this.options.slider ).slider( "option", "max") ) {
                    from = $( this.options.slider ).slider( "option", "max") - 1;
                }

                if (from < $( this.options.slider ).slider( "option", "min")) {
                    from = $( this.options.slider ).slider( "option", "min");
                }

                if (to > $( this.options.slider ).slider( "option", "max") ) {
                    to = $( this.options.slider ).slider( "option", "max");
                }

                if (to < $( this.options.slider ).slider( "option", "min")) {
                    to = $( this.options.slider ).slider( "option", "min") + 1;
                }

                $( this.options.slider ).slider( "option", "values", [ from, to ] );
                $('#filter-price-amount-from').html(from);
                $('#filter-price-amount-to').html(to);
            }

            price.change(from, to);

        }

    });

    return $.plumrocket.priceinput;
});
