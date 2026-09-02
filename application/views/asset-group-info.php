<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4 tabradius">

            <div class="card-body">
                <div class="bg-white card-header tabradius">
                    <h6 class="m-0 font-weight-bold text_warning_color">Edit Asset group</h6>
                </div>
                <form class="form-horizontal" action="<?= site_url("asset_groups/update"); ?>" method="post">
                    <div class="row">
                        <?= $this->steve->form_group_label_input("text", "name", "Asset group name", "col-sm-12", 1, $info->equipment_group_name, 125); ?>

                        <?= $this->steve->form_group_label_input("text", "code", "Asset group code", "col-sm-12 uppercase", 0, $info->equipment_group_code, 30); ?>

                        <?= $this->steve->form_group_label_textarea("notes", "Notes", "col-sm-12", 0, $info->equipment_group_notes); ?>
                    </div>
                    <div class="text-center">
                        <input type="hidden" name="id" value="<?= $info->equipment_group_id; ?>" />
                        <button type="submit" class="btn bg_success text-white font-weight-bold">Save changes</button>
                        <a class="btn border_success text_successb" data-dismiss="modal" href=".">Go back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow mb-4 tabradius">
            <div class="card-body">
                <div class="bg-white card-header tabradius">
                    <h6 class="m-0 font-weight-bold text_warning_color">Asset associated with group</h6>
                </div>

                <?php if (count($equipments)) { ?>
                    <ul class="list-group">
                        <?php foreach ($equipments as $equipment) { ?>
                            <li class="list-group-item"><a href="<?= site_url("equipments/info?id=" . $this->steve->id_encode($equipment->equipment_id)); ?>"><?= $equipment->equipment_name; ?></a></li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    No Asset assigned
                <?php } ?>
            </div>
        </div>
    </div>
</div>