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
        <h6 class="m-0 font-weight-bold text-primary bg-light">List of States
            <?php if ($this->user_model->has_perm("add_equipment_types")) { ?>
            <a class="float-right" href="#addModal" data-toggle="modal" data-target="#addModal"
                title="Add new State"><i class="fa fa-plus"></i> New State</a>
            <?php } ?>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table
                class="table table-bordered table-striped <?= ($this->user_model->has_perm("edit_equipment_types") ? "" : "read-only"); ?>"
                id="states_tabel" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>State Name</th>
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
                <h5 class="modal-title">New State
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("states/add"); ?>" method="post">
                <div class="container">
                    <div class="modal-body row">
                        

                        <div class="col-md-12 mb-4">
                            <label for="">State Name <sup style="color:red;">Required</sup> </label>
                            <input class="form-control" type="text" name="state_name" placeholder="State Name" required  >
                        </div>

                        
                    </div>
                    

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add State</button>
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
                <h5 class="modal-title" id="editModalLabel">Edit State</h5>
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
                                <label for="">State Name <sup style="color:red;">Required</sup> </label>
                                <input class="form-control" type="text" id="stateName" name="state_name" placeholder="State Name" required  >
                            </div>

                          
                    
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

