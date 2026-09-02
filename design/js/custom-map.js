$(function () {

    const ACCESS_TOKEN = window.AMS_MAPBOX_TOKEN || "";
    mapboxgl.accessToken = ACCESS_TOKEN;
    var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [101.9758, 4.2105], // starting position ([lng, lat] for Mombasa, Kenya)
        zoom: 9
    });

    function loadMapBox(searches) {
        if (Array.isArray(searches)) {
            searches.forEach((search) => {
                // var query = `${search.address_line_1},${search.state},${search.city}, Malaysia, ${search.longitude} , ${search.latitude} `;
                // var query = `${search.longitude} , ${search.latitude}`;
                if (search.longitude === undefined && search.latitude === undefined) {
                    var query = `${search.address},${search.state},${search.city}, Malaysia `;
                } else{
                    var query = `${search.longitude} , ${search.latitude} `;
                }
                var url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${query}.json?limit=1&types=addresses,country,district,place,region,postcode&access_token=${ACCESS_TOKEN}`;

                // var url = `https://api.mapbox.com/geocoding/v5/mapbox.places/$%7B${query}%7D.json?limit=1&types=address,country,district,place,region,postcode&access_token=${ACCESS_TOKEN}`;
                $.ajax({
                    dataType: 'json',
                    url: url,
                    success: function (geojson) {
                        const el = document.createElement('div');
                        el.className = 'marker';
                        // make a marker for each feature and add to the map
                        new mapboxgl.Marker(el).setLngLat(geojson.features[0].center).addTo(map);
                        map.flyTo({
                            center: geojson.features[0].center
                        });
                    }
                });
            });
        }
    }

    // driver planned orders
    $("#OrderDriverModal #driver").on("change", function () {
        var driverid = $(this).val();
        var orderid = $("#new-order-order-id").val();

        $.ajax({
            "url": "/Orders/driverPlannedOrders",
            "method": "GET",
            "dataType": "json",
            "data": {
                "driverid": driverid,
                "orderid": orderid
            },
            success: function (response) {
                $("#driver-planned-order-table tbody").html('');
                $("#driver-planned-order-table tbody").html(response.table);

                // company address
                if (response.addresses) {
                    var addresses = response.addresses;
                    $(".marker").remove();
                    loadMapBox(addresses);
                    map.on('load', loadMapBox);
                }
            }
        });
    });
    $("#OrderDriverModal #driver").trigger('change');


    // driver and specific truck planned orders
    $("#OrderDriverModal #truckField").on("change", function () {
        var truckid = $(this).val();
        var driverid = $('#OrderDriverModal #driver').val();
        var orderid = $("#new-order-order-id").val();

        $.ajax({
            "url": "/Orders/driverPlannedOrders",
            "method": "GET",
            "dataType": "json",
            "data": {
                "driverid": driverid,
                "truckid": truckid,
                "orderid": orderid
            },
            success: function (response) {
                $("#driver-planned-order-table tbody").html('');
                $("#driver-planned-order-table tbody").html(response.table);
                // company addresses
                if (response.addresses) {
                    var addresses = response.addresses;
                    $(".marker").remove();
                    loadMapBox(addresses);
                    map.on('load', loadMapBox);
                }
            }
        });
    });

    // when click on new order add button
    $(document).on("click", ".new-order-add-btn", function (evt) {
        var orderid = $(this).data('orderid');
        $("#new-order-order-id").val(orderid);

        // get order driver and company location
        $.ajax({
            "url": "/Orders/getOrderDriverAndLocation",
            "method": "GET",
            "dataType": "json",
            "data": {
                "orderid": orderid
            },
            success: function (response) {
                if (response.driver_id && response.truck_id) {
                    $("#OrderDriverModal #driver").val(response.driver_id);
                    $("#OrderDriverModal #truckField").val(response.truck_id);
                    $("#OrderDriverModal #truckField").trigger('change');
                }
                if (response.addresses) {
                    $(".marker").remove();
                    loadMapBox(response.addresses);
                    map.on('load', loadMapBox);
                }
            }
        });
    });

});
