<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tasks"></i> Maintenance Task Details - <?= $equipment->equipment_name ?>
                    </h6>
                    <a href="<?= site_url('Assets_Item_maintenance') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Calendar
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Equipment Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Equipment Name:</th>
                                    <td><?= $equipment->equipment_name ?></td>
                                </tr>
                                <tr>
                                    <th>Equipment Type:</th>
                                    <td><?= $equipment->equipment_type_name ?></td>
                                </tr>
                                <tr>
                                    <th>Registration:</th>
                                    <td><?= $equipment->equipment_registration ?></td>
                                </tr>
                                <tr>
                                    <!-- <th>Store Location:</th>
                                    <td><?= $equipment->store_location_name ?></td> -->
                                </tr>
                            </table>
                            
                            <!-- ✅ HIDDEN FIELDS FOR JAVASCRIPT -->
                            <input type="hidden" name="equipment_id" id="equipment_id" value="<?= $equipment->equipment_id ?>">
                            <input type="hidden" name="maintenance_id" id="maintenance_id" value="<?= $maintenance_id ?>">
                            
                            <?php if ($maintenance_id): ?>
                            <!-- <div class="alert alert-info">
                                <strong>Maintenance ID:</strong> <?= $maintenance_id ?>
                            </div> -->
                            <?php else: ?>
                            <div class="alert alert-warning">
                                <strong>Note:</strong> No maintenance ID found. Creating new maintenance record.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Task List</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="tasks-table" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Task Name</th>
                                                    <th>Assigned User</th>
                                                    <th>Cost</th>
                                                    <th>File</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- DataTable se data load hoga -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="edit-task-form" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Edit Task</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="task_id" id="edit-task-id">

          <!-- <input type="hidden" name="equipment_id" id="edit-equipment-id" value="<?= $equipment->equipment_id ?>">
          <input type="hidden" name="maintenance_id" id="edit-maintenance-id" value="<?= $maintenance_id ?>">
            <input type="hidden" name="task_list_id" id="edit-task-list-id"> -->

        <input type="hidden" name="equipment_id" id="edit-equipment-id" value="<?= $equipment->equipment_id ?>">
          <input type="hidden" name="maintenance_id" id="edit-maintenance-id" value="<?= $maintenance_id ?>">
          <input type="hidden" name="task_list_id" id="edit-task-list-id">

          <div class="form-group">
            <label>Cost</label>
            <input type="number" step="0.01" class="form-control" name="cost" id="edit-cost" required>
          </div>

          <div class="form-group">
            <label>Assign User</label>
            <select class="form-control" name="user_id" id="edit-user">
              <option value="">-- Select User --</option>
              <?php foreach ($users as $user): ?>
                <option value="<?= $user->user_id ?>">
                  <?= $user->full_name ?> (<?= $user->username ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>File</label>
            <input type="file" class="form-control" name="file" id="edit-file">
            <small id="current-file" class="form-text text-muted"></small>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status" id="edit-status">
              <option value="pending">Pending</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
            </select>
          </div>

        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success" id="update-task-btn">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- <div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tasks"></i> Maintenance Task Details - <?= $equipment->equipment_name ?>
                    </h6>
                    <a href="<?= site_url('Assets_Item_maintenance') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Calendar
                    </a>
                </div>
                <div class="card-body">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Equipment Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Equipment Name:</th>
                                    <td><?= $equipment->equipment_name ?></td>
                                </tr>
                                <tr>
                                    <th>Equipment Type:</th>
                                    <td><?= $equipment->equipment_type_name ?></td>
                                </tr>
                                <tr>
                                    <th>Registration:</th>
                                    <td><?= $equipment->equipment_registration ?></td>
                                </tr>
                                <tr>
                                    <th>Store Location:</th>
                                    <td><?= $equipment->store_location_name ?></td>
                                </tr>
                                <?php if ($maintenance_id): ?>
                                <div class="alert alert-info">
                                    <strong>Maintenance ID:</strong> <?= $maintenance_id ?>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-warning">
                                    <strong>Note:</strong> No maintenance ID found. Creating new maintenance record.
                                </div>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Manage Tasks</h5>
                                    <button type="button" class="btn btn-success btn-sm float-right" id="add-task-btn">
                                        <i class="fas fa-plus"></i> Add Task
                                    </button>
                                </div>
                                <div class="card-body">
                                    <form id="tasks-form">
                                        <input type="hidden" name="equipment_id" value="<?= $equipment->equipment_id ?>">
                                        <input type="hidden" name="maintenance_id" value="<?= $maintenance_id ?>">
                                        
                                        <div class="alert alert-secondary">
                                            <small>
                                                Equipment ID: <?= $equipment->equipment_id ?><br>
                                                Maintenance ID: <?= $maintenance_id ?: 'NULL (New Maintenance)' ?>
                                            </small>
                                        </div>
                                        
                                        <div id="tasks-container">

                                        </div>

                                        <div class="text-center mt-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Save Tasks
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Task List</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="tasks-table" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Task Name</th>
                                                    <th>Assigned User</th>
                                                    <th>Cost</th>
                                                    <th>File</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/template" id="task-row-template">
    <div class="task-row card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label>Task Name</label>
                    <select name="tasks[{{index}}][task_list_id]" class="form-control task-select" required>
                        <option value="">Select Task</option>
                        <?php foreach ($task_lists as $task): ?>
                            <option value="<?= $task->id ?>"><?= $task->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Cost (₹)</label>
                    <input type="number" name="tasks[{{index}}][cost]" class="form-control" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="col-md-2">
                    <label>Assigned User</label>
                    <select name="tasks[{{index}}][user_id]" class="form-control user-select">
                        <option value="">Select User</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user->user_id ?>">
                                <?= $user->full_name ?> (<?= $user->username ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Status</label>
                    <select name="tasks[{{index}}][status]" class="form-control status-select">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>File Upload</label>
                    <input type="file" name="tasks[{{index}}][file]" class="form-control-file">
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-block remove-task">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>


<div class="modal fade" id="editTaskModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="edit-task-form" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Edit Task</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="task_id" id="edit-task-id">
          <input type="hidden" name="equipment_id" id="edit-equipment-id" value="<?= $equipment->equipment_id ?>">
          <input type="hidden" name="maintenance_id" id="edit-maintenance-id" value="<?= $maintenance_id ?>">

          <div class="form-group">
            <label>Cost</label>
            <input type="number" step="0.01" class="form-control" name="cost" id="edit-cost" required>
          </div>

          <div class="form-group">
            <label>Assign User</label>
            <select class="form-control" name="user_id" id="edit-user">
              <option value="">-- Select User --</option>
              <?php foreach ($users as $user): ?>
                <option value="<?= $user->user_id ?>">
                  <?= $user->full_name ?> (<?= $user->username ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>File</label>
            <input type="file" class="form-control" name="file" id="edit-file">
            <small id="current-file" class="form-text text-muted"></small>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status" id="edit-status">
              <option value="pending">Pending</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
            </select>
          </div>

        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success" id="update-task-btn">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


</script> -->
