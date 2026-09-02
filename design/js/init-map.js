var oldCenter = [0, 0];
var centerCor = [0, 0];
var coordinatesArray = [];
var key = 0;
var change = 0.000500;
$(function() {

    const ACCESS_TOKEN = window.AMS_MAPBOX_TOKEN || "";
    mapboxgl.accessToken = ACCESS_TOKEN;
    var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [101.9758, 4.2105], // starting position ([lng, lat] for Mombasa, Kenya)
        zoom: 8
    });

    window.iniMap = map;

    window.loadMapBox = function(searches) {
        if (Array.isArray(searches)) {
            searches.forEach((search) => {
                
                // var query = `${search.address},${search.state},${search.city}, Malaysia `;
                
                
                 if (search.longitude === undefined && search.latitude === undefined) {
                    var query = `${search.address},${search.state},${search.city}, Malaysia `;
                } else{
                    var query = `${search.longitude} , ${search.latitude} `;
                }

                
                //  console.log("query result" + search.lon);
                
                 var url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${query}.json?limit=1&types=address,country,district,place,region,postcode&access_token=${ACCESS_TOKEN}`;
                $.ajax({
                    dataType: 'json',
                    url: url,
                    success: function(geojson) {

                        console.log("my check map"+geojson);

                        const el = document.createElement('div');
                        el.className = 'marker';
                        markerCenter = geojson.features[0].center;
                        // make a marker for each feature and add to the map
                        marker = new mapboxgl.Marker(el);
                        // console.log(geojson.features[0].center);
                        // if (oldCenter[0] == geojson.features[0].center[0] && oldCenter[1] == geojson.features[0].center[1]) {
                        //     console.log('ooo');
                        //     geojson.features[0].center[0] += 0.00100;
                        //     // geojson.features[0].center[1] += 0.00100;

                        // }
                        const sameMarkers = coordinatesArray.filter(coordinatesArray => coordinatesArray[0] === markerCenter[0] && coordinatesArray[1] === markerCenter[1]);
                        console.log(sameMarkers);
                        if (sameMarkers.length > 1) {

                            geojson.features[0].center[0] += change;
                            change += 0.000500;
                            console.log();
                        }
                        // console.log('-----');
                        // console.log(geojson.features[0].center);
                        marker.setLngLat(geojson.features[0].center);
                        // $('.marker').css('background-color', 'black');
                        const markerDiv = marker.getElement();
                        markerDiv.addEventListener('mouseenter', () => {
                            // console.log(search);

                            $('#asset_type').html(search.asset_type);
                            $('#asset_name').html(search.asset_name);
                            $('#asset_num').html(search.asset_number);
                            $('#loc').html(search.address + ', ' + search.state + ', ' + search.city + ', ' + search.country_code);
                        });


                        // locDisplay.textContent = search.asset_type,

                        // markerDiv.addEventListener('mouseleave', () => console.log(search));
                        marker.addTo(map);
                        map.flyTo({
                            center: geojson.features[0].center
                        });
                        coordinatesArray.push(geojson.features[0].center);

                        // oldCenter[0] = geojson.features[0].center[0];
                        // oldCenter[1] = geojson.features[0].center[1];
                    }
                });
                key++;
            });
        }
        console.log(coordinatesArray);
    }

    // const magDisplay = document.getElementById('mag');
    const locDisplay = document.getElementById('loc');
    const assetTypeDisplay = document.getElementById('asset_type');
    const assetNameDisplay = document.getElementById('asset_name');
    const assetNumberDisplay = document.getElementById('asset_num');


    // const dateDisplay = document.getElementById('date');

    // We only want to return earthquakes that happened in the last week
    // Use JavaScript to get today's date
    const today = new Date();
    // Use JavaScript to get the date a week ago
    const priorDate = new Date().setDate(today.getDate() - 7);
    // Set that to an ISO8601 timestamp as required by the USGS earthquake API
    const priorDateTs = new Date(priorDate);
    const sevenDaysAgo = priorDateTs.toISOString();

    // Target the span elements used in the sidebar
    // const magDisplay = document.getElementById('mag');
    // const locDisplay = document.getElementById('loc');
    // const dateDisplay = document.getElementById('date');

    // map.on('load', () => {
    //     // When the map loads, add the data from the USGS earthquake API as a source
    //     // map.addSource('earthquakes', {
    //     //     'type': 'geojson',
    //     //     // 'data': 
    //     //     'data': `https://earthquake.usgs.gov/fdsnws/event/1/query?format=geojson&eventtype=earthquake&minmagnitude=1&starttime=${sevenDaysAgo}`, // Use the sevenDaysAgo variable to only retrieve quakes from the past week
    //     //         'generateId': true // This ensures that all features have unique IDs
    //     // });
    //     // console.log(map);

    //     // Add earthquakes as a layer and style it
    //     map.addLayer({
    //         'id': 'earthquakes-viz',
    //         'type': 'circle',
    //         'source': searches,
    //         'paint': {
    //             // The feature-state dependent circle-radius expression will render
    //             // the radius size according to its magnitude when
    //             // a feature's hover state is set to true
    //             'circle-radius': [
    //                 'case', ['boolean', ['feature-state', 'hover'], false],
    //                 [
    //                     'interpolate', ['linear'],
    //                     ['get', 'mag'],
    //                     1,
    //                     8,
    //                     1.5,
    //                     10,
    //                     2,
    //                     12,
    //                     2.5,
    //                     14,
    //                     3,
    //                     16,
    //                     3.5,
    //                     18,
    //                     4.5,
    //                     20,
    //                     6.5,
    //                     22,
    //                     8.5,
    //                     24,
    //                     10.5,
    //                     26
    //                 ],
    //                 5
    //             ],
    //             'circle-stroke-color': '#000',
    //             'circle-stroke-width': 1,
    //             // The feature-state dependent circle-color expression will render
    //             // the color according to its magnitude when
    //             // a feature's hover state is set to true
    //             'circle-color': [
    //                 'case', ['boolean', ['feature-state', 'hover'], false],
    //                 [
    //                     'interpolate', ['linear'],
    //                     ['get', 'mag'],
    //                     1,
    //                     '#fff7ec',
    //                     1.5,
    //                     '#fee8c8',
    //                     2,
    //                     '#fdd49e',
    //                     2.5,
    //                     '#fdbb84',
    //                     3,
    //                     '#fc8d59',
    //                     3.5,
    //                     '#ef6548',
    //                     4.5,
    //                     '#d7301f',
    //                     6.5,
    //                     '#b30000',
    //                     8.5,
    //                     '#7f0000',
    //                     10.5,
    //                     '#000'
    //                 ],
    //                 '#000'
    //             ]
    //         }
    //     });

    //     let quakeID = null;

    //     map.on('mousemove', 'earthquakes-viz', (event) => {
    //         map.getCanvas().style.cursor = 'pointer';
    //         // Set variables equal to the current feature's magnitude, location, and time
    //         const quakeMagnitude = event.features[0].properties.mag;
    //         const quakeLocation = event.features[0].properties.place;
    //         const quakeDate = new Date(event.features[0].properties.time);

    //         if (event.features.length === 0) return;
    //         // Display the magnitude, location, and time in the sidebar
    //         magDisplay.textContent = quakeMagnitude;
    //         locDisplay.textContent = quakeLocation;
    //         dateDisplay.textContent = quakeDate;

    //         // When the mouse moves over the earthquakes-viz layer, update the
    //         // feature state for the feature under the mouse
    //         if (quakeID) {
    //             map.removeFeatureState({
    //                 source: 'earthquakes',
    //                 id: quakeID
    //             });
    //         }

    //         quakeID = event.features[0].id;

    //         map.setFeatureState({
    //             source: 'earthquakes',
    //             id: quakeID
    //         }, {
    //             hover: true
    //         });
    //     });

    //     // When the mouse leaves the earthquakes-viz layer, update the
    //     // feature state of the previously hovered feature
    //     map.on('mouseleave', 'earthquakes-viz', () => {
    //         if (quakeID) {
    //             map.setFeatureState({
    //                 source: 'earthquakes',
    //                 id: quakeID
    //             }, {
    //                 hover: false
    //             });
    //         }
    //         quakeID = null;
    //         // Remove the information from the previously hovered feature from the sidebar
    //         magDisplay.textContent = '';
    //         locDisplay.textContent = '';
    //         dateDisplay.textContent = '';
    //         // Reset the cursor style
    //         map.getCanvas().style.cursor = '';
    //     });
    // });


});
