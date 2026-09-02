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

    #vendor_manufacturing_drawing_number>tbody>tr>td>span {
        padding: 8px 13px;
    }
</style>

<a class="float-right text_successo btn btn_border" href="#addModal" data-toggle="modal" data-target="#addModal" title="Add new Manufacturer drawing Number"><i class="fa fa-plus"></i>New Manufacturer drawing Number</a>

<hr style="color: #DBDBE0;" />
<div class="card shadow mb-4 tabradius">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless table-striped" id="vendor_manufacturing_drawing_number" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="bg-white text-dark font-weight-bold">id</th>
                        <th class="bg-white text-dark font-weight-bold">drawing Number</th>
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
                <h5 class="modal-title">New Manufacturer drawing Number</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("VendorManufacturingDrawingNumber/add"); ?>" method="post">
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">
                            <div class="form-group time_picker uppercase">
                                <label for="drawing_number">Enter drawing Number</label>
                                <input type="text" name="drawing_number" class="form-control" placeholder="drawing Number" required autocomplete="off" />
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add drawing Number</button>
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
                <h5 class="modal-title">Edit drawing Number</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("VendorManufacturingDrawingNumber/update"); ?>" id="drawing_number_udpate_form" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">
                            <div class="form-group time_picker uppercase">
                                <label for="drawing_number_edit">Manufacturer Name</label>
                                <input type="text" name="drawing_number_edit" id="drawing_number_edit" class="form-control" placeholder="Enter Drawing Number" required autocomplete="off" />
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