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
    'Plumrocket_ProductFilter/js/model/group',
    'jquery/ui'
], function ($, group) {
        'use strict';

        return {

            isCustomOption: function (event) {
                if (
                    $(event.toElement).parents('[data-configid="custom_options"]').length
                    || $(event.target).parents('[data-configid="custom_options"]').length
                    ) {
                    return true;
                }

                return false;
            },

            init: function(options, item) {

                if (options.isCustomOptions) {
                    return false;
                }
                var self = this;

                var droppableParams = {
                    accept: options.itemSelector,
                    hoverClass: "ui-state-hover",
                    drop: function( event, ui) {

                        if ($(event.srcElement).hasClass('group') || $(event.srcElement).hasClass('group_name') || self.isCustomOption(event)) {
                            return false;
                        }
                        event.toElement['dropTo'] = event.target;
                    }
                };

                if (!item) {
                    $(options.attributeEnabledListSelector)
                        .find('.attr_item')
                        .droppable(droppableParams);
                } else {
                    item.droppable(droppableParams);
                }
            }
        };
    }
);
