<style type="text/css">
    .pagination>li>a {
        border-radius: 10px;
    }

    #resource_types_next>a {
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    #resource_types_previous>a {
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    #maintenance-type-color-code>tbody>tr>td>span {
        padding: 8px 13px;
    }
</style>

<a class="float-right text_successo btn btn_border" href="#addModal" data-toggle="modal" data-target="#addModal" title="Add new Maintenance Type Color"><i class="fa fa-plus"></i>New Color</a>

<hr style="color: #DBDBE0;" />
<div class="card shadow mb-4 tabradius">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless table-striped" id="maintenance-type-color-code" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="bg-white text-dark font-weight-bold">Maintenance Type</th>
                        <th class="bg-white text-dark font-weight-bold">Color</th>

                        <th width="10%" class="bg-white text-dark font-weight-bold">Actions</th>
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
                <h5 class="modal-title">New Maintenance Color</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("MaintenanceTypeColorCode/add"); ?>" method="post">
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">

                            <div class="form-group time_picker uppercase">
                                <label for="maintenance_type">Maintenance Type <sup>REQUIRED</sup></label>
                                <input type="text" name="maintenance_type" class="form-control" placeholder="maintenance type" required autocomplete="off" />
                            </div>

                            <div class="form-group time_picker uppercase">
                                <label for="color">color in hex <sup>REQUIRED</sup></label>
                                <input type="color" name="color" class="form-control" placeholder="Color" required autocomplete="off" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Color</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>





<div class="modal fade" tabindex="-1" role="dialog" id="editModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("MaintenanceTypeColorCode/update"); ?>" id="maintenance-type-color-code_udpate_form" method="post">

                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">
                            <input type="hidden" name="id_edit" id="id_edit" />

                            <div class="form-group time_picker uppercase">
                                <label for="maintenance_type_edit">Maintenance Type </label>
                                <input type="text" name="maintenance_type_edit" id="maintenance_type_edit" class="form-control" placeholder="Maintenance Type Color" required autocomplete="off"/>
                            </div>
                            <div class="form-group time_picker uppercase">
                                <label for="color_edit">Maintenance Type Color</label>
                                <input type="color" name="color_edit" id="color_edit" class="form-control" placeholder="Maintenance Type Color" required autocomplete="off" />
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
