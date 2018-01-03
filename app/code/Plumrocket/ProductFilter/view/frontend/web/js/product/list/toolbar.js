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
    'productListToolbarForm',
    'Plumrocket_ProductFilter/js/model/url'
], function($, toolbar, url) {

    return {

        rewrite: function() {
            $.mage.productListToolbarForm.prototype.changeUrl = this.changeUrl;
        },

        changeUrl: function (pName, pValue, defValue) {
            var actionUrl = url.getUrl(pName, pValue, defValue, undefined, true);
            $.plumrocket.productfilter.prototype.toolbarAction(actionUrl);
        }
    }

});
