/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ZohocrmIntegration extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ZohocrmIntegration
 * @author   ThaoPV
 */
define(
    [
    "jquery",
    "Magento_Ui/js/lib/core/class",
    "Magenest_ZohocrmIntegration/js/fields",
    "underscore"
    ],
    function ($, Class, Fields, _) {
        "use strict";
        return Class.extend(
            {
                defaults: {
                    /**
                     * Initialized solutions
                     */
                    url : '',
                    config: {'lead' : 'Leads'},
                    /**
                     * The elements of created solutions
                     */
                    solutionsElements: {},
                    /**
                     * The selector element responsible for configuration of payment method (CSS class)
                     */
                    buttonRefresh: '.button.action-refresh'
                },
                /**
                 * Constructor
                 */
                initialize: function (url) {
                    this.initConfig(url)
                    .initMappings(url);
                    return this;
                },
                /**
                 * Initialization and configuration solutions
                 */
                initMappings: function (url) {
                    $('#type').change(
                        function () {
                            var type = $(this).val();
                            var data = {'type' : type};
                            $.ajax(
                                {
                                    type: "POST",
                                    url: url.url,
                                    data: data,
                                    showLoader: true,
                                    success: function (response) {
                                        var responseObj = JSON.parse(response);
                                        console.log(responseObj);
                                        $('#zoho_field').html(responseObj.zoho_options);
                                        $('#magento_field').html(responseObj.magento_options);
                                    }
                                }
                            );
                        }
                    );
                    return this;
                }

            }
        );
    }
);
