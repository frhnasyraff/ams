<style>
    .item-table {
        font-size: 10px !important;
        width: auto !important;
        max-width: 100% !important;
        border-collapse: collapse !important;
        table-layout: auto !important;
    }

    .item-table th,
    .item-table td {
        border: 1px solid #ddd !important;
        padding: 2px 5px !important;
        text-align: left !important;
        white-space: nowrap !important;
    }

    .item-table th {
        background-color: #f2f2f2 !important;
        font-weight: bold !important;
    }

    .item-table td {
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        /* max-width: 1px !important; */
    }
</style>

<?php
$itemDetails = "<p>No items available</p>";
?>

<div class="maintenance-workspace-page">
        <?php if (!empty($alertMessage)): ?>
                <?php echo $alertMessage; ?>
        <?php endif; ?>

        <?php if (!empty($item_maintenanceAlertMessage)): ?>
            <?php echo $item_maintenanceAlertMessage; ?>
        <?php endif; ?>

<section class="maintenance-hero">
    <div class="maintenance-hero-icon"><i class="fas fa-tools"></i></div>
    <div class="maintenance-hero-copy">
        <span>Maintenance workspace</span>
        <h2>Schedule &amp; Service Calendar</h2>
        <p>Plan upcoming work, review monthly activity and open maintenance details directly from the calendar.</p>
    </div>
    <form action="<?= site_url('Assets_Item_maintenance') ?>" method="GET" id="filter-form" class="maintenance-filter-form">
        <label for="filter">Schedule Type</label>
        <select id="filter" name="filter" onchange="document.getElementById('filter-form').submit();" class="filter-orders">
            <option value="corrective" <?= htmlspecialchars($this->input->get('filter')) == 'corrective' ? 'selected' : '' ?>>Corrective Maintenance</option>
            <option value="preventive" <?= $this->input->get('filter') == 'preventive' ? 'selected' : '' ?>>Preventive Maintenance</option>
        </select>
    </form>
</section>

<section class="maintenance-status-strip" aria-label="Maintenance status legend">
    <div><span class="maintenance-status-dot status-planned"></span><strong>Planned / Pending</strong><small>Requires attention</small></div>
    <div><span class="maintenance-status-dot status-progress"></span><strong>In Progress</strong><small>Work underway</small></div>
    <div><span class="maintenance-status-dot status-completed"></span><strong>Completed</strong><small>Service closed</small></div>
</section>

<section class="project-tab maintenance-content">
    <div class="row">
        <div class="col-12">
            <div class="tab-content">
                <div class="tab-pane show active" id="nav-asset" role="tabpanel" aria-labelledby="asset_faulty">


                    <div class="maintenance-calendar-layout">
                        <div class="maintenance-calendar-panel">
                            <div class="maintenance-panel-heading">
                                <div><span>Planning board</span><h3>Maintenance Calendar</h3></div>
                                <p>Select a date or event to view its details.</p>
                            </div>
                            <div class="maintenance-calendar-body">
                                    <div id='fullcalendar'></div>
                            </div>
                        </div>
                        <aside class="maintenance-agenda-panel">
                            <div class="maintenance-panel-heading">
                                <div><span>Monthly queue</span><h3>Maintenance Agenda</h3></div>
                            </div>
                            <div class="maintenance-agenda-body" id="date-orders-container">
                                <div class="maintenance-empty-agenda">
                                    <i class="far fa-calendar-check"></i>
                                    <strong>No schedule selected</strong>
                                    <span>Monthly maintenance records will appear here.</span>
                                </div>
                            </div>
                        </aside>
                    </div>

                    <!-- Schedule Planned Order Modal -->
                    <div class="modal fade schedule-modal planned" id="plannedOrderScheduleModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body p-0">
                                    <div class="header">
                                        <div class="left">
                                            <div class="dots">
                                                <span class="dot"></span>
                                                <span class="dot"></span>
                                                <span class="dot"></span>
                                            </div>
                                        </div>
                                        <div class="right">
                                            <p>Status</p>
                                            <span class="status">{{STATUS}}</span>
                                        </div>
                                    </div>
                                    <div class="content">
                                        <div class='wrapper'>
                                            <div class='key'>Ticket Number</div>
                                            <div class='value ticket-number'>{{TICKET_NUMBER}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Date</div>
                                            <div class="value issue-date">{{ISSUE_DATE}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Due Date</div>
                                            <div class="value reminder-date">{{REMINDER_DATE}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Asset Name</div>
                                            <div class="value equipment-name">{{EQUIPMENT_NAME}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Equipment Type</div>
                                            <div class="value equipment-type-name">{{EQUIPMENT_TYPE_NAME}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Equipment Registration 1</div>
                                            <div class="value equipment-registration">{{EQUIPMENT_REGISTRATION}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Store Location</div>
                                            <div class="value store-location-name">{{STORE_LOCATION_NAME}}</div>
                                        </div>

                                        <div class="wrapper">
                                            <div class="key">Items 1</div>
                                            <div class="item-box">
                                                <?php echo $itemDetails; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Progress Order Modal -->
                    <div class="modal fade schedule-modal progresss" id="progressOrderScheduleModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body p-0">
                                    <div class="header">
                                        <div class="left">
                                            <div class="dots">
                                                <span class="dot"></span>
                                                <span class="dot"></span>
                                                <span class="dot"></span>
                                            </div>
                                        </div>
                                        <div class="right">
                                            <p>Status</p>
                                            <span class="status">{{STATUS}}</span>
                                        </div>
                                    </div>
                                    <div class="content">
                                        <div class='wrapper'>
                                            <div class='key'>Ticket Number</div>
                                            <div class='value ticket-number'>{{TICKET_NUMBER}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Date</div>
                                            <div class="value issue-date">{{ISSUE_DATE}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Due Date</div>
                                            <div class="value reminder-date">{{REMINDER_DATE}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Asset Name</div>
                                            <div class="value equipment-name">{{EQUIPMENT_NAME}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Equipment Type</div>
                                            <div class="value equipment-type-name">{{EQUIPMENT_TYPE_NAME}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Equipment Registration 2</div>
                                            <div class="value equipment-registration">{{EQUIPMENT_REGISTRATION}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Store Location</div>
                                            <div class="value store-location-name">{{STORE_LOCATION_NAME}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Items 2</div>
                                            <div class="item-box">
                                                <?php echo $itemDetails; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Completed Order Modal -->
                    <div class="modal fade schedule-modal completed" id="completedOrderScheduleModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body p-0">
                                    <div class="header">
                                        <div class="left">
                                            <div class="dots">
                                                <span class="dot"></span>
                                                <span class="dot"></span>
                                                <span class="dot"></span>
                                            </div>
                                        </div>
                                        <div class="right">
                                            <p>Status</p>
                                            <span class="status">{{STATUS}}</span>
                                        </div>
                                    </div>
                                    <div class="content">
                                        <div class='wrapper'>
                                            <div class='key'>Ticket Number</div>
                                            <div class='value ticket-number'>{{TICKET_NUMBER}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Date</div>
                                            <div class="value issue-date">{{ISSUE_DATE}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Due Date</div>
                                            <div class="value reminder-date">{{REMINDER_DATE}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Asset Name</div>
                                            <div class="value equipment-name">{{EQUIPMENT_NAME}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Equipment Type</div>
                                            <div class="value equipment-type-name">{{EQUIPMENT_TYPE_NAME}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Equipment Registration 3</div>
                                            <div class="value equipment-registration">{{EQUIPMENT_REGISTRATION}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Store Location</div>
                                            <div class="value store-location-name">{{STORE_LOCATION_NAME}}</div>
                                        </div>
                                        <div class="wrapper">
                                            <div class="key">Items 3</div>
                                            <div class="item-box">
                                                <?php echo $itemDetails; ?>
                                            </div>
                                        </div>

                                        <!-- ✅ NEW: Actions Section -->
                                        <div class='wrapper'>
                                            <div class='key'>Actions</div>
                                            <div class='value'>
                                                <a href='#' class='btn btn-info btn-sm details-btn' id='dynamic-details-btn'>
                                                    <i class='fas fa-list'></i> Details
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="replanModal" tabindex="-1" aria-labelledby="replanModalLabel" aria-hidden="false">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="replanForm" action="<?= site_url('Assets_Item_maintenance/replan'); ?>" method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="replanModalLabel">Replan Maintenance</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="equipment_id" id="modalEquipmentId">
                                        <div class="mb-3">
                                            <label for="newMaintenanceDate" class="form-label">New Maintenance Date</label>
                                            <input type="date" class="form-control" id="newMaintenanceDate" name="new_next_maintenance_date" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Replan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).on("click", ".open-replan-modal", function () {

        let equipmentId = $(this).data("equipment-id"); 
        let dueDate = $(this).data("current-due-date");
        
        $("#modalEquipmentId").val(equipmentId);
        $("#newMaintenanceDate").val(dueDate);
    });
</script>
                </div>

            </div>
        </div>
    </div>
</section>
</div>

<?php if (!empty($open_modal)) : ?>
    <script>
        window.addEventListener("DOMContentLoaded", function() {
            const replanModal = new bootstrap.Modal(document.getElementById('replanModal'));
            replanModal.show();
        });
    </script>
<?php endif; ?>
