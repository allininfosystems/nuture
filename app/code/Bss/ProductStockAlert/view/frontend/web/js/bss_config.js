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
    'jquery',
    'mage/translate',
    'jquery/ui'
], function($, $t) {
    "use strict";

    $.widget('mage.catalogAddToCart', {
        _create: function() {
            $('.product-item .stock.unavailable').append(function(){
                $(this).append(
                    '<button type="button" title="Notify Me" class="product_alert_notify action primary"><span>Notify Me</span></button>');

            });
            jQuery(document).on('click', '.product_alert_notify.action', function(e) {
                var url = jQuery(this).closest(".product-item").find(".product-item-info a").attr("href");
                window.location.replace(url);
            });
        },
    });

    return $.mage.catalogAddToCart;
});
