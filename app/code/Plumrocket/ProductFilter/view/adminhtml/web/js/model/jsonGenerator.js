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
    'jquery'
], function ($) {
        'use strict';

        return {

            data: {},

            created: false,

            init: function(options) {
                if (options.itemSelector) {
                    this.created = true;
                    var self = this;
                    varienGlobalEvents.attachEventHandler('formSubmit', function(event) {
                        //Save all data and attributes
                        self.generate(options);
                    });

                    return this.options;
                }
            },

            generate: function(options) {
                //var options = this.options;
                var $items = $(options.attributeEnabledListSelector).find(options.itemSelector);
                var self = this,
                    _data = {};

                $items.each(function(){
                    var _val = $(this).data('value');
                    var label = $(this).data('label');
                    if (!$(this).hasClass('group')) {


                        if ($(this).parents('.group').length) {

                            var _group = $(this).parents('.group');
                            var groupName = _group.find('.group_head').data('group-name');
                            if (!_data[groupName]) {
                                _data[groupName] = {};
                            }
                            _data[groupName][_val] = label;
                        } else {
                            _data[_val] = label;
                        }
                    } else {
                        var groupName = $(this).find('.group_head').data('group-name');
                        if (!_data[groupName]) {
                            _data[groupName] = {};
                        }
                        _data[groupName][_val] = label;
                    }
                });


                this.data = _data;

                var valueString = JSON.stringify(_data);

                $(options.elementId).val(valueString);
            }

        };
    }
);
