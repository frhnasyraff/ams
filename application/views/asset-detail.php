<style>
    .filter-input {
        border: 1px solid #0186D0;
        outline: none;
        padding: 8px;
        color: #0186D0;
        font-size: 14px;
        border-radius: 5px;
        width: 103px;
        margin: 1px;
        margin-bottom: 12px;
    }
</style>

<div class="row align-items-start">
    <div class="col-lg-7 order-lg-0 order-md-1">
        <div id="map" style="height: 500px; width: 98%"></div>
        <div class='quake-info'>
            <!-- <div><strong>Magnitude:</strong> <span id='mag'></span></div> -->
            <div><strong>Location:</strong> <span id='loc'></span></div>
            <div><strong>Asset Type:</strong> <span id='asset_type'></span></div>
            <div><strong>Asset Name:</strong> <span id='asset_name'></span></div>
            <div><strong>Asset Number:</strong> <span id='asset_num'></span></div>
            <!-- <div><strong>Date:</strong> <span id='date'></span></div> -->
        </div>
    </div>
    <div class="col-lg-5 mt-25 order-lg-1 order-md-0">
        <div class="row">
            <div class="col-md-12">
                <div class="folder detail">
                    <div class="content-table">
                        <h2 class="heading" style="color:#FAA202;"><?= $type ?></h2>
                        <div class="divider"></div>
                        <?php if ($type  == 'deployed') { ?>
                            <h2 class="total"><?= $assets_deployed ?></h2>
                        <?php } else { ?>
                            <h2 class="total"><?= count($assets) ?></h2>
                        <?php } ?>
                    </div>


                    <div class="seperator"></div>

                </div>
                <div class="table-container">
                    <?php if ($type  == 'deployed') { ?>
                        <div>
                            <div class="filter-container">
                                <div class="right-filters">
                                    <select name="search" class="filter-input" id="companyNameInput">
                                        <option value="">Company Name</option>
                                        <?php foreach ($assets as $asset) { ?>
                                            <option value="<?= $asset->company_name ?>"><?= $asset->company_name ?></option>
                                        <?php } ?>
                                    </select>

                                    <select name="search" class="filter-input" id="assetTypeInput">
                                        <option value="">Asset Type</option>
                                        <?php
                                        // Create an array to store unique asset names
                                        $uniqueNames = array();

                                        // Loop through the assets
                                        foreach ($assets as $asset) {
                                            // Check if the asset name is not already in the $uniqueNames array
                                            if (!in_array($asset->name, $uniqueNames)) {
                                                // Add the asset name to the $uniqueNames array
                                                $uniqueNames[] = $asset->name;
                                                // Output the option element with the asset name
                                        ?>
                                                <option value="<?= $asset->name ?>"><?= $asset->name ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>


                                    <input type="text" name="search" id="assetNumberInput" class="filter-input form-controll" placeholder="Asset Number">

                                    <button id="searchButton" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
                                </div>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Asset Type</th>
                                    <th>Asset Name</th>
                                    <th>Asset Reg</th>
                                    <th>Company Name</th>
                                    <th>Company Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($_GET['assetTypeInput']) || !empty($_GET['assetNumberInput'])) { ?>
                                    
                                <?php } ?>
                            </tbody>
                        </table>
                        
                    <?php } else if ($type  == 'maintenance') { ?>
                        <div>
                            <div class="filter-container">
                                <div class="right-filters">
                                   
                                    <select name="search" class="filter-input" id="assetTypeInput">
                                        <option value="">Asset Type</option>
                                        <?php 
                                        // Create an array to store unique asset names
                                        $uniqueNames = array();

                                        // Loop through the assets
                                        foreach ($assets as $asset) {
                                            // Check if the asset name is not already in the $uniqueNames array
                                            if (!in_array($asset->name, $uniqueNames)) {
                                                // Add the asset name to the $uniqueNames array
                                                $uniqueNames[] = $asset->name;
                                                // Output the option element with the asset name
                                        ?>
                                                <option value="<?= $asset->name ?>"><?= $asset->name ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>


                                    <input type="text" name="search" id="assetNumberInput" class="filter-input form-controll" placeholder="Asset Number">

                                    <button id="searchButton" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
                                </div>
                            </div>
                        </div>
                        <table class="table">
                            <thead>

                                <tr>
                                    <th>Asset Type</th>
                                    <th>Asset Name</th>
                                    <th>Asset Reg</th>
                                    <th>Branch Name</th>
                                    <th>Branch Address</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($_GET['assetTypeInput']) || !empty($_GET['assetNumberInput'])) { ?>
                                    
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } else if ($type  == 'warehouse') { ?>
                        <div>
                            <div class="filter-container">
                                <div class="right-filters">

                                    <select name="search" class="filter-input" id="assetTypeInput">
                                        <option value="">Asset Type</option>
                                        <?php
                                        // Create an array to store unique asset names
                                        $uniqueNames = array();

                                        // Loop through the assets
                                        foreach ($assets as $asset) {
                                            // Check if the asset name is not already in the $uniqueNames array
                                            if (!in_array($asset->name, $uniqueNames)) {
                                                // Add the asset name to the $uniqueNames array
                                                $uniqueNames[] = $asset->name;
                                                // Output the option element with the asset name
                                        ?>
                                                <option value="<?= $asset->name ?>"><?= $asset->name ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>


                                    <input type="text" name="search" id="assetNumberInput" class="filter-input form-controll" placeholder="Asset Number">

                                    <button id="searchButton" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
                                </div>
                            </div>
                        </div>                        
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Asset Type</th>
                                    <th>Asset Name</th>
                                    <th>Asset Reg</th>
                                    <th>Asset Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($_GET['assetTypeInput']) || !empty($_GET['assetNumberInput'])) { ?>
                                    
                                <?php }
                                
                                 else {
                                                echo "<tr><td colspan='100%' class='text-center'>No records found</td></tr>";
                                            } ?>
                            </tbody>
                        </table>                            
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.assets = <?php echo json_encode($assets); ?>;
</script>
<script src="http://localhost\design\js\asset-detail.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    // $(document).ready(function() {
    //     $("#filterButton").on("click", function() {
    //         var value1 = $("#myInput").val().toLowerCase();
    //         var value2 = $("#myInput2").val().toLowerCase();

    //         $(".myTable tr").each(function() {
    //             var firstColumnText = $(this).find("td:nth-child(1)").text().toLowerCase();
    //             var thirdColumnText = $(this).find("td:nth-child(3)").text().toLowerCase();
    //             var showRow = firstColumnText.indexOf(value1) > -1 && thirdColumnText.indexOf(value2) > -1;
    //             $(this).toggle(showRow);
    //         });
    //     });
    // });

    $(document).ready(function() {
    $("#searchButton").on("click", function() {
        // Check if the companyNameInput element exists
        var companyNameInput = $("#companyNameInput");
        var companyName = companyNameInput.length > 0 ? companyNameInput.val().toLowerCase() : '';

        var assetType = $("#assetTypeInput").val().toLowerCase();
        var assetNumber = $("#assetNumberInput").val().toLowerCase();

        $(".table tr:not(:has(th))").each(function() {
            var companyColumnText = $(this).find("td:nth-child(4)").text().toLowerCase();
            var assetTypeColumnText = $(this).find("td:nth-child(1)").text().toLowerCase();
            var assetNumberColumnText = $(this).find("td:nth-child(3)").text().toLowerCase();
            
            var showRow = true;

            // Check if filters are applied and hide row if necessary
            if (companyName !== '' && companyColumnText.indexOf(companyName) === -1) {
                showRow = false;
            }
            if (assetType !== '' && assetTypeColumnText.indexOf(assetType) === -1) {
                showRow = false;
            }
            if (assetNumber !== '' && assetNumberColumnText.indexOf(assetNumber) === -1) {
                showRow = false;
            }

            $(this).toggle(showRow);
        });
    });

    // Add event listener to reset filters when input fields are cleared
    $("#companyNameInput, #assetTypeInput, #assetNumberInput").on("input", function() {
        if ($(this).val() === '') {
            $(".table tr:not(:has(th))").show(); // Show all rows
        }
    });
});



</script>