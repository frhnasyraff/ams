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

    #asset-type-colors>tbody>tr>td>span {
        padding: 8px 13px;
    }
</style>

<a class="float-right text_successo btn btn_border" href="#addModal" data-toggle="modal" data-target="#addModal" title="Add new Asset Type Color"><i class="fa fa-plus"></i>New Color</a>

<hr style="color: #DBDBE0;" />
<div class="card shadow mb-4 tabradius">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless table-striped" id="asset-type-colors" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="bg-white text-dark font-weight-bold">Asset Type</th>
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
                <h5 class="modal-title">New Asset Type Color</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("AssetTypesColors/add"); ?>" method="post">
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">
                            <div class="form-group time_picker uppercase">
                            <label for="asset-type-name">Asset type <sup>REQUIRED</sup></label>
                            <select name="asset-type-name" class="form-control" id="asset-type-name" required>
                                        <option value="">Select</option>
                                        <?php
                                        // Create an array to store unique asset names
                                        $uniqueNames = array();

                                        // Loop through the assets
                                        foreach ($assets as $asset) {
                                            // Check if the asset name is not already in the $uniqueNames array
                                            if (!in_array($asset->name, $uniqueNames)) {
                                                // Add the asset name to the $uniqueNames array
                                                $uniqueNames[] = $asset->name;
                                                // Output the option element with the asset name
                                        ?>
                                                <option value="<?= $asset->name ?>"><?= $asset->name ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                            </div>
                            <div class="form-group time_picker uppercase">
                                <label for="asset-type-color">color in hex <sup>REQUIRED</sup></label>
                                <input type="color" name="asset-type-color" class="form-control" placeholder="Color" required autocomplete="off" />
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
                <h5 class="modal-title">Edit Color</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("AssetTypesColors/update"); ?>" id="asset_type_color_udpate_form" method="post">
                <input type="hidden" name="asset_type_color_id" id="asset_type_color_id">
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">
                            <div class="form-group time_picker uppercase">
                                <label for="asset_type_color_edit">Asset Type Color</label>
                                <input type="color" name="asset_type_color_edit" id="asset_type_color_edit" class="form-control" placeholder="Asset Type Color" required autocomplete="off" />
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