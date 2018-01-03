/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license sliderConfig is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_LayeredNavigationUltimate
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define(
    [
        'jquery',
        'mage/storage',
        'Mageplaza_LayeredNavigationUltimate/js/model/loader'
    ],
    function ($, storage, loader) {
        'use strict';

        var productContainer = $('#layer-product-list'),
            isLoading = false;

        return function (layer, listEl) {
            if (isLoading) {
                return;
            }

            var nextItem = productContainer.find('.pages-item-next');
            if (!nextItem.length) {
                return;
            }

            var submitUrl = nextItem.find('a.action.next').first().prop('href');
            if (!layer.checkUrl(submitUrl)) {
                return;
            }

            loader.startLoader();
            isLoading = true;

            return storage.post(submitUrl).done(
                function (response) {
                    var productsHtml = response.products;
                    if (productsHtml) {
                        var products = $('<div>' + productsHtml + '</div>');

                        listEl.find('li').last().after(products.find('.items.product-items').html().trim());
                        nextItem.html(products.find('ul.items.pages-items').html());
                        productContainer.trigger('contentUpdated');
                    }
                }
            ).fail(
                function () {
                    window.location.reload();
                }
            ).always(
                function () {
                    loader.stopLoader();
                    isLoading = false;
                }
            );
        };
    }
);
