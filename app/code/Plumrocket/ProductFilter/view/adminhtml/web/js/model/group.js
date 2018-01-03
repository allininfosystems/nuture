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
    'text!Plumrocket_ProductFilter/template/group.html'
], function ($, groupHtml) {
        'use strict';

        return {

            options: {
                groupNameBefore: ''
            },


            initGroup: function (target, current)
            {
                if (current.parents(this.options.attributeListSelector).length || target.parents(this.options.attributeListSelector).length) {
                    return false;
                }

                if (!current.hasClass('group') && !current.parents('.group').length) {
                    var html = groupHtml
                        .replace(/%group_name%/g, current.find('label').text());
                    current.addClass('group').prepend(html);
                    target.appendTo($('.list_enabled', current));
                    this.groupProc();
                } else {
                    target.appendTo(current.find('.list_enabled').first());
                }
            },

            checkGroup: function(event)
            {
                var self = this;
                $(this.options.attributeEnabledListSelector).find('.group').each(function(){
                    if (!$(this).find('.list_enabled').children().length) {
                        self.destroyGroup($(this));
                    }
                });

                $(this.options.attributeListSelector).find('.group').each(function(){
                    self.destroyGroup($(this));
                });
            },

            destroyGroup: function(group)
            {
                var self = this;
                if (group.find('.list_enabled').children().length) {
                    group.find('.list_enabled').children().each(function() {
                        $(this).appendTo($(this).parents(self.options.connector).first());
                    });
                }

                group.removeClass('group');
                group.find('.group_name, .group_head').remove();
                return this;
            },

            //Add events to group
            groupProc: function()
            {
                //Initialization of button events
                var self = this;
                $('.group_field', this.options.attributeEnabledListSelector).on('keypress',function(e) {
                    if (e.which == 13) {
                        self.groupLabelSave(e);
                    }
                });

                $('.group_field', this.options.attributeEnabledListSelector).on('change',$.proxy(this.groupLabelChange, this));
                $('.group_field, .edit', this.options.attributeEnabledListSelector).on('click',$.proxy(this.groupLabelFocus, this));

                $('.cancel', this.options.attributeEnabledListSelector).on('click',$.proxy(this.groupLabelCancel, this));
                $('.ok', this.options.attributeEnabledListSelector).on('click',$.proxy(this.groupLabelSave, this));
                $('.group_field', this.options.attributeEnabledListSelector).on('blur',$.proxy(this.groupLabelCancel, this));

            },

            groupLabelFocus: function(event) {
                var $item = $(event.target).parents('.group_head');


                $item.find('.group_field').focus();
                $item.find('.edit').hide();
                $item.find('.ok, .cancel').show();
            },

            groupLabelCancel: function(event) {
                var $item = $(event.target).parents('.group_head');

                this._buttonP($item);

                $item.find('.group_field').val($item.data('group-name'));
                if (event.type != 'blur') {
                    $item.find('.group_field').blur();
                }
            },

            //Show edit button and hide oK and cancel buttons
            _buttonP: function($item) {
                $item.find('.edit').show();
                $item.find('.ok, .cancel').hide();
            },

            groupLabelSave: function(event) {
                var $item = $(event.target).parents('.group_head');
                this._buttonP($item);

                var field = $item.find('.group_field');

                $item.data('group-name', field.val());
                field.blur();
            }

        };
    }
);
