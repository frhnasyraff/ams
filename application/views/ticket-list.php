<style type="text/css">
    .pagination>li>a {
        border-radius: 10px;
        /*background-color: #fff !important;*/
        /*color: #fff !important;*/
    }

    .pagination>.active>a {
        background-color: #073D11 !important;
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

<?php if ($this->user_model->has_perm("issue_ticket_add")) { ?>
    <a class="float-right text_successo btn btn-default btn_border" href="#addModal" data-toggle="modal" data-target="#addModal"
        title="Add new Ticket"><i class="fa fa-plus"></i> New Ticket</a>
<?php } ?>

<div class="card shadow mb-4 tabradius">
    <div class="card-body">
        <div class="table-responsive">
            <table
                class="table" id="ticket_list" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Ticket Number</th>
                        <th>Issue Date</th>
                        <th>Asset Name</th>
                        <th>Components</th>
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

<!-- eye icone button modal  -->
<div class="modal fade" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="equipmentModalLabel">Component Ticket Details</h5>
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
            <form class="form-horizontal" action="<?= site_url("ticket/add"); ?>" method="post">
                <div class="modal-body row">
                    <?= $this->steve->form_group_label_input("date", "issue_date", "Issue Date", "col-sm-4 uppercase", 0, '', 30); ?>

                    <div class="col-md-4 mb-4">
                        <label for="asset_number">Asset Name</label>
                        <select name="asset_number" class="form-control" id="asset_number">
                            <?php foreach ($asset as $name): ?>
                                <option value="<?= htmlspecialchars($name->equipment_id, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($name->equipment_name, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="form-group col-sm-4 uppercase">
                        <label>Faulty Type</label><br />
                        <select class="form-control" id="ticket_fault_type" name="ticket_fault_type">
                            <option value="">--Select--</option>
                            <?php foreach ($faulty as $f) { ?>
                                <option value="<?= $f->fault_type; ?>" <?= ($f->id == $info->faulty_type_id) ? 'selected' : ''; ?>>
                                    <?= $f->fault_type; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="ticket_location">Location</label>
                        <input type="text" class="form-control" id="ticket_location" name="ticket_location" readonly>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="ticket_state">State</label>
                        <input type="text" class="form-control" id="ticket_state" name="ticket_state" readonly>
                    </div>

                    <div class="form-group col-sm-4 uppercase">
                        <label>Severity</label><br />
                        <select class="form-control" id="severity" name="severity">
                            <option value="">--Select--</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>

                    <!-- <?= $this->steve->form_group_label_input("date", "date_of_completion", "Completion Date", "col-sm-6 uppercase", 0, '', 30); ?> -->
                    <?= $this->steve->form_group_label_textarea("details_of_issue", "Detail of issue", "col-sm-6", "uppercase"); ?>

                    <!-- Section for Items Faulty -->
                    <div class="col-12">
                        <h5>Faulty Components</h5>
                        <button type="button" id="addItemBtn" class="btn btn-primary btn-sm">Add Component</button>
                    </div>

                    <div class="col-12" id="faultyItemsContainer" style="margin-top: 5px;"></div>
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
    document.addEventListener("DOMContentLoaded", function() {
        const assetNumber = document.getElementById("asset_number");
        const ticketLocation = document.getElementById("ticket_location");
        const ticketState = document.getElementById("ticket_state");
        const addItemBtn = document.getElementById("addItemBtn");
        const container = document.getElementById("faultyItemsContainer");
        let itemCount = 0;

        // Fetch asset details when asset is selected
        assetNumber.addEventListener("change", function() {
            const assetName = this.value;

            fetch('<?= site_url("ticket/get_asset_details") ?>', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `equipment_name=${encodeURIComponent(assetName)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (!ticketLocation || !ticketState) {
                        console.error("Location or State field missing.");
                        return;
                    }

                    // Populate ticket location and state
                    ticketLocation.value = data.name || "";
                    ticketState.value = data.state_name || "";

                    // Populate first item dropdown (for dynamic rows)
                    updateItemDropdown(data.item_names);
                })
                .catch(error => console.error("Error fetching asset details:", error));
        });

        function updateItemDropdown(itemNames) {
            document.querySelectorAll(".item_name").forEach(dropdown => {
                dropdown.innerHTML = '<option value="">--Select--</option>';

                if (itemNames && itemNames.length > 0) {
                    itemNames.forEach(item => {
                        const option = document.createElement("option");
                        option.value = item.id;
                        option.textContent = item.item_name;
                        dropdown.appendChild(option);
                    });
                } else {
                    dropdown.innerHTML = '<option value="">No component found</option>';
                }
            });
        }

        // Add Item button functionality
        addItemBtn.addEventListener("click", function() {
            itemCount++;

            const itemDiv = document.createElement("div");
            itemDiv.classList.add("row", "mb-2", "align-items-center");
            itemDiv.setAttribute("id", `itemRow${itemCount}`);

            itemDiv.innerHTML = `
                <div class="col-md-4">
                    <label for="item_name_${itemCount}">component Name</label>
                    <select name="item_name[]" class="form-control item_name">
                        <option value="">--Select--</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="item_fault_type_${itemCount}">Faulty Type</label>
                    <select name="item_fault_type[]" class="form-control">
                        <option value="">--Select--</option>
                        <?php foreach ($faulty as $f) { ?>
                            <option value="<?= $f->fault_type; ?>"><?= $f->fault_type; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-md removeItemBtn">X</button>
                </div>
            `;

            container.appendChild(itemDiv);

            // Remove Item button functionality
            itemDiv.querySelector(".removeItemBtn").addEventListener("click", function() {
                itemDiv.remove();
            });

            // Populate new item dropdown
            const selectedAsset = assetNumber.value;
            if (selectedAsset) {
                fetch('<?= site_url("ticket/get_asset_details") ?>', {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `equipment_name=${encodeURIComponent(selectedAsset)}`
                    })
                    .then(response => response.json())
                    .then(data => updateItemDropdown(data.item_names))
                    .catch(error => console.error("Error updating item dropdown:", error));
            }
        });
    });
</script>
