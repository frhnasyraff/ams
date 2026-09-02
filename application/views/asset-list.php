<?php
// Current list-page defaults added so legacy edit-modal snippets do not raise warnings.
// Legacy snippets below still reference $info/$assets when the same markup is reused for edit screens.
if (!isset($info) || !is_object($info)) {
    $info = (object) [
        'date_installed' => '',
        'equipment_name' => '',
        'equipment_registration' => '',
        'equipment_status' => '',
        'equipment_picture' => '',
        'equipment_id' => '',
        'item' => '',
        'vendor_part_number' => '',
        'manufacturer_drwing_number' => '',
    ];
}
if (!isset($assets)) {
    $assets = [];
}
?><style type="text/css">
    .pagination>li>a {
        border-radius: 10px;
        /*background-color: #fff !important;*/
        /*color: #fff !important;*/
    }

    .pagination>.active>a {
        background-color: #0D2A5A !important;
    }

    #equipments_next>a {
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    #equipments_previous>a {
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    .scrollable-form {
        max-height: 500px;
        /* Set a height for the scrollable area */
        overflow-y: auto;
        /* Enable vertical scrolling */
        overflow-x: hidden;
        padding-right: 15px;
        /* Optional: to prevent content from hiding behind scrollbar */
    }

    .content-div {
        display: none;
    }

    .highlight-row {
        background-color: rgba(246, 194, 62, 0.24) !important;
        /* Light Yellow Highlight */
    }

    .medium-bold {
        font-weight: 500;
        /* Slightly bold but not too much */
    }

    .expiry-box.blue::before {
        content: '' !important;
        position: absolute !important;
        top: 17px !important;
        right: 0px !important;
        width: 100px !important;
        height: 130px !important;
        background-image: url(/design/img/Union-white.png) !important;
        background-size: contain !important;
        background-repeat: no-repeat !important;
        opacity: 4%;
    }

    .expiry-box::before {
        content: '' !important;
        position: absolute !important;
        top: 17px !important;
        right: 0px !important;
        width: 100px !important;
        height: 130px !important;
        background-image: url(/design/img/Union.png) !important;
        background-size: contain !important;
        background-repeat: no-repeat !important;
        opacity: 4%;
    }

    .expiry-box h2 {
        color: #ffffffff !important;
        font-size: -webkit-xxx-large !important;
        font-weight: bold !important;
        margin-left: 0% !important;
        position: relative !important;
        z-index: 10 !important;
    }

    /* The theme previously injected a second decorative icon before every
       filter chip. Keep only the real Font Awesome icon in the button. */
    #defaultDiv.assets-redesign-page .asset-filter-panel .asset-filter-chip::before {
        content: none !important;
        display: none !important;
        margin: 0 !important;
    }

    /* Keep both filter sections on the same complete-card layout. */
    html body:has(#defaultDiv.assets-redesign-page) #defaultDiv.assets-redesign-page
    .asset-filter-panel .equipment_type_filter,
    html body:has(#defaultDiv.assets-redesign-page) #defaultDiv.assets-redesign-page
    .asset-filter-panel .equipment_group_filter {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)) !important;
        gap: 10px !important;
        align-items: stretch !important;
        justify-content: stretch !important;
        overflow: visible !important;
    }

    html body:has(#defaultDiv.assets-redesign-page) #defaultDiv.assets-redesign-page
    .asset-filter-panel .asset-chip-group .asset-filter-chip {
        width: 100% !important;
        min-width: 0 !important;
        max-width: none !important;
        height: 46px !important;
        padding: 0 16px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 9px !important;
        flex: none !important;
        border: 1px solid rgba(48, 112, 173, .42) !important;
        border-radius: 12px !important;
        color: #b7c9df !important;
        background: rgba(5, 20, 39, .62) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .035) !important;
        overflow: hidden !important;
        white-space: nowrap !important;
        box-sizing: border-box !important;
    }

    html body:has(#defaultDiv.assets-redesign-page) #defaultDiv.assets-redesign-page
    .asset-filter-panel .asset-chip-group .asset-filter-chip > i,
    html body:has(#defaultDiv.assets-redesign-page) #defaultDiv.assets-redesign-page
    .asset-filter-panel .asset-chip-group .asset-filter-chip > span {
        position: static !important;
        inset: auto !important;
        width: auto !important;
        height: auto !important;
        margin: 0 !important;
        transform: none !important;
    }

    html body:has(#defaultDiv.assets-redesign-page) #defaultDiv.assets-redesign-page
    .asset-filter-panel .asset-chip-group .asset-filter-chip.active,
    html body:has(#defaultDiv.assets-redesign-page) #defaultDiv.assets-redesign-page
    .asset-filter-panel .asset-chip-group .asset-filter-chip.btn-primary {
        opacity: 1 !important;
        color: #fff !important;
        border-color: #38bdf8 !important;
        background: linear-gradient(135deg, #2563eb, #1da9ed) !important;
        box-shadow: 0 10px 24px rgba(37, 99, 235, .35) !important;
    }

    @media (max-width: 760px) {
        html body:has(#defaultDiv.assets-redesign-page) #defaultDiv.assets-redesign-page
        .asset-filter-panel .equipment_type_filter,
        html body:has(#defaultDiv.assets-redesign-page) #defaultDiv.assets-redesign-page
        .asset-filter-panel .equipment_group_filter {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 480px) {
        html body:has(#defaultDiv.assets-redesign-page) #defaultDiv.assets-redesign-page
        .asset-filter-panel .equipment_type_filter,
        html body:has(#defaultDiv.assets-redesign-page) #defaultDiv.assets-redesign-page
        .asset-filter-panel .equipment_group_filter {
            grid-template-columns: 1fr !important;
        }
    }

    
</style>

<?php
$typeFilter = $_GET['type_filter'] ?? '';
$activeFilter = $_GET['filter'] ?? '';
$filterAssetTypes = $this->steve->asset_types();
$statusIcons = [
    'SERVICEABLE' => 'fa-check-circle',
    'UNSERVICEABLE' => 'fa-times-circle',
    'MAINTENANCE' => 'fa-tools',
    'STORE' => 'fa-warehouse',
    'AVAILABLE' => 'fa-box-open',
];
$assetKpis = [
    ['label' => 'Total Assets', 'value' => $totalAssets, 'icon' => 'fa-cubes', 'tone' => 'blue', 'note' => 'Registered records'],
    ['label' => 'Locations', 'value' => $totalLocations, 'icon' => 'fa-map-marker-alt', 'tone' => 'cyan', 'note' => 'Operating sites'],
    ['label' => 'Serviceable', 'value' => $totalAssetsServiceable, 'icon' => 'fa-check-circle', 'tone' => 'green', 'note' => 'Ready for use'],
    ['label' => 'Unserviceable', 'value' => $UnServiceable_assets, 'icon' => 'fa-times-circle', 'tone' => 'red', 'note' => 'Needs attention'],
    ['label' => 'Maintenance', 'value' => $totalAssetsInMaintenance, 'icon' => 'fa-wrench', 'tone' => 'amber', 'note' => 'Work in progress'],
    ['label' => 'In Store', 'value' => $totalAssetsStore, 'icon' => 'fa-warehouse', 'tone' => 'purple', 'note' => 'Held in storage'],
];
?>

<div id="defaultDiv" class="assets-redesign-page">
    <section class="asset-action-toolbar">
        <div class="asset-toolbar-copy">
            <span class="asset-toolbar-icon"><i class="fas fa-cubes"></i></span>
            <div>
                <span class="asset-toolbar-eyebrow">Fleet Registry</span>
                <h2>Asset Workspace</h2>
                <p>Search, maintain and import every registered asset from one workspace.</p>
            </div>
        </div>

        <div class="asset-toolbar-actions">
            <form action="<?= site_url('assets/uploadExcel') ?>" method="POST" enctype="multipart/form-data" id="excel-upload-form" class="asset-import-form">
                <label class="asset-file-picker" for="excel-file-upload">
                    <i class="fa fa-file-excel"></i>
                    <span id="asset-file-name">Select CSV / XLSX / XLS</span>
                    <input type="file" name="excel_file" id="excel-file-upload" accept=".xlsx,.xls,.csv">
                </label>
                <button class="asset-primary-btn asset-upload-submit" type="submit" id="upload-excel-btn">
                    <i class="fa fa-upload"></i><span>Upload</span>
                </button>
            </form>

            <?php if ($this->user_model->has_perm("add_equipments")) { ?>
                <a class="asset-primary-btn asset-new-btn" href="#addModal" data-toggle="modal" data-target="#addModal" title="Add new Asset">
                    <i class="fa fa-plus"></i><span>New Asset</span>
                </a>
            <?php } ?>
        </div>
    </section>

    <section class="asset-filter-panel">
        <div class="asset-filter-heading">
            <div>
                <span class="asset-toolbar-eyebrow">Quick Filters</span>
                <h3>Find the right assets faster</h3>
                <p>Filter the registry by asset type or current operational status.</p>
            </div>
            <?php if ($typeFilter !== '' || $activeFilter !== ''): ?>
                <a class="asset-clear-filters" href="<?= site_url('assets'); ?>"><i class="fas fa-undo-alt"></i><span>Clear Filters</span></a>
            <?php else: ?>
                <span class="asset-filter-count"><i class="fas fa-filter"></i><?= count($filterAssetTypes); ?> types · <?= count($assetStatus); ?> statuses</span>
            <?php endif; ?>
        </div>
        <div class="asset-filter-block">
            <div class="asset-filter-title"><i class="fa fa-truck"></i><span>Asset Type</span></div>
            <div class="btn-group equipment_type_filter asset-chip-group" role="group" aria-label="Equipments filter actions">
                <button type="button" class="btn asset-filter-chip <?= $typeFilter === '' ? 'btn-primary active' : '' ?>" <?= $typeFilter === '' ? 'disabled' : '' ?> data-filter="" title="Show all asset types"><i class="fas fa-th-large"></i><span>All Types</span></button>
                <?php foreach ($filterAssetTypes as $t): ?>
                    <?php $isActive = (string) $typeFilter == (string) $t->asset_id; ?>
                    <button type="button" class="btn asset-filter-chip text-uppercase tip <?= $isActive ? 'btn-primary active' : '' ?>" data-filter="<?= $t->asset_id; ?>" title="Show only <?= $t->name; ?>">
                        <i class="fas fa-cube"></i><span><?= $t->name ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="asset-filter-block">
            <div class="asset-filter-title"><i class="fa fa-folder-open"></i><span>Status</span></div>
            <div id="orders-list" class="project-tab btn-group equipment_group_filter asset-chip-group" role="group" aria-label="Equipment groups filter">
                <button id="nav-tab" type="button" class="btn asset-filter-chip nav-item nav-link <?= $activeFilter === '' ? 'active btn-primary' : '' ?>" <?= $activeFilter === '' ? 'disabled' : '' ?> data-filter="" title="Show all equipment groups">
                    <i class="fas fa-th-list"></i><span>All Statuses</span>
                </button>
                <?php foreach ($assetStatus as $t) { $isActive = strtoupper($activeFilter) === strtoupper($t->name); ?>
                    <button type="button" class="btn asset-filter-chip nav-item nav-link text-uppercase tip <?= $isActive ? 'active btn-primary' : '' ?>" data-filter="<?= $t->name; ?>" title="Show only <?= $t->name; ?>">
                        <i class="fas <?= $statusIcons[strtoupper($t->name)] ?? 'fa-tag'; ?>"></i><span><?= $t->name; ?></span>
                    </button>
                <?php } ?>
            </div>
        </div>
    </section>

    <section class="asset-kpi-grid">
        <?php foreach ($assetKpis as $kpi): ?>
            <article class="asset-kpi-card tone-<?= $kpi['tone']; ?>">
                <span class="asset-kpi-icon"><i class="fa <?= $kpi['icon']; ?>"></i></span>
                <div class="asset-kpi-copy">
                    <small><?= $kpi['label']; ?></small>
                    <strong><?= $kpi['value']; ?></strong>
                    <span><?= $kpi['note']; ?></span>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="asset-table-shell">
        <form action="<?= site_url('assets/generateQrPDF') ?>" method="POST">
            <div class="asset-table-toolbar">
                <div>
                    <h3>Asset Records</h3>
                    <p>Review, select, print QR/RFID and update asset records.</p>
                </div>
                <div class="asset-table-actions">
                    <button type="submit" formaction="<?= site_url('assets/printRFID') ?>" class="asset-icon-btn asset-download-btn" title="Print RFID">
                        <i class="fa fa-download"></i><span>Print RFID</span>
                    </button>
                    <button type="submit" formaction="<?= site_url('assets/generateQrPDF') ?>" class="asset-icon-btn asset-qr-btn" title="Generate QR">
                        <i class="fa fa-qrcode"></i><span>Generate QR</span>
                    </button>
                    <?php if ($this->user_model->has_perm("add_equipments")) { ?>
                        <a class="asset-icon-btn assets-table-add asset-add-btn" href="#addModal" data-toggle="modal" data-target="#addModal" title="Add new Asset">
                            <i class="fa fa-plus"></i><span>New Asset</span>
                        </a>
                    <?php } ?>
                </div>
            </div>

            <div class="asset-table-card table-responsive">
                <table class="table" id="assets" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><a href="javascript:void(0)" id="select_all_checkboxes">Select All</a></th>
                            <th>Components</th>
                            <th>Asset Type</th>
                            <th>System Name</th>
                            <th>Asset ID</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </form>
    </section>
</div>

<script>
    document.addEventListener('change', function (event) {
        if (event.target && event.target.id === 'excel-file-upload') {
            var label = document.getElementById('asset-file-name');
            if (label) {
                label.textContent = event.target.files && event.target.files[0] ? event.target.files[0].name : 'Select CSV / XLSX / XLS';
            }
        }
    });
</script>
<!-- eye icone button modal  -->
<div class="modal fade" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="equipmentModalLabel">List Component Details</h5>
                <button type="button" class="btn-close hideEyeModal" data-bs-dismiss="modal"
                    aria-label="Close">X</button>
            </div>
            <div class="modal-body" id="modal-body-content">
                <!-- Dynamic content will be injected here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary hideEyeModal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php if ($this->user_model->has_perm("add_equipments")) { ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="addModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Asset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form class="form-horizontal scrollable-form" action="<?= site_url("assets/add"); ?>" method="post"
                    enctype="multipart/form-data">
                    <div class="modal-body row">

                        <!-- Asset Type Dropdown -->
                        <div class="form-group col-sm-4 uppercase">
                            <label>Asset type <sup style="color:red; font-size:8px;">Required</sup></label><br />
                            <select class="form-control" id="equipment_type_calibration" name="equipment_type">
                                <option value="">--Select--</option>
                                <?php foreach ($assetTypes as $et) { ?>
                                    <option value="<?= $et->asset_id; ?>" data-manufacturer="<?= $et->manufacturer_id; ?>"
                                        data-part-number="<?= $et->part_number_id; ?>">
                                        <?= $et->name ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Manufacturer Drawing Number Dropdown -->
                        <!-- Manufacturer Dropdown -->
                        <div class="col-sm-4" id="manufacturerField">
                            <label>Asset Manufacturer</label> <sup style="color:red; font-size:8px;">Required</sup><br />
                            <select class="form-control" id="manufacturerSelect" name="equipment_manufacturer">
                                <option value="">--Select--</option>
                                <?php foreach ($manufacturer_number as $mn) { ?>
                                    <option value="<?= $mn->id ?>"><?= $mn->manufacturer_name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <!-- Serial Number -->
                        <?= $this->steve->form_group_label_input("text", "serial_number", "Serial Number", "col-sm-4", 1); ?>

                        <div class="form-group col-sm-4 date_picker_now">
                            <label for="form_date_installed">Date Installed<sup>REQUIRED</sup></label>
                            <!-- <input type="text" name="date_installed" class="form-control" step="0.00001" id="form_date_installed" min="0" placeholder="Date Installed" value="<?= $info->date_installed ?>" required="" autocomplete="off" maxlength="10"> -->
                            <input type="text" name="date_installed" class="form-control" step="0.00001" id="form_date_installed" min="0" placeholder="Date Installed" value="<?= isset($info) ? $info->date_installed : '' ?>" required="" autocomplete="off" maxlength="10">
                        </div>
                        <!-- <?= $this->steve->form_group_label_input("text", "name", "System name", "col-sm-4", 0, $info->equipment_name, 125); ?> -->
                        <?= $this->steve->form_group_label_input("text", "name", "System name", "col-sm-4", 0, isset($info) ? $info->equipment_name : '', 125); ?>
                        <!-- <?= $this->steve->form_group_label_input("text", "equipment_registration", "Registration number", "col-sm-4 uppercase", 0, $info->equipment_registration, 125); ?> -->
                        <?= $this->steve->form_group_label_input("text", "equipment_registration", "Registration number", "col-sm-4 uppercase", 0, isset($info) ? $info->equipment_registration : '', 125); ?>



                        <!-- <?= $this->steve->form_group_label_input("text", "purchase_date", "Purchase date", "col-sm-4 date_picker_now"); ?> -->

                        <!-- <?= $this->steve->form_group_label_select("equipment_statuss", "Asset statuss", $this->steve->equipment_statuses(), "", "", "col-sm-4", $info->equipment_status); ?> -->

                        <div class="form-group col-sm-4 uppercase">
                            <label>Asset Status</label><br />
                            <select class="form-control" class="p-0" name="equipment_status">
                                <option value="">--Select--</option>
                                <?php foreach ($assetStatus as $as) { ?>
                                    <option value="<?= $as->name ?>"><?= $as->name ?></option>
                                <?php } ?>
                            </select>
                        </div>




                        <!-- Manufacturer Drawing Number Dropdown -->
                        <div class="form-group col-sm-4 uppercase">
                            <label> Drwing Number</label><br />
                            <select class="form-control" class="p-0" name="drawing_number">
                                <option value="">--Select--</option>
                                <?php foreach ($drawing_numbers as $dn) { ?>
                                    <option value="<?= $dn->drawing_number ?>"><?= $dn->drawing_number ?></option>
                                <?php } ?>
                            </select>
                        </div>


                        <div class="form-group col-sm-4 uppercase">
                            <label>State </label><br />
                            <select class="form-control" id="stateSelect" name="state_id">
                                <option value="">--Select--</option>
                                <?php foreach ($states as $state) { ?>
                                    <option value="<?= $state->id; ?>"><?= $state->state_name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <!-- <sup style="color:red; font-size:8px;">Required</sup>  -->
                        <div class="form-group col-sm-4 uppercase">
                            <label>Locations </label><br />
                            <select class="form-control" id="locationSelect" name="location_id">
                                <option value="">--Select--</option>
                            </select>
                        </div>

                        <!-- <div class="form-group col-sm-4 uppercase">
                        <label>Locations</label><br />
                        <select class="form-control" class="p-0" name="location_id">
                            <option value="">--Select--</option>
                            <?php foreach ($locations as $branch) { ?>
                            <option value="<?= $branch->id; ?>"><?= $branch->name ?></option>
                            <?php } ?>
                        </select>
                    </div> -->

                        <div class="form-group col-sm-4 uppercase">
                            <label>Mnaged By </label><br />
                            <select class="form-control" class="p-0" name="ownership">
                                <option value="">--Select--</option>
                                <?php foreach ($managedBys as $manage) { ?>
                                    <option value="<?= $manage->id; ?>"><?= $manage->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group col-sm-4 uppercase">
                            <label>Store Location</label><br />
                            <select class="form-control" class="p-0" name="store_location">
                                <option value="0">--Select--</option>
                                <?php foreach ($storeLocation as $sl) { ?>
                                    <option value="<?= $sl->id ?>"><?= $sl->name ?></option>
                                <?php } ?>
                            </select>
                        </div>

                    <div class="form-group col-sm-4 uppercase">
                        <label>Cost (Purchase Price)</label><br />
                        <input type="number" step="0.01" class="form-control" name="price_of_purchase" 
                            placeholder="Purchase Price" min="0">
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="purchase_date">Purchase Date <sup style="color:red; font-size:8px;">Required</sup></label>
                        <input type="date" name="purchase_date" class="form-control" id="purchase_date" 
                            max="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="company_name">Company Name <sup style="color:red; font-size:8px;">Required</sup></label>
                        <input type="text" name="company_name" class="form-control" id="company_name" 
                            placeholder="Company Name" required>
                    </div>

                    <!-- <div class="form-group col-sm-4 uppercase">
                        <label>Disposal Method</label><br />
                        <select class="form-control" name="disposal_method_id">
                            <option value="">--Select--</option>
                            <?php if(isset($disposal_methods) && !empty($disposal_methods)): ?>
                                <?php foreach ($disposal_methods as $dm) { ?>
                                    <option value="<?= $dm->id ?>"><?= $dm->disposal_method ?></option>
                                <?php } ?>
                            <?php else: ?>
                                <option value="">No Disposal Methods Found</option>
                            <?php endif; ?>
                        </select>
                    </div>

                        <div class="form-group col-sm-4 uppercase">
                            <label>Useful Life (Years)</label><br />
                            <input type="number" class="form-control" name="useful_life_years" 
                                placeholder="Useful Life in Years" min="0">
                        </div>

                        <div class="form-group col-sm-4 uppercase">
                            <label>Salvage Value</label><br />
                            <input type="number" step="0.01" class="form-control" name="salvage_value" 
                                placeholder="Salvage Value" min="0">
                        </div> -->

                        <?= $this->steve->form_group_label_textarea("notes", "Notes", "col-sm-4"); ?>

                        <!-- Vendor Part Number Dropdown -->
                        <div class="col-sm-4" id="partNumberField">
                            <label>Vendor Part Number</label><sup style="color:red; font-size:8px;">Required</sup><br />

                            <select class="form-control" id="partNumberSelect" name="vendor_part_number_id">
                                <option value="">--Select--</option>
                                <?php foreach ($part_numbers as $pn) { ?>
                                    <option value="<?= $pn->id ?>"><?= $pn->part_number ?></option>
                                <?php } ?>
                            </select>
                        </div>


                        <div class="form-group col-sm-4 uppercase" id="faulty_type_field">
                            <label>Faulty Type</label><br />
                            <select class="form-control" id="faulty_type" class="p-0" name="faulty_type">
                                <option value="">--Select--</option>
                                <?php foreach ($faulty as $f) { ?>
                                    <option value="<?= $f->id; ?>"><?= $f->fault_type; ?></option>
                                <?php } ?>
                            </select>
                        </div>



                        <!-- celebration -->
                        <div class="form-group col-sm-4 uppercase" id="calibration_date" style="display: none;">
                            <label>1st Calibration Date</label><br />
                            <input type="date" class="form-control" name="calibration_date"
                                placeholder="1st Calibration Date">
                        </div>

                        <div class="form-group col-sm-4 uppercase" id="frequency_day" style="display: none;">
                            <label>Frequency In Days</label><br />
                            <input type="text" class="form-control" name="frequency_day" placeholder="90">
                        </div>

                        <div class="form-group col-sm-4 uppercase" id="reminder_day" style="display: none;">
                            <label>Reminder In Days</label><br />
                            <input type="text" class="form-control" name="reminder_day" placeholder="7">
                        </div>



                        <!-- maintenance -->

                        <div class="form-group col-sm-4 uppercase" id="maintenance_date" style="display: none;">
                            <label>Maintenance Date</label><br />
                            <input type="date" class="form-control" name="maintenance_date"
                                placeholder="1st maintenance Date">
                        </div>

                        <div class="form-group col-sm-4 uppercase" id="frequency_year" style="display: none;">
                            <label>Frequency In Years</label><br />
                            <input type="text" class="form-control" name="frequency_year" placeholder="2">
                        </div>

                        <div class="form-group col-sm-4 uppercase" id="maintenance_reminder_day" style="display: none;">
                            <label>Reminder In Days</label><br />
                            <input type="text" class="form-control" name="maintenance_reminder_day" placeholder="30">
                        </div>

                        <!-- check faulty -->
                        <div class="col-md-12"></div>
                        <div class="col-md-12">
                            <label for="">Check for Faulty type</label>
                            <input type="checkbox" id="faulty_type_toggle">
                        </div>

                        <!-- Asset Picture Upload Section -->
                        <div class="form-group col-sm-6 uppercase mt-4">
                            <div class="card shadow mb-6 tabradius">
                                <div class="card-body">
                                    <div class="bg-white card-header py-6">
                                        <h6 class="bg-white m-0 font-weight-bold text-primary">Asset Picture</h6>
                                    </div>
                                    <div class="form-group">
                                        <!-- Picture Upload (File Input) -->
                                        <input type="file" name="equipment_picture" id="assetImage" accept="image/*"
                                            onchange="previewImage();" />
                                    </div>
                                    <div class="form-group">
                                        <!-- Show the existing image if available -->
                                        <?php if ($info->equipment_picture) { ?>
                                            <img id="imagePreview" class="rounded-square img-thumbnail"
                                                src="<?= site_url("storage/Asset-" . $info->equipment_id . "/" . $info->equipment_picture); ?>" />
                                        <?php } else { ?>
                                            <img id="imagePreview" class="rounded-square img-thumbnail mb-2" src="#"
                                                style="display:none;" />
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group col-sm-4">
                            <label for="invoice">Invoice (PDF, Excel, Word)</label>
                            <input type="file" name="invoice" class="form-control" id="invoice" 
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        </div>


                    </div>

                    <!-- Add Items Section -->
                    <div class="bg-white card-header py-3">
                        <h6 class="bg-white m-0 font-weight-bold text-primary">Add Component</h6>
                    </div>

                    <!-- Container for items (Ensure this container exists) -->
                    <div id="itemContainer">
                        <div class="itemSection">
                            <div class="modal-body row">
                                <?= $this->steve->form_group_label_input("text", "item[]", "Component", "col-sm-4", 0, $info->item, 125); ?>

                                <div class="form-group col-sm-4 uppercase">
                                    <label>Component Type <sup style="color:red; font-size:8px;">Required</sup> </label><br />
                                    <select class="form-control item-type-calibration" id="item_type" name="item_type[]">
                                        <option value="0">--Select--</option>
                                        <?php foreach ($itemTypes as $it) { ?>
                                            <option value="<?= $it->id ?>"><?= $it->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- Vendor Part Number Dropdown -->
                                <div class="col-sm-4 form-group">
                                    <label for="vendor_part_number">Vendor Part Number</label>
                                    <select name="vendor_part_number[]" id="part_number_item" class="form-control">
                                        <option value="">Select Vendor Part Number</option>
                                        <?php foreach ($part_number as $part): ?>
                                            <option value="<?= $part['part_number']; ?>"
                                                <?= ($part['part_number'] == $info->vendor_part_number) ? 'selected' : ''; ?>>
                                                <?= $part['part_number']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Manufacturer Name -->
                                <div class="form-group col-sm-4 uppercase">
                                    <label>Manufacturer Name</label><br />
                                    <select class="form-control" class="p-0" id="menufacturer_item" name="manufacturer_name[]">
                                        <option value="">--Select--</option>
                                        <?php foreach ($manufacturer_number as $mn) { ?>
                                            <option value="<?= $mn->manufacturer_name ?>"><?= $mn->manufacturer_name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>


                                <!-- Manufacturer Drawing Number Dropdown -->
                                <div class="col-sm-4 form-group">
                                    <label for="manufacturer_drawing_number">Manufacturer Drawing #</label>
                                    <select name="manufacturer_drwing_number[]" class="form-control">
                                        <option value="">Select Manufacturer Drawing Number</option>
                                        <?php foreach ($drawing_number as $drawing): ?>
                                            <option value="<?= $drawing['drawing_number']; ?>"
                                                <?= ($drawing['drawing_number'] == $info->manufacturer_drwing_number) ? 'selected' : ''; ?>>
                                                <?= $drawing['drawing_number']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>



                                <div class="form-group col-sm-4 uppercase">
                                    <label>Component Status</label><br />
                                    <select class="form-control" class="p-0" name="item_status[]">
                                        <option value="0">--Select--</option>
                                        <?php foreach ($itemStatus as $is) { ?>
                                            <option value="<?= $is->id ?>"><?= $is->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group col-sm-4 uppercase">
                                    <label>Store Location</label><br />
                                    <select class="form-control" class="p-0" name="store_location_item[]">
                                        <option value="0">--Select--</option>
                                        <?php foreach ($storeLocation as $sl) { ?>
                                            <option value="<?= $sl->id ?>"><?= $sl->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>



                                <!-- Item Picture Upload -->
                                <div class="col-sm-4 form-group">
                                    <label for="item_picture">Component Picture</label>
                                    <input type="file" name="item_picture[]" accept="image/*" class="form-control" />
                                </div>

                                <div class="form-group col-sm-4 uppercase" id="faulty_type_field_item">
                                    <label>Faulty Type</label><br />
                                    <select class="form-control" id="faulty_type_item" class="p-0"
                                        name="faulty_type_item[]">
                                        <option value="">--Select--</option>
                                        <?php foreach ($faulty as $f) { ?>
                                            <option value="<?= $f->id; ?>"><?= $f->fault_type; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- celebration -->

                                <div class="form-group col-sm-4 uppercase calibration_date_item" id="calibration_date_item"
                                    style="display: none;">
                                    <label>1st Calibration Date</label><br />
                                    <input type="date" class="form-control" name="calibration_date_item[]"
                                        placeholder="1st Calibration Date">
                                </div>

                                <div class="form-group col-sm-4 uppercase" id="frequency_day_item" style="display: none;">
                                    <label>Frequency In Days</label><br />
                                    <input type="text" class="form-control" name="frequency_day_item[]" placeholder="90">
                                </div>

                                <div class="form-group col-sm-4 uppercase" id="reminder_day_item" style="display: none;">
                                    <label>Reminder In Days</label><br />
                                    <input type="text" class="form-control" name="reminder_day_item[]" placeholder="7">
                                </div>


                                <!-- maintenance -->

                                <div class="form-group col-sm-4 uppercase maintenance_date_item" id="maintenance_date_item"
                                    style="display: none;">
                                    <label>Maintenance Date</label><br />
                                    <input type="date" class="form-control" name="maintenance_date_item[]"
                                        placeholder="maintenance Date">
                                </div>

                                <div class="form-group col-sm-4 uppercase" id="frequency_year_item" style="display: none;">
                                    <label>Frequency In Years</label><br />
                                    <input type="text" class="form-control" name="frequency_year_item[]" placeholder="90">
                                </div>

                                <div class="form-group col-sm-4 uppercase" id="maintenance_reminder_day_item" style="display: none;">
                                    <label>Maintenance Reminder In Days</label><br />
                                    <input type="text" class="form-control" name="maintenance_reminder_day_item[]" placeholder="7">
                                </div>


                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <label for="">Check for Faulty type</label>
                                    <input type="checkbox" id="faulty_type_toggle_item">
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Button to Add More Items -->
                    <div class="col-sm-4">
                        <button type="button" class="btn btn-primary" id="addItemButton">Add More Component</button>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Add Asset</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
<?php } ?>

<!-- JavaScript to handle adding/removing item fields -->
<script>
    // Wait for the DOM to load
    document.addEventListener("DOMContentLoaded", function() {

        // Get references to the item container and the "Add More Items" button
        const itemContainer = document.getElementById('itemContainer');
        const addItemButton = document.getElementById('addItemButton');

        // Function to add remove button to a section
        function addRemoveButton(section) {
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.classList.add('btn', 'btn-danger', 'removeItemButton', 'form-group');
            removeButton.textContent = 'X';
            section.querySelector('.modal-body').appendChild(removeButton);

            // Remove the section on "X" button click
            removeButton.addEventListener('click', () => section.remove());
        }

        // Toggle visibility of faulty type field based on checkbox status
        function addFaultyCheckboxToggle(section) {
            const faultyCheckbox = section.querySelector('#faulty_type_toggle_item');
            const faultyTypeField = section.querySelector('#faulty_type_field_item');

            faultyCheckbox.addEventListener('change', function() {
                faultyTypeField.style.display = faultyCheckbox.checked ? 'block' : 'none';
            });
        }

        // Get the first item section (original) and flag it as non-deletable
        const originalItemSection = document.querySelector('.itemSection');

        // Event listener for the "Add More Items" button
        addItemButton.addEventListener('click', function() {
            // Show the container if hidden
            if (itemContainer.style.display === 'none') {
                itemContainer.style.display = 'block';
            }

            // Clone the original item section and reset input values
            const newItemSection = originalItemSection.cloneNode(true);
            newItemSection.style.display = 'block'; // Make cloned section visible
            newItemSection.querySelectorAll('input').forEach(input => input.value = '');
            newItemSection.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

            // Hide specific fields in the cloned section by default
            newItemSection.querySelector('#calibration_date_item').style.display = 'none';
            newItemSection.querySelector('#frequency_day_item').style.display = 'none';
            newItemSection.querySelector('#reminder_day_item').style.display = 'none';

            newItemSection.querySelector('#maintenance_date_item').style.display = 'none';
            newItemSection.querySelector('#frequency_year_item').style.display = 'none';
            newItemSection.querySelector('#maintenance_reminder_day_item').style.display = 'none';

            // Add the remove button and faulty checkbox toggle to the new section
            addRemoveButton(newItemSection);
            addFaultyCheckboxToggle(newItemSection);

            // Append the cloned item section to the container
            itemContainer.appendChild(newItemSection);

            // Add remove button for cloned item section
            // var removeButton = document.createElement('button');
            // removeButton.type = 'button';
            // removeButton.classList.add('btn', 'btn-danger', 'removeItemButton', 'form-group');
            // removeButton.textContent = 'X';
            // newItemSection.querySelector('.modal-body').appendChild(removeButton);

            // // Remove the section when the "X" button is clicked
            // removeButton.addEventListener('click', function() {
            //     newItemSection.remove();
            // });

            // Toggle faulty type field visibility based on checkbox
            var faultyCheckbox = newItemSection.querySelector('#faulty_type_toggle_item');
            faultyCheckbox.addEventListener('change', function() {
                var faultyTypeField = newItemSection.querySelector('#faulty_type_field_item');
                faultyTypeField.style.display = faultyCheckbox.checked ? 'block' : 'none';
            });


        });


        // Disable removal for the original item section
        var initialItemSections = document.querySelectorAll('.itemSection');
        initialItemSections.forEach(function(section) {
            // Only apply the delete functionality if it's a cloned section
            if (section !== originalItemSection) {
                addRemoveButtonEvent(section);
            }
        });
    });
</script>

<!-- JavaScript to preview the selected image -->
<script>
    function previewImage() {
        var file = document.getElementById("assetImage").files[0];
        var reader = new FileReader();
        reader.onloadend = function() {
            var preview = document.getElementById("imagePreview");
            preview.src = reader.result;
            preview.style.display = "block"; // Show the image
        }
        if (file) {
            reader.readAsDataURL(file);
        } else {
            document.getElementById("imagePreview").src = "#";
            document.getElementById("imagePreview").style.display = "none"; // Hide if no image selected
        }
    }
</script>
<script>
    window.assets = <?php echo json_encode($assets); ?>;
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="modal fade" tabindex="-1" role="dialog" id="addMileageModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add mileage - <span class="equipment_registration"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("assets/add_mileage"); ?>" method="post">
                <div class="modal-body row">

                    <?= $this->steve->form_group_label_input("number", "mileage", "Current mileage", "col-sm-12", 1, '', 10); ?>

                    <?= $this->steve->form_group_label_input("text", "record_date", "Record date", "col-sm-12 date_picker_now", 1, '', 10); ?>

                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id" class="equipment_id" />
                    <button type="submit" class="btn btn-success">Add mileage</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="addScheduledMaintenanceModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add scheduled maintenance - <span class="equipment_registration"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("assets/add_scheduled_maintenance"); ?>" method="post">
                <div class="modal-body">

                    <?= $this->steve->form_group_label_input("text", "next_maintenance_date", "Next scheduled maintenance date", "date_picker", 0, '', 10); ?>

                    <?= $this->steve->form_group_label_input("number", "next_maintenance_mileage", "Next scheduled maintenance mileage"); ?>

                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id" class="equipment_id" />
                    <button type="submit" class="btn btn-success">Add scheduled maintenance</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>





