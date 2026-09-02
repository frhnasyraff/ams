<style type="text/css">
    .pagination>li>a {
        border-radius: 10px;
    }

    .pagination>.active>a {
        background-color: #09073dff !important;
    }

    #wastage_types_next>a {
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    #wastage_types_previous>a {
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

<div class="card shadow mb-4 tabradius">

    <div class="card-body">

        <div class="bg-white card-header py-3">
            <h6 class="bg-white m-0 font-weight-bold text-primary">List of asset type
                <?php if ($this->user_model->has_perm("add_assettypes")) { ?>
                    <a class="float-right" href="#addModal" data-toggle="modal" data-target="#addModal"
                        title="Add new asset type"><i class="fa fa-plus"></i> New asset type</a>
                <?php } ?>
            </h6>
        </div>

        <div class="table-responsive">
            <table
                class="table <?= ($this->user_model->has_perm("edit_assettypes") ? "" : "read-only"); ?>"
                id="assettypes" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="30%">Name</th>
                        <th width="30%">Manufacturer</th>
                        <th width="30%">Vendor Part Number</th>
                        <th width="30%">Calibration</th>
                        <th width="30%">Maintenance</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($this->user_model->has_perm("add_assettypes")) { ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="addModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New asset type
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" action="<?= site_url("assettypes/add"); ?>" method="post">
                    <div class="modal-body row">


                        <?= $this->steve->form_group_label_input("text", "name", "Asset Type Name", "col-sm-12", 0, '', 30); ?>

                        <!-- Manufacturer Name with Searchable Dropdown -->
                        <div class="col-md-12 mb-4 uppercase">
                            <label for="manufacturer_dropdown">Manufacturer Name</label><br />
                            <select name="manufacturer" id="manufacturer_name"
                                class="form-control searchable-dropdown">
                                <option value="">--Select--</option>
                                <?php foreach ($manufacturer_name as $mn): ?>
                                    <option value="<?= $mn->manufacturer_name ?>"
                                        <?= ($mn->id == $mn->manufacturer_name) ? 'selected' : ''; ?>>
                                        <?= $mn->manufacturer_name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12 mb-4">
                            <label for="vendor_part_number">Vendor Part Number</label>
                            <select name="vendor_part_number" class="form-control" id="vendor_part_number">
                                <?php foreach ($part_numbers as $pn): ?>
                                    <option value="<?= $pn->id ?>"><?= ($pn->part_number) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>


                        <div class="col-md-12 mb-4">
                            <label>Depreciation Method</label>
                            <select class="form-control" name="depreciation_method_id" id="depreciation_method">
                                <option value="">-- Select --</option>
                                <?php foreach ($depreciation_methods as $dm): ?>
                                    <option value="<?= $dm->id ?>"
                                        <?= ($dm->id == $info->depreciation_method_id) ? 'selected' : '' ?>>
                                        <?= $dm->depreciation_method ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="straight_line_fields">

                            <div class="col-md-6 mb-4">
                                <label>Useful Life (Years)</label>
                                <input type="number" class="form-control"
                                    name="useful_life_years"
                                    value="<?= $info->useful_life_years ?>">
                            </div>

                            <div class="col-md-6 mb-4">
                                <label>Salvage Value</label>
                                <input type="number" step="0.01"
                                    class="form-control"
                                    name="salvage_value"
                                    value="<?= $info->salvage_value ?>">
                            </div>

                        </div>


                        <div id="reducing_balance_field" style="display:none">
                            <label>Depreciate Value (%)</label>
                            <input type="number" name="depreciate_value" class="form-control"
                                value="<?= $info->depreciate_value ?>">
                        </div>


                        <div class="col-md-12 mb-4">
                            <label for="task_lists_add">Task Lists</label>
                            <select name="task_lists[]" id="task_lists_add" class="form-control select2-multiple" multiple="multiple">
                                <?php foreach ($task_lists as $task): ?>
                                    <option value="<?= $task->id ?>">
                                        <?= htmlspecialchars($task->name, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Select multiple tasks for this asset type</small>
                        </div>


                        <div class="col-md-12 mb-4">
                            <label for=""> Check For Calibration </label>
                            <input type="checkbox" name="calibration" id="calibration-check" value="0">
                        </div>
                        <div class="col-md-12 mb-4">                            
                            <label for=""> Check For Maintenance </label>
                            <input type="checkbox" name="maintenance" id="maintenance-check" value="0">
                        </div>

                        <div id="item-container"></div>

                        <div class="col-md-4 mb-4">

                            <button type="checkbox" id="itm_qty" class="form-control btn btn-primary" value="0"> Add Items Qty </button>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Add Asset Type</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>


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

        $('#task_lists_add').select2({
            placeholder: "Select tasks",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#addModal') // Important for modal
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

        $('#depreciation_method').on('change', function () {

            let selectedText = $("#depreciation_method option:selected")
                                .text()
                                .toLowerCase();

            if (selectedText.includes('reducing') || selectedText.includes('research')) {
                $('#straight_line_fields').hide();
                $('#reducing_balance_field').show();
            } else {
                $('#straight_line_fields').show();
                $('#reducing_balance_field').hide();
            }
        });

        // Page load (EDIT case)
        $('#depreciation_method').trigger('change');


        // Close dropdown when clicking outside of any searchable-dropdown
        $(document).on('click', function(e) {
            // Only close if the click is outside the dropdowns
            if (!$(e.target).closest('.searchable-dropdown').length) {
                $('.dropdown-search').hide();
            }
        });
    });
</script>