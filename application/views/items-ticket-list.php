<style type="text/css">
    .pagination>li>a {
        border-radius: 10px;
        /*background-color: #fff !important;*/
        /*color: #fff !important;*/
    }

    .pagination>.active>a {
        background-color: #08073dff !important;
    }

    #equipment_groups_next>a {
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    #equipment_groups_previous>a {
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
</style>


<a class="float-right text_successo btn btn-default btn_border" href="#addModal" data-toggle="modal" data-target="#addModal"
    title="Add new Ticket"><i class="fa fa-plus"></i> New Ticket</a>


<div class="card shadow mb-4 tabradius">
    <div class="card-body">
        <div class="table-responsive">
            <table
                class="table" id="list" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Ticket Number</th>
                        <th>Asset Name</th>
                        <th>Item Name</th>
                        <th>Issue Date</th>
                        <th>Fault Type</th>
                        <th>Location</th>
                        <th>State</th>
                        <th>Detail of issue</th>
                        <th>Severity</th>
                        <th>Compliiton Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Ticket
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("items_ticket/add"); ?>" method="post">
                <div class="modal-body row">

                    <!-- <?= $this->steve->form_group_label_input("text", "number", "Ticket Number", "col-sm-6", 1, '', 125); ?> -->
                    <?= $this->steve->form_group_label_input("date", "issue_date", "Issue Date", "col-sm-4 uppercase", 0, '', 30); ?>
                    <div class="col-md-4 mb-4">
                        <label for="asset_number">Asset Name</label>
                        <select name="asset_number" class="form-control" id="asset_number">
                            <option value="">--Select--</option>
                            <?php foreach ($asset as $name): ?>
                                <option value="<?= $name->equipment_id ?>"><?= $name->equipment_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="item_type">Item Type</label>
                        <select name="item_type" class="form-control" id="item_type">
                            <option value="">--Select--</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="location">Location</label>
                        <input type="text" name="location" class="form-control" id="location" readonly>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="state">State</label>
                        <input type="text" name="state" class="form-control" id="state" readonly>
                    </div>


                    <div class="form-group col-sm-4 uppercase" id="fault_type">
                        <label>Faulty Type</label><br />
                        <select class="form-control" id="fault_type" name="fault_type">
                            <option value="">--Select--</option>
                            <?php foreach ($faulty as $f) { ?>
                                <option value="<?= $f->id; ?>"
                                    <?= ($f->id == $info->faulty_type_id) ? 'selected' : ''; ?>>
                                    <?= $f->fault_type; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>



                    <div class="form-group col-sm-4 uppercase" id="severity">
                        <label>Severity</label><br />
                        <select class="form-control" id="severity" class="p-0" name="severity">
                            <option value="">--Select--</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>

                        </select>
                    </div>

                    <?= $this->steve->form_group_label_input("date", "date_of_completion", "Completion Date", "col-sm-4 uppercase", 0, '', 30); ?>


                    <?= $this->steve->form_group_label_textarea("details_of_issue", "Detail of issue", "col-sm-4"); ?>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Issue Ticket</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('asset_number').addEventListener('change', function() {
        const assetId = this.value;

        // Clear existing fields and dropdowns
        document.getElementById('location').value = '';
        document.getElementById('state').value = '';
        const itemTypeField = document.getElementById('item_type');
        itemTypeField.innerHTML = '<option value="">Select an Item Type</option>';

        if (!assetId) return; // Exit if no asset is selected

        // AJAX request to fetch asset details
        fetch('<?= site_url("items_ticket/get_asset_details") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `equipment_id=${encodeURIComponent(assetId)}`
            })
            .then(response => response.json())
            .then(data => {
                // Populate Location and State fields
                document.getElementById('location').value = data.name || 'N/A';
                document.getElementById('state').value = data.state_name || 'N/A';

                // Populate Item Type dropdown
                if (data.item_names && data.item_names.length > 0) {
                    data.item_names.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id; // Use item.id for the value
                        option.textContent = item.item_name; // Use item.item_name for the text
                        itemTypeField.appendChild(option);
                    });
                } else {
                    const noItemsOption = document.createElement('option');
                    noItemsOption.value = '';
                    noItemsOption.textContent = 'No items found';
                    itemTypeField.appendChild(noItemsOption);
                }

            })
            .catch(error => console.error('Error fetching asset details:', error));
    });
</script>