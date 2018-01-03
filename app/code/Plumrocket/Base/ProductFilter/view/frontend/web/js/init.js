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
    "Plumrocket_ProductFilter/js/model/url",
    'Plumrocket_ProductFilter/js/model/processor',
    'Plumrocket_ProductFilter/js/product/list/toolbar',
    'Plumrocket_ProductFilter/js/model/swatch',
    'Plumrocket_ProductFilter/js/model/price',
    "domReady!"
], function ($, url, processor, toolbar, swatch, price) {
    "use strict";

    $.widget('plumrocket.productfilter', {


        options : {
            filterButton: '#manual-filter',
            selected: {}
        },

        _create: function () {

            if (this.options.filterItemSelector) {
                var self = this;

                //Set pareameters for other js
                price.auto = this.options.auto;
                price.init = this;
                price.urlModel = url;

                url.separator = this.options.filterParamSeparator;
                url.isSeoFriendly = this.options.isSeoFriendly;
                url.categoryUrlSufix = this.options.categoryUrlSufix;

                //Rewrite toolbar function for getting url for toolbar item
                toolbar.rewrite();

                swatch.isSeoFriendly = this.options.isSeoFriendly;
                if (this.options.isSeoFriendly) {
                    //Emulate seleted parametes for swatches
                    //Emulate necessary only if seo friendly enabled
                    //In other case logic of magento working
                    swatch.emulateSelected(this.options.realParams);
                }

                processor.init(this);
                if (this.options.auto) {
                    $(this.options.filterItemSelector).on('click',$.proxy(this._run, this));
                } else {
                    $(this.options.filterItemSelector).on('click',$.proxy(this._add, this));
                }

                $(this.options.actionsSelector).on('click',$.proxy(this._action, this));
                $(this.options.clearButton).on('click',$.proxy(this._clearAction, this));
                $(this.options.removeFilterLink).on('click',$.proxy(this._removeItemAction, this));

                $('#narrow-by-list .item a[data-request="price"]').on('click', $.proxy(price.changeRange, this));


                var containerCollapsible = $(self.options.filterSelector).find('[data-role="collapsible"]');

                if ($(window).width() <= 750 ) {
                    try {
                        containerCollapsible.collapsible('deactivate');
                    } catch(e) {}
                }

                if ($('body').hasClass('ppf-pos-toolbar')) {
                    $(document).mouseup(function (e)
                    {
                        if (!containerCollapsible.is(e.target) // if the target of the click isn't the container...
                            && containerCollapsible.has(e.target).length === 0) // ... nor a descendant of the container
                        {
                            try {
                                containerCollapsible.collapsible('deactivate');
                            } catch (e) {}
                        }
                    });
                }
            }
        },

        addSelected: function (varName, value)
        {
            if (!this.options.selected[varName]) {
                this.options.selected[varName] = [];
            }

            var index = this.options.selected[varName].indexOf(value);
            if (index > -1) {
                this.options.selected[varName].splice(index, 1);
                return false;
            } else {
                this.options.selected[varName].push(value);
            }

            return true;
        },

        removeSelected: function (varName, value) {
            if (!value) {
                if (this.options.selected[varName]) {
                    delete this.options.selected[varName];
                }
            } else {

                if (!this.options.selected[varName]) {
                    return this;
                }

                var index = this.options.selected[varName].indexOf(value);

                if (this.options.selected[varName][index]) {
                    this.options.selected[varName].splice(index, 1);
                }

                if (!this.options.selected[varName].length) {
                    delete this.options.selected[varName];
                }
            }

            return this;
        },

        _add: function (event)
        {
            var $item = $(event.currentTarget),
                canRemove = $item.hasClass('selected');

            if ($item.data('radio') == true) {
                $item.parents('.filter-options-content').find('.item a').removeClass('selected');
                this.removeSelected($item.data('request'));
            }

            if (swatch.isSwatch($item)) {
                var res = swatch.addSelected($item);
                var request = swatch.getItemRequest($item);

                request.value = url.convertValue(request.value);

                if (res) {
                    this.addSelected(request.var, request.value);
                } else {
                    this.removeSelected(request.var, request.value)
                }
            } else {
                if (canRemove) {
                    $item.removeClass('selected')
                    this.removeSelected($item.data('request'), $item.data('value'));
                } else {
                    $item.addClass('selected')
                    this.addSelected($item.data('request'), $item.data('value'));
                }
            }

            this.showFilterButton();

            return false;
        },

        showFilterButton: function () {
            //Show filter button
            if (!$(this.options.filterButton).is(':visible')) {
                $(this.options.filterButton).show();
                $(this.options.filterButton).on('click', $.proxy(this._manualFilter, this));
            }
        },

        toolbarAction: function (url) {
            processor.run(url);
        },

        _manualFilter: function (event)
        {
            var paramsFromUrl = url.getParamsFromUrl();

            if (paramsFromUrl.length) {
                this.options.selected = $.merge(this.options.selected, paramsFromUrl);
            }

            var self = this;

            var requestUrl = url.getManualUrl(this.options.selected, this.options._currentUrl);
            processor.run(requestUrl);
            return false;
        },

        _run: function (event)
        {
            var $item = $(event.currentTarget);

            $item.hasClass('selected') ? $item.removeClass('selected') : $item.addClass('selected');

            //Fix for disabled configurable swatches
            if ($item.hasClass('swatch-option-link-layered') && $item.find('.disabled').length) {
                return false;
            }

            processor.run($item.attr('href'));
            return false;
        },

        _action: function (event) {
            var $item = $(event.currentTarget);
            var href = $item.attr('href');
            processor.run(href);
            return false;
        },

        _clearAction: function (event) {
            this.options.selected = {};
            this._action(event);

            return false;
        },

        _removeItemAction: function(event) {
            var $item = $(event.currentTarget);
            this.removeSelected($item.data('request'), $item.data('value'));
            this._action(event);

            return false;
        }
    });

    return $.plumrocket.productfilter;
});
