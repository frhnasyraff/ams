<style type="text/css">
  .pagination>li>a{
        border-radius: 10px;
        /*background-color: #fff !important;*/
        /*color: #fff !important;*/
    }
.pagination > .active>a{
        background-color: #07073dff !important;
    }

    #equipment_groups_next>a{
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
    #equipment_groups_previous>a{
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
</style>

<?php if ($this->user_model->has_perm("add_equipment_groups")) { ?>
    <a class="float-right text_successo btn btn-default btn_border" href="#addModal" data-toggle="modal" data-target="#addModal"
    title="Add new Asset group"><i class="fa fa-plus"></i> New Asset group</a>
<?php } ?>

<div class="card shadow mb-4 tabradius">
    <div class="card-body">
        <div class="table-responsive">
            <table
                class="table <?= ($this->user_model->has_perm("edit_equipment_groups") ? "" : "read-only"); ?>" id="asset_groups" width="100%" cellspacing="0">
                <thead>
                    <tr>
                    <th>Code</th>
                    <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($this->user_model->has_perm("add_equipment_groups")) { ?>
<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Asset group
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("asset_groups/add"); ?>" method="post">
                <div class="modal-body row">

                <?=$this->steve->form_group_label_input("text", "name", "Asset group name", "col-sm-12", 1, '', 125);?>

                <?=$this->steve->form_group_label_input("text", "code", "Asset group code", "col-sm-12 uppercase", 0, '', 30);?>

                <?=$this->steve->form_group_label_textarea("notes", "Notes", "col-sm-12");?>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Asset group</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>
