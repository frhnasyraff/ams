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
        <h6 class="m-0 font-weight-bold text-primary bg-light">List of Locations
            <?php if ($this->user_model->has_perm("add_equipment_types")) { ?>
            <a class="float-right" href="#addModal" data-toggle="modal" data-target="#addModal"
                title="Add new Location"><i class="fa fa-plus"></i> New Location</a>
            <?php } ?>
        </h6>
    </div>
    <div class="card-body">
        <div class="text-center">

            <div class="btn-group equipment_group_filter small mt-2" role="group" aria-label="Equipment groups filter">
                <button type="button" id="allStates" class="btn btn-primary btn-sm"  data-filter="allState"
                    title="Show all equipment groups">All</button>
                <?php foreach ($states as $t) { ?>
                <button type="button" id="filterTab" class="btn btn-sm text-uppercase tip filterTab"
                    data-filter="<?= $t->state_name; ?>" title="Show only <?= $t->state_name; ?>">
                    <?= $t->state_name; ?>
                </button>
                <?php } ?>
            </div>
        </div>
        <div class="table-responsive">
            <table
                class="table table-bordered table-striped <?= ($this->user_model->has_perm("edit_equipment_types") ? "" : "read-only"); ?>"
                id="locations_tabel" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <!-- <th>Country Name</th> -->
                        <th>Name</th>
                        <th>State </th>
                        <th>Latitude</th>
                        <th>Longitude</th>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Location
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("locations/add"); ?>" method="post">
                <div class="container">
                    <div class="modal-body row">
                        <!-- <div class="col-md-12 mb-4">
                            <label for="">Country <sup style="color:red;">Required</sup> </label>

                            <select name="country_name" id="" class="form-control">

                                <option value="">Select Country</option>
                                <?php foreach ($countries as $c) { ?>
                                <option value="<?= $c->countryname?>"><?= $c->countryname?></option>
                                <?php } ?>
                            </select>
                        </div> -->

                        <!-- <div class="col-md-6 mb-4">
                            <label for="">State Name <sup style="color:red;">Required</sup> </label>
                            <input class="form-control" type="text" name="state_name" placeholder="State Name" required  >
                        </div> -->

                        <div class="col-md-6 mb-4">
                            <label for="">Name <sup style="color: red;">Required</sup></label>
                            <input class="form-control" type="text" name="name" placeholder="Name" required>
                        </div>


                        <div class="col-md-6 mb-4">
                            <label for="">State Name <sup style="color:red;">Required</sup> </label>

                            <?php // Legacy: <select name="state_name" id="" class="form-control"> ?>
                            <select name="state_id" class="form-control" required>

                                <option value="">Select State</option>
                                <?php foreach ($states as $s) { ?>
                                <?php // Legacy option value: $s->state_name ?>
                                <option value="<?= $s->id ?>"><?= $s->state_name ?></option>
                                <?php } ?>
                            </select>
                        </div>


                        <div class="col-md-6 mb-4">
                            <label for="">Latitude<sup style="color: red;">Required</sup></label>
                            <input class="form-control" type="number" name="lat" placeholder="Latitude" min="-90" max="90" step="0.000001" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="">Longitude<sup style="color: red;">Required</sup></label>
                            <input class="form-control" type="number" name="long" placeholder="Longitude" required min="-180" max="180" step="0.000001" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="">Colour Code <sup style="color: red;">Required</sup></label>
                            <input class="form-control" type="text" name="colour" placeholder="Colour Code" required>
                        </div> 

                        <div class="col-md-6">
                            <label for="">Address <sup style="color: red;">Required</sup></label>
                            <textarea name="address" id="" rows="3" cols="40" required></textarea>
                        </div>

                    </div>


                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Location</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>


<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Location</h5>
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
                        <!-- <div class="col-md-12 mb-4">
                            <label for="">Country <sup style="color:red;">Required</sup> </label>

                            <select name="country_name" id="countryName" class="form-control">

                                <option value="">Select Country</option>
                                <?php foreach ($countries as $c) { ?>
                                <option value="<?= $c->countryname?>"><?= $c->countryname?></option>
                                <?php } ?>
                            </select>
                        </div> -->

                        <div class="col-md-6">
                            <label for="">Name <sup style="color: red;">Required</sup></label>
                            <input class="form-control" type="text" id="name" name="name" placeholder="Name" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="">State Name <sup style="color:red;">Required</sup> </label>

                            <?php // Legacy: <select name="state_name" id="stateName" class="form-control"> ?>
                            <select name="state_id" id="stateName" class="form-control" required>

                                <option value="">Select State</option>
                                <?php foreach ($states as $s) { ?>
                                <?php // Legacy option value: $s->state_name ?>
                                <option value="<?= $s->id ?>"><?= $s->state_name ?></option>
                                <?php } ?>
                            </select>
                        </div>



                       
                        <div class="col-md-6 mb-4">
                            <label for="">Latitude<sup style="color: red;">Required</sup></label>
                            <input class="form-control" type="number" name="lat" id="lat" placeholder="Latitude" min="-90" max="90" step="0.000001" required>

                        </div>

                        <div class="col-md-6">
                            <label for="">Longitude<sup style="color: red;">Required</sup></label>
                           
                            <input class="form-control" type="number" name="long" id="long" placeholder="Longitude" required min="-180" max="180" step="0.000001" required>

                        </div>

                        <div class="col-md-6">
                            <label for="">Colour Code <sup style="color: red;">Required</sup></label>
                            <input class="form-control" type="text" name="colour" id="colorCode"
                                placeholder="Colour Code" required>
                        </div>
                       

                        <div class="col-md-6">
                            <label for="">Address <sup style="color: red;">Required</sup></label>
                            <textarea name="address" id="address" rows="3" cols="40" required></textarea>
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
