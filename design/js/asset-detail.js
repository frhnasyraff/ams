$(document).ready(function () {

    var type = getQueryStringValue('type');

    if (type == 'deployed') {
        callAjax('/asset_detail/assetDeployedLocation');
    }else
    if (type == 'warehouse') {
        callAjax('/asset_detail/assetWarehouseLocation');
    }

    function callAjax(url) {
        // get order  company location
        $.ajax({
            "url": url,
            "method": "GET",
            "dataType": "json",
            "data": {},
            success: function(response) {
                if (response.addresses) {
                    $(".marker").remove();
                    window.loadMapBox(response.addresses);
                    window.iniMap.on('load', window.loadMapBox);
                }
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("searchButton").addEventListener("click", function() {
        var assetTypeValue = document.getElementById("assetTypeInput").value.trim();
        var assetNumberValue = document.getElementById("assetNumberInput").value.trim();
        var companyNameElement = document.getElementById("companyNameInput");
        var companyNameValue = companyNameElement ? companyNameElement.value.trim() : '';

        // Check if none of the input fields are filled
        if (assetTypeValue === "" && assetNumberValue === "" && companyNameValue === "") {
            // Handle the exception here (e.g., display an error message)
            tableBody.innerHTML = ""; 
            return; // Exit the function to prevent further execution
        }
        
        // Perform search based on asset type or asset number
        var filteredAssets = [];
        if (assetTypeValue !== "") {
            filteredAssets = filterAssetsByType(assetTypeValue);
        } else if (assetNumberValue !== "") {
            filteredAssets = filterAssetsByNumber(assetNumberValue);
        }
        else if (companyNameValue !== "") {
            filteredAssets = filterAssetsByCompanyName(companyNameValue);
        }

        
        // Display search results in the table
        displaySearchResults(filteredAssets);
    });
});

function filterAssetsByType(type) {
    // Implement logic to filter assets by type
    // Example: You may filter assets array based on type and return filtered result
    return assets.filter(function(asset) {
        return asset.name.toLowerCase().includes(type.toLowerCase());
    });
}

function filterAssetsByNumber(number) {
    // Implement logic to filter assets by number
    // Example: You may filter assets array based on number and return filtered result
    return assets.filter(function(asset) {
        return asset.equipment_registration.toLowerCase().includes(number.toLowerCase());
    });
}

function filterAssetsByCompanyName(companyName) {
    // Implement logic to filter assets by company name
    // Example: You may filter assets array based on company name and return filtered result
    return assets.filter(function(asset) {
        return asset.company_name.toLowerCase().includes(companyName.toLowerCase());
    });
}

function displaySearchResults(assets) {
    var type = getQueryStringValue('type');
    var tableBody = document.querySelector(".table-container table tbody");
    tableBody.innerHTML = ""; // Clear existing table data
    
    if (type == 'deployed') {
        assets.forEach(function(asset) {
            var row = document.createElement("tr");
            row.innerHTML = `
                <td>${asset.name}</td>
                <td>${asset.equipment_name}</td>
                <td>${asset.equipment_registration}</td>
                <td>${asset.company_name}</td>
                <td>${asset.address_line_1}, ${asset.address_line_2}</td>
                
            `;
            tableBody.appendChild(row);
        });
    }else  if (type == 'warehouse') {
     
        assets.forEach(function(asset) {
            var row = document.createElement("tr");
            row.innerHTML = `
                <td>${asset.name}</td>
                <td>${asset.equipment_name}</td>
                <td>${asset.equipment_registration}</td>
                <td>${asset.equipment_status}</td>
                
                
            `;
            tableBody.appendChild(row);
        });
    }else  if (type == 'maintenance') {
     
        assets.forEach(function(asset) {
            var row = document.createElement("tr");
            row.innerHTML = `
                <td>${asset.name}</td>
                <td>${asset.equipment_name}</td>
                <td>${asset.equipment_registration}</td>
                <td>${asset.branch_name}</td>
                <td>UER ${asset.branch_code}</td>
                
                
            `;
            tableBody.appendChild(row);
        });
    }
    

    // Display the table if there are search results
    var tableContainer = document.querySelector(".table-container");
    tableContainer.style.display = assets.length > 0 ? "block" : "none";
}