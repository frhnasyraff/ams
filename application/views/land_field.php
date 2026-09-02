<style type="text/css">
    .pagination>li>a {
        border-radius: 10px;
    }

    .pagination>.active>a {
        background-color: #09073dff !important;
    }

    #masters_companies_next>a {
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    #masters_companies_previous>a {
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
</style>

<a class="float-right text_successo btn btn_border" href="#addModal" data-toggle="modal" data-target="#addModal" title="Add new field location"><i class="fa fa-plus"></i> New Field Location</a>

<div class="card shadow mb-4 tabradius">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless table-striped" id="land_field_location" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="bg-white text-dark font-weight-bold">&nbsp;</th>
                        <th class="bg-white text-dark font-weight-bold">Location Name</th>
                        <th class="bg-white text-dark font-weight-bold">Address</th>
                        <th class="bg-white text-dark font-weight-bold">Latitude</th>
                        <th class="bg-white text-dark font-weight-bold">Longitude</th>
                        <th class="bg-white text-dark font-weight-bold">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Field Location
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("land_field/add"); ?>" method="post">
                <div class="modal-body row">
                    <?= $this->steve->form_group_label_input("text", "location_name", "Location Name", "col-sm-12", 1); ?>
                    <div class="form-group col-sm-12">
                        <label for="">Branch Office</label>
                        <select name="branch_id" class="form-control" id="">
                            <?php foreach ($branch_office as $branch) { ?>
                                <option value="<?= $branch->branch_id ?>"><?= $branch->branch_name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?= $this->steve->form_group_label_input("text", "address", "Address", "col-sm-12", 1); ?>
                    <?= $this->steve->form_group_label_input("text", "latitude", "Latitude", "col-sm-12", 1); ?>
                    <?= $this->steve->form_group_label_input("text", "longitude", "Longitude", "col-sm-12", 1); ?>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Field Location</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>