$(document).ready(function () {
    // Initialize plain Leaflet map (not L.mapbox.map)
    const map = L.map('map').setView([5, 113], 6);

    const mapboxToken = window.AMS_MAPBOX_TOKEN || '';
    const tileLayerUrl = mapboxToken
        ? 'https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=' + encodeURIComponent(mapboxToken)
        : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

    L.tileLayer(tileLayerUrl, mapboxToken ? {
        tileSize: 512,
        zoomOffset: -1,
        attribution: '© <a href="https://www.mapbox.com/">Mapbox</a>'
    } : {
        maxZoom: 19,
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const markerGroup = L.layerGroup().addTo(map);

    function refreshMapSize() {
        window.requestAnimationFrame(function () {
            map.invalidateSize(false);
        });
    }

    window.setTimeout(refreshMapSize, 150);

    // Apply small jitter to avoid overlapping markers
    function jitterCoordinates(lat, lng) {
        const jitter = 0.0003; // Small enough to distinguish but not distort
        const offsetLat = (Math.random() - 0.5) * jitter;
        const offsetLng = (Math.random() - 0.5) * jitter;
        return [parseFloat(lat) + offsetLat, parseFloat(lng) + offsetLng];
    }

    function getColor(asset) {
        return asset.color || 'black';
    }

    function updateMapMarkers(typeFilter, groupFilter) {
        $.ajax({
            url: '/assets/assetLocationPointer',
            method: 'POST',
            data: {
                equipment_type: typeFilter,
                equipment_group: groupFilter
            },
            success: function (response) {
                markerGroup.clearLayers();

                if (!Array.isArray(response.states)) {
                    console.error("Invalid response format:", response);
                    return;
                }

                const bounds = L.latLngBounds();
                let hasMarkers = false;

                response.states.forEach(asset => {
                    const lat = parseFloat(asset.latitude);
                    const lng = parseFloat(asset.longitude);

                    if (isNaN(lat) || isNaN(lng)) return;

                    const [jLat, jLng] = jitterCoordinates(lat, lng);

                    const icon = L.divIcon({
                        className: '',
                        html: `<i class="fa fa-map-marker" style="color:${getColor(asset)}; font-size:36px;"></i>`,
                        iconSize: [30, 30],
                        iconAnchor: [15, 30],
                        popupAnchor: [0, -30]
                    });

                    const marker = L.marker([jLat, jLng], { icon: icon })
                        .bindPopup(`
                            <strong>Asset Type:</strong> ${asset.asset_type}<br>
                            <strong>Asset Name:</strong> ${asset.asset_name}<br>
                            <strong>Location:</strong> ${asset.location_name}<br>
                            <strong>Status:</strong> ${asset.status}
                        `);

                    markerGroup.addLayer(marker);
                    bounds.extend([jLat, jLng]);
                    hasMarkers = true;
                });

                if (hasMarkers) {
                    refreshMapSize();
                    map.fitBounds(bounds, { padding: [20, 20] });
                }
            },
            error: function () {
                console.error("Failed to load asset data.");
            }
        });
    }

    $(".equipment_type_filter .btn, .equipment_group_filter .btn").click(function () {
        const typeFilter = $(".equipment_type_filter .btn-primary").data("filter");
        const groupFilter = $(".equipment_group_filter .btn-primary").data("filter");
        updateMapMarkers(typeFilter, groupFilter);
    });

    updateMapMarkers(null, null);

    let mapResizeTimer;
    $(window).on('resize.assetSummaryMap', function () {
        window.clearTimeout(mapResizeTimer);
        mapResizeTimer = window.setTimeout(refreshMapSize, 120);
    });
});

