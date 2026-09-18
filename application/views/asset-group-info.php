<section class="asset-group-manage-page">
    <div class="asset-group-manage-layout">
        <div class="asset-group-editor-card">
            <div class="asset-group-card-head">
                <div class="asset-group-head-icon"><i class="fas fa-layer-group"></i></div>
                <div>
                    <span>Asset Group Setup</span>
                    <h3>Edit Asset Group</h3>
                    <p>Update group identity, code and notes in one clean workspace.</p>
                </div>
            </div>

            <form class="form-horizontal asset-group-form" action="<?= site_url("asset_groups/update"); ?>" method="post">
                <div class="row">
                    <?= $this->steve->form_group_label_input("text", "name", "Asset Group Name", "col-sm-12", 1, $info->equipment_group_name, 125); ?>

                    <?= $this->steve->form_group_label_input("text", "code", "Asset Group Code", "col-sm-12 uppercase", 0, $info->equipment_group_code, 30); ?>

                    <?= $this->steve->form_group_label_textarea("notes", "Notes", "col-sm-12", 0, $info->equipment_group_notes); ?>
                </div>
                <div class="asset-group-form-actions">
                    <input type="hidden" name="id" value="<?= $info->equipment_group_id; ?>" />
                    <button type="submit" class="btn asset-group-save-btn"><i class="fas fa-save"></i> Save Changes</button>
                    <a class="btn asset-group-back-btn" href="<?= site_url('asset_groups'); ?>"><i class="fas fa-arrow-left"></i> Go Back</a>
                </div>
            </form>
        </div>

        <aside class="asset-group-associated-card">
            <div class="asset-group-card-head asset-group-card-head--compact">
                <div class="asset-group-head-icon"><i class="fas fa-link"></i></div>
                <div>
                    <span>Linked Assets</span>
                    <h3>Assets Associated With Group</h3>
                </div>
            </div>

            <?php if (count($equipments)) { ?>
                <ul class="asset-group-linked-list">
                    <?php foreach ($equipments as $equipment) { ?>
                        <li><a href="<?= site_url("equipments/info?id=" . $this->steve->id_encode($equipment->equipment_id)); ?>"><i class="fas fa-cube"></i><?= $equipment->equipment_name; ?></a></li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <div class="asset-group-empty-state">
                    <i class="far fa-folder-open"></i>
                    <strong>No Asset Assigned</strong>
                    <span>This group is currently not linked to any asset.</span>
                </div>
            <?php } ?>
        </aside>
    </div>
</section>
