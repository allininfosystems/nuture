function searchBarcode() {
    jQuery(function ($) {
        var value = $('#search-field').val();
        if (value) {
            var urlPath = $(location).attr('href').split("key");
            location.href = $(location).attr('origin') + '/nuture/admin/webinse_barcode/scan/index/barcode/' + value + "/key" + urlPath[1];
        } else {
            location.reload();
        }
    });
}
