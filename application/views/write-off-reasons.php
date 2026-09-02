<style>
    .write-off-reasons-container {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .action-buttons .btn {
        padding: 3px 8px;
        font-size: 12px;
        margin-right: 5px;
    }
    
    #reasonTable th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
        border-top: none;
    }
    
    .modal-header {
        background-color: #133C81;
        color: white;
    }
    
    .modal-header .close {
        color: white;
        opacity: 0.8;
    }
    
    .badge {
        font-size: 12px;
        padding: 4px 8px;
    }
</style>

<div class="write-off-reasons-container">
    <!-- Header with Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 style="color: #333; font-weight: 600; margin-bottom: 5px;">Write Off Reasons</h4>
            <p style="color: #666; margin-bottom: 0;">Manage write-off reasons for asset disposal</p>
        </div>
        <button type="button" class="btn btn-primary" id="addReasonBtn">
            <i class="fas fa-plus"></i> Add New Reason
        </button>
    </div>
    
    <!-- Search and Filter -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Search reasons...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <select class="form-control" id="statusFilter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-secondary w-100" id="resetFilters">
                <i class="fas fa-redo"></i> Reset Filters
            </button>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="reasonTable">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="25%">Write-off Reason</th>
                    <th width="40%">Description</th>
                    <th width="10%">Status</th>
                    <th width="15%">Created Date</th>
                    <th width="10%">Actions</th>
                </tr>
            </thead>
            <tbody id="reasonTableBody">
                <!-- Data will be loaded via AJAX -->
                <?php if (!empty($write_off_reasons)): ?>
                    <?php foreach ($write_off_reasons as $reason): ?>
                        <tr>
                            <td><?php echo $reason->id; ?></td>
                            <td><?php echo htmlspecialchars($reason->write_off_reason); ?></td>
                            <td><?php echo htmlspecialchars($reason->description ?: '-'); ?></td>
                            <td>
                                <?php if ($reason->status == 'active'): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($reason->created_at)); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-sm btn-outline-primary edit-reason" 
                                            data-id="<?php echo $reason->id; ?>"
                                            data-reason="<?php echo htmlspecialchars($reason->write_off_reason); ?>"
                                            data-description="<?php echo htmlspecialchars($reason->description); ?>"
                                            data-status="<?php echo $reason->status; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-reason" 
                                            data-id="<?php echo $reason->id; ?>"
                                            data-reason="<?php echo htmlspecialchars($reason->write_off_reason); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-database fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No write-off reasons found</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div style="font-size: 14px; color: #666;">
            Showing <?php echo ($page * $config['per_page']) + 1; ?> to 
            <?php echo min(($page + 1) * $config['per_page'], $total_rows); ?> of 
            <?php echo $total_rows; ?> entries
        </div>
        <div>
            <?php echo $pagination; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Modal (Bootstrap 4 compatible) -->
<div class="modal fade" id="reasonModal" tabindex="-1" role="dialog" aria-labelledby="reasonModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reasonModalLabel">Add Write-off Reason</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="reasonForm">
                <div class="modal-body">
                    <input type="hidden" id="reasonId" name="id">
                    
                    <div class="form-group mb-3">
                        <label for="write_off_reason" class="form-label">Write-off Reason *</label>
                        <input type="text" class="form-control" id="write_off_reason" name="write_off_reason" required>
                        <div class="invalid-feedback" id="reasonError"></div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal (Bootstrap 4 compatible) -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteReasonName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.WRITE_OFF_URLS = {
        save: "<?= site_url('write_off_reasons/save'); ?>",
        delete: "<?= site_url('write_off_reasons/delete'); ?>",
        status: "<?= site_url('write_off_reasons/change_status'); ?>",
        list: "<?= site_url('write_off_reasons/get_ajax_list'); ?>"
    };
</script>
