<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<div class="card shadow mb-4">

    <!-- ajax messages -->
    <div class="flash-message-container"></div>

    <!-- messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger">
            <?php echo $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary bg-light">List of Component Types
            <?php if ($this->user_model->has_perm("add_equipment_types")) { ?>
                <a class="float-right" href="#addModal" data-toggle="modal" data-target="#addModal" title="Add new State"><i
                        class="fa fa-plus"></i> New Type</a>
            <?php } ?>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table
                class="table table-bordered table-striped <?= ($this->user_model->has_perm("edit_equipment_types") ? "" : "read-only"); ?>"
                id="item_tabel" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Manufacturer</th>
                        <th>Vendor Part Number</th>
                        <th>Calibration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($this->user_model->has_perm("add_equipment_types")) { ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="addModal">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Item Type
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" action="<?= site_url("item_type/add"); ?>" method="post">
                    <div class="container">
                        <div class="modal-body row">

                            <div class="col-md-12 ">
                                <label for="">Component Name <sup style="color:red;">Required</sup> </label>
                                <input class="form-control" type="text" name="name" placeholder="Component Type Name" required>
                            </div>

                            <!-- Manufacturer Name with Searchable Dropdown -->
                            <div class="col-md-12 mt-3 uppercase">
                                <label for="manufacturer_dropdown">Manufacturer</label><br />
                                <select class="form-control" name="manufacturer" id="manufacturer_name"
                                    class="form-control searchable-dropdown">
                                    <?php foreach ($manufacturer_name as $mn): ?>
                                        <option value="<?= $mn->id ?>">
                                            <?= $mn->manufacturer_name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-12 mt-3">
                                <label for="vendor_part_number">Vendor Part Number</label>
                                <select name="vendor_part_number" class="form-control">
                                    <?php foreach ($part_numbers as $pn): ?>
                                        <option value="<?= $pn->id ?>"><?= ($pn->part_number) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for=""> Check For Calibration </label>
                                <input type="checkbox" name="calibration" id="calibration-check" value="0">

                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="maintenance"> Check For Maintenance </label>
                                <input type="checkbox" name="maintenance" id="maintenance-check" value="0">

                            </div>


                        </div>


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


<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Component Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="row">
                        <!-- <div class="form-group">
                            <label for="countryName">Country Name</label>
                            <input type="text" class="form-control" id="countryName" name="country_name" required>
                        </div> -->


                        <div class="col-md-12">
                            <label for="">Name <sup style="color:red;">Required</sup> </label>
                            <input class="form-control" type="text" id="stateName" name="name"
                                placeholder="Component Type Name" required>
                        </div>
                            
                        <div class="col-md-12 mt-3">
                            <label for="manufacturer">Manufacturer</label>
                            <select name="manufacturer" class="form-control" id="manufacturer">
                                <?php foreach ($manufacturer_name as $mn): ?>
                                    <option value="<?= $mn->id ?>"><?= ($mn->manufacturer_name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label for="vendor_part_number">Vendor Part Number</label>
                            <select name="vendor_part_number" class="form-control" id="vendor_part_number">
                                <?php foreach ($part_numbers as $pn): ?>
                                    <option value="<?= $pn->id ?>"><?= ($pn->part_number) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label for="calibration"> Check For Calibration </label>
                            <input type="checkbox" name="calibration" id="calibration-check-edit" value="1">

                        </div>

                        <!-- <div class="col-md-4 mt-3">
                            <label for="maintenance"> Check For Miantenance </label>
                            <input type="checkbox" name="maintenance" id="maintenance-check-edit" value="1">

                        </div> -->



                        <input type="hidden" id="editId" name="id">
                    </div>
                    <div class="modal-footer" style="float:end;">
                        <button type="submit" class="btn btn-success">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                    <!-- <button type="submit" class="btn btn-primary mt-4 float-right">Save changes</button> -->

                </form>
            </div>
        </div>
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