<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4 tabradius">

            <div class="card-body">
                <div class="bg-white card-header tabradius">
                    <h6 class="m-0 font-weight-bold text_warning_color">Edit Ticker</h6>
                </div>
                <form class="form-horizontal" action="<?= site_url("items_ticket/update"); ?>" method="post">
                    <div class="row">

                        <div class="form-group col-sm-4">
                            <label for="number">Ticket Number</label>
                            <input type="text" class="form-control" id="number" name="number" value="<?= $info->number ?>" readonly>
                        </div>
                        <?= $this->steve->form_group_label_input("date", "issue_date", "Issue Date", "col-sm-4 uppercase", 0, $info->issue_date, 30); ?>

                        <div class="col-md-4 mb-4">
                            <label for="asset_number">Asset Name</label>
                            <select name="asset_number" class="form-control" id="asset_number">
                                <option value="">--Select--</option>
                                <?php foreach ($asset as $name): ?>
                                    <option value="<?= $name->equipment_id ?>"
                                        <?= ($name->equipment_id == $info->equipment_id) ? 'selected' : '' ?>>
                                        <?= $name->equipment_name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>


                        <div class="col-md-4 mb-4">
                            <label for="item_type">Item Type</label>
                            <select name="item_type" class="form-control" id="item_type">
                                <?php foreach ($item as $single_item): ?>
                                    <?php if ($single_item->id == $info->item_id): ?>
                                        <option value="<?= $single_item->id ?>" selected>
                                            <?= $single_item->item_name ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>



                        <div class="col-md-4 mb-4">
                            <label for="location">Location</label>
                            <input type="text" name="location" class="form-control" id="location" value="<?= $info->location ?>" readonly>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label for="state">State</label>
                            <input type="text" name="state" class="form-control" id="state" value="<?= $info->state ?>" readonly>
                        </div>

                        <div class="form-group col-sm-4 uppercase" id="severity">
                            <label>Severity</label><br />
                            <select class="form-control" id="severity" class="p-0" name="severity">
                                <option value="<?= $info->severity ?>"><?= $info->severity ?></option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>

                            </select>
                        </div>
                        <?= $this->steve->form_group_label_input("date", "date_of_completion", "Completion Date", "col-sm-4 uppercase", 0, $info->date_of_completion, 30); ?>
                        <div class="form-group col-sm-4">
                            <label for="form_details_of_issue">Detail of issue</label>
                            <textarea
                                name="details_of_issue"
                                class="form-control form-control-sm"
                                id="form_details_of_issue"
                                rows="5"
                                placeholder="Detail of issue"
                                required=""><?= isset($info->details_of_issue) ? htmlspecialchars($info->details_of_issue, ENT_QUOTES, 'UTF-8') : '' ?></textarea>
                        </div>

                    </div>
                    <div class="text-center">
                        <input type="hidden" name="id" value="<?= $info->id; ?>" />
                        <button type="submit" class="btn bg_success text-white font-weight-bold">Save changes</button>
                        <a class="btn border_success text_successb" data-dismiss="modal" href=".">Go back</a>
                    </div>
                </form>
            </div>
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