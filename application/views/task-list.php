<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css"> -->

<style type="text/css">
    .pagination>li>a {
        border-radius: 10px;
    }
    #task-list_next>a {
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
    #task-list_previous>a {
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
    .btn_border{
        margin-bottom: 100px !important;
    }
</style>

<section class="task-list-page"><div class="task-list-hero"><div><span>Maintenance Library</span><h2>Task List</h2><p>Manage recurring maintenance task templates and their frequency.</p></div>

<a class="task-list-new-btn" href="#addModal" data-toggle="modal" data-target="#addModal" title="Add new Task"><i class="fa fa-plus"></i> New Task</a></div>


<div class="card shadow mb-4 tabradius task-list-card" style="width: 100% !important;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless table-striped" id="task-list" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Frequency (Days)</th>
                        <th width="22%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables automatically populate karega -->
                </tbody>
            </table>
        </div>
    </div>
</div>

</section>

<!-- Add Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Task</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form class="form-horizontal" id="addForm" method="post">
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">
                            <div class="form-group">
                                <label for="name">Maintenance task</label>
                                <input type="text" name="name" class="form-control" placeholder="Example: Monthly safety inspection" required autocomplete="off" />
                            </div>
                            <div class="form-group">
                                <label for="frequency_in_days">Frequency (Days)</label>
                                <input type="number" name="frequency_in_days" class="form-control" placeholder="Frequency in days" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="editModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Task</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form class="form-horizontal" id="editForm" method="post">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">
                            <div class="form-group">
                                <label for="name_edit">Name</label>
                                <input type="text" name="name_edit" id="edit_name" class="form-control" placeholder="Enter Name" required autocomplete="off" />
                            </div>
                            <div class="form-group">
                                <label for="frequency_edit">Frequency (Days)</label>
                                <input type="number" name="frequency_edit" id="edit_frequency" class="form-control" placeholder="Frequency in days" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>




