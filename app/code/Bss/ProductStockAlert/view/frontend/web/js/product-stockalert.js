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
/*jshint browser:true jquery:true*/
define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";
    
    $.widget('mage.productStockalert', {
        options: {
        },
        _create: function () {
            var self = this;
            this.stockAlertEmail = $(this.options.stockAlertEmail);

            $(this.options.applyButton).on('click', $.proxy(function () {
                this.stockAlertEmail.attr('data-validate', '{required:true,email:true}');
                $(this.element).validation().submit();
            }, this));

            $(this.options.cancelButton).on('click', $.proxy(function () {
                var action = this.element.attr('action');
                action = action.replace('productstockalert/add/stock', 'productstockalert/unsubscribe/stock');
                this.element.attr('action', action);
                this.stockAlertEmail.removeAttr('data-validate');
                this.element.submit();
            }, this));
        }
    });

    return $.mage.productStockalert;
});
