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

<a class="float-right text_successo btn btn_border" href="#addModal" data-toggle="modal" data-target="#addModal"
    title="Upload logo image"><i class="fa fa-plus"></i>Upload Image</a>

<hr style="color: #DBDBE0;" />
<div class="card shadow mb-4 tabradius">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless table-striped" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="bg-white text-dark font-weight-bold">Image <span style="margin-left:50%;">Delete</span></th>
                    </tr>
                </thead>
                <tbody>
                    <td>
                        <?php if (!empty($image_path)): ?>
                        <img src="<?php echo base_url($image_path); ?>" alt="Logo Image" width="20%">
                        <!-- Delete Button -->
                        <form action="<?= site_url('LogoImage/delete'); ?>" method="post" style="display:inline; ">
                            <input type="hidden" name="image_path" value="<?= $image_path; ?>">
                            <button style="margin-left:50%;" type="submit" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this image?');">
                                Delete
                            </button>
                        </form>
                        <?php else: ?>
                        <p>No image found.</p>
                        <?php endif; ?>
                    </td>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Logo Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("LogoImage/add"); ?>" method="post"
                enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">
                            <div class="form-group time_picker uppercase">
                                <label for="asset-type-name">Select Image <sup>REQUIRED</sup></label>
                                <input type="file" name="logoImage" id="logoImage" class="form-control">
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Upload Image</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
