/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_ProductStockAlert
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mage.productStockalertAjax', {
        options: {
        },
        _create: function () {
            var self = this;
            $.ajax({
                url: this.options.url,
                dataType: 'json',
                data: {"action" : this.options.action, "productId" : this.options.productId, "statusAvailable" : this.options.statusAvailable},
                type : "post"
            }).done(function (data) {
                $(self.element).html(data.html);
                $(self.element).trigger('contentUpdated');
            })
        }
    });

    return $.mage.productStockalertAjax;
});
