<style type="text/css">
    .pagination>li>a {
        border-radius: 10px;
        /*background-color: #fff !important;*/
        /*color: #fff !important;*/
    }

    .pagination>.active>a {
        background-color: #07083dff !important;
    }

    /* Simple Loader Styles */
.dataTables_processing {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
    background: rgba(255, 255, 255, 0.9);
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

/* Hide the global loader when DataTable is processing */
.dataTables_wrapper .dataTables_processing {
    display: none !important;
}

    #datatable-loader {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.95);
    z-index: 9999;
    text-align: center;
    backdrop-filter: blur(5px);
}

#datatable-loader > div {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    min-width: 200px;
}

#datatable-loader .spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 4px;
    color: #07083dff;
}

#datatable-loader > div > div {
    margin-top: 15px;
    font-weight: 600;
    color: #333;
}

/* Table styling */
#equipment_new_maintenance th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #333;
    padding: 12px 15px;
    border-bottom: 2px solid #dee2e6;
}

#equipment_new_maintenance td {
    padding: 10px 15px;
    vertical-align: middle;
}

.badge-success {
    background-color: #28a745 !important;
}

.badge-warning {
    background-color: #ffc107 !important;
    color: #212529;
}

.badge-secondary {
    background-color: #6c757d !important;
}

.btn-group .btn {
    padding: 5px 10px;
    margin: 0 2px;
}

/* Modal styling */
.modal-body strong {
    color: #333;
    min-width: 120px;
    display: inline-block;
}

.modal-body .table th {
    background-color: #f8f9fa;
}

    #datatable-loader {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.9);
    z-index: 9999;
    text-align: center;
}

#datatable-loader > div {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

    #equipment_usage_next>a,
    #equipment_fuel_next>a,
    #equipment_maintenance_next>a,
    #equipment_consumable_next>a,
    #consumable_purchases_next>a {
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    #equipment_usage_previous>a,
    #equipment_fuel_previous>a,
    #equipment_maintenance_previous>a,
    #equipment_consumable_previous>a,
    #consumable_purchases_previous>a {
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    .drop-area {
        border: 2px dashed #ccc;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
        background-color: #f9f9f9;
        cursor: pointer;
    }

    .drop-area.dragging {
        border-color: #000000ff;
        background-color: #f0f0f0;
    }

    .gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }

    .gallery img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .gallery img:hover {
        transform: scale(1.05);
        /* Scale up image slightly on hover */
        filter: brightness(0.6) saturate(1.5) hue-rotate(0deg);
        /* Change color effect */
    }

    .drop-area {
        border: 2px dashed #ccc;
        border-radius: 20px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
    }

    .highlight {
        border-color: purple;
    }

    .gallery-image {
        display: inline-block;
        width: 100px;
        height: auto;
    }

    .position-relative {
        position: relative;
    }

    .btn-delete {
        position: absolute;
        top: -10px;
        right: 85px;
        background-color: rgba(255, 0, 0, 0.7);
        border: none;
        color: white;
        padding: 5px 8px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 13px;
    }

    .btn-delete:hover {
        background-color: rgba(255, 0, 0, 1);
    }

    .btn-delete i {
        font-size: 14px;
    }

    img {
        display: block;
        max-width: 100%;
        height: auto;
    }

    .card-header {
        position: relative;
        /* Ensures that the icon can be positioned relative to the card header */
    }

    .trash-icon {
        position: absolute;
        top: 50%;
        right: 20px;
        /* Adjust the right spacing as needed */
        transform: translateY(-50%);
        /* Centers the icon vertically */
        cursor: pointer;
        /* Makes the icon clickable */
        font-size: 18px;
        /* Adjust the size of the trash icon */
        color: #dc3545;
        /* Optional: Give it a red color */
    }

    .trash-icon:hover {
        color: #ff0000;
        /* Optional: Change color on hover for better user experience */
    }


    .expandable-container {
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .expandable-header {
        cursor: pointer;
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .expandable-content {
        padding: 10px;
        border-top: 1px solid #ccc;
        display: none;
    }

    .arrow {
        font-size: 16px;
        transition: transform 0.3s ease;
    }



    #logs_next>a {
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    #logs_previous>a {
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
</style>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>


<nav>
    <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-details-tab" data-toggle="tab" href="#nav-details" role="tab"
            aria-controls="nav-details" aria-selected="true">Details</a>

        <a class="nav-item nav-link" id="nav-qr-tab" data-toggle="tab" href="#nav-qr" role="tab"
            aria-controls="nav-qr" aria-selected="true">QR Code Generator</a>

        <a class="nav-item nav-link" id="nav-usage-tab" data-toggle="tab" href="#nav-usage" role="tab"
            aria-controls="nav-usage" aria-selected="true">Asset Usage History</a>

        <a class="nav-item nav-link" id="nav-new-maintenance-tab" data-toggle="tab" href="#nav-new-maintenance"
            role="tab" aria-controls="nav-maintenance" aria-selected="true">Maintenance</a>

        <a class="nav-item nav-link" id="nav-new-logs-tab" data-toggle="tab" href="#nav-new-logs"
            role="tab" aria-controls="nav-new-logs" aria-selected="true">Logs</a>

    </div>
</nav>

<div class="row fade tab-pane active show" id="nav-details" role="tabpanel">
    <div class="col-md-8">
        <div class="card shadow mb-4 tabradius">
            <div class="card-body">
                <div class="row col-sm-12 mr-8">
                    <?php if ($this->user_model->has_perm("add_equipments")) { ?>
                        <a class=" float-right text_successo btn btn-default btn_border" href="#addModal"
                            data-toggle="modal" data-target="#addModal" title="Add new Component"><i class="fa fa-plus"></i> New
                            Component</a>
                    <?php } ?>
                </div>
                <div class="bg-white card-header py-3">
                    <h6 class="m-0 font-weight-bold text_warning_color">Edit Asset</h6>
                </div>
                <form class="form-horizontal" action="<?= site_url("assets/update"); ?>" method="post" enctype="multipart/form-data">
                    <div class="row">

                        <?= $this->steve->form_group_label_input("text", "name", "System name", "col-sm-4", 1, $info->equipment_name, 125); ?>

                        <?= $this->steve->form_group_label_input("text", "serial_number", "Serial Number", "col-sm-4", 1, $info->serial_number); ?>


                        <div class="form-group col-sm-4 ">
                            <label>Vendor Part Number</label><br />
                            <select class="form-control" class="p-0" name="vendor_part_number_id">
                                <option value="">--Select--</option>
                                <?php foreach ($part_numbers as $pn) { ?>
                                    <option value="<?= $pn->id ?>"
                                        <?= ($pn->id == $info->vendor_part_number_id) ? 'selected' : ''; ?>>
                                        <?= $pn->part_number ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- <?= $this->steve->form_group_label_input("text", "purchase_date", "Purchase date", "col-sm-4 date_picker_now"); ?> -->

                        <!-- <?= $this->steve->form_group_label_select("equipment_status", "Asset status", $this->steve->equipment_statuses(), "", "", "col-sm-4", $info->equipment_status); ?> -->

                        <div class="form-group col-sm-4 uppercase">
                            <label>Asset Status</label><br />
                            <select class="form-control" class="p-0" name="equipment_status">
                                <option value="">--Select--</option>
                                <?php foreach ($assetStatus as $as) { ?>
                                    <option value="<?= $as->name ?>"
                                        <?= ($as->name == $info->equipment_status) ? 'selected' : ''; ?>><?= $as->name ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>


                        <!-- <?= $this->steve->form_group_label_select("equipment_manufacturer", "Asset manufacturer", $this->steve->manufacturers(), "manufacturer_id", "manufacturer_name", "col-sm-4", $info->equipment_manufacturer); ?> -->

                        <!-- Manufacturer Name with Searchable Dropdown -->
                        <div class="form-group col-sm-4 uppercase">
                            <label for="manufacturer_name">Asset Manufacturer</label><br />
                            <select name="equipment_manufacturer" id="manufacturer_name" class="form-control searchable-dropdown">
                                <option value="">--Select--</option>
                                <?php foreach ($manufacturer_number as $mn): ?>
                                    <option value="<?= htmlspecialchars($mn->id, ENT_QUOTES, 'UTF-8') ?>"
                                        <?= ($mn->id == $info->equipment_manufacturer) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($mn->manufacturer_name, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>


                        <!-- Manufacturer Drawing Number Dropdown -->
                        <!-- <div class="form-group col-sm-4 uppercase">
                            <label>Manufacturer Drwing Number</label><br />
                            <select class="form-control" class="p-0" name="drawing_number">
                                <option value="">--Select--</option>
                                <?php foreach ($drawing_numbers as $dn) { ?>
                                    <option value="<?= $dn->drawing_number ?>"
                                        <?= ($dn->drawing_number == $info->manufacturer_drwing_number) ? 'selected' : ''; ?>>
                                        <?= $dn->drawing_number ?></option>
                                <?php } ?>
                            </select>
                        </div> -->



                        <div class="form-group col-sm-4 uppercase">
                            <label>Asset type</label><br />
                            <select class="form-control" id="equipment_type_calibration_edit" name="equipment_type">
                                <option value="">--Select--</option>
                                <?php foreach ($assetTypes as $asset) { ?>
                                    <option value="<?= $asset->asset_id; ?>"
                                        <?= ($asset->asset_id == $info->equipment_type) ? 'selected' : ''; ?>>
                                        <?= $asset->name ?></option>
                                <?php } ?>
                            </select>
                        </div>



                        <?php //$this->steve->form_group_label_input("file", "equipment_picture", "Asset picture", "col-sm-12", '', '', '');
                        ?>

                        <div class="form-group col-sm-4">
                            <label for="form_date_installed">Date Installed<sup>REQUIRED</sup></label>
                            <input type="date" name="date_installed" class="form-control" step="0.00001" id="form_date_installed" min="0" placeholder="Date Installed" value="<?= $info->date_installed ?>" required="" autocomplete="off" maxlength="10">
                        </div>

                        <div class="form-group col-sm-4 uppercase">
                            <label>State</label><br />
                            <select class="form-control" id="stateSelect" name="state_id">
                                <option value="">--Select--</option>
                                <?php foreach ($states as $state) { ?>
                                    <option value="<?= $state->id; ?>"
                                        <?= ($state->id == $info->state_id) ? 'selected' : ''; ?>><?= $state->state_name ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group col-sm-4 uppercase">
                            <label>Locations</label><br />
                            <select class="form-control" id="locationSelect" name="location_id">
                                <option value="">--Select--</option>
                                <?php foreach ($locations as $l) { ?>
                                    <option value="<?= $l->id ?>" <?= ($l->id == $info->location_id) ? 'selected' : ''; ?>>
                                        <?= $l->name ?></option>
                                <?php } ?>
                            </select>
                        </div>


                        <div class="form-group col-sm-4 uppercase">
                            <label>Mnaged By </label><br />
                            <select class="form-control" class="p-0" name="ownership">
                                <option value="">--Select--</option>
                                <?php foreach ($managedBys as $manage) { ?>
                                    <option value="<?= $manage->id; ?>"
                                        <?= ($manage->id == $info->ownership) ? 'selected' : ''; ?>><?= $manage->name; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group col-sm-4 uppercase">
                            <label> Equipment Registration </label>
                            <input type="text" name="equipment_registration" class="form-control"
                                value="<?= $info->equipment_registration ?>" placeholder="Equipment Registration">

                        </div>

                        <div class="form-group col-sm-4 uppercase" id="faulty_type_field">
                            <label>Faulty Type</label><br />
                            <select class="form-control" id="faulty_type" class="p-0" name="faulty_type">
                                <option value="">--Select--</option>
                                <?php foreach ($faulty as $f) { ?>
                                    <option value="<?= $f->id; ?>"
                                        <?= ($f->id == $info->faulty_type_id) ? 'selected' : ''; ?>><?= $f->fault_type; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group col-sm-4 uppercase">
                            <label>Store Location</label><br />
                            <select class="form-control" class="p-0" name="store_location">
                                <option value="0">--Select--</option>
                                <?php foreach ($storeLocation as $sl) { ?>
                                    <option value="<?= $sl->id ?>" <?= ($sl->id == $info->store_location_id) ? 'selected' : ''; ?>><?= $sl->name ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <?= $this->steve->form_group_label_textarea("notes", "Notes", "col-sm-4"); ?>
                        <div class="col-md-12"></div>
                        <!-- celebration -->
                        <div class="form-group col-sm-4 uppercase" id="calibration_date" style="display: none;">
                            <label>1st Calibration Date</label><br />
                            <input type="date" class="form-control" value="<?= ($info->calibration_date) ? $info->calibration_date : '' ?>"
                                name="calibration_date" placeholder="1st Calibration Date">
                        </div>

                        <div class="form-group col-sm-4 uppercase" id="frequency_day" style="display: none;">
                            <label>Frequency In Days</label><br />
                            <input type="text" class="form-control" value="<?= $info->frequency_day ?>"
                                name="frequency_day" placeholder="90">
                        </div>



                        <div class="form-group col-sm-4 uppercase" id="reminder_day" style="display: none;">
                            <label>Reminder In Days</label><br />
                            <input type="text" class="form-control" value="<?= $info->reminder_day ?>"
                                name="reminder_day" placeholder="7">
                        </div>


                        <!-- maintenance -->
                        <div class="form-group col-sm-4 uppercase" id="maintenance_date" style="display: none;">
                            <label>Maintenance Date</label><br />
                            <input type="date" class="form-control" value="<?= ($info->maintenance_date) ? $info->maintenance_date : '' ?>"
                                name="maintenance_date" placeholder="Maintenance Date">
                        </div>

                        <div class="form-group col-sm-4 uppercase">
                            <label>Cost (Purchase Price)</label><br />
                            <input type="number" step="0.01" class="form-control" name="price_of_purchase" 
                                placeholder="Purchase Price" min="0"
                                value="<?= $info->price_of_purchase ?>">
                        </div>


                        <div class="form-group col-sm-4">
                            <label for="purchase_date">Purchase Date <sup style="color:red; font-size:8px;">Required</sup></label>
                            <input type="date" name="purchase_date" class="form-control" id="purchase_date" 
                                max="<?php echo date('Y-m-d'); ?>" 
                                value="<?= $info->purchase_date ? date('Y-m-d', strtotime($info->purchase_date)) : '' ?>" required>
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="company_name">Company Name <sup style="color:red; font-size:8px;">Required</sup></label>
                            <input type="text" name="company_name" class="form-control" id="company_name" 
                                placeholder="Company Name" value="<?= $info->company_name ?>" required>
                        </div>


                        <div class="form-group col-sm-4">
                            <label for="invoice">Invoice (PDF, Excel, Word)</label>
                            <input type="file" name="invoice" class="form-control" id="invoice" 
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                            <?php if ($info->invoice): ?>
                                <small class="text-muted">Current: 
                                    <a href="<?= base_url('storage/Asset-Invoices/' . $info->invoice) ?>" target="_blank">
                                        View Invoice
                                    </a>
                                </small>
                            <?php endif; ?>
                        </div>

                        <!-- <div class="form-group col-sm-4 uppercase">
                            <label>Disposal Method</label><br />
                            <select class="form-control" name="disposal_method_id">
                                <option value="">--Select--</option>
                                <?php if(isset($disposal_methods) && !empty($disposal_methods)): ?>
                                    <?php foreach ($disposal_methods as $dm) { ?>
                                        <option value="<?= $dm->id ?>" 
                                            <?= ($dm->id == $info->disposal_method_id) ? 'selected' : '' ?>>
                                            <?= $dm->disposal_method ?>
                                        </option>
                                    <?php } ?>
                                <?php else: ?>
                                    <option value="">No Disposal Methods Found</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group col-sm-4 uppercase">
                            <label>Useful Life (Years)</label><br />
                            <input type="number" class="form-control" name="useful_life_years" 
                                placeholder="Useful Life in Years" min="0"
                                value="<?= $info->useful_life_years ?>">
                        </div>

                        <div class="form-group col-sm-4 uppercase">
                            <label>Salvage Value</label><br />
                            <input type="number" step="0.01" class="form-control" name="salvage_value" 
                                placeholder="Salvage Value" min="0"
                                value="<?= $info->salvage_value ?>">
                        </div> -->

                        <div class="form-group col-sm-4 uppercase" id="frequency_year" style="display: none;">
                            <label>Frequency In Years</label><br />
                            <input type="text" class="form-control" value="<?= $info->frequency_year ?>"
                                name="frequency_year" placeholder="2">
                        </div>



                        <div class="form-group col-sm-4 uppercase" id="maintenance_reminder_day" style="display: none;">
                            <label>Reminder In Days</label><br />
                            <input type="text" class="form-control" value="<?= $info->maintenance_reminder_day ?>"
                                name="maintenance_reminder_day" placeholder="30">
                        </div>


                        <!-- <div class="col-md-6">
                            <label for="">Check for Faulty type</label>
                            <input type="checkbox" id="faulty_type_toggle">
                        </div> -->




                    </div>

                    <!-- Edit Items Section -->
                    <div class="row justify-content-center" style="display: contents;">
                        <?php foreach ($items as $key => $item): ?>
                            <div class="expandable-container">
                                <!-- Expandable Header -->
                                <div class="expandable-header bg-white card-header py-3 ml-4 position-relative" onclick="toggleExpandable(this)">
                                    <h6 class="m-0 font-weight-bold text-warning">Edit Component - <?= $item->item_name ?></h6>
                                    <span class="arrow">&#9660;</span>
                                </div>

                                <!-- Expandable Content -->
                                <div class="expandable-content" style="display: none;">
                                    <div class="item-section">
                                        <!-- Looping through items -->
                                        <div class="col-sm-11  bg-white card-header py-3 ml-4 position-relative">

                                            <a onclick="return confirm('Are you sure to this this record?')"
                                                href="<?= base_url('assets/deleteItem') . '?id=' . $item->id . '&assetid=' . $this->steve->id_encode($info->equipment_id) ?>"
                                                class=" btn-danger mt-3"><i class="fa fa-trash trash-icon"></i></a>

                                        </div>
                                        <div class="modal-body row">

                                            <input type="hidden" id="item_id" name="item_id[]" value="<?= $item->id; ?>">

                                            <!-- Item Name Field -->
                                            <?= $this->steve->form_group_label_input("text", "item[]", "Component", "col-sm-4", 0, $item->item_name, 125); ?>

                                            <!-- Vendor Part Number Dropdown -->
                                            <div class="col-sm-4 form-group">
                                                <label for="vendor_part_number_<?= $key; ?>">Vendor Part Number</label>
                                                <select name="vendor_part_number[]" id="vendor_part_number_<?= $key; ?>"
                                                    class="form-control">
                                                    <option value="<?= $item->part_number ?>">Select Vendor Part Number</option>
                                                    <?php foreach ($part_numbers as $pn): ?>
                                                        <option value="<?= $pn->part_number ?>"
                                                            <?= ($pn->part_number == $item->vendor_part_number) ? 'selected' : ''; ?>>
                                                            <?= $pn->part_number ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>


                                            <!-- Manufacturer Name -->
                                            <div class="form-group col-sm-4 uppercase">
                                                <label>Manufacturer Name</label><br />
                                                <select class="form-control" class="p-0" name="manufacturer_name[]">
                                                    <option value="">--Select--</option>
                                                    <?php foreach ($manufacturer_number as $mn) { ?>
                                                        <option value="<?= $mn->manufacturer_name ?>"
                                                            <?= ($mn->manufacturer_name == $item->manufacturer_name) ? 'selected' : ''; ?>>
                                                            <?= $mn->manufacturer_name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-sm-4 uppercase">
                                                <label>Store Location</label><br />
                                                <select class="form-control" class="p-0" name="store_location_item[]">
                                                    <option value="0">--Select--</option>
                                                    <?php foreach ($storeLocation as $sl) { ?>
                                                        <option value="<?= $sl->id ?>" <?= ($sl->id == $item->store_location_id) ? 'selected' : ''; ?>><?= $sl->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <!-- Manufacturer Part Number Dropdown -->
                                      
                                            <!-- Item Status Dropdown -->
                                            <div class="form-group col-sm-4 uppercase">
                                                <label>Component Status</label><br />
                                                <select class="form-control" class="p-0" name="item_status[]">
                                                    <option value="0">--Select--</option>
                                                    <?php foreach ($itemStatus as $is) { ?>
                                                        <option value="<?= $is->id ?>" <?= ($is->id == $item->item_status_id) ? 'selected' : ''; ?>><?= $is->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <!-- Item type Dropdown -->
                                            <div class="col-sm-4 form-group">
                                                <label for="item_type_<?= $key; ?>">Component Type</label>
                                                <select name="item_type[]" class="form-control item_type_calibration_edit">
                                                    <?php foreach ($itemTypes as $it) { ?>
                                                        <option value="<?= $it->id ?>"
                                                            <?= ($it->id == $item->item_type_id) ? 'selected' : ''; ?>>
                                                            <?= $it->name; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div
                                                class="form-group col-sm-4 uppercase edit_faulty_type_field_item edit_faulty_type_field_item_<?= $key; ?>">
                                                <label>Faulty Type</label><br />
                                                <select class="form-control" name="faulty_type_item[]">
                                                    <option value="">--Select--</option>
                                                    <?php foreach ($faulty as $f) { ?>
                                                        <option value="<?= $f->id; ?>"
                                                            <?= ($f->id == $item->faulty_type_id) ? 'selected' : ''; ?>>
                                                            <?= $f->fault_type; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <!-- celebration -->
                                            <div class="form-group col-sm-4 uppercase calibration_date_item"
                                                id="calibration_date_item_edit" style="<?= ($item->calibration == 1) ? 'display: block;' : 'display: none;' ?>">
                                                <label>1st Calibration Date</label><br />
                                                <input type="date" class="form-control" value="<?= $item->calibration_date ?>" name="calibration_date_item[]"
                                                    placeholder="1st Calibration Date">
                                            </div>

                                            <div class="form-group col-sm-4 uppercase" id="frequency_day_item_edit"
                                                style="<?= ($item->calibration == 1) ? 'display: block;' : 'display: none;' ?>">
                                                <label>Frequency In Days</label><br />
                                                <input type="text" class="form-control" value="<?= $item->frequency_day ?>" name="frequency_day_item[]"
                                                    placeholder="90">
                                            </div>

                                            <div class="form-group col-sm-4 uppercase" id="reminder_day_item_edit"
                                                style="<?= ($item->calibration == 1) ? 'display: block;' : 'display: none;' ?>">
                                                <label>Reminder In Days</label><br />
                                                <input type="text" class="form-control" value="<?= $item->reminder_day ?>" name="reminder_day_item[]" placeholder="7">
                                            </div>


                                            <!-- maintenance Details -->
                                            <div class="form-group col-sm-4 uppercase maintenance_date_item"
                                                id="maintenance_date_item_edit"
                                                style="<?= ($items->maintenance == 1) ? 'display: block;' : 'display: none;' ?>">
                                                <label>Maintenance Date</label><br />
                                                <input type="date" class="form-control" value="<?= $items->maintenance_date ?>"
                                                    name="maintenance_date_item" placeholder="Maintenance Date">
                                            </div>

                                            <div class="form-group col-sm-4 uppercase" id="frequency_year_item_edit"
                                                style="<?= ($items->maintenance == 1) ? 'display: block;' : 'display: none;' ?>">
                                                <label>Frequency In years</label><br />
                                                <input type="text" class="form-control" value="<?= $items->frequency_year ?>"
                                                    name="frequency_year_item" placeholder="2">
                                            </div>

                                            <div class="form-group col-sm-4 uppercase" id="maintenance_reminder_day_item_edit"
                                                style="<?= ($items->maintenance == 1) ? 'display: block;' : 'display: none;' ?>">
                                                <label>Reminder In Days</label><br />
                                                <input type="text" class="form-control" value="<?= $items->maintenance_reminder_day ?>"
                                                    name="maintenance_reminder_day_item" placeholder="30">
                                            </div>

                                            <div class="col-md-12"></div>

                                            <div class="row py-3 ml-2 position-relative">
                                                <!-- QR Code Section -->
                                                <div class="card col-sm-4 tabradius">
                                                    <div class="card-body">
                                                        <div class="bg-white card-header py-3">
                                                            <h6 class="m-0 font-weight-bold text-warning">QR Code Generator</h6>
                                                        </div>

                                                        <div class="table-responsive">
                                                            <?php if (isset($item->items_qr_code) && $item->items_qr_code == 1): ?>
                                                                <?php

                                                                $chlvalue = "Component name: " . $item->item_name . "\n"
                                                                    . "Vendor Part number: " . $item->vendor_part_number . "\n"
                                                                    . "Serial Number: " . $item->serial_number . "\n"
                                                                    . "Manufacturer Name: " . $item->manufacturer_name . "\n"
                                                                    . "status: " . $info->equipment_status . "\n";
                                                                $chlvalue = urlencode($chlvalue);
                                                                ?>
                                                                <div class="col-8 offset-2">
                                                                    <center>
                                                                        <img width="100px"
                                                                            src="https://quickchart.io/chart?chs=300x300&cht=qr&chl=<?= $chlvalue ?>&choe=UTF-8"
                                                                            title="Scan QR Code" />
                                                                        <br>
                                                                        <a onclick="return confirm('Are you sure to Delete QR Code for this record?')"
                                                                            href="<?= base_url('assets/itemsqrdel') . '?id=' . $this->steve->id_encode($info->equipment_id) . '&unique_id=' . $item->id ?>"
                                                                            class="btn btn-danger mt-3">Delete</a>

                                                                    </center>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="col-md-12">
                                                                    <center>
                                                                        <a onclick="return confirm('Are you sure to Generate QR Code for this record?')"
                                                                            href="<?= base_url('assets/itemsqrgen') . '?id=' . $this->steve->id_encode($info->equipment_id) . '&unique_id=' . $item->id ?>"
                                                                            class="btn btn-primary mt-3">Generate QR Code</a>
                                                                    </center>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Item Pictures Gallery -->
                                                <div class=" col-md-4 mt-2">
                                                    <div class="col-md-12">
                                                        <div id="picture-gallery-<?= $key ?>" class="gallery">
                                                            <div class="row col-md-12" style="justify-content: center;">

                                                                <h6 class="m-0 font-weight-bold text-warning">Component Pictures</h6>

                                                            </div>
                                                            <?php
                                                            // Initialize a flag to check if pictures are found for the current item
                                                            $hasPictures = false;

                                                            // Loop through pictures and display those that match the current item's id
                                                            foreach ($pictures as $picture) {
                                                                if ($picture->add_asset_items_id == $item->id) {
                                                                    $hasPictures = true;  // Set flag to true if a match is found
                                                            ?>
                                                                    <!-- Display each picture with a click event to delete -->
                                                                    <div class="gallery-item">
                                                                        <img src="<?= base_url('storage/' . $picture->item_picture); ?>"
                                                                            alt="Component Picture" style="width:100%; max-width:200px;"
                                                                            class="deletable-picture" data-picture-id="<?= $picture->id; ?>">
                                                                    </div>
                                                            <?php
                                                                }
                                                            }

                                                            // If no pictures found for the item, show a message
                                                            if (!$hasPictures) {
                                                                echo "<p>No pictures available for this Component.</p>";
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Item Pictures Section -->
                                                <div class="col-md-4 mt-2">
                                                    <div class="col-md-12">
                                                        <div id="drop-area-<?= $key ?>" class="drop-area">
                                                            <p>Drag and drop your images here</p>
                                                            <label for="fileElem-<?= $key ?>" class="btn btn-primary">Choose Files</label>
                                                            <input type="file" id="fileElem-<?= $key ?>" name="item_picture[]" multiple
                                                                accept="image/*" style="display:none" />
                                                        </div>
                                                        <div id="gallery-<?= $key ?>" class="gallery mt-3"></div>
                                                        <center>
                                                            <button type="button" id="saveBtn-<?= $key ?>"
                                                                class="btn btn-primary mt-2 mb-5">Save</button>
                                                        </center>
                                                    </div>
                                                </div>

                                            </div>
                                        </div> <!-- End modal-body -->
                                    </div>


                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>


                    <div class="text-center mt-4">
                        <input type="hidden" name="id" value="<?= $info->equipment_id; ?>" />
                        <button type="submit" class="btn font-weight-bold asset-save-btn">Save changes</button>
                        <a class="btn asset-back-btn" data-dismiss="modal" href=".">Go back</a>

                        <a href="<?= site_url('depreciation_details/index/' . $info->equipment_id) ?>" 
                            class="btn font-weight-bold asset-depreciation-btn">
                            <i class="fas fa-chart-line"></i> Depreciation Details
                        </a>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- drag an drop item images -->
    <script>
        $(document).ready(function() {
            <?php foreach ($items as $key => $item): ?>
                let dropArea<?= $key ?> = $('#drop-area-<?= $key ?>');
                let fileInput<?= $key ?> = $('#fileElem-<?= $key ?>');
                let gallery<?= $key ?> = $('#gallery-<?= $key ?>');

                // Prevent default drag behaviors
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropArea<?= $key ?>.on(eventName, preventDefaults, false);
                    $(document).on(eventName, preventDefaults, false);
                });

                // Highlight the drop area when file is dragged over
                dropArea<?= $key ?>.on('dragover', () => dropArea<?= $key ?>.addClass('highlight'));
                dropArea<?= $key ?>.on('dragleave', () => dropArea<?= $key ?>.removeClass('highlight'));

                // Handle dropped files
                dropArea<?= $key ?>.on('drop', handleDrop<?= $key ?>);
                fileInput<?= $key ?>.on('change', handleFiles<?= $key ?>); // Ensure file input changes are detected

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                // Handle both drag-and-drop files and input-selected files
                function handleDrop<?= $key ?>(e) {
                    const dt = e.originalEvent.dataTransfer;
                    const files = dt.files;
                    handleFiles<?= $key ?>(files);
                }

                function handleFiles<?= $key ?>(files) {
                    const fileArray = Array.from(files);
                    const currentFiles = Array.from(fileInput<?= $key ?>[0].files);

                    const allFiles = [...currentFiles, ...fileArray]; // Merge existing files with newly selected ones
                    const dataTransfer = new DataTransfer();

                    gallery<?= $key ?>.empty(); // Clear the gallery before displaying new images

                    allFiles.forEach((file, index) => {
                        dataTransfer.items.add(file); // Add all files to the data transfer object

                        // Preview the images
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.className = 'gallery-image';
                        img.style.width = '100px';
                        img.style.margin = '5px';
                        img.dataset.index = index; // Set an index for tracking images
                        gallery<?= $key ?>.append(img);

                        // Image delete functionality on click
                        img.addEventListener('click', function() {
                            deleteFile<?= $key ?>(index);
                            this.remove(); // Remove the image preview
                        });
                    });

                    // Update the file input's file list
                    fileInput<?= $key ?>[0].files = dataTransfer.files;
                }

                // Delete selected files by index
                function deleteFile<?= $key ?>(fileIndex) {
                    const currentFiles = Array.from(fileInput<?= $key ?>[0].files);
                    const dataTransfer = new DataTransfer();

                    currentFiles.forEach((file, index) => {
                        if (index !== fileIndex) {
                            dataTransfer.items.add(file); // Only keep files that were not deleted
                        }
                    });

                    // Update file input with the new list of files
                    fileInput<?= $key ?>[0].files = dataTransfer.files;
                }

                // Save button functionality for uploading images
                $('#saveBtn-<?= $key ?>').click(function(e) {
                    e.preventDefault();

                    var formData = new FormData();
                    var files = fileInput<?= $key ?>[0].files;

                    for (var i = 0; i < files.length; i++) {
                        formData.append('item_picture[]', files[i]);
                    }

                    formData.append('id', '<?= $info->equipment_id ?>');
                    formData.append('unique_id', '<?= $item->id ?>');

                    // AJAX request to upload images
                    $.ajax({
                        url: "<?= base_url('assets/itemsImagesAdd') ?>",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            window.location.reload();
                        },
                        error: function(xhr, status, error) {
                            alert('An error occurred while uploading the images.');
                            console.error(xhr.responseText);
                        }
                    });
                });
            <?php endforeach; ?>
        });
    </script>

    <!-- item picture galary -->
    <script>
        // JavaScript to handle picture deletion
        document.addEventListener('DOMContentLoaded', function() {
            const deletablePictures = document.querySelectorAll('.deletable-picture');

            deletablePictures.forEach(picture => {
                picture.addEventListener('click', function() {
                    const pictureId = this.dataset
                        .pictureId; // Get the picture ID from data attribute

                    // Confirm deletion
                    if (confirm('Are you sure you want to delete this picture?')) {
                        // Make an AJAX request to delete the picture
                        fetch('<?= base_url("assets/delete_picture"); ?>', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    id: pictureId
                                }) // Send the picture ID
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Picture deleted successfully.');
                                    // Optionally, remove the image from the DOM
                                    this.parentElement
                                        .remove(); // Remove the image from the gallery
                                } else {
                                    alert('Error deleting picture: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Error deleting picture. Please try again later.');
                            });
                    }
                });
            });
        });
    </script>

    <div class="col-md-4">
        <div class="card shadow mb-4 tabradius">

            <div class="card-body">
                <div class="bg-white card-header py-3">
                    <h6 class="bg-white m-0 font-weight-bold text-primary">Asset picture
                    </h6>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <form method="post" action="<?= site_url("assets/upload_picture"); ?>" class="dropzone"
                            enctype="multipart/form-data">
                            <input type="hidden" name="id" readonly value="<?= $info->equipment_id; ?>">
                            <div class="fallback">
                                <input name="file[]" type="file" accept="image/*" />
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 position-relative">
                        <?php if ($info->equipment_picture) { ?>
                            <img class="rounded-square img-thumbnail"
                                src="<?= site_url("storage/Asset-" . $info->equipment_id . "/" . $info->equipment_picture); ?>"
                                alt="Equipment Picture" style="width: 100%; height: auto;"
                                onerror="this.style.display='none';" />

                            <!-- Add delete icon over the image -->
                            <button class="btn-delete"
                                onclick="deletePicture(<?= $info->equipment_id; ?>, '<?= $info->equipment_picture; ?>')">
                                <i class="fa fa-trash"></i>
                            </button>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>


        <script>
            function deletePicture(equipmentId, pictureName) {
                if (confirm("Are you sure you want to delete this picture?")) {
                    fetch("<?= site_url('assets/delete_asset_picture'); ?>", {
                            method: "POST",
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id: equipmentId,
                                picture: pictureName
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("Picture deleted successfully.");
                                location.reload(); 
                            } else {
                                alert("Failed to delete picture: " + data.message);
                            }
                        })
                        .catch(error => {
                            console.error("Error deleting picture:", error);
                            alert("An error occurred. Please try again.");
                        });
                }
            }
        </script>

        <?php if ($this->user_model->has_perm("qr_generator")) { ?>
            <?php if (isset($info->qr_code) && $info->qr_code == 1) { ?>
                <div class="card shadow mb-4 tabradius">
                    <?php
                    $itemsDetails = "";
                    $iterationCount = 0; 

                    if ($items) {

                        foreach ($items as $item) {
                            $iterationCount++; 

                            $statusName = 'N/A';
                            foreach ($itemStatus as $status) {
                                if ($status->id == $item->item_status_id) {
                                    $statusName = $status->name;
                                    break;
                                }
                            }

                            $itemsDetails .= "Component: " . $iterationCount . "\n" 
                                . "Component Name: " . (isset($item->item_name) ? $item->item_name : 'N/A') . "\n"
                                . "Serial Number: " . (isset($item->serial_number) ? $item->serial_number : 'N/A') . "\n";
                        }
                    }else {
                        $itemsDetails = "N/A";
                    }

                    $manufacturerName = 'N/A';
                    foreach ($manufacturer_number as $mn) {
                        if ($mn->id == $info->equipment_manufacturer) {
                            $manufacturerName = $mn->manufacturer_name;
                            break;
                        }
                    }

                    $vendorPartNumber = 'N/A';
                    foreach ($part_numbers as $pn) {
                        if ($pn->id == $info->vendor_part_number_id) {
                            $vendorPartNumber = $pn->part_number;
                            break;
                        }
                    }

                    $chlvalue =
                        "Asset name: " . (isset($info->equipment_name) ? $info->equipment_name : 'N/A') . "\n"
                        . "Asset Status: " . (isset($info->equipment_status) ? $info->equipment_status : 'N/A') . "\n"
                        . "RFID: " . (isset($info->rfid) ? $info->rfid : 'N/A') . "\n"
                        . "Serial Number: " . (isset($info->serial_number) ? $info->serial_number : 'N/A') . "\n"
                        . "Location: " . (isset($info->location_name) ? $info->location_name : 'N/A') . "\n\n"
                        . "List of Component \n \n"
                        . $itemsDetails; 

                    $chlvalue = urlencode($chlvalue);
                    ?>
                    <div class="col-md-12">
                        <center>
                            <img width="300px" src="https://quickchart.io/chart?chs=300x300&cht=qr&chl=<?= $chlvalue ?>&choe=UTF-8"
                                title="Scan QR Code" />
                            <br />
                            <a style="margin-top: -25px; margin-bottom: 10px;"
                                onclick="return confirm('Are you sure to Delete QR Code for this record?')"
                                href="<?= base_url('assets/qrdel') . '?id=' . $this->steve->id_encode($info->equipment_id) ?>"
                                class="btn btn-danger">Delete QR Code</a>
                        </center>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

        <!-- <div class="card shadow mb-4 tabradius">
            <div class="card-body">
                <div class="bg-white card-header py-3">
                    <h6 class="bg-white m-0 font-weight-bold text-primary">RFID
                    </h6>
                </div>

                <div class="col-md-12" style="margin-top: 15px; text-align: center;">
                    <?php if (isset($info->rfid) && $info->rfid) { ?>
                        <h5><span><?= htmlspecialchars($info->rfid); ?></span></h5>
                    <?php } else { ?>
                        <h5><span>No RFID</span></h5>
                    <?php } ?>


                    <a style="margin-top: 5px; margin-bottom: 10px; "
                        onclick="return confirm('Are you sure to Delete QR Code for this record?')"
                        href="<?= base_url('assets/rfid_del') . '?id=' . $this->steve->id_encode($info->equipment_id) ?>"
                        class="btn btn-danger">Delete RFID</a>


                </div>
            </div>



        </div> -->

        <div class="card shadow mb-4 tabradius">
            <div class="card-body">
                <div class="bg-white card-header py-3">
                    <h6 class="bg-white m-0 font-weight-bold text-primary">RFID</h6>
                </div>

                <!-- display rfid -->
                <div class="col-md-12" style="margin-top: 15px; text-align: center;">
                    <?php if (isset($info->rfid) && $info->rfid) { ?>
                        <h5><span><?= htmlspecialchars($info->rfid); ?></span></h5>
                    <?php } else { ?>
                        <h5><span>No RFID</span></h5>
                    <?php } ?>

                    <a style="margin-top: 5px; margin-bottom: 10px; "
                        onclick="return confirm('Are you sure to Delete RFID for this record?')"
                        href="<?= base_url('assets/rfid_del') . '?id=' . $this->steve->id_encode($info->equipment_id) ?>"
                        class="btn btn-danger">Delete RFID</a>
                </div>
            </div>
        </div>

        <!-- INVOICE FILE CARD - NAYA CARD -->
        <div class="card shadow mb-4 tabradius invoice-card">
            <div class="card-body">
                <div class="bg-white card-header py-3">
                    <h6 class="bg-white m-0 font-weight-bold text-primary">Asset Invoice</h6>
                </div>

                <div class="col-md-12" style="margin-top: 15px; text-align: center;">
                    <?php if ($info->invoice_file): ?>
                        <?php
                        // File extension nikalein
                        $file_extension = pathinfo($info->invoice_file, PATHINFO_EXTENSION);
                        $file_name = pathinfo($info->invoice_file, PATHINFO_FILENAME);
                        
                        // File type ke hisaab se icon select karein
                        $icon_class = "fa-file";
                        $file_type = "Document";
                        
                        if (in_array(strtolower($file_extension), ['pdf'])) {
                            $icon_class = "fa-file-pdf";
                            $file_type = "PDF Document";
                        } elseif (in_array(strtolower($file_extension), ['doc', 'docx'])) {
                            $icon_class = "fa-file-word";
                            $file_type = "Word Document";
                        } elseif (in_array(strtolower($file_extension), ['xls', 'xlsx'])) {
                            $icon_class = "fa-file-excel";
                            $file_type = "Excel Document";
                        } elseif (in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                            $icon_class = "fa-file-image";
                            $file_type = "Image";
                        }
                        
                        // Original file name (timestamp hata ke)
                        $original_name = preg_replace('/^\d+-invoice-/', '', $file_name);
                        ?>
                        
                        <!-- File Icon aur Information -->
                        <div style="margin-bottom: 15px;">
                            <i class="fas <?= $icon_class ?> fa-3x"></i>
                            <h5 style="margin-top: 10px; font-size: 16px; word-break: break-all;">
                                <?= htmlspecialchars($original_name) ?>
                            </h5>
                            <p class="text-muted">
                                <small><?= strtoupper($file_extension) ?> • <?= $file_type ?></small>
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div style="display: flex; justify-content: center; flex-wrap: wrap; gap: 5px;">
                            <!-- Download Button -->
                            <a href="<?= base_url('uploads/asset_invoice/' . $info->invoice_file) ?>" 
                            class="btn btn-success btn-sm" 
                            download="<?= $original_name . '.' . $file_extension ?>"
                            style="min-width: 100px;">
                                <i class="fa fa-download"></i> Download
                            </a>                            
                            
                            <!-- Delete Button -->
                            <button onclick="deleteInvoice(<?= $info->equipment_id; ?>, '<?= $info->invoice_file; ?>')" 
                                    class="btn btn-danger btn-sm" 
                                    style="min-width: 100px;">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </div>

                    <?php else: ?>
                        <!-- No Invoice Message -->
                        <div style="margin-bottom: 15px; padding: 20px 0;">
                            <i class="fas fa-file-alt fa-3x" style="color: #6c757d;"></i>
                            <h5 style="margin-top: 10px; color: #6c757d;">No Invoice Uploaded</h5>
                            <p class="text-muted">Upload invoice from edit form above</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>


    </div>

</div>

<!-- NAV New Maintenance -->
<div class="row fade tab-pane" id="nav-new-maintenance" role="tabpanel">
    <!-- Maintenance Card - Left Side -->
    <div class="col-md-7">
        <div class="card shadow mb-4 tabradius">
            <div class="card-body">
                <div class="bg-white card-header py-3">
                    <h6 class="m-0 font-weight-bold text_warning_color">
                        Maintenance
                        <?php if ($this->user_model->has_perm("add_maintenance_log_asset")) { ?>
                            <a class="float-right mr-2" href="#addNewMaintenancce" data-toggle="modal"
                                data-target="#addNewMaintenancce" title="Add new maintenance">
                                <i class="fa fa-plus"></i> Add New Maintenance
                            </a>
                        <?php } ?>
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless table-striped" id="equipment_new_maintenance" width="100%"
                        cellspacing="0">
                        <thead>
                            <tr>

                                <th>Update Date</th>
                                <th>Maintenance Type</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will populate here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Logs Card - Right Side -->
    <div class="col-md-5">
        <div class="card shadow mb-4 tabradius">
            <div class="card-body" style="padding: 0.4rem;">
                <div class="bg-white card-header py-3">
                    <h6 class="m-0 font-weight-bold text_warning_color">Ticket Logs</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless table-striped" id="ticket_logs" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Ticket No.</th>
                                <th>Details</th>
                                <th>Issue Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ticket as $row): ?>
                                <tr>
                                    <td>
                                        <span class="badge" style="color: #634300; background-color: #ffe29b; font-size:16px;">
                                            <?= htmlspecialchars($row->ticket_number) ?>
                                        </span>
                                    </td>
                                    <td><?= mb_strtolower($row->details_of_issue) ?></td>
                                    <td><?= date('d-m-Y', strtotime($row->issue_date)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row fade tab-pane" id="nav-new-logs" role="tabpanel">
    <!-- Maintenance Card - Left Side -->
    <div class="col-md-12">
        <div class="card shadow mb-4 tabradius">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped " id="logs" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="bg-white text-dark font-weight-bold">Timestamp</th>
                                <th class="bg-white text-dark font-weight-bold">User</th>

                                <th width="10%" class="bg-white text-dark font-weight-bold">Activity</th>
                                <th width="70%" class="bg-white text-dark font-weight-bold">details </th>
                            </tr>
                        </thead>
                        <tbody>


                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>

<div class="card shadow mb-4 tabradius">
    <div id="datatable-loader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(255,255,255,0.7); z-index:9999; text-align:center;">
        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
            <span class="spinner-border text-primary" role="status"></span>
            <div>Loading...</div>
        </div>
    </div>
</div>

<!-- eye icone button modal  -->
<div class="modal fade" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="equipmentModalLabel">List Task Details</h5>
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

<div class="row fade tab-pane" id="nav-consumable" role="tabpanel">
    <div class="col-md-12">
        <div class="card shadow mb-4 tabradius">
            <div class="card-body">
                <div class="bg-white card-header py-3">
                    <h6 class="m-0 font-weight-bold text_warning_color">Consumable
                        <?php if ($this->user_model->has_perm("add_equipment_consumption")) { ?>
                            <a class="float-right" href="#addConsumableModal" data-toggle="modal"
                                data-target="#addConsumableModal" title="Add new consumable"><i class="fa fa-plus"></i> New
                                consumption</a>
                        <?php } ?>
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless table-striped" id="equipment_consumable" width="100%"
                        cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Consumable name</th>
                                <th>Consumed quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row fade tab-pane" id="nav-usage" role="tabpanel">
    <div class="col-md-12">
        <div class="card shadow mb-4 tabradius">
            <div class="card-body">

                <div class="bg-white card-header py-3">
                    <h6 class="m-0 font-weight-bold text_warning_color">Asset Usage History
                        <?php if ($this->user_model->has_perm("add_history")) { ?>
                            <a class="float-right" href="#addusageModal" data-toggle="modal" data-target="#addusageModal"
                                title="Add Asset usage History"><i class="fa fa-plus"></i> Add Asset Usage History</a>
                        <?php } ?>

                    </h6>
                </div>

                <div class="table-responsive">

                    <table class="table table-borderless table-striped" id="equipment_usage" width="100%"
                        cellspacing="0">
                        <div class="right-filters ml-2" style="float:right; ">
                            <select name="order_year" id="order_year_adhoc" class="form-control select-box">
                                <option value="">Year</option>
                                <option value="2025" <?= $this->input->post('year') == '2025' ? 'selected' : '' ?>>2025
                                </option>
                                <option value="2026" <?= $this->input->post('year') == '2026' ? 'selected' : '' ?>>2026
                                </option>
                            </select>

                        </div>
                        <thead>
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Start time</th>
                                <th>End time</th>
                                <th>Location</th>
                                <!-- <th>End Location</th> -->
                                <!-- <th>Driver name</th>
                                <th>IC No</th> -->
                            </tr>
                        </thead>
                        <tbody id="myTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row fade tab-pane" id="nav-fuel" role="tabpanel">
    <div class="col-md-12">
        <div class="card shadow mb-4 tabradius">
            <div class="card-body">

                <div class="bg-white card-header py-3">
                    <h6 class="m-0 font-weight-bold text_warning_color">Fuel
                        <?php if ($this->user_model->has_perm("add_fuel")) { ?>
                            <a class="float-right" href="#addFuelModal" data-toggle="modal" data-target="#addFuelModal"
                                title="Add new fuel"><i class="fa fa-plus"></i> New fuel</a>
                        <?php } ?>
                    </h6>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless table-striped" id="equipment_fuel" width="100%"
                        cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Mileage</th>
                                <th>Consumed quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row fade tab-pane" id="nav-qr" role="tabpanel">
    <div class="col-md-12">
        <div class="card shadow mb-4 tabradius tabradius">
            <div class="card-body">

                <div class="bg-white card-header py-3">
                    <h6 class="m-0 font-weight-bold text_warning_color">QR Code Generator</h6>
                </div>

                <div class="table-responsive">
                    <?php if ($this->user_model->has_perm("qr_generator")) { ?>
                        <?php
                        // Initialize an empty string to collect item details
                        $itemsDetails = "";
                        $iterationCount = 0; // Initialize the iteration counter

                        if ($items) {
                            foreach ($items as $item) {
                                $iterationCount++; // Increment the counter for each iteration

                                // Append item details to the variable
                                $itemsDetails .= "Item: " . $iterationCount . "\n" // Display the iteration count

                                    . "Component Name: " . (isset($item->item_name) ? $item->item_name : 'N/A') . "\n"
                                    . "Serial Number: " . (isset($item->serial_number) ? $item->serial_number : 'N/A') . "\n";
                                // . "Vendor Part Number: " . (isset($item->vendor_part_number) ? $item->vendor_part_number : 'N/A') . "\n"
                                // . "Manufacturer Name: " . (isset($item->manufacturer_name) ? $item->manufacturer_name : 'N/A') . "\n"
                                // . "Manufacturer Drawing Number: " . (isset($item->manufacturer_drawing_number) ? $item->manufacturer_drwing_number : 'N/A') . "\n" . "\n"; // Added extra newline for separation
                            }
                        } else {
                            $itemsDetails = "N/A"; // Message if no items
                        }


                        // Check if QR code should be generated
                        if (isset($info->qr_code) && $info->qr_code == 1) {
                            $chlvalue =
                                "Asset name: " . (isset($info->equipment_name) ? $info->equipment_name : 'N/A') . "\n"
                                . "Asset Status: " . (isset($info->equipment_status) ? $info->equipment_status : 'N/A') . "\n"
                                . "RFID: " . (isset($info->rfid) ? $info->rfid : 'N/A') . "\n"
                                . "Serial Number: " . (isset($info->serial_number) ? $info->serial_number : 'N/A') . "\n"
                                . "Location: " . (isset($info->location_name) ? $info->location_name : 'N/A') . "\n\n"
                                . "List of Component \n \n"
                                . $itemsDetails; // Append collected item details

                            $chlvalue = urlencode($chlvalue);
                        ?>
                            <div class="col-8 offset-2">
                                <center>
                                    <img width="300px"
                                        src="https://quickchart.io/chart?chs=300x300&cht=qr&chl=<?= $chlvalue ?>&choe=UTF-8"
                                        title="Scan QR Code" />
                                    <h3 class="text-info mt-2">Scan QR Code</h3>
                                </center>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div class="col-md-12">
                                <center>
                                    <a onclick="return confirm('Are you sure to Generate QR Code for this record?')"
                                        href="<?= base_url('assets/qrgen') . '?id=' . $this->steve->id_encode($info->equipment_id) ?>"
                                        class="btn btn-success mt-3">Generate QR Code</a>
                                </center>
                            </div>
                        <?php
                        }
                        ?>
                    <?php } ?>
                </div>


            </div>
        </div>
    </div>
</div>



<div class="modal fade" tabindex="-1" role="dialog" id="addNewMaintenancce">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Maintenance
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("assets/addMaintenace"); ?>" method="post">
                <input type="hidden" name="id" readonly value="<?= $info->equipment_id; ?>">
                <input type="hidden" class="form-control" value="<?= ($info->maintenance_date) ? $info->maintenance_date : '' ?>" name="maintenance_date" placeholder="Maintenance Date">
                <input type="hidden" class="form-control" value="<?= $info->frequency_year ?>" name="frequency_year" placeholder="2">
                <div class="modal-body">
    <div id="dynamic-form">
        <div class="row form-entry">
            <div class="form-group col-sm-6 date_picker_now">
                <label for="form_update_date">Update Date <sup>REQUIRED</sup></label>
                <input type="text" name="update_date" class="form-control" step="0.00001" id="form_update_date" min="0" placeholder="Update Date" value="" required="" autocomplete="off" maxlength="10">
            </div>
            <div class="form-group col-sm-6">
                <label>Maintenance Type <sup>REQUIRED</sup></label>
                <select name="maintenance_type" class="form-control maintenance-type">
                    <option value="">--Select--</option>
                    <option value="preventive">Preventive</option>
                    <option value="corrective">Corrective</option>
                </select>
            </div>

            <!-- ADDED: Remarks Field -->
            <div class="form-group col-sm-12">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control" rows="3" placeholder="Enter remarks..."></textarea>
            </div>

            <!-- REMOVED: Hidden ticket and faulty type fields -->

            <div class="form-group col-sm-6">
                <label>Task Done</label>
                <select class="form-control " name="task_done[]">
                    <option value="">--Select--</option>
                    <?php foreach ($task as $tasks) { ?>
                        <option value="<?= $tasks->name; ?>">
                            <?= $tasks->name; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group col-sm-6">
                <label>Task Remarks </label>
                <textarea name="task_remarks[]" class="form-control" rows="2" placeholder="Enter task remarks..."></textarea>
            </div>

            <div class="form-group col-sm-6">
                <label>Final Status <sup>REQUIRED</sup></label>
                <select name="final_status" class="form-control">
                    <option value="">--Select--</option>
                    <option value="complete">Complete</option>
                    <option value="in_progress">Inprogress</option>
                </select>
            </div>
        </div>
    </div>
    <button type="button" id="add-more" class="btn btn-primary">Add More Tasks</button>
    <button type="button" class="btn btn-danger remove-entry">Remove Task</button>
</div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="equipment_id" value="<?= $info->equipment_id; ?>" />
                    <button type="submit" class="btn btn-primary">Add</button>
                    <button type="button" class="btn border_success text_successb" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="addMileageModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add mileage - <?= $info->equipment_registration; ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("assets/add_mileage"); ?>" method="post">
                <div class="modal-body row">

                    <?= $this->steve->form_group_label_input("number", "current_mileage", "Current mileage or hours used", "col-sm-12", 1, '', 10); ?>

                    <?= $this->steve->form_group_label_input("text", "record_date", "Record date", "col-sm-12 date_picker_now", 1, '', 10); ?>

                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id" class="equipment_id" value="<?= $info->equipment_id; ?>" />
                    <button type="submit" class="btn btn-success">Add mileage</button>
                    <button type="button" class="btn border_success text_successb" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php if ($this->user_model->has_perm("add_equipment_consumption")) { ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="addConsumableModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add consumption - <?= $info->equipment_registration; ?>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" action="<?= site_url("assets/add_consumable"); ?>" method="post">
                    <div class="modal-body row">

                        <?= $this->steve->form_group_label_select("consumable_id", "Consumable name", $this->steve->consumables(), "consumable_id", "consumable_name", "col-sm-6", 1); ?>


                        <?= $this->steve->form_group_label_input("number", "consumable_quantity", "Quantity consumed", "col-sm-6", 1, '', 10); ?>

                        <?= $this->steve->form_group_label_input("text", "consumable_date", "Consumption recorded date", "col-sm-12 date_picker_now", 1, '', 10); ?>

                    </div>

                    <div class="modal-footer">
                        <input type="hidden" name="id" class="equipment_id" value="<?= $info->equipment_id; ?>" />
                        <button type="submit" class="btn btn-success">Add consumption</button>
                        <button type="button" class="btn border_success text_successb" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

<?php if ($this->user_model->has_perm("add_history")) { ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="addusageModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Asset usage history
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" action="<?= site_url("assets/add_usage"); ?>" method="post">
                    <div class="modal-body row">

                        <?= $this->steve->form_group_label_input("date", "vh_date", "Start Date", "col-sm-6", 1, '', 10); ?>
                        <?= $this->steve->form_group_label_input("date", "vh_date_end", "End Date", "col-sm-6", 1, '', 10); ?>
                        <div class="form-group col-sm-6">
                            <label for="form_vh_start_time">Start time</label>
                            <input type="time" class="form-control" placeholder="Start time" id="form_vh_start_time"
                                name="vh_time_start" step="0000000" />
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="form_vh_end_time">End time</label>
                            <input type="time" class="form-control" placeholder="End time" id="form_vh_end_time"
                                name="vh_time_end" step="0000000" />
                        </div>

                        <?= $this->steve->form_group_label_textarea("vh_location_start", "Location", "col-sm-12", 0, ''); ?>

                        <!-- <div class="form-group col-sm-12">
                            <label for="form_vh_locations">Driver Name | IC No <sup>REQUIRED</sup></label>
                            <select class="form-control" name="driver_id">
                                <?php foreach ($this->steve->workers() as $workers) { ?>
                                    <option value="<?= $workers->worker_id . '|' . $workers->ic_number ?>"
                                        <?= (in_array($workers->worker_id, ($driver_id ?? [])) ? 'selected' : '') ?>>
                                        <?= $workers->worker_name . ' | ' . $workers->ic_number ?></option>
                                <?php } ?>
                            </select>
                        </div> -->

                    </div>

                    <div class="modal-footer">
                        <input type="hidden" name="id" class="equipment_id" value="<?= $info->equipment_id; ?>" />
                        <button type="submit" class="btn btn-success">Add Asset Usage history</button>
                        <button type="button" class="btn border_success text_successb" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>


<!-- Edit Maintenance Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="editMaintenanceModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Maintenance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" id="editMaintenanceForm" method="post">
                <input type="hidden" name="maintenance_id" id="edit_maintenance_id">
                <input type="hidden" name="equipment_id" value="<?= $info->equipment_id; ?>">
                <div class="modal-body">
                    <div id="edit-dynamic-form">
                        <!-- Dynamic fields will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php if ($this->user_model->has_perm("add_equipments")) { ?>
    <div class="modal fade component-modal" tabindex="-1" role="dialog" id="addModal">


        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Component</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form class="form-horizontal scrollable-form" action="<?= site_url("assets/addAssetItems"); ?>"
                    method="post" enctype="multipart/form-data">
                    <?= $this->steve->form_group_label_input("hidden", "asset_id", "", "col-sm-4", 0, $info->equipment_id, 125); ?>
                    <!-- Add Items Section -->
                    <div class="bg-white card-header py-3">
                        <h6 class="bg-white m-0 font-weight-bold text-primary">Add Component</h6>
                    </div>

                    <!-- Container for items (Ensure this container exists) -->
                    <div id="itemContainer">
                        <div class="itemSection">
                            <div class="modal-body row">

                                <?= $this->steve->form_group_label_input("text", "item[]", "Component", "col-sm-4", 0, $info->item, 125); ?>

                                <!-- item type  -->
                                <div class="form-group col-sm-4 uppercase">
                                    <label>Component Type <sup style="color:red; font-size:8px;">Required</sup></label><br />
                                    <select class="form-control item-type-calibration" name="item_type[]">
                                        <?php foreach ($itemTypes as $it) { ?>
                                            <option value="<?= $it->id ?>"><?= $it->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- Vendor Part Number Dropdown -->
                                <div class="col-sm-4 form-group">
                                    <label for="vendor_part_number">Vendor Part Number <sup style="color:red; font-size:8px;">Required</sup></label>
                                    <select name="vendor_part_number[]" class="form-control">
                                        <option value="">-- Select --</option>
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
                                    <label>Manufacturer Name <sup style="color:red; font-size:8px;">Required</sup></label><br />
                                    <select class="form-control" class="p-0" name="manufacturer_name[]">
                                        <option value="">--Select--</option>
                                        <?php foreach ($manufacturer_number as $mn) { ?>
                                            <option value="<?= $mn->manufacturer_name ?>"><?= $mn->manufacturer_name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- Manufacturer Drawing Number Dropdown -->
                                <div class="col-sm-4 form-group">
                                    <label for="manufacturer_drawing_number">Drawing Number</label>
                                    <select name="manufacturer_drawing_number[]"
                                        id="manufacturer_drawing_number_<?= $key; ?>" class="form-control">
                                        <option value="">-- Select --</option>
                                        <?php foreach ($drawing_numbers as $drawing_number): ?>
                                            <option value="<?= $drawing_number->drawing_number ?>">
                                                <?= $drawing_number->drawing_number ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Manufacturer Part Number Dropdown -->
                                <!-- <div class="col-sm-4 form-group">
                                    <label for="manufacturer_part_number">Manufacturer Part Number</label>
                                    <select name="manufacturer_part_number[]" class="form-control">
                                        <option value="">-- Select --</option>

                                    </select>
                                </div> -->



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

                                <div class="form-group col-sm-4 uppercase" id="faulty_type_new">
                                    <label>Faulty Type</label><br />
                                    <select class="form-control" id="faulty_type_new" class="p-0" name="faulty_type_item[]">
                                        <option value="">--Select--</option>
                                        <?php foreach ($faulty as $f) { ?>
                                            <option value="<?= $f->id; ?>"
                                                <?= ($f->id == $info->faulty_type_id) ? 'selected' : ''; ?>>
                                                <?= $f->fault_type; ?></option>
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
                                        placeholder="Maintenance Date">
                                </div>

                                <div class="form-group col-sm-4 uppercase" id="frequency_year_item" style="display: none;">
                                    <label>Frequency In years</label><br />
                                    <input type="text" class="form-control" name="frequency_year_item[]" placeholder="2">
                                </div>

                                <div class="form-group col-sm-4 uppercase" id="maintenance_reminder_day_item" style="display: none;">
                                    <label>Reminder In Days</label><br />
                                    <input type="text" class="form-control" name="maintenance_reminder_day_item[]" placeholder="30">
                                </div>


                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <label for="">Check for Faulty type</label>
                                    <input type="checkbox" id="faulty_type_toggle_new">
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Button to Add More Items -->
                    <div class="col-sm-4">
                        <button type="button" class="btn btn-primary" id="addItemButton">Add More Component</button>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Add</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
<?php } ?>

<!-- expandable view for items -->
<script>
    function toggleExpandable(header) {
        const content = header.nextElementSibling;
        const arrow = header.querySelector('.arrow');
        if (content.style.display === 'block') {
            content.style.display = 'none';
            arrow.innerHTML = '&#9660;'; // Down arrow
        } else {
            content.style.display = 'block';
            arrow.innerHTML = '&#9650;'; // Up arrow
        }
    }
</script>

<script>
    $(document).ready(function() {
        // Add new form entry
// Update the JavaScript for adding more tasks
$('#add-more').click(function() {
    const newEntry = `
    <div class="row form-entry new-entry">
        <div class="form-group col-sm-6">
            <label>Task Done</label>
            <select class="form-control task-done-select" name="task_done[]">
                <option value="">--Select--</option>
                <?php foreach ($task as $tasks) { ?>
                    <option value="<?= $tasks->name; ?>">
                        <?= $tasks->name; ?></option>
                <?php } ?>
            </select>
        </div>
        
        <div class="form-group col-sm-6">
            <label>Task Remarks</label>
            <textarea name="task_remarks[]" class="form-control" rows="2" placeholder="Enter task remarks..."></textarea>
        </div>
    </div>`;
    $('#dynamic-form').append(newEntry);
});

        // Remove last added form entry
        $('.remove-entry').click(function() {
            const newEntries = $('.new-entry');
            if (newEntries.length > 0) {
                newEntries.last().remove();
            } else {
                alert("No more fields to remove!");
            }
        });

        // Show Ticket and Faulty Type fields conditionally
        $(document).on('change', '.maintenance-type', function() {
            const formEntry = $(this).closest('.form-entry');
            const ticketField = formEntry.find('.ticket');
            const ticketGroup = ticketField.closest('.form-group');

            if ($(this).val() === 'corrective') {
                ticketGroup.show();

                // Add <sup>REQUIRED</sup> to the label if not already present
                const label = ticketGroup.find('label');
                if (!label.html().includes('REQUIRED')) {
                    label.append(' <sup>REQUIRED</sup>');
                }

                // Make field required
                ticketField.prop('required', true);

            } else {
                ticketGroup.hide();

                // Remove required attribute
                ticketField.prop('required', false);

                // Optionally remove <sup>REQUIRED</sup> from label (optional)
                const label = ticketGroup.find('label');
                label.html(label.text().replace('REQUIRED', '').trim());
            }

            // Always hide faulty type on change
            formEntry.find('.faulty-type').parent().hide();
        });


        // Populate Faulty Type based on selected Ticket
        $(document).on('change', '.ticket', function() {
            const formEntry = $(this).closest('.form-entry');
            const selectedTicket = $(this).val();

            if (selectedTicket) {
                $.ajax({
                    url: '<?= site_url("assets/getFaultyType"); ?>',
                    type: 'POST',
                    data: {
                        ticket_number: selectedTicket
                    },
                    dataType: 'json',
                    success: function(response) {
                        const faultyTypeDropdown = formEntry.find('.faulty-type');
                        faultyTypeDropdown.empty(); // Clear existing options


                        response.forEach(function(item) {
                            faultyTypeDropdown.append(`<option value="${item.value}">${item.label}</option>`);
                        });

                        faultyTypeDropdown.parent().show();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching Faulty Type:', error);
                        alert('Error fetching Faulty Type.');
                    }
                });
            } else {
                formEntry.find('.faulty-type').parent().hide();
            }
        });
    });
</script>


<!-- JavaScript to handle adding/removing item fields -->
<script>
    // Wait for the DOM to load
    document.addEventListener("DOMContentLoaded", function() {

        // Get references to the item container and the "Add More Items" button
        var itemContainer = document.getElementById('itemContainer');
        var addItemButton = document.getElementById('addItemButton');

        // Function to add remove button functionality
        function addRemoveButtonEvent(section) {
            var removeButton = section.querySelector('.removeItemButton');
            removeButton.addEventListener('click', function() {
                // Ensure original fields are not removed
                if (section !== originalItemSection) {
                    section.remove(); // Remove the cloned item section
                }
            });
        }

        // Get the first item section (original) and flag it as non-deletable
        var originalItemSection = document.querySelector('.itemSection');

        // Event listener for the "Add More Items" button
        addItemButton.addEventListener('click', function() {
            // Clone the original item section
            var newItemSection = originalItemSection.cloneNode(true);

            // Clear input fields in the cloned section
            newItemSection.querySelectorAll('input').forEach(input => input.value = '');

            // Reset select dropdowns
            newItemSection.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

            // Ensure #calibration_asset_item is unchecked by default

            // Remove any existing remove buttons from cloned section
            newItemSection.querySelectorAll('.removeItemButton').forEach(button => button.remove());
            const calibrationDate = newItemSection.querySelector('#calibration_date_item');
            const frequencyDay = newItemSection.querySelector('#frequency_day_item');
            const reminderDay = newItemSection.querySelector('#reminder_day_item');

            const maintenanceDate = newItemSection.querySelector('#maintenance_date_item');
            const frequencyYear = newItemSection.querySelector('#frequency_year_item');
            const maintenanceReminderDay = newItemSection.querySelector('#maintenance_reminder_day_item');

            calibrationDate.style.display = 'none';
            frequencyDay.style.display = 'none';
            reminderDay.style.display = 'none';
            maintenanceDate.style.display = 'none';
            frequencyYear.style.display = 'none';
            maintenanceReminderDay.style.display = 'none';
            // Append the cloned item section to the container
            itemContainer.appendChild(newItemSection);

            // Add remove button for cloned item section
            var removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.classList.add('btn', 'btn-danger', 'removeItemButton', 'form-group');
            removeButton.textContent = 'X';
            newItemSection.querySelector('.modal-body').appendChild(removeButton);

            // Remove the section when the "X" button is clicked
            removeButton.addEventListener('click', function() {
                newItemSection.remove();
            });

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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


<script>
    $(document).ready(function() {
        $("#order_year_adhoc").on("change", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const frequencyInput = document.getElementById('frequency_day_input');
        const reminderInput = document.getElementById('reminder_day_input');

        // Event listener for frequency day input
        frequencyInput.addEventListener('input', validateDays);

        // Event listener for reminder day input
        reminderInput.addEventListener('input', validateDays);

        function validateDays() {
            const frequencyDay = parseInt(frequencyInput.value) || 0;
            const reminderDay = parseInt(reminderInput.value) || 0;

            if (reminderDay > frequencyDay) {
                alert('Reminder In Days should not be greater than Frequency In Days.');
                reminderInput.value = ''; // Clear the invalid input
            }
        }
    });
</script>

<!-- jQuery Script to Enable Dropdown and Filtering -->
<script>
    
    $(document).ready(function() {

        // Initialize Select2 on Asset dropdown
        $('#asset_id').select2({
            placeholder: "Select Asset", // Optional
            allowClear: true // Allow clearing the selection
        });

        // Initialize Select2 on Manufacturer Name dropdown
        $('#manufacturer_name').select2({
            placeholder: "Select Manufacturer", // Optional
            allowClear: true // Allow clearing the selection
        });

        // Optional: If you need to show a custom "Manufacturer Name" on selection
        $('#manufacturer_name').on('change', function() {
            var selectedText = $(this).find('option:selected').text();
            $('#manufacturer_dropdown .selected-text').text(selectedText);
        });

        // Toggle Manufacturer Dropdown visibility
        $('#manufacturer_dropdown').on('click', function(e) {
            e.stopPropagation();
            $('#manufacturer_searchable_dropdown .dropdown-search').toggle();
        });

        // Toggle Asset Dropdown visibility (if needed)
        $('#asset_id').on('click', function(e) {
            e.stopPropagation();
            $('#asset_searchable_dropdown .dropdown-search').toggle();
        });

        // Manufacturer Search Filter
        $('#manufacturer_search').on('keyup', function(e) {
            e.stopPropagation(); // Prevent closing the dropdown
            var searchText = $(this).val().toLowerCase();
            $('#manufacturer_options .dropdown-item').each(function() {
                var optionText = $(this).text().toLowerCase();
                $(this).toggle(optionText.includes(searchText));
            });
        });

        // Asset Search Filter (if needed)
        $('#asset_search').on('keyup', function(e) {
            e.stopPropagation(); // Prevent closing the dropdown
            var searchText = $(this).val().toLowerCase();
            $('#asset_options .dropdown-item').each(function() {
                var optionText = $(this).text().toLowerCase();
                $(this).toggle(optionText.includes(searchText));
            });
        });

        // Select Manufacturer Option
        $('#manufacturer_options .dropdown-item').on('click', function(e) {
            e.stopPropagation();
            var selectedValue = $(this).data('value');
            var selectedText = $(this).text();

            $('#manufacturer_dropdown .selected-text').text(selectedText);
            $('#manufacturer_name').val(selectedValue);

            $('#manufacturer_searchable_dropdown .dropdown-search').hide();
        });

        // Select Asset Option
        $('#asset_id').on('change', function(e) {
            e.stopPropagation();
            var selectedValue = $(this).val();
            var selectedText = $(this).find('option:selected').text();

            $('#asset_id').val(selectedValue);
        });

        // Close dropdown when clicking outside of any searchable-dropdown
        $(document).on('click', function(e) {
            // Only close if the click is outside the dropdowns
            if (!$(e.target).closest('.searchable-dropdown').length) {
                $('.dropdown-search').hide();
            }
        });
    });

    

</script>

<script>
$(document).ready(function() {
    // Store DataTable instances
    var dataTableInstances = {};
    
// Function to initialize DataTable
// Update your DataTable initialization function
function initDataTable(tableId, config) {
    var $table = $(tableId);
    
    // Destroy existing instance properly
    if ($.fn.DataTable.isDataTable(tableId)) {
        $table.DataTable().clear().destroy();
    }
    
    // Show loader before AJAX call
    $('#datatable-loader').show();
    
    // Initialize with callbacks
    var dataTable = $table.DataTable({
        ...config,
        "initComplete": function(settings, json) {
            // Hide loader when table is fully initialized
            $('#datatable-loader').hide();
            console.log('Table initialized:', tableId);
        },
        "drawCallback": function(settings) {
            // Hide loader after each draw
            $('#datatable-loader').hide();
        },
        "error": function(xhr, error, thrown) {
            // Hide loader on error too
            $('#datatable-loader').hide();
            console.error('Error loading data for', tableId, ':', error);
        }
    });
    
    dataTableInstances[tableId] = dataTable;
    return dataTable;
}
    
    // Initialize Asset Usage Table
    function initAssetUsageTable() {
        var tableId = '#equipment_usage';
        var config = {
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= site_url('assets/usage_ajax_list'); ?>",
                "type": "POST",
                "data": function(d) {
                    d.id = "<?= $info->equipment_id; ?>";
                },
                "dataSrc": function(json) {
                    console.log('Usage Data:', json); // Debug
                    return json.data;
                }
            },
            "columns": [
                { 
                    "data": "vh_date",
                    "render": function(data) {
                        return data ? moment(data).format('DD/MM/YYYY') : '';
                    }
                },
                { 
                    "data": "vh_date_end",
                    "render": function(data) {
                        return data ? moment(data).format('DD/MM/YYYY') : '';
                    }
                },
                { "data": "vh_time_start" },
                { "data": "vh_time_end" },
                { "data": "vh_location_start" }
            ],
            "order": [[0, "desc"]],
            "pageLength": 10,
            "language": {
                "processing": "<i class='fa fa-spinner fa-spin'></i> Loading...",
                "emptyTable": "No usage records found"
            }
        };
        
        return initDataTable(tableId, config);
    }
    
    // Initialize the table
    var usageTable = initAssetUsageTable();
    
    // Handle Add Asset Usage History form submission
        $('#addusageModal form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var modal = $('#addusageModal');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                modal.modal('hide');
                
                // Show success message
                alert('Asset Usage History added successfully!');
                
                // Refresh the page after 1 second
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            },
            error: function() {
                alert('Error adding usage history');
            }
        });
    });
    
    // Toast notification function
    function showToast(type, message) {
        // Adjust this based on your notification system
        alert(message); // Temporary simple alert
        
        // If you have toastr or similar:
        // toastr[type](message);
    }




// Update your maintenance table initialization
function initNewMaintenanceTable() {
    var tableId = '#equipment_new_maintenance';
    
    var config = {
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= site_url('assets/new_maintenance_ajax_list'); ?>",
            "type": "POST",
            "data": function(d) {
                d.id = "<?= $info->equipment_id; ?>";
                return d;
            },
            "dataSrc": function(json) {
            console.log('data', json.d);
                console.log('Maintenance Data Loaded:', json.data.length, 'records');
                return json.data;
            }
        },
        "columns": [
            {
                "data": "equipment_maintenance_id",
                "render": function(data, type, row) {
                    // Debug log
                    console.log('Rendering button for maintenance ID:', data);
                    
                    return `
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-info btn-sm view-maintenance" 
                                data-id="${data}" title="View Details">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-warning btn-sm edit-maintenance" 
                                data-id="${data}" title="Edit">
                                <i class="fa fa-edit"></i>
                            </button>
                        </div>
                    `;
                },
                "orderable": false,
                "searchable": false
            },
            { 
                "data": "update_date",
                "render": function(data) {
                    return data ? moment(data).format('DD/MM/YYYY') : 'N/A';
                }
            },
            { 
                "data": "maintenance_type_id",
                "render": function(data) {
                    if (!data) return 'N/A';
                    return data.charAt(0).toUpperCase() + data.slice(1);
                }
            },
            { 
                "data": "ticket_number",
                "render": function(data) {
                    return data || 'N/A';
                }
            },
            { 
                "data": "faulty_type",
                "render": function(data) {
                    return data || 'N/A';
                }
            },
            { 
                "data": "final_status",
                "render": function(data) {
                    if (!data) return '<span class="badge badge-secondary">N/A</span>';
                    
                    var badgeClass = 'badge-';
                    if (data === 'complete') {
                        badgeClass = 'badge-success';
                    } else if (data === 'in_progress') {
                        badgeClass = 'badge-warning';
                    } else {
                        badgeClass = 'badge-secondary';
                    }
                    return `<span class="badge ${badgeClass}">${data.replace('_', ' ')}</span>`;
                }
            },
            { 
                "data": "created_at",
                "render": function(data) {
                    return data ? moment(data).format('DD/MM/YYYY HH:mm') : 'N/A';
                }
            }
        ],
        "order": [[1, "desc"]],
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "language": {
            "processing": "<div class='spinner-border' role='status'><span class='sr-only'>Loading...</span></div>",
            "emptyTable": "No maintenance records found",
            "loadingRecords": "Loading...",
            "zeroRecords": "No matching records found"
        },
        "drawCallback": function(settings) {
            // Re-bind event listeners after table redraw
            bindMaintenanceEvents();
        }
    };
    
    return initDataTable(tableId, config);
}

// Function to bind maintenance event listeners
function bindMaintenanceEvents() {
    // View Maintenance Details
    $('.view-maintenance').off('click').on('click', function() {
        var maintenanceId = $(this).data('id');
        console.log('View clicked for ID:', maintenanceId);
        
        $.ajax({
            url: '<?= site_url("assets/getMaintenanceDetails"); ?>',
            type: 'POST',
            data: { id: maintenanceId },
            dataType: 'json',
            success: function(response) {
                console.log('View response:', response);
                if (response.success && response.data) {
                    var content = `
                        <div class="modal-body">
                            <h6 class="font-weight-bold text-primary">Maintenance Details</h6>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Date:</strong> ${response.data.update_date || 'N/A'}</p>
                                    <p><strong>Type:</strong> ${response.data.maintenance_type_id || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> <span class="badge badge-${response.data.final_status === 'complete' ? 'success' : 'warning'}">${response.data.final_status || 'N/A'}</span></p>
                                    <p><strong>Created:</strong> ${response.data.created_at ? response.data.created_at.split(' ')[0] : 'N/A'}</p>
                                </div>
                            </div>
                            <!-- ADDED: Remarks Section -->
                            <div class="row">
                                <div class="col-md-12">
                                    <hr>
                                    <h6 class="font-weight-bold text-primary">Remarks</h6>
                                    <p>${response.data.remarks || 'No remarks provided'}</p>
                                </div>
                            </div>
                    `;
                    
                    if (response.tasks && response.tasks.length > 0) {
                        content += `
                            <hr>
                            <h6 class="font-weight-bold">Tasks Done:</h6>
                            <ul class="list-group">
                        `;
                        
                        response.tasks.forEach(function(task) {
                            content += `
                                <li class="list-group-item">
                                    <strong>${task.task_done || 'N/A'}</strong><br>
                                    <small>Remarks: ${task.remarks || 'No remarks'}</small>
                                </li>
                            `;
                        });
                        
                        content += '</ul>';
                    }
                    
                    content += '</div>';
                    
                    $('#modal-body-content').html(content);
                    $('#equipmentModal').modal('show');
                } else {
                    alert('Error loading maintenance details: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Error loading maintenance details. Please try again.');
            }
        });
    });
    
    // Edit Maintenance
    $('.edit-maintenance').off('click').on('click', function() {
        var maintenanceId = $(this).data('id');
        console.log('Edit clicked for ID:', maintenanceId);
        
        
        $.ajax({
            url: '<?= site_url("assets/getMaintenanceDetails"); ?>',
            type: 'POST',
            data: { id: maintenanceId },
            dataType: 'json',
            success: function(response) {

                console.log('Edit response:', response);
                if (response.success && response.data) {
                    $('#edit_maintenance_id').val(maintenanceId);
                    var formContent = '';
                    

                    // Add main fields
                    formContent += `
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Update Date *</label>
                                <input type="date" name="update_date" class="form-control"                                         
                                    value="${response.data.update_date ? response.data.update_date.split(' ')[0] : ''}" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Maintenance Type *</label>
                                <select name="maintenance_type" class="form-control" required>
                                    <option value="">--Select--</option>
                                    <option value="preventive" ${response.data.maintenance_type_id == 'preventive' ? 'selected' : ''}>Preventive</option>
                                    <option value="corrective" ${response.data.maintenance_type_id == 'corrective' ? 'selected' : ''}>Corrective</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Final Status *</label>
                                <select name="final_status" class="form-control" required>
                                    <option value="">--Select--</option>
                                    <option value="complete" ${response.data.final_status == 'complete' ? 'selected' : ''}>Complete</option>
                                    <option value="in_progress" ${response.data.final_status == 'in_progress' ? 'selected' : ''}>In Progress</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <h6>Tasks & Remarks</h6>
                        <div id="task-container">
                    `;
                    
                    // Add task fields
                    if (response.tasks && response.tasks.length > 0) {
                        response.tasks.forEach(function(task, index) {
                            formContent += `
                                <div class="row task-row" data-index="${index}">
                                    <div class="form-group col-sm-6">
                                        <label>Task Done</label>
                                        <select name="task_done[]" class="form-control">
                                            <option value="">--Select--</option>
                                            <?php foreach ($task as $tasks) { ?>
                                                <option value="<?= $tasks->name; ?>" ${task.task_done == '<?= $tasks->name; ?>' ? 'selected' : ''}>
                                                    <?= $tasks->name; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-5">
                                        <label>Remarks</label>
                                        <textarea name="remarks[]" class="form-control" rows="2">${task.remarks || ''}</textarea>
                                    </div>
                                    <div class="form-group col-sm-1" style="padding-top: 30px;">
                                        ${index > 0 ? '<button type="button" class="btn btn-danger btn-sm remove-task-btn"><i class="fa fa-trash"></i></button>' : ''}
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        // Default empty task
                        formContent += `
                            <div class="row task-row" data-index="0">
                                <div class="form-group col-sm-6">
                                    <label>Task Done</label>
                                    <select name="task_done[]" class="form-control">
                                        <option value="">--Select--</option>
                                        <?php foreach ($task as $tasks) { ?>
                                            <option value="<?= $tasks->name; ?>"><?= $tasks->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-5">
                                    <label>Remarks</label>
                                    <textarea name="remarks[]" class="form-control" rows="2" placeholder="Enter remarks..."></textarea>
                                </div>
                                <div class="form-group col-sm-1" style="padding-top: 30px;">
                                    <!-- First row no delete button -->
                                </div>
                            </div>
                        `;
                    }
                    
                    formContent += `
                        </div>
                        <div class="text-right mt-2">
                            <button type="button" id="add-task-btn" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus"></i> Add Task
                            </button>
                        </div>
                    `;
                    
                    $('#edit-dynamic-form').html(formContent);
                    
                    // Bind task management events
                    bindTaskManagementEvents();
                    
                    $('#editMaintenanceModal').modal('show');
                } else {
                    alert('Error loading maintenance details for editing: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Error loading maintenance details. Please try again.');
            }
        });
    });
}

// Function to bind task management events
function bindTaskManagementEvents() {
    // Add task button
    $('#add-task-btn').off('click').on('click', function() {
        var index = $('.task-row').length;
        var newTask = `
            <div class="row task-row" data-index="${index}">
                <div class="form-group col-sm-6">
                    <label>Task Done</label>
                    <select name="task_done[]" class="form-control">
                        <option value="">--Select--</option>
                        <?php foreach ($task as $tasks) { ?>
                            <option value="<?= $tasks->name; ?>"><?= $tasks->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-sm-5">
                    <label>Remarks</label>
                    <textarea name="remarks[]" class="form-control" rows="2" placeholder="Enter remarks..."></textarea>
                </div>
                <div class="form-group col-sm-1" style="padding-top: 30px;">
                    <button type="button" class="btn btn-danger btn-sm remove-task-btn">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#task-container').append(newTask);
        
        // Bind remove event to new button
        $('.remove-task-btn').off('click').on('click', function() {
            $(this).closest('.task-row').remove();
            // Re-index remaining rows
            $('.task-row').each(function(i) {
                $(this).attr('data-index', i);
            });
        });
    });
    
    // Remove task buttons
    $('.remove-task-btn').off('click').on('click', function() {
        $(this).closest('.task-row').remove();
        // Re-index remaining rows
        $('.task-row').each(function(i) {
            $(this).attr('data-index', i);
        });
    });
}


    // Add this JavaScript to handle edit and view
$(document).ready(function() {
    var maintenanceTable = initNewMaintenanceTable();
    
    // View Maintenance Details
    $(document).on('click', '.view-maintenance', function() {
        var maintenanceId = $(this).data('id');
        
        $.ajax({
            url: '<?= site_url("assets/getMaintenanceDetails"); ?>',
            type: 'POST',
            data: { id: maintenanceId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var content = `
                        <h6>Maintenance Details</h6>
                        <p><strong>Date:</strong> ${response.data.update_date}</p>
                        <p><strong>Type:</strong> ${response.data.maintenance_type_id}</p>
                        <p><strong>Ticket:</strong> ${response.data.ticket_number || 'N/A'}</p>
                        <p><strong>Faulty Type:</strong> ${response.data.faulty_type || 'N/A'}</p>
                        <p><strong>Status:</strong> ${response.data.final_status}</p>
                        <hr>
                        <h6>Tasks Done:</h6>
                        <ul>
                    `;
                    
                    response.tasks.forEach(function(task) {
                        content += `
                            <li>
                                <strong>${task.task_done}</strong><br>
                                <small>Remarks: ${task.remarks || 'No remarks'}</small>
                            </li>
                        `;
                    });
                    
                    content += '</ul>';
                    
                    $('#modal-body-content').html(content);
                    $('#equipmentModal').modal('show');
                } else {
                    alert('Error loading maintenance details');
                }
            }
        });
    });
    
    // Edit Maintenance
    $(document).on('click', '.edit-maintenance', function() {
        var maintenanceId = $(this).data('id');
        
        $.ajax({
            url: '<?= site_url("assets/getMaintenanceDetails"); ?>',
            type: 'POST',
            data: { id: maintenanceId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#edit_maintenance_id').val(maintenanceId);
                    var formContent = '';
                    
                    // Add main fields
                    formContent += `
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Update Date</label>
                                <input type="date" name="update_date" class="form-control" 
                                    value="${response.data.update_date.split(' ')[0]}" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Maintenance Type</label>
                                <select name="maintenance_type" class="form-control" required>
                                    <option value="preventive" ${response.data.maintenance_type_id == 'preventive' ? 'selected' : ''}>Preventive</option>
                                    <option value="corrective" ${response.data.maintenance_type_id == 'corrective' ? 'selected' : ''}>Corrective</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Final Status</label>
                                <select name="final_status" class="form-control" required>
                                    <option value="complete" ${response.data.final_status == 'complete' ? 'selected' : ''}>Complete</option>
                                    <option value="in_progress" ${response.data.final_status == 'in_progress' ? 'selected' : ''}>In Progress</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <h6>Tasks & Remarks</h6>
                    `;
                    
                    // Add task fields
                    response.tasks.forEach(function(task, index) {
                        formContent += `
                            <div class="row task-row" data-index="${index}">
                                <div class="form-group col-sm-6">
                                    <label>Task Done</label>
                                    <select name="task_done[]" class="form-control">
                                        <option value="">--Select--</option>
                                        <?php foreach ($task as $tasks) { ?>
                                            <option value="<?= $tasks->name; ?>" ${task.task_done == '<?= $tasks->name; ?>' ? 'selected' : ''}>
                                                <?= $tasks->name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label>Remarks</label>
                                    <textarea name="remarks[]" class="form-control" rows="2">${task.remarks || ''}</textarea>
                                </div>
                            </div>
                        `;
                    });
                    
                    formContent += `
                        <button type="button" id="add-task" class="btn btn-sm btn-primary mt-2">Add Task</button>
                        <button type="button" id="remove-task" class="btn btn-sm btn-danger mt-2">Remove Task</button>
                    `;
                    
                    $('#edit-dynamic-form').html(formContent);
                    $('#editMaintenanceModal').modal('show');
                } else {
                    alert('Error loading maintenance details for editing');
                }
            }
        });
    });
    
    // Add task button in edit modal
    $(document).on('click', '#add-task', function() {
        var index = $('.task-row').length;
        var newTask = `
            <div class="row task-row" data-index="${index}">
                <div class="form-group col-sm-6">
                    <label>Task Done</label>
                    <select name="task_done[]" class="form-control">
                        <option value="">--Select--</option>
                        <?php foreach ($task as $tasks) { ?>
                            <option value="<?= $tasks->name; ?>"><?= $tasks->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label>Remarks</label>
                    <textarea name="remarks[]" class="form-control" rows="2" placeholder="Enter remarks..."></textarea>
                </div>
            </div>
        `;
        $('#edit-dynamic-form').append(newTask);
    });
    
    // Remove task button in edit modal
    $(document).on('click', '#remove-task', function() {
        var rows = $('.task-row');
        if (rows.length > 1) {
            rows.last().remove();
        }
    });
    
    // Handle edit form submission
    $('#editMaintenanceForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= site_url("assets/updateMaintenance"); ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editMaintenanceModal').modal('hide');
                    maintenanceTable.ajax.reload();
                    alert('Maintenance updated successfully');
                } else {
                    alert('Error updating maintenance: ' + response.message);
                }
            }
        });
    });
});

});
</script>

<script>
$(document).ready(function() {
    // Global loader control
    function showLoader() {
        $('#datatable-loader').fadeIn(300);
    }
    
    function hideLoader() {
        $('#datatable-loader').fadeOut(300);
    }
    
    // Initialize Maintenance DataTable
function initMaintenanceTable() {
    // Hide global loader first
    $('#datatable-loader').hide();
    
    // Destroy existing table if any
    if ($.fn.DataTable.isDataTable('#equipment_new_maintenance')) {
        $('#equipment_new_maintenance').DataTable().destroy();
    }
    
    var table = $('#equipment_new_maintenance').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= site_url('assets/new_maintenance_ajax_list'); ?>",
            "type": "POST",
            "data": function(d) {
                return {
                    id: "<?= $info->equipment_id; ?>"
                };
            },
            "beforeSend": function() {
                // Table ke upar ek simple loader show karo
                $('#equipment_new_maintenance_wrapper').prepend(
                    '<div id="table-loader" style="text-align:center;padding:20px;">' +
                    '<div class="spinner-border text-primary" role="status"></div>' +
                    '<div>Loading maintenance data...</div>' +
                    '</div>'
                );
            },
            "complete": function() {
                // Table loader hide karo
                $('#table-loader').remove();
            }
        },
        "columns": [
            { 
                "data": "update_date",
                "render": function(data) {
                    return data ? moment(data,'DD/MM/YYYY').format('DD/MM/YYYY') : 'N/A';
                }
            },
            { 
                "data": "maintenance_type_id",
                "render": function(data) {
                    return data || 'N/A';
                }
            },
            { 
                "data": "final_status",
                "render": function(data) {
                    var badgeClass = 'badge-';
                    if (data === 'complete') {
                        badgeClass = 'badge-success';
                    } else if (data === 'in_progress') {
                        badgeClass = 'badge-warning';
                    } else {
                        badgeClass = 'badge-secondary';
                    }
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            { 
                "data": "created_at",
                "render": function(data) {
                    // return data ? moment(data).format('DD/MM/YYYY HH:mm') : 'N/A';
                    return data ? moment(data,'DD/MM/YYYY HH:mm').format('DD/MM/YYYY HH:mm') : 'N/A';
                }
            },
            {
                "data": "equipment_maintenance_id",
                "render": function(data, type, row) {
                    return `
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-info btn-sm view-maintenance" 
                                data-id="${data}" title="View Details">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-warning btn-sm edit-maintenance" 
                                data-id="${data}" title="Edit">
                                <i class="fa fa-edit"></i>
                            </button>
                        </div>
                    `;
                },
                "orderable": false,
                "searchable": false
            }
        ],
        "order": [[0, "desc"]],
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "language": {
            "processing": "<div class='spinner-border' role='status'></div> Loading...",
            "emptyTable": "No maintenance records found",
            "loadingRecords": "Loading...",
            "zeroRecords": "No matching records found"
        },
        "initComplete": function(settings, json) {
            bindMaintenanceEvents();
        },
        "drawCallback": function(settings) {
            bindMaintenanceEvents();
        }
    });
    
    return table;
}
    
    // Initialize the table
    var maintenanceTable = initMaintenanceTable();
    
    // Bind maintenance event listeners
    function bindMaintenanceEvents() {
        // View Maintenance Details
        $(document).off('click', '.view-maintenance').on('click', '.view-maintenance', function() {
            var maintenanceId = $(this).data('id');
            console.log('View clicked for ID:', maintenanceId);
            
            showLoader();
            
            $.ajax({
                url: '<?= site_url("assets/getMaintenanceDetails"); ?>',
                type: 'POST',
                data: { id: maintenanceId },
                dataType: 'json',
                success: function(response) {
                    hideLoader();
                    console.log('View response:', response);
                    
                    if (response.success && response.data) {
                        var content = `
                            <div class="modal-body">
                                <h6 class="font-weight-bold text-primary">Maintenance Details</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Date:</strong> ${response.data.update_date || 'N/A'}</p>
                                        <p><strong>Type:</strong> ${response.data.maintenance_type_id || 'N/A'}</p>
                                        <p><strong>Ticket:</strong> ${response.data.ticket_number || 'N/A'}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Faulty Type:</strong> ${response.data.faulty_type || 'N/A'}</p>
                                        <p><strong>Status:</strong> <span class="badge badge-${response.data.final_status === 'complete' ? 'success' : 'warning'}">${response.data.final_status || 'N/A'}</span></p>
                                        <p><strong>Created:</strong> ${response.data.created_at ? response.data.created_at.split(' ')[0] : 'N/A'}</p>
                                    </div>
                                </div>
                        `;
                        
                        if (response.tasks && response.tasks.length > 0) {
                            content += `
                                <hr>
                                <h6 class="font-weight-bold text-primary">Tasks Done:</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Task Done</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
                            
                            response.tasks.forEach(function(task) {
                                content += `
                                    <tr>
                                        <td><strong>${task.task_done || 'N/A'}</strong></td>
                                        <td>${task.remarks || 'No remarks'}</td>
                                    </tr>
                                `;
                            });
                            
                            content += `
                                        </tbody>
                                    </table>
                                </div>
                            `;
                        } else {
                            content += `
                                <hr>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> No tasks recorded for this maintenance.
                                </div>
                            `;
                        }
                        
                        content += '</div>';
                        
                        $('#modal-body-content').html(content);
                        $('#equipmentModal').modal('show');
                    } else {
                        alert('Error loading maintenance details: ' + (response.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    hideLoader();
                    console.error('AJAX Error:', error);
                    alert('Error loading maintenance details. Please try again.');
                }
            });
        });
        
        // Edit Maintenance
        $(document).off('click', '.edit-maintenance').on('click', '.edit-maintenance', function() {
            var maintenanceId = $(this).data('id');
            console.log('Edit clicked for ID:', maintenanceId);
            
            showLoader();
            
            $.ajax({
                url: '<?= site_url("assets/getMaintenanceDetails"); ?>',
                type: 'POST',
                data: { id: maintenanceId },
                dataType: 'json',
                success: function(response) {
                    hideLoader();
                    console.log('Edit response:', response);
                    
                    if (response.success && response.data) {
                        $('#edit_maintenance_id').val(maintenanceId);
                        var formContent = '';
                        
                        // Add main fields
                        formContent += `
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label>Update Date *</label>
                                    <input type="date" name="update_date" class="form-control" 
                                        value="${response.data.update_date ? response.data.update_date.split(' ')[0] : ''}" required>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label>Maintenance Type *</label>
                                    <select name="maintenance_type" class="form-control" required>
                                        <option value="">--Select--</option>
                                        <option value="preventive" ${response.data.maintenance_type_id == 'preventive' ? 'selected' : ''}>Preventive</option>
                                        <option value="corrective" ${response.data.maintenance_type_id == 'corrective' ? 'selected' : ''}>Corrective</option>
                                    </select>
                                </div>
                                <!-- ADDED: Remarks Field -->
                                <div class="form-group col-sm-12">
                                    <label>Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="3" placeholder="Enter remarks...">${response.data.remarks || ''}</textarea>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label>Final Status *</label>
                                    <select name="final_status" class="form-control" required>
                                        <option value="">--Select--</option>
                                        <option value="complete" ${response.data.final_status == 'complete' ? 'selected' : ''}>Complete</option>
                                        <option value="in_progress" ${response.data.final_status == 'in_progress' ? 'selected' : ''}>In Progress</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <h6 class="font-weight-bold text-primary">Tasks & Task Remarks</h6>
                            <div id="task-container">
                        `;
                        
                        // Add task fields
                        if (response.tasks && response.tasks.length > 0) {
                            response.tasks.forEach(function(task, index) {
                                formContent += `
                                    <div class="row task-row" data-index="${index}">
                                        <div class="form-group col-sm-5">
                                            <label>Task Done</label>
                                            <select name="task_done[]" class="form-control">
                                                <option value="">--Select--</option>
                                                <?php foreach ($task as $tasks) { ?>
                                                    <option value="<?= $tasks->name; ?>" ${task.task_done == '<?= $tasks->name; ?>' ? 'selected' : ''}>
                                                        <?= $tasks->name; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label>Remarks</label>
                                            <textarea name="remarks[]" class="form-control" rows="2" placeholder="Enter remarks...">${task.remarks || ''}</textarea>
                                        </div>
                                        <div class="form-group col-sm-1" style="padding-top: 30px;">
                                            ${index > 0 ? '<button type="button" class="btn btn-danger btn-sm remove-task-btn"><i class="fa fa-trash"></i></button>' : ''}
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            // Default empty task
                            formContent += `
                                <div class="row task-row" data-index="0">
                                    <div class="form-group col-sm-5">
                                        <label>Task Done</label>
                                        <select name="task_done[]" class="form-control">
                                            <option value="">--Select--</option>
                                            <?php foreach ($task as $tasks) { ?>
                                                <option value="<?= $tasks->name; ?>"><?= $tasks->name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label>Remarks</label>
                                        <textarea name="remarks[]" class="form-control" rows="2" placeholder="Enter remarks..."></textarea>
                                    </div>
                                    <div class="form-group col-sm-1" style="padding-top: 30px;">
                                        <!-- First row no delete button -->
                                    </div>
                                </div>
                            `;
                        }
                        
                        formContent += `
                            </div>
                            <div class="text-right mt-2">
                                <button type="button" id="add-task-btn" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i> Add Task
                                </button>
                            </div>
                        `;
                        
                        $('#edit-dynamic-form').html(formContent);
                        
                        // Bind task management events
                        bindTaskManagementEvents();
                        
                        $('#editMaintenanceModal').modal('show');
                    } else {
                        alert('Error loading maintenance details for editing: ' + (response.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    hideLoader();
                    console.error('AJAX Error:', error);
                    alert('Error loading maintenance details. Please try again.');
                }
            });
        });
    }
    
    // Function to bind task management events
    function bindTaskManagementEvents() {
        // Add task button
        $('#add-task-btn').off('click').on('click', function() {
            var index = $('.task-row').length;
            var newTask = `
                <div class="row task-row" data-index="${index}">
                    <div class="form-group col-sm-5">
                        <label>Task Done</label>
                        <select name="task_done[]" class="form-control">
                            <option value="">--Select--</option>
                            <?php foreach ($task as $tasks) { ?>
                                <option value="<?= $tasks->name; ?>"><?= $tasks->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Remarks</label>
                        <textarea name="remarks[]" class="form-control" rows="2" placeholder="Enter remarks..."></textarea>
                    </div>
                    <div class="form-group col-sm-1" style="padding-top: 30px;">
                        <button type="button" class="btn btn-danger btn-sm remove-task-btn">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#task-container').append(newTask);
            
            // Bind remove event to new button
            $('.remove-task-btn').off('click').on('click', function() {
                $(this).closest('.task-row').remove();
                // Re-index remaining rows
                $('.task-row').each(function(i) {
                    $(this).attr('data-index', i);
                });
            });
        });
        
        // Remove task buttons
        $('.remove-task-btn').off('click').on('click', function() {
            $(this).closest('.task-row').remove();
            // Re-index remaining rows
            $('.task-row').each(function(i) {
                $(this).attr('data-index', i);
            });
        });
    }
    
    // Handle edit form submission
    $('#editMaintenanceForm').on('submit', function(e) {
        e.preventDefault();
        
        showLoader();
        
        $.ajax({
            url: '<?= site_url("assets/updateMaintenance"); ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                hideLoader();
                if (response.success) {
                    $('#editMaintenanceModal').modal('hide');
                    maintenanceTable.ajax.reload(null, false);
                    alert('Maintenance updated successfully');
                } else {
                    alert('Error updating maintenance: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                hideLoader();
                alert('Error updating maintenance. Please try again.');
            }
        });
    });
    
    // Handle tab change to refresh table
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var target = $(e.target).attr("href");
        if (target === '#nav-new-maintenance') {
            maintenanceTable.ajax.reload(null, false);
        }
    });
});
</script>
