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
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
        'jquery',
        'productListToolbarForm'
    ],
    function ajaxPopup($) {

        function loadAjax(link) {
            $('.ln_overlay').show();
            $.ajax({
                type: 'POST',
                url: link,
                success: function (reponse) {
                    if (reponse.status == 'ok') {
                        $('.related-product-modal-content').html(
                            reponse.product
                        );
                        initProductListUrl();
                        initPageUrl();
                        $('body').trigger('contentUpdated');
                        $('.ln_overlay').hide();
                    }
                },
                error:function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);

                }
            });
        }

        function initPageUrl() {
            var self = this;
            var pageElement = $('#layer-product-list').find($('.pages').find('a'));
            pageElement.each(function () {
                var el = $(this),
                    link = el.prop('href');
                if(!link){
                    return;
                }
                el.bind('click',function (e) {
                    loadAjax(link);
                    e.stopPropagation();
                    e.preventDefault();

                })

            });

        }

        function initProductListUrl() {

            var isProcessToolbar = false;
            $.mage.productListToolbarForm.prototype.changeUrl = function (paramName, paramValue, defaultValue)  {
                if (isProcessToolbar) {
                    return;
                }
                isProcessToolbar = true;

                var urlPaths = this.options.url.split('?'),
                    baseUrl = urlPaths[0],
                    urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                    paramData = {},
                    parameters;
                for (var i = 0; i < urlParams.length; i++) {
                    parameters = urlParams[i].split('=');
                    paramData[parameters[0]] = parameters[1] !== undefined
                        ? window.decodeURIComponent(parameters[1].replace(/\+/g, '%20'))
                        : '';
                }
                paramData[paramName] = paramValue;
                if (paramValue === defaultValue) {
                    delete paramData[paramName];
                }
                paramData = $.param(paramData);
                link = baseUrl + (paramData.length ? '?' + paramData : '')
                loadAjax(link);
            }
        }


            $('.fa-eye').each(function () {
                var faEl = $(this);
                faEl.click(function () {
                    $('.ln_overlay').show();
                    var brandId = $(this).attr('id');
                    var url = window.quickviewUrl + brandId;
                    $.ajax({
                        type: 'POST',
                        url: url ,
                        success: function (reponse) {
                            if (reponse.status == 'ok') {
                                $('.brand_title').text(reponse.brand.value);
                                $('.quickview_img').attr('src',reponse.brand.image);
                                $('.related-product-modal-content').html(
                                    reponse.product
                                );

                                if(reponse.brand.short_description){
                                    $('.brand_description').text($("<p>" + reponse.brand.short_description + "</p>").text());
                                }else{
                                    $('.brand_description').text('No description.');
                                }
                                $('.ln_overlay').hide();
                                initProductListUrl();
                                initPageUrl();
                                $('body').trigger('contentUpdated');
                            }
                        },
                        error:function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);

                        }
                    })

                })
            });

    }
);