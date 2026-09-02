<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">List of equipment types
            <?php if ($this->user_model->has_perm("add_equipment_types")) { ?>
            <a class="float-right" href="#addModal" data-toggle="modal" data-target="#addModal"
                title="Add new equipment type"><i class="fa fa-plus"></i> New equipment type</a>
            <?php } ?>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table
                class="table table-bordered table-striped <?= ($this->user_model->has_perm("edit_equipment_types") ? "" : "read-only"); ?>"
                id="equipment_types" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Equipment type</th>
                        <th>Short code</th>
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

<?php if ($this->user_model->has_perm("add_equipment_types")) { ?>
<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New equipment type
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("equipment_types/add"); ?>" method="post">
                <div class="modal-body row">

                    <?=$this->steve->form_group_label_input("text", "name", "Equipment type name", "col-sm-4", 1, '', 30);?>

                    <?=$this->steve->form_group_label_input("text", "short_code", "Equipment type short code", "col-sm-4 uppercase", 1, $info->equipment_type_short_code, 6);?>

                    <?= $this->steve->form_group_label_select("resource_type", "Operated by", $this->steve->resource_types(), 'resource_type_id', 'resource_type_name', 'col-sm-4', $info->operator_id); ?>

                    <?=$this->steve->form_group_label_textarea("description", "Description", "col-sm-8");?>
                    
                    <div class="text-center colorwheel col-sm-4">
                    <input id="color-block" type="text" value="<?= $info->equipment_type_colour; ?>" data-wheelcolorpicker="" data-wcp-format="css" name="colour"
                        data-wcp-layout="block" data-wcp-sliders="wsvp" data-wcp-cssclass="color-block"
                        data-wcp-autoresize="false" />
                </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add equipment type</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>