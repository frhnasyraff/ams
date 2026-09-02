$(document).ready(function () {
    L.mapbox.accessToken = window.AMS_MAPBOX_TOKEN || "";

    const map = L.mapbox.map('map').setView([3.5, 108], 5);
    L.mapbox.styleLayer('mapbox://styles/mapbox/streets-v11').addTo(map);

    function updateMapMarkers(typeFilter, groupFilter) {
        $.ajax({
            url: '/items/itemsLocationPointer',
            method: 'POST',
            dataType: 'json',
            data: {
                item_type_filter: typeFilter,
                item_group_filter: groupFilter
            },
            success: function (response) {
                if (Array.isArray(response.states)) {
                    // Remove previous markers
                    map.eachLayer(function (layer) {
                        if (layer instanceof L.Marker) {
                            map.removeLayer(layer);
                        }
                    });

                    const bounds = L.latLngBounds();

                    response.states.forEach(item => {
                        const icon = L.divIcon({
                            className: '',
                            html: `<i class="fa fa-map-marker" style="color: ${getColor(item)}; font-size: 40px;"></i>`,
                            iconSize: [30, 30],
                            iconAnchor: [15, 30],
                            popupAnchor: [0, -30]
                        });

                        const marker = L.marker([parseFloat(item.latitude), parseFloat(item.longitude)], { icon })
                            .addTo(map)
                            .bindPopup(`
                                <strong>Item Type:</strong> ${item.item_type}<br>
                                <strong>Item Name:</strong> ${item.item_name}<br>
                                <strong>Location:</strong> ${item.location_name}<br>
                                <strong>Status:</strong> ${item.status}
                            `);

                        bounds.extend(marker.getLatLng());
                    });

                    if (bounds.isValid()) {
                        map.fitBounds(bounds.pad(0.1));
                    } else {
                        map.setView([3.5, 108], 5); // fallback center
                    }
                } else {
                    console.error('Invalid response format:', response);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    }

    function getColor(item) {
        return item.color || 'blue'; // color from PHP or default
    }

    $(".item_type_filter .btn, .item_group_filter .btn").click(function () {
        const typeFilter = $(".item_type_filter .btn-primary").data("filter");
        const groupFilter = $(".item_group_filter .btn-primary").data("filter");
        updateMapMarkers(typeFilter, groupFilter);
    });

    updateMapMarkers(null, null); // Initial load
});
