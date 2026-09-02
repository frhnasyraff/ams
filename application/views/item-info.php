<style type="text/css">
    .pagination>li>a {
        border-radius: 10px;
        /*background-color: #fff !important;*/
        /*color: #fff !important;*/
    }

    .pagination>.active>a {
        background-color: #07083dff !important;
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
        border-color: #000;
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
</style>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<nav>
    <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-details-tab" data-toggle="tab" href="#nav-details" role="tab"
            aria-controls="nav-details" aria-selected="true">Details</a>

        <a class="nav-item nav-link" id="nav-new-maintenance-tab" data-toggle="tab" href="#nav-new-maintenance"
            role="tab" aria-controls="nav-maintenance" aria-selected="true">Maintenance</a>

    </div>
</nav>

<div class="row fade tab-pane active show" id="nav-details" role="tabpanel">
    <div class="col-md-8">
        <div class="card shadow mb-4 tabradius">
            <div class="card-body">
                <div class="bg-white card-header py-3">
                    <h6 class="m-0 font-weight-bold text_warning_color">Edit Component</h6>
                    <a onclick="return confirm('Are you sure to this this record?')"
                        href="<?= base_url('items/deleteItem') . '?id=' . $items->id . '&assetid=' . $this->steve->id_encode($items->asset_id) ?>"
                        class=" btn-danger mt-3"><i class="fa fa-trash trash-icon"></i></a>
                </div>
                <form id="formA" class="form-horizontal" action="<?= site_url("items/update"); ?>" method="post">
                    <div class="row">
                        <div class="item-section row">
                            <div class="modal-body row">

                                <input type="hidden" id="item_id" name="item_id" value="<?= $items->id; ?>">

                                <!-- Item Name Field -->
                                <?= $this->steve->form_group_label_input("text", "item", "Component", "col-sm-4", 0, $items->item_name, 125); ?>


                                <div class="col-sm-4 form-group">
                                    <label for="asset_id">Asset</label>
                                    <select name="asset_id" id="asset_id" class="form-control searchable-dropdown">
                                        <option value="<?= $items->asset_id ?>">Select Asset</option>
                                        <?php foreach ($equipments as $pn): ?>
                                            <option value="<?= $pn->equipment_id ?>"
                                                <?= ($pn->equipment_id == $items->asset_id) ? 'selected' : ''; ?>>
                                                <?= $pn->equipment_name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Serial Number -->
                                <?= $this->steve->form_group_label_input("text", "serial_number", "Serial Number", "col-sm-4", 1, $items->serial_number); ?>


                                <!-- Vendor Part Number Dropdown -->
                                <div class="col-sm-4 form-group">
                                    <label for="vendor_part_number">Vendor Part Number</label>
                                    <select name="vendor_part_number" id="vendor_part_number" class="form-control">
                                        <option value="<?= $items->part_number ?>">Select Vendor Part Number</option>
                                        <?php foreach ($part_numbers as $pn): ?>
                                            <option value="<?= $pn->part_number ?>"
                                                <?= ($pn->part_number == $items->vendor_part_number) ? 'selected' : ''; ?>>
                                                <?= $pn->part_number ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Manufacturer Name with Searchable Dropdown -->
                                <div class="form-group col-sm-4 uppercase">
                                    <label for="manufacturer_dropdown">Manufacturer Name</label><br />
                                    <select name="manufacturer_name" id="manufacturer_name"
                                        class="form-control searchable-dropdown">
                                        <option value="">--Select--</option>
                                        <?php foreach ($manufacturer_number as $mn): ?>
                                            <option value="<?= $mn->manufacturer_name ?>"
                                                <?= ($mn->manufacturer_name == $items->manufacturer_name) ? 'selected' : ''; ?>>
                                                <?= $mn->manufacturer_name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>


                                <!-- Manufacturer Drawing Number Dropdown -->
                                <!-- <div class="col-sm-4 form-group">
                                    <label for="manufacturer_drawing_number">Drawing Number</label>
                                    <select name="manufacturer_drawing_number" id="manufacturer_drawing_number"
                                        class="form-control">
                                        <option value="<?= $items->drawing_number ?>">Select Manufacturer Drawing Number
                                        </option>
                                        <?php foreach ($drawing_numbers as $drawing_number): ?>
                                            <option value="<?= $drawing_number->drawing_number ?>"
                                                <?= ($drawing_number->drawing_number == $items->manufacturer_drawing_number) ? 'selected' : ''; ?>>
                                                <?= $drawing_number->drawing_number ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div> -->

                                <!-- Store Location -->
                                <div class="form-group col-sm-4 uppercase">
                                    <label>Store Location</label><br />
                                    <select class="form-control" name="store_location_item">
                                        <option value="0">--Select--</option>
                                        <?php foreach ($storeLocation as $sl): ?>
                                            <option value="<?= $sl->id ?>"
                                                <?= ($sl->id == $items->store_location_id) ? 'selected' : ''; ?>>
                                                <?= $sl->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Item Status Dropdown -->
                                <div class="form-group col-sm-4 uppercase">
                                    <label>Component Status</label><br />
                                    <select class="form-control" name="item_status">
                                        <option value="0">--Select--</option>
                                        <?php foreach ($itemStatus as $is): ?>
                                            <option value="<?= $is->id ?>"
                                                <?= ($is->id == $items->item_status_id) ? 'selected' : ''; ?>>
                                                <?= $is->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Item Type Dropdown -->
                                <div class="col-sm-4 form-group">
                                    <label for="item_type">Component Type</label>
                                    <select name="item_type" class="form-control item_type_calibration_edit">
                                        <?php foreach ($itemTypes as $it): ?>
                                            <option value="<?= $it->id ?>"
                                                <?= ($it->id == $items->item_type_id) ? 'selected' : ''; ?>>
                                                <?= $it->name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Faulty Type Dropdown -->
                                <div class="form-group col-sm-4 uppercase edit_faulty_type_field_item"
                                    id="faulty_type_field_item">
                                    <label>Faulty Type</label><br />
                                    <select class="form-control" name="faulty_type_item">
                                        <option value="">--Select--</option>
                                        <?php foreach ($faulty as $f): ?>
                                            <option value="<?= $f->id; ?>"
                                                <?= ($f->id == $items->faulty_type_id) ? 'selected' : ''; ?>>
                                                <?= $f->fault_type; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Calibration Details -->
                                <div class="form-group col-sm-4 uppercase calibration_date_item"
                                    id="calibration_date_item_edit"
                                    style="<?= ($items->calibration == 1) ? 'display: block;' : 'display: none;' ?>">
                                    <label>1st Calibration Date</label><br />
                                    <input type="date" class="form-control" value="<?= $items->calibration_date ?>"
                                        name="calibration_date_item" placeholder="1st Calibration Date">
                                </div>

                                <div class="form-group col-sm-4 uppercase" id="frequency_day_item_edit"
                                    style="<?= ($items->calibration == 1) ? 'display: block;' : 'display: none;' ?>">
                                    <label>Frequency In Days</label><br />
                                    <input type="text" class="form-control" value="<?= $items->frequency_day ?>"
                                        name="frequency_day_item" placeholder="90">
                                </div>

                                <div class="form-group col-sm-4 uppercase" id="reminder_day_item_edit"
                                    style="<?= ($items->calibration == 1) ? 'display: block;' : 'display: none;' ?>">
                                    <label>Reminder In Days</label><br />
                                    <input type="text" class="form-control" value="<?= $items->reminder_day ?>"
                                        name="reminder_day_item" placeholder="7">
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

                                <!-- Faulty Type Checkbox -->
                                <div class="col-md-6">
                                    <label for="">Check for Faulty Type</label>
                                    <input type="checkbox" class="edit_faulty_type_toggle_item"
                                        id="faulty_type_toggle_item">
                                </div>
                            </div> <!-- End modal-body -->
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <input type="hidden" name="id" value="<?= $items->asset_id; ?>" />
                        <button type="submit" class="btn btn-primary text-white font-weight-bold">Save changes</button>
                        <a class="btn border_success text_successb" data-dismiss="modal" href=".">Go back</a>
                    </div>
                </form>

            </div>
        </div>
    </div>



    <div class="col-md-4">
        <div class="card shadow mb-4 tabradius">
            <div class="card-body">
                <div class="bg-white card-header py-3">
                    <h6 class="bg-white m-0 font-weight-bold text-primary">Component Picture</h6>
                </div>
                <div class="row">
                    <!-- Gallery Section -->
                    <!-- Item Pictures Gallery -->
                    <div class="row col-md-12 mt-2">
                        <div class="col-md-12">
                            <div id="picture-gallery" class="gallery">
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
                                            <img src="<?= base_url('storage/' . $picture->item_picture); ?>" alt="Component Picture"
                                                style="width:100%; max-width:200px;" class="deletable-picture"
                                                data-picture-id="<?= $picture->id; ?>">
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


                    <!-- Upload Section -->
                    <div class="col-md-12 mt-2">
                        <div id="drop-area" class="drop-area">
                            <p>Drag and drop your images here</p>
                            <label for="fileElem" class="btn btn-primary file-choose-btn">Choose Files</label>
                            <input type="file" id="fileElem" class="file-input" name="item_picture" multiple
                                accept="image/*" style="display:none" />
                        </div>
                        <div id="gallery" class="gallery mt-3"></div>
                        <center>
                            <button type="button" id="saveBtn" class="btn btn-primary mt-2 mb-5 save-btn">Save</button>
                        </center>
                    </div>

                </div>
                <!-- QR Code Section -->
                <div class="row tabradius">
                    <div class="card-body">
                        <div class="bg-white card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">QR Code Generator</h6>
                        </div>

                        <div class="table-responsive">
                            <?php if (isset($items->items_qr_code) && $items->items_qr_code == 1): ?>
                                <?php
                                // Find the status name based on ID
                                $statusName = 'Unknown';
                                foreach ($itemStatus as $is) {
                                    if ($is->id == $items->item_status_id) {
                                        $statusName = $is->name;
                                        break;
                                    }
                                }

                                // Build the string with readable status name
                                $chlvalue = "Component name: " . $items->item_name . "\n"
                                    . "Vendor Part number: " . $items->vendor_part_number . "\n"
                                    . "Serial Number: " . $items->serial_number . "\n"
                                    . "Manufacturer Name: " . $items->manufacturer_name . "\n"
                                    . "Status: " . $statusName;

                                // URL encode the string
                                $chlvalue = urlencode($chlvalue);
                                ?>
                                <div class="col-8 offset-2">
                                    <center>
                                        <img width="100px"
                                            src="https://quickchart.io/chart?chs=300x300&cht=qr&chl=<?= $chlvalue ?>&choe=UTF-8"
                                            title="Scan QR Code" />
                                        <br>
                                        <a onclick="return confirm('Are you sure to Delete QR Code for this record?')"
                                            href="<?= base_url('items/itemsqrdel') . '?id=' . $this->steve->id_encode($items->asset_id) . '&unique_id=' . $items->id ?>"
                                            class="btn btn-danger mt-3">Delete</a>

                                    </center>
                                </div>
                            <?php else: ?>
                                <div class="col-md-12">
                                    <center>
                                        <a onclick="return confirm('Are you sure to Generate QR Code for this record?')"
                                            href="<?= base_url('items/itemsqrgen') . '?id=' . $this->steve->id_encode($items->id) . '&unique_id=' . $items->id ?>"
                                            class="btn btn-primary mt-3">Generate QR Code</a>
                                    </center>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="row fade tab-pane active show" id="nav-new-maintenance" role="tabpanel">
    <div class="col-md-7">
        <div class="card shadow mb-4 tabradius">
            <div class="card-body">
                <div class="bg-white card-header py-3">
                    <h6 class="m-0 font-weight-bold text_warning_color">Maintenance
                        <?php if ($this->user_model->has_perm("add_maintenance_log_item")) { ?>
                            <a class="float-right mr-2" href="#addNewMaintenancce" data-toggle="modal"
                                data-target="#addNewMaintenancce" title="Add new maintenance"><i class="fa fa-plus"></i>
                                Add New Maintenance
                            </a>
                        <?php } ?>
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless table-striped" id="item_maintenance" width="100%" cellspacing="0">
                        <thead>

                        </thead>
                        <tbody>
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
                                        <span class="badge" style="color: #634300; background-color: #ffe29b;">
                                            <?= htmlspecialchars($row->number) ?>
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
            <form class="form-horizontal" action="<?= site_url("items/addMaintenace"); ?>" method="post">


                <div class="modal-body">
                    <div id="dynamic-form">
                        <div class="row form-entry">
                            <div class="form-group col-sm-6 date_picker_now">
                                <label for="form_update_date">Update Date <sup>REQUIRED</sup></label>
                                <input type="text" name="update_date" class="form-control" step="0.00001" id="form_update_date" min="0" placeholder="Update Date" value="" required="" autocomplete="off" maxlength="10">
                            </div>

                            <div class="form-group col-sm-6">
                                <label>Ticket <sup>REQUIRED</sup></label>
                                <select class="form-control ticket" name="ticket" required>
                                    <option value="">--Select--</option>
                                    <?php foreach ($ticket as $tk) { ?>
                                        <option value="<?= $tk->id; ?>">
                                            <?= $tk->number; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group col-sm-6" style="display: none;">
                                <label>Fault Type</label>
                                <select class="form-control fault-type" name="fault_type">
                                    <option value="">--Select--</option>
                                </select>
                            </div>


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
                                <label>Remarks</label>
                                <textarea name="remarks[]" class="form-control"></textarea>
                            </div>

                            <div class="form-group col-sm-6">
                                <label>Final Status</label>
                                <select name="final_status" class="form-control">
                                    <option value="">--Select--</option>
                                    <option value="COMPLETE">COMPLETE</option>
                                    <option value="INPROGRESS">INPROGRESS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-more" class="btn btn-primary">Add More</button>
                    <button type="button" class="btn btn-danger remove-entry">Remove</button>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="equipment_id" value="<?= $items->id; ?>" />
                    <button type="submit" class="btn btn-primary">Add</button>
                    <button type="button" class="btn border_success text_successb" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Add new form entry
        $('#add-more').click(function() {
            const newEntry = `
        <div class="row form-entry new-entry">
            <!-- Form fields go here -->
            <div class="form-group col-sm-6">
                <label>Task Done</label>
                <select class="form-control" name="task_done[]">
                    <option value="">--Select--</option>
                    <?php foreach ($task as $tasks) { ?>
                        <option value="<?= $tasks->name; ?>">
                            <?= $tasks->name; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group col-sm-6">
                <label>Remarks</label>
                <textarea name="remarks[]" class="form-control"></textarea>
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

        // Populate Fault Type based on selected Ticket
        $(document).on('change', '.ticket', function() {
            const formEntry = $(this).closest('.form-entry');
            const selectedTicket = $(this).val();

            if (selectedTicket) {
                $.ajax({
                    url: '<?= site_url("items/getFaultyType"); ?>',
                    type: 'POST',
                    data: {
                        ticket: selectedTicket
                    },
                    dataType: 'json',
                    success: function(response) {
                        const faultTypeDropdown = formEntry.find('.fault-type');

                        // Handle errors in the response
                        if (response.error) {
                            console.error('Error:', response.error);
                            alert(response.error);
                            faultTypeDropdown.val('').parent().hide();
                            return;
                        }

                        // Populate dropdown if response is valid
                        faultTypeDropdown.empty(); // Clear existing options
                        if (response.length > 0) {
                            response.forEach(function(result) {
                                faultTypeDropdown.append(
                                    `<option value="${result.value}">${result.label}</option>`
                                );
                            });
                            faultTypeDropdown.parent().show();
                        } else {
                            faultTypeDropdown.val('').parent().hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching Fault Type:', error);
                        alert('Error fetching Fault Type. Please try again.');
                    }
                });
            } else {
                // If no ticket is selected, hide the Fault Type field
                formEntry.find('.fault-type').val('').parent().hide();
            }
        });


    });
</script>
<script>
    $(document).ready(function() {
        let dropArea = $('#drop-area');
        let fileInput = $('#fileElem');
        let galleryy = $('#gallery');

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.on(eventName, preventDefaults, false);
            $(document).on(eventName, preventDefaults, false);
        });

        // Highlight the drop area when file is dragged over
        dropArea.on('dragover', () => dropArea.addClass('highlight'));
        dropArea.on('dragleave', () => dropArea.removeClass('highlight'));

        // Handle dropped files
        dropArea.on('drop', handleDrop);
        fileInput.on('change', handleFiles); // Ensure file input changes are detected

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Handle both drag-and-drop files and input-selected files
        function handleDrop(e) {
            const dt = e.originalEvent.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        function handleFiles(files) {
            const fileArray = Array.from(files);
            const currentFiles = Array.from(fileInput[0].files);

            const allFiles = [...currentFiles, ...fileArray]; // Merge existing files with newly selected ones
            const dataTransfer = new DataTransfer();

            galleryy.empty(); // Clear the gallery before displaying new images

            allFiles.forEach((file, index) => {
                dataTransfer.items.add(file); // Add all files to the data transfer object

                // Preview the images
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'gallery-image';
                img.style.width = '100px';
                img.style.margin = '5px';
                img.dataset.index = index; // Set an index for tracking images
                galleryy.append(img);

                // Image delete functionality on click
                img.addEventListener('click', function() {
                    deleteFile(index);
                    this.remove(); // Remove the image preview
                });
            });

            // Update the file input's file list
            fileInput[0].files = dataTransfer.files;
        }

        // Delete selected files by index
        function deleteFile(fileIndex) {
            const currentFiles = Array.from(fileInput[0].files);
            const dataTransfer = new DataTransfer();

            currentFiles.forEach((file, index) => {
                if (index !== fileIndex) {
                    dataTransfer.items.add(file); // Only keep files that were not deleted
                }
            });

            // Update file input with the new list of files
            fileInput[0].files = dataTransfer.files;
        }

        // Save button functionality for uploading images
        $('#saveBtn').click(function(e) {
            e.preventDefault();

            var formData = new FormData();
            var files = fileInput[0].files;

            for (var i = 0; i < files.length; i++) {
                formData.append('item_picture[]', files[i]);
            }

            formData.append('id', '<?= $items->id ?>');
            formData.append('unique_id', '<?= $items->id ?>');

            // AJAX request to upload images
            $.ajax({
                url: "<?= base_url('items/itemsImagesAdd') ?>",
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



    });
</script>

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
                    fetch('<?= base_url("items/delete_picture"); ?>', {
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


<script>
    $(document).ready(function() {
        $('.drop-area').each(function() {
            const dropArea = $(this);
            const fileInput = dropArea.find('.file-input');
            const gallery = dropArea.siblings('.gallery');
            const saveBtn = dropArea.find('.save-btn');

            // Prevent default drag behaviors and highlight drop area
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.on(eventName, preventDefaults);
                dropArea.on('dragover', () => dropArea.addClass('highlight'));
                dropArea.on('dragleave drop', () => dropArea.removeClass('highlight'));
            });

            // Prevent default event handling for drag events
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Handle file drop
            dropArea.on('drop', (e) => {
                const files = e.originalEvent.dataTransfer.files;
                handleFiles(files);
            });

            // Open file dialog on "Choose Files" button click
            // dropArea.find('.file-choose-btn').on('click', () => fileInput.click());

            // Handle file input selection
            fileInput.on('change', () => handleFiles(fileInput[0].files));

            // Handle files and display previews in gallery
            function handleFiles(files) {
                const dataTransfer = new DataTransfer();
                gallery.empty(); // Clear gallery before adding new images

                Array.from(files).forEach((file, index) => {
                    dataTransfer.items.add(file); // Add files to dataTransfer

                    // Display image preview
                    const img = $('<img>')
                        .attr('src', URL.createObjectURL(file))
                        .addClass('gallery-image')
                        .css({
                            width: '100px',
                            margin: '5px'
                        })
                        .data('index', index)
                        .appendTo(gallery);

                    // Click to remove image
                    img.on('click', function() {
                        deleteFile(index, img);
                    });
                });

                // Update file input with the new files
                fileInput[0].files = dataTransfer.files;
            }

            // Delete a specific file by index
            function deleteFile(index, imgElement) {
                const currentFiles = Array.from(fileInput[0].files);
                const dataTransfer = new DataTransfer();

                currentFiles.forEach((file, i) => {
                    if (i !== index) dataTransfer.items.add(
                        file); // Add files not matching the index
                });

                // Update file input and remove the image preview
                fileInput[0].files = dataTransfer.files;
                imgElement.remove();
            }

            // Save button handler
            saveBtn.on('click', function(e) {
                e.preventDefault();

                // Check if files are selected
                if (fileInput[0].files.length === 0) {
                    alert('Please select at least one image before saving.');
                    return;
                }

                const formData = new FormData();
                Array.from(fileInput[0].files).forEach((file) => {
                    formData.append('item_picture[]', file);
                });

                formData.append('id', '<?= $info->equipment_id ?>');
                formData.append('unique_id', '<?= $items_id ?>');

                // AJAX request to upload images
                $.ajax({
                    url: "<?= base_url('assets/itemsImagesAdd') ?>",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            alert('Images saved successfully.');
                            window.location.reload();
                        } else {
                            alert('Failed to save images. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred while uploading the images.');
                        console.error('Error response:', xhr.responseText);
                    }
                });
            });
        });


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