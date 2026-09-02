function amsUrl(path) {
    var basePath = window.appBasePath || '';
    return basePath + '/' + String(path || '').replace(/^\/+/, '');
}

$(document).ready(function () {
    L.mapbox.accessToken = window.AMS_MAPBOX_TOKEN || "";

    // Initialize the map
    const map = L.mapbox.map('map').setView([0, 3], 2);

    // Add Mapbox tiles
    L.mapbox.styleLayer('mapbox://styles/mapbox/streets-v11').addTo(map);

    // Fetch assets from the backend
    $.ajax({
        // Legacy root URL breaks when app runs in /assets_IT-usman/: url: '/Asset_dashboard/assetLocationPointer'
        url: amsUrl('Asset_dashboard/assetLocationPointer'), // Replace with your actual endpoint
        method: 'GET',
        success: function(response) {
            console.log('Response:', response); 
        
            if (Array.isArray(response.states)) {
                response.states.forEach(asset => {
                    let iconClass = '';
                    if (asset.status.toLowerCase() === 'in use') {
                        iconClass = 'fa fa-map-marker';
                    } else if (asset.status.toLowerCase() === 'maintenance') {
                        // iconClass = 'fa fa-puzzle-piece';
                        iconClass = 'fa fa-map-marker';
                        
                    } else if (asset.faulty_status) {
                        
                        // iconClass = 'fa fa-plane';
                        iconClass = 'fa fa-map-marker';
                    } else {
                        console.error(`Unknown status: ${asset.status}`);
                        return;
                    }
        
                    const icon = L.divIcon({
                        className: '',
                        html: `<i class="${iconClass}" aria-hidden="true" style="color: ${getColor(asset)}; font-size: 44px;"></i>`,
                        iconSize: [30, 30],
                        iconAnchor: [15, 30],
                        popupAnchor: [0, -30]
                    });
        
                    const marker = L.marker([asset.latitude, asset.longitude], { icon: icon })
                        .addTo(map)
                        .bindPopup(`
                            <strong>Asset Type:</strong> ${asset.asset_type}<br>
                            <strong>Asset Name:</strong> ${asset.asset_name}<br>
                            <strong>Location Name:</strong> ${asset.location_name}
                        `);
                });
        
                const bounds = L.latLngBounds();
                response.states.forEach(asset => bounds.extend([asset.latitude, asset.longitude]));
                map.fitBounds(bounds);
            } else {
                console.error('Expected states array, but got:', response);
            }
        }
    });

    // Function to get color based on status_color from the response
    function getColor(asset) {
       
        return asset.status_color || 'black'; // Use status_color from response or default to black
    }
});

