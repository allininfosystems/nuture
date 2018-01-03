require([
    "jquery",
    "loader",
    'Magento_Ui/js/modal/confirm',
    'mage/translate'],
    function  ($,loader,confirm) {
    $(document).ready(function(){
        $('#earning-delete-button').click(function () {
            var msg = $.mage.__('Are you sure you want to do this?'),
                url = $('#earning-delete-button').data('url');
            confirm({
                'content': msg,
                'actions': {

                    /**
                     * 'Confirm' action handler.
                     */
                    confirm: function () {
                        window.location  = url;
                    }
                }
            });

            return false;
        });
        $("body").append("<div id='modelInfoTransaction'></div>");
        var element = $('<div>');
        element.appendTo('body');
        element.loader();
        $('body').delegate('a.action-menu-item','click',function(){
            element.loader('show');
            var url = $(this).attr('href');
            jQuery.ajax({
                url: url,
                type: 'GET',
                dataType: 'xml/html/script/json/jsonp',
                complete: function(data,xhr, textStatus) {
                    $("#modelInfoTransaction").empty();
                    $("#modelInfoTransaction").append(data.responseText);
                    $('#modelInfoTransaction').modal({modalClass:'dialogInfoTransaction'});
                    $('#modelInfoTransaction').modal('openModal');
                    element.loader('hide');
                },
                error: function(xhr, textStatus, errorThrown) {
                    //called when there is an error
                }
            });
            return false;
        });
        if(typeof $('select[name="rewardpoints_spendingrates[max_price_spended_type]"]') !='undefined') {
            setInterval(function () {
                if ($('select[name="rewardpoints_spendingrates[max_price_spended_type]"]').val() == 'none') {
                    $('input[name="rewardpoints_spendingrates[max_price_spended_value]"]').parent().parent().hide();
                } else {
                    $('input[name="rewardpoints_spendingrates[max_price_spended_value]"]').parent().parent().show();
                }
            }, 1000);
        }
        $('body').delegate('select[name="rewardpoints_spendingrates[max_price_spended_type]"]','change',function(){
            if($(this).val() == 'none'){
                $('input[name="rewardpoints_spendingrates[max_price_spended_value]"]').parent().parent().hide();
            }else{
                $('input[name="rewardpoints_spendingrates[max_price_spended_value]"]').parent().parent().show();
            }
        });
    })
});