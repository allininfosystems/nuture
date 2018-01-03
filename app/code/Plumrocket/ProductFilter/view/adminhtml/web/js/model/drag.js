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
    'Plumrocket_ProductFilter/js/model/drop',
    'jquery',
    'Plumrocket_ProductFilter/js/model/group',
    'jquery/ui'
], function (drop, $, group) {
        'use strict';

        return {

            itited : false,

            /**
             * Drag'n'drop logic for attributes
             */
            init: function(options) {

                if (!this.itited) {

                    var self = this;

                    if (!options.isCustomOptions) {
                        drop.init(options);
                        group.options = options;
                    }

                    $(options.attributeListSelector + "," + options.attributeEnabledListSelector).sortable({
                        connectWith: options.connector,
                        items: 'li.attr_item',
                        receive: function() {
                            var currentElement = self.getCurrent(event);
                            if (!options.isCustomOptions) {
                                drop.init(options, currentElement);
                            }
                        },
                        stop: function( event, ui ) {
                            group.checkGroup(event);
                            if (typeof event.srcElement.dropTo != 'undefined') {
                                var currentElement = self.getCurrent(event);

                                if (currentElement.hasClass('group')) {
                                    return false;
                                }

                                group.initGroup(currentElement, $(event.srcElement.dropTo));
                                delete event.srcElement.dropTo;
                            }
                        }
                    });


                    if (!options.isCustomOptions) {
                        drop.init(options);
                    }
                    self.inited = true;
                }
            },

            getCurrent: function(event) {
                if (event.srcElement.nodeName == "LABEL") {
                    var currentElement = $(event.srcElement).parent();
                } else {
                    var currentElement = $(event.srcElement);
                }
                return currentElement;
            }
        };
    }
);
