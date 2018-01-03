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
    'jquery'
], function($){
    "use strict";

   return {

        options: {
            swatchLinkClass: 'swatch-option-link-layered',
            swatchAttributeContainer: '.swatch-attribute',
            swatchOptionClass: '.swatch-option'
        },

        //Check is item a configurable swatch
        isSwatch: function(item) {
            return item.hasClass(this.options.swatchLinkClass);
        },

        getItemRequest: function(item) {
            var request = { var: null, value: null};
            if (this.isSwatch(item)) {
                request.var = item.parents(this.options.swatchAttributeContainer).attr('attribute-code');
                request.value = this.isSeoFriendly ? item.find(this.options.swatchOptionClass).attr('option-label') : item.find(this.options.swatchOptionClass).attr('option-id');
            }
            return request;
        },

        addSelected: function(item, res) {
            item.find(this.options.swatchOptionClass).toggleClass('selected');

            return item.find(this.options.swatchOptionClass).hasClass("selected");
        },

        //This function is analog of swatches function
        //Look to SwatchRender widget to method _EmulateSelected
        emulateSelected: function(params)
        {
            var attributeClass = ($.mage.SwatchRenderer) ? $.mage.SwatchRenderer.prototype.options.classes.attributeClass : 'swatch-attribute';

            $('#narrow-by-list').find('.swatch-layered .swatch-option').removeClass('selected');
            $('.product-item-details').find('.swatch-attribute-options .swatch-option').removeClass('selected');

            $.each(params, $.proxy(function (attributeCode, optionId) {

                var optionIds = optionId.split(',');
                $.each(optionIds, function(key, value) {
                    $('.product-item-details').find('.' + attributeClass +
                        '[attribute-code="' + attributeCode + '"] [option-id="' + value + '"]').trigger('click');

                    $('#narrow-by-list').find('.swatch-layered' +
                        '[attribute-code="' + attributeCode + '"] [option-id="' + value + '"]').addClass('selected');

                    $('#narrow-by-list').find('.swatch-layered' +
                        '[attribute-code="' + attributeCode + '"] [option-id="' + value + '"]').parent().addClass('selected');
                });
            }, this));
        }
    };
});
