require([
    'jquery',
    'priceUtils',
    'priceBox'
], function ($) {
    $('.price-box').on('updatePrice', function(e, data) {
        if (!data || !$('.rewards__product-points').length) {
            return;
        }
        // @todo fix rounding
        var option;
        var optionId;
        var displayPoints = $('.rewards__product-points .price', this).data("points");
        var text          = $('.rewards__product-points .price', this).data("label");
        if (!data.prices || !data.prices.rewardRules) {
            for (var i in data) {
                option   = data[i];
                optionId = i;
                break;
            }
            if (typeof option.rewardRules == 'undefined' || !option.rewardRules) {
                var newPoints     = $('.rewards__product-points .price', this).data("new-points");
                var optionPoints  = $('.rewards__product-points .price', this).data("used"+optionId);
                if (newPoints) {
                    newPoints -= optionPoints;
                    $('.rewards__product-points .price', this).data("new-points", newPoints);
                    $('.rewards__product-points .price', this).html(text.replace(displayPoints, newPoints));
                }
                $('.rewards__product-points .price', this).data("used"+optionId, 0);
                return;
            }
        } else {
            option = data.prices;
        }
        var oldPoints     = $('.rewards__product-points .price', this).data("current-points");
        var isUsed        = $('.rewards__product-points .price', this).data("used"+optionId);
        var qty           = $('[name="qty"]').val();
        var rounding      = option.rewardRules.rounding;
        var newPoints     = oldPoints;
        var optionPoints  = 0;
        if (qty <= 0) {
            qty = option.rewardRules.minAllowed;
        }
        if (typeof option.rewardRules.amount == 'undefined' || option.rewardRules.amount < 0) {
            if ($('.rewards__product-points .price', this).data("new-points")) {
                newPoints = $('.rewards__product-points .price', this).data("new-points");
            }
            if (isUsed) {
                return;
            }
            delete option.rewardRules.amount;
            delete option.rewardRules.minAllowed;
            delete option.rewardRules.rounding;
            for (var prodId in option.rewardRules) {
                rounding = option.rewardRules.rounding;
                for (var i in option.rewardRules[prodId]) {
                    var rule = option.rewardRules[prodId][i];

                    if (rule.points) {
                        optionPoints += rule.points * qty;
                    } else {
                        var rulePoints = option.finalPrice.amount * qty / rule.coefficient;
                        if (rule.options.limit && rulePoints > rule.options.limit) {
                            rulePoints = rule.options.limit;
                        }
                        optionPoints += rulePoints;
                    }
                }
            }
            if (parseInt(rounding) == 1) {
                optionPoints = Math.floor(optionPoints);
            } else {
                optionPoints = Math.ceil(optionPoints);
            }
            newPoints += optionPoints;
            option.rewardRules.amount = -1;
        } else {
            newPoints += option.rewardRules.amount;
        }
        if (parseInt(rounding) == 1) {
            newPoints = Math.floor(newPoints);
        } else {
            newPoints = Math.ceil(newPoints);
        }

        $('.rewards__product-points .price', this).data("used"+optionId, optionPoints);
        $('.rewards__product-points .price', this).data("new-points", newPoints);
        $('.rewards__product-points .price', this).html(text.replace(displayPoints, newPoints));
    });

    //bundle
    $('#product_addtocart_form').on('updateProductSummary', function(e, data) {
        if (!$('.rewards__product-points').length) {
            return;
        }

        var totalPoints = data.config.baseProductPoints;
        $.each(data.config.selected, function(index, values) {
            $.each(values, function(i, value) {
                if (!value) {
                    return;
                }
                var rules      = data.config.options[index]['selections'][value]['rewardRules'];
                var finalPrice = data.config.options[index]['selections'][value].prices.finalPrice.amount;
                var productId  = data.config.options[index]['selections'][value]['optionId'];
                var qty        = data.config.options[index]['selections'][value]['qty'];
                $.each(rules[productId], function(n, rule) {
                    if (rule.points) {
                        totalPoints += rule.points * qty;
                    } else {
                        var rulePoints = rule.rewardsPrice * qty / rule.coefficient;
                        if (rule.options.limit && rulePoints > rule.options.limit) {
                            rulePoints = rule.options.limit;
                        }
                        totalPoints += rulePoints;
                    }
                });
            })
        });
        if (totalPoints) {
            if (parseInt(data.config.rounding) == 1) {
                totalPoints = Math.floor(totalPoints);
            } else {
                totalPoints = Math.ceil(totalPoints);
            }
            $('.price-box.price-configured_price .rewards__product-points .price').html(
                totalPoints + ' ' + data.config.rewardLabel
            );
        }
    });

    //
    $('.input-text.qty').keyup(function() {
        if ($('.page-product-bundle').length || !$('.rewards__product-points').length) {
            return;
        }
        var qty       = $(this).val();
        var el        = $('.rewards__product-points .price', $(this).parents('tr')[0])
        var oldPoints = $(el).data("points");
        var newPoints = oldPoints * qty;
        var text      = $(el).data("label");

        $(el).data("new-points", newPoints).html(text.replace(oldPoints, newPoints));
    });
});