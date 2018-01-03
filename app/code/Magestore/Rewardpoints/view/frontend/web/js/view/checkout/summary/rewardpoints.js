/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magestore_Rewardpoints/js/model/earningpoints',
    ],
    function ($,Component, rewardpoints) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Rewardpoints/checkout/summary/rewardpoints'
            },
            rewardpoints:  rewardpoints.getData(),

            /**
             * Check is displayed use point
             * @returns {boolean}
             */
            isDisplayedUsePoint: function() {
                if(!this.rewardpoints().displayUsePoint || !this.rewardpoints().enableReward){
                    $('tr.rewardpoint-use_point').hide();
                }
                return true;
            },
            /**
             * Get Earning Point
             * @returns {number}
             */
            getUsePoint: function() {
                var point = 0;
                if(this.rewardpoints().rewardpointsUsePoint){
                    point  = this.rewardpoints().rewardpointsUsePoint;
                }
                return point;
            },

            /**
             * Check is displayed earning point
             * @returns {boolean}
             */
            isDisplayedEarning: function() {
                if(!this.rewardpoints().displayEarning){
                    $('tr.rewardpoint-earning').hide();
                }
                return true;
            },
            /**
             * Get Earning Label
             * @returns text
             */
            getEarningLabel: function() {
                var earningLabel = 'You will earn';
                if(this.rewardpoints().earningLabel){
                    earningLabel  = this.rewardpoints().earningLabel;
                }
                return earningLabel;
            },
            /**
             * Get Earning Point
             * @returns {number}
             */
            getEarningPoint: function() {
                var point = 0;
                    if(this.rewardpoints().rewardpointsEarning){
                        point  = this.rewardpoints().rewardpointsEarning;
                    }
                return point;
            },
            /**
             * Check is displayed spending point
             * @returns {boolean}
             */
            isDisplayedSpending: function() {
                if(!this.rewardpoints().displaySpending || !this.rewardpoints().enableReward){
                    $('tr.rewardpoint-spending').hide();
                }
                return true;
            },
            /**
             *Get Spending Label
             * @returns text
             */
            getSpendingLabel: function() {
                var spendingLabel = 'You will spend';
                if(this.rewardpoints().spendingLabel){
                    spendingLabel  = this.rewardpoints().spendingLabel;
                }
                return spendingLabel;
            },
            /**
             *Get Spending Point
             * @returns {number}
             */
            getSpendingPoint: function() {
                var point = 0;
                if(this.rewardpoints().rewardpointsSpending){
                    point  = this.rewardpoints().rewardpointsSpending;
                }
                return point;
            }
        });
    }
);