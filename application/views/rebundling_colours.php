<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">List of rebundling colours
            <?php if ($this->user_model->has_perm("add_rebundling_colours")) { ?>
            <a class="float-right" href="#addModal" data-toggle="modal" data-target="#addModal"
                title="Add new rebundling colour"><i class="fa fa-plus"></i> New rebundling colour</a>
            <?php } ?>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table
                class="table table-bordered table-striped <?= ($this->user_model->has_perm("edit_rebundling_colours") ? "" : "read-only"); ?>"
                id="rebundling_colours" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Rebundling colour</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($this->user_model->has_perm("add_rebundling_colours")) { ?>
<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New rebundling colour
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("rebundling_colours/add"); ?>" method="post">
                <div class="modal-body row">

                    <?=$this->steve->form_group_label_input("text", "name", "Rebundling colour", "col-sm-12", 1, '', 30);?>

                    <?=$this->steve->form_group_label_textarea("description", "Description", "col-sm-12");?>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add rebundling colour</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>