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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    "jquery",
    'Plumrocket_ProductFilter/js/model/full-screan-loader',
    'Plumrocket_ProductFilter/js/model/swatch',
    'Plumrocket_ProductFilter/js/model/url',
    "domReady!"
], function($, loader, swatch, urlModel){
    "use strict";

    return {

        options : {},

        _init: {},

        pushState: true,

        isRunning: false,

        dataCache : {},

        init: function(_init) {
            this.options = _init.options;
            this._init = _init;
        },

        done: function(url, data) {
            var self = this;

            if (data.productlist) {
                if (!self._replaceElements(data.productlist)) {
                    $(self.options.productlistSelector).html(data.productlist);
                }
            }

            if (data.leftnav) {
                if (!self._replaceElements(data.leftnav)) {
                    if ($(self.options.filterSelector).length) {
                        $(self.options.filterSelector).replaceWith(data.leftnav);
                    } else {
                        $(self.options.productlistSelector).prepend(data.leftnav);
                        $('.filter-options').hide(); /* fix */
                    }
                }
            }

            /*Add history to browser address line*/
            try {
                if (self.pushState) {
                    window.history.pushState(url, $(document).find("title").text(), url);
                }
            } catch (e) {
                console.log(e);
            }
            self.pushState = true;

            self._affterAjax(data);
            loader.stopLoader();
            self.isRunning = false;

        },

        run: function(url) {

            var self = this;

            url = urlModel.removePriceFromUrl(url);
            url = urlModel.beforeProcess(url);

            if(self.isRunning) {
                return false;
            }

            loader.startLoader();

            self.isRunning = true;

            if (self.dataCache[url]) {
                self.done(url, self.dataCache[url]);
            } else {
                $.post(url, { 'prfilter_ajax' : true } )
                    .done(function(data){
                        self.dataCache[url] = data;
                        self.done(url, data);
                    });
            }

            return false;
        },

        _replaceElements : function(html) {
            var self = this;
            var items = $(html);
            var selector, classes, cl, item;
            for (var i=0;i<items.length;i++) {
                item = jQuery(items[i]);

                if (item.prop('nodeName') && item.prop('nodeName').toLowerCase() == 'script') {
                    var parent = $(self.options.productlistSelector);
                    if (parent.length == 1) {
                        parent.append(item);
                    } else {
                        $('body').append(item);
                    }
                    continue;
                }

                if (item.attr('id')) {
                    selector = '#' + item.attr('id');
                } else if (item.attr('class')) {
                    classes = item.attr('class').split(' ');
                    selector = '';
                    for(var j=0;j<classes.length;j++) {
                        var cl = $.trim(classes[j]);

                        if (cl.indexOf(':') != -1 || cl.indexOf('.') != -1) {
                            continue;
                        }

                        if (cl) {
                            selector += '.' + cl;
                        }
                    }
                } else {
                    selector = '';
                }

                if (selector) {
                    if ($(selector).length) {
                        $(selector).replaceWith(item);
                    } else {
                        return false;
                    }
                }
            }

            return true;
        },

        _affterAjax: function(data) {
            $('body').trigger('contentUpdated');
            if ($.fn.catalogAddToCart) {
                $("form[data-role='tocart-form']").catalogAddToCart();
            }
            $('.swatch-option-tooltip').hide();
            setTimeout(function(){
                if (swatch && data.realParams) {
                    swatch.emulateSelected(data.realParams)
                }
            }, 1000);

            window.scrollTo(0,0);

            var self = this;
            setTimeout(function(){
                self._init._create();
            }, 500);

            if (window.setGridItemsEqualHeight) {
                setGridItemsEqualHeight($);
            }
        }
    };
});
