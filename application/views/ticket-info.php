<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4 tabradius">

            <div class="card-body">
                <div class="bg-white card-header tabradius">
                    <h6 class="m-0 font-weight-bold text_warning_color">Edit Ticket</h6>
                </div>
                <form class="form-horizontal" action="<?= site_url("ticket/update"); ?>" method="post">
                    <div class="row">

                        <div class="form-group col-sm-4">
                            <label for="ticket_number">Ticket Number</label>
                            <input type="text" class="form-control" id="ticket_number" name="ticket_number" value="<?= $info->ticket_number ?>" readonly>
                        </div>
                        <?= $this->steve->form_group_label_input("date", "issue_date", "Issue Date", "col-sm-4 uppercase", 0, $info->issue_date, 30); ?>
                        <div class="col-md-4 mb-4">
                            <label for="asset_number">Asset Name</label>
                            <select name="asset_number" class="form-control" id="asset_number">
                                <?php foreach ($asset as $row): ?>
                                    <option value="<?= $row->equipment_id ?>" <?= ($row->equipment_id == $info->equipment_id) ? 'selected' : '' ?>>
                                        <?= $row->equipment_name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="ticket_location">Location</label>
                            <input type="text" class="form-control" id="ticket_location" name="ticket_location" value="<?= $info->ticket_location ?>" readonly>
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="ticket_state">State</label>
                            <input type="text" class="form-control" id="ticket_state" name="ticket_state" value="<?= $info->ticket_state ?>" readonly>
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
                        <?= $this->steve->form_group_label_input("date", "date_of_completion", "Completion Date", "col-sm-4 uppercase", 0, $info->date_of_completion, 10); ?>
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
        const assetName = this.value;

        // AJAX request to fetch asset details
        fetch('<?= site_url("ticket/get_asset_details") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `equipment_name=${encodeURIComponent(assetName)}`
            })
            .then(response => response.json())
            .then(data => {
                // Check if fields exist
                const locationField = document.getElementById('ticket_location');
                const stateField = document.getElementById('ticket_state');

                if (!locationField || !stateField) {
                    console.error("Location or State field not found in the DOM.");
                    return;
                }

                // Update Location and State fields
                locationField.value = data.name || '';
                stateField.value = data.state_name || '';
            })
            .catch(error => console.error('Error fetching asset details:', error));
    });
</script>