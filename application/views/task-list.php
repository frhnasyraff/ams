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

<h2 class="mb-4">Task List</h2>

<a class="float-right text_successo btn btn_border" href="#addModal" data-toggle="modal" data-target="#addModal" title="Add new Task" style="margin-top: -80px !important; margin-right: 5px; "><i class="fa fa-plus"></i>New Task</a>

<hr style="color: #DBDBE0;" />
<div class="card shadow mb-4 tabradius" style="width: 100% !important;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless table-striped" id="task-list" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="bg-white text-dark font-weight-bold">ID</th>
                        <th class="bg-white text-dark font-weight-bold">Name</th>
                        <th class="bg-white text-dark font-weight-bold">Frequency (Days)</th>
                        <th width="20%" class="bg-white text-dark font-weight-bold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables automatically populate karega -->
                </tbody>
            </table>
        </div>
    </div>
</div>

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
                    <button type="submit" class="btn btn-success">Add Task</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                    <button type="submit" class="btn btn-success">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
