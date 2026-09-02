function amsUrl(path) {
    var basePath = window.appBasePath || '';
    return basePath + '/' + String(path || '').replace(/^\/+/, '');
}

$(function () {

    // get order driver and company location
    $.ajax({
        // Legacy root URL breaks when app runs in /assets_IT-usman/: "url": "/asset_dashboard/assetLocation",
        "url": amsUrl("asset_dashboard/assetLocation"),
        "method": "GET",
        "dataType": "json",
        "data": {
        },
        success: function (response) {
            if (response.addresses) {
                $(".marker").remove();
                window.loadMapBox(response.addresses);
                window.iniMap.on('load', window.loadMapBox);
            }
        }
    });
});

