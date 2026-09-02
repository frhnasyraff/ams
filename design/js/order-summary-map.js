$(function () {

    const urlParams = new URLSearchParams(window.location.search);
    const from = urlParams.get('from');
    const to = urlParams.get('to');
    const type = urlParams.get('type');

    const ACCESS_TOKEN = window.AMS_MAPBOX_TOKEN || "";
    mapboxgl.accessToken = ACCESS_TOKEN;
    var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [101.9758, 4.2105], // starting position ([lng, lat] for Mombasa, Kenya)
        zoom: 8
    });

    function loadMapBox(searches) {
        if (Array.isArray(searches)) {
            searches.forEach((search) => {
                var query = `${search.address_line_1},${search.state},${search.city},"Malaysia"`;
                var url = `https://api.mapbox.com/geocoding/v5/mapbox.places/$%7B${query}%7D.json?limit=1&types=address,country,district,place,region,postcode&access_token=${ACCESS_TOKEN}`;
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

    // get order driver and company location
    $.ajax({
        "url": "/order_summary/getAllOrdersLocation",
        "method": "GET",
        "dataType": "json",
        "data": {
            "from": from,
            "to": to,
            "type": type
        },
        success: function (response) {
            if (response.addresses) {
                $(".marker").remove();
                loadMapBox(response.addresses);
                map.on('load', loadMapBox);
            }
        }
    });
});
