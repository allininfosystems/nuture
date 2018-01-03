/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'ko',
        'jquery',
        'uiComponent',
        "Magento_Checkout/js/action/get-payment-information",
        "Magento_Checkout/js/model/totals",
        "Magestore_Rewardpoints/js/ion.rangeSlider",
        'Magento_Checkout/js/action/set-shipping-information'
    ],
    function (
        ko,
        $,
        Component,
        getPaymentInformationAction,
        totals,
        rangeSlider,
        setShippingInformationAction) {
        "use strict";
        if(rewardpointConfig.enableReward){
            $("body").delegate(
                "#shipping-method-buttons-container .primary button.continue",
                "click",
                function(){
                    setShippingInformationAction().done(
                        function() {
                            $('.payment-reward-spending').addClass('_loading');
                            jQuery.ajax({
                                url: rewardpointConfig.urlRefreshPoint,
                                type: 'POST',
                                complete: function(data) {
                                    var result = $.parseJSON(data.responseText);
                                    if(result.rate && result.rate.optionType  && result.rate.sliderOption){
                                        if(result.rate.optionType == 'slider' && result.rate.sliderOption.maxPoints){
                                            var $range = $("#range_reward_point");
                                            var slider = $range.data("ionRangeSlider");
                                            slider.update({
                                                max: result.rate.sliderOption.maxPoints
                                            });
                                        }
                                    }
                                    $('.payment-reward-spending').removeClass('_loading');
                                },
                            });

                        }
                    );
                })
        }
        function stopRKey(evt) {
            var evt = (evt) ? evt : ((event) ? event : null);
            var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
            if ((evt.keyCode == 13) && (node.id == "reward_sales_point")) {
                return false;
            }
        }
        document.onkeypress = stopRKey;
        var options = {
            rewardSliderRules: $.parseJSON(rewardpointConfig.getRulesJson)
        };
        var rewardSliderRules = ((rewardpointConfig.enableReward && options.rewardSliderRules)?options.rewardSliderRules.rate:false);

        function updateTotal (point){
            var listReward = {
                '0':'rewardpoint-earning',
                '1':'rewardpoint-spending',
                '2':'rewardpoint-use_point'
            };
            totals.isLoading(true);
            jQuery.ajax({
                url: rewardpointConfig.urlUpdateTotal,
                type: 'POST',
                data:{'reward_sales_rule':'rate','reward_sales_point':point},
                complete: function(data) {
                    var arrDataReward = $.map($.parseJSON(data.responseText), function(value, index) {
                        return [value];
                    });
                    $.dataReward = arrDataReward ;
                    var deferred = $.Deferred();
                    getPaymentInformationAction(deferred);
                    $.when(deferred).done(function () {
                        $.each(listReward,function(key,val){
                            $('tr.'+val).show();
                            $('tr.'+val+' td.amount span').text( $.dataReward[key] );
                        })
                        totals.isLoading(false);
                    });
                },
            });
        }
        ko.bindingHandlers.spendingPoint = {
            init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
                rewardpointConfig.checkMaxpoint = parseInt(rewardpointConfig.checkMaxpoint);
                if(rewardpointConfig.checkMaxpoint){
                    updateTotal(rewardSliderRules.sliderOption.maxPoints);
                    $('#reward_sales_point').val(rewardSliderRules.sliderOption.maxPoints);
                }
                $(element).ionRangeSlider({
                    grid:true,
                    grid_num:((rewardSliderRules.sliderOption.maxPoints<4)?rewardSliderRules.sliderOption.maxPoints:4),
                    min:rewardSliderRules.sliderOption.minPoints,
                    max:rewardSliderRules.sliderOption.maxPoints,
                    step:rewardSliderRules.sliderOption.pointStep,
                    from:((rewardpointConfig.checkMaxpoint)?rewardSliderRules.sliderOption.maxPoints:rewardpointConfig.usePoint),
                    onFinish: function (data) {
                        if(rewardSliderRules.sliderOption.maxPoints == data.from){
                            $('#reward_max_points_used').attr('checked','checked');
                        }else{
                            $('#reward_max_points_used').removeAttr('checked');
                        }
                        $("#reward_sales_point").val(data.from);
                        updateTotal(data.from);
                    },
                });
            },
        };
        return Component.extend({
            defaults: {
                template: rewardpointConfig.template
            },
            options:{
                rewardSliderRules:$.parseJSON(rewardpointConfig.getRulesJson)
            },
            checkOptionType: function () {

                if (rewardSliderRules) {
                    return rewardSliderRules.optionType;
                }
            },
            oldval: function(){
                if(rewardpointConfig.checkMaxpoint && rewardSliderRules.sliderOption && rewardSliderRules.sliderOption.maxPoints){
                    return rewardSliderRules.sliderOption.maxPoints;
                }else if(rewardpointConfig.usePoint){
                    return rewardpointConfig.usePoint;
                }else{
                    return 0;
                }
            },

            checkedMax:((rewardpointConfig.checkMaxpoint)?true:false),

            textNeedmorePoint:rewardpointConfig.textNeedMorePoint,

            enableReward:function(){
                return rewardpointConfig.enableReward;
            },

            checkEarnwhenSpend:rewardpointConfig.checkEarnwhenSpend,

            textRule:rewardpointConfig.rule,

            textPoint: rewardpointConfig.textPoint,

            usePoint: function(){
                if(rewardpointConfig.checkMaxpoint && rewardSliderRules.sliderOption && rewardSliderRules.sliderOption.maxPoints){
                    return rewardSliderRules.sliderOption.maxPoints;
                }else if(rewardpointConfig.usePoint){
                    return rewardpointConfig.usePoint;
                }else{
                    return 0;
                }
            },

            changSpendingPoint: function (val){
                var _this = $('#reward_sales_point');
                val = _this.val();
                if( $.isNumeric(val) ){
                    if(val > rewardSliderRules.sliderOption.maxPoints){
                        val = rewardSliderRules.sliderOption.maxPoints;
                        _this.val(val);
                    }
                    $(_this).data('oldval',val);
                    $(_this).removeAttr('style');
                    var $range = $("#range_reward_point");
                    var slider = $range.data("ionRangeSlider");
                    slider.update({
                        from: val
                    });
                    updateTotal(val);
                    if(rewardSliderRules.sliderOption.maxPoints == val){
                        $('#reward_max_points_used').attr('checked','checked');
                    }else{
                        $('#reward_max_points_used').removeAttr('checked');
                    }
                }else{
                    $(_this).css({"border":"solid 1px red"});
                    $(_this).val($('#reward_sales_point').data('oldval'));
                }
            },
            maxSpendingPoint: function () {
                if ($('#reward_max_points_used').attr('checked')) {
                    var point = rewardSliderRules.sliderOption.maxPoints;
                } else {
                    var point = rewardSliderRules.sliderOption.minPoints;
                    ;
                }
                $('#reward_sales_point').val(point)
                var $range = $("#range_reward_point");
                var slider = $range.data("ionRangeSlider");
                slider.update({
                    from: point
                });
                updateTotal(point);
            }
        });
    }
);