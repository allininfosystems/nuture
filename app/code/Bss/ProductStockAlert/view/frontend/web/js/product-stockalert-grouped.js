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
    "jquery/ui",
    'jquery/validate',
    'mage/translate'
], function($){
    "use strict";
    
    $.widget('mage.productStockalertGrouped', {
        options: {
        },
        _create: function () {
            var self = this;
            this.stockAlertEmail = $(this.options.stockAlertEmail);

            $(this.options.applyButton).on('click', $.proxy(function () {
                var value = $(self.options.stockAlertEmail).val();
                var testRequired = (value === '' || (value == null) || (value.length === 0) || /^\s+$/.test(value));
                var testMail = /^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(value);
                if(testRequired) {
                    $(self.options.elementError).html($.mage.__('This is a required field.'));
                    $(self.options.stockAlertEmail).addClass("mage-error");
                }
                else if(!testMail) {
                    $(self.options.elementError).html($.mage.__('Please enter a valid email address.'));
                    $(self.options.stockAlertEmail).addClass("mage-error");
                }else{
                    $(self.options.elementError).html("");
                    $(self.options.stockAlertEmail).removeClass("mage-error");
                    var url = self.element.attr('action');
                    url = url.replace('productstockalert/add/stock', 'productstockalert/add/stock/stockalert_email/'+value);
                    window.location.replace(url);
                }
                
            }, this));

            $(this.options.cancelButton).on('click', $.proxy(function () {
                var url = this.element.attr('action');
                url = url.replace('productstockalert/add/stock', 'productstockalert/unsubscribe/stock');
                window.location.replace(url);
            }, this));
        }
    });

    return $.mage.productStockalertGrouped;
});
