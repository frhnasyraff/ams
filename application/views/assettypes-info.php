<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<style>
.select2-container--default .select2-selection--single {
    height: 37px !important;
    min-height: 37px !important;
    border: 1px solid #6E707E !important;
    border-radius: 4px;
    box-shadow: none !important;
}

/* Focus / open */
.select2-container--default.select2-container--focus 
.select2-selection--single,
.select2-container--default.select2-container--open 
.select2-selection--single {
    border-color: #6E707E !important;
    box-shadow: none !important;
}

/* Text align */
.select2-container--default .select2-selection__rendered {
    line-height: 37px !important;
}

/* Arrow align */
.select2-container--default .select2-selection__arrow {
    height: 37px !important;
}
</style>

<div class="card shadow mb-4 tabradius">

    <div class="card-body">

        <div class="bg-white card-header py-3">
            <h6 class="bg-white m-0 font-weight-bold text-primary">Edit asset type</h6>
        </div>

        <form class="form-horizontal" action="<?= site_url('assettypes/update'); ?>" method="post">
            <div class="row">
                <?= $this->steve->form_group_label_input("text", "name", "Asset Name", "col-md-12", 0, $info->name); ?>
                <!-- Manufacturer Name with Searchable Dropdown -->
                <div class="col-md-6 mb-4 uppercase">
                    <label for="manufacturer_dropdown">Manufacturer</label><br />
                    <select name="manufacturer" id="manufacturer_name" class="searchable-dropdown" >
                        <?php foreach ($manufacturer_name as $mn): ?>
                            <option value="<?= htmlspecialchars($mn->manufacturer_name, ENT_QUOTES, 'UTF-8') ?>"
                                <?= ($mn->manufacturer_name == $info->manufacturer) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mn->manufacturer_name, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>



                <!-- Vendor Part Number Selection -->
                <div class="col-md-6 mb-4">
                    <label for="vendor_part_number">Vendor Part Number</label>
                    <select name="vendor_part_number" class="form-control" id="vendor_part_number">
                        <?php foreach ($part_numbers as $pn): ?>
                            <option value="<?= $pn->id ?>" <?= ($pn->id == $info->vendor_part_number) ? 'selected' : '' ?>>
                                <?= $pn->part_number ?>
                            </option>
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

                    <div class="col-md-12 mb-4">
                        <label>Useful Life (Years)</label>
                        <input type="number" class="form-control"
                            name="useful_life_years"
                            placeholder="Useful Life in Years" min="0">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label>Salvage Value</label>
                        <input type="number" step="0.01"
                            class="form-control"
                            name="salvage_value"
                            placeholder="Salvage Value" min="0">
                    </div>

                </div>


                <div id="reducing_balance_field" style="display:none">
                    <label>Depreciate Value (%)</label>
                    <input type="number" name="depreciate_value" class="form-control"
                        value="<?= $info->depreciate_value ?>">
                </div>


                <div class="col-md-12 mb-4">
                    <label for="task_lists">Task Lists</label>
                    <select name="task_lists[]" id="task_lists" class="form-control select2-multiple" multiple="multiple">
                        <?php foreach ($task_lists as $task): ?>
                            <option value="<?= $task->id ?>" 
                                <?= in_array($task->id, $selected_task_ids) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($task->name, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted">Select multiple tasks for this asset type</small>
                </div>


                <!-- Calibration Checkbox -->
                <div class="col-md-3 mb-4">
                    <label for="calibration-check-edit">Check For Calibration 1</label>
                    <input type="checkbox" name="calibration" id="calibration-check-edit" value="1"
                        <?= ($info->calibration == 1) ? 'checked' : ''; ?>>
                </div>

                <!-- maintenance Checkbox -->
                <div class="col-md-3 mb-4">
                    <label for="maintenance-check-edit">Check For Maintenance</label>
                    <input type="checkbox" name="maintenance" id="maintenance-check-edit" value="1"
                        <?= ($info->maintenance == 1) ? 'checked' : ''; ?>>
                </div>

                <!-- Dynamic Item Type Rows -->
                <div id="dynamic-item-container" class="col-md-12">
                    <?php if (!empty($asset_type_items)): ?>
                        <?php foreach ($asset_type_items as $item): ?>
                            <div class="dynamic-item-row mb-2 d-flex align-items-center">
                                <div class="col-md-5 pe-1">
                                    <select class="form-control mb-2 dynamic-item-type" name="item_type[]">
                                        <?php foreach ($item_types as $pn): ?>
                                            <option value="<?= $pn->id ?>"
                                                <?= ($pn->id == $item->item_type_id) ? 'selected' : ''; ?>>
                                                <?= $pn->name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5 pe-1">
                                    <input type="number" class="form-control mb-2 dynamic-quantity" name="quantity[]"
                                        placeholder="Enter Quantity" value="<?= $item->quantity ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No items added yet.</p>
                    <?php endif; ?>
                </div>

                <!-- Add Item Button -->
                <div class="col-md-12 text-start mt-3 mb-5">
                    <button type="button" id="add-item-btn" class="btn btn-success">Add Item</button>
                </div>
            </div>

            <!-- Submit and Back Buttons with Center Alignment -->
            <div class="text-center mt-4">
                <input type="hidden" name="id" value="<?= $info->asset_id; ?>" />
                <button type="submit" class="btn btn-primary me-2">Save changes</button>
                <a class="btn btn-secondary" href=".">Go back</a>
            </div>
        </form>

    </div>
</div>

<!-- jQuery Script to Enable Dropdown and Filtering -->
<script>
    $(document).ready(function() {

        // Initialize Select2 on Asset dropdown
        $('#asset_id').select2({
            placeholder: "Select Asset", // Optional
            allowClear: true // Allow clearing the selection
        });

        $('#task_lists').select2({
            placeholder: "Select tasks",
            allowClear: true,
            width: '100%'
        });

        // Initialize Select2 on Manufacturer Name dropdown
        $('#manufacturer_name').select2({
            placeholder: "Select Manufacturer", // Optional
            allowClear: true, // Allow clearing the selection
            
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
            let selectedText = $("#depreciation_method option:selected").text().toLowerCase();

            if (selectedText.includes('reducing') || selectedText.includes('research')) {
                $('#straight_line_fields').hide();
                $('#reducing_balance_field').show();
            } else {
                $('#straight_line_fields').show();
                $('#reducing_balance_field').hide();
            }
        });

        // Trigger on page load (edit case)
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