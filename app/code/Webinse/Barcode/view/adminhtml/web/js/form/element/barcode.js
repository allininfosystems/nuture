define(['Magento_Ui/js/form/element/abstract'],function(Abstract) {
    return Abstract.extend({
        setBarcodeImage: function () {
            return "data:image/jpeg;base64," + this.value();
        }
    });
});