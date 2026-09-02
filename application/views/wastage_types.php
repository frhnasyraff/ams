<style type="text/css">
  .pagination>li>a{
        border-radius: 10px;
    }
    .pagination > .active>a{
        background-color: #073D11 !important;
    }

    #wastage_types_next>a{
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
    #wastage_types_previous>a{
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
</style>

<div class="card shadow mb-4 tabradius">
    
    <div class="card-body">

        <div class="bg-white card-header py-3">
            <h6 class="bg-white m-0 font-weight-bold text-primary">List of wastage types
                <?php if ($this->user_model->has_perm("add_wastage_types")) { ?>
                <a class="float-right" href="#addModal" data-toggle="modal" data-target="#addModal"
                    title="Add new wastage type"><i class="fa fa-plus"></i> New wastage type</a>
                <?php } ?>
            </h6>
        </div>

        <div class="table-responsive">
            <table
                class="table table-borderless table-striped <?= ($this->user_model->has_perm("edit_wastage_types") ? "" : "read-only"); ?>"
                id="wastage_types" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="30%">Wastage type</th>
                        <th width="50%">Description</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($this->user_model->has_perm("add_wastage_types")) { ?>
<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New wastage type
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("wastage_types/add"); ?>" method="post">
                <div class="modal-body row">

                    <?=$this->steve->form_group_label_input("text", "name", "Wastage type name", "col-sm-12", 1, '', 30);?>

                    <?=$this->steve->form_group_label_textarea("description", "Description", "col-sm-12");?>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add wastage type</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>