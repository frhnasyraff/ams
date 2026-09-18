<?php
$canAdd = $this->user_model->has_perm('add_'.$access['route']);
$canEdit = $this->user_model->has_perm('edit_'.$access['route']);
$isPermission = $access['route'] === 'permissions';
?>
<?php if ($canAdd): ?>
<button type="button" class="btn access-add-button" data-toggle="modal" data-target="#addModal" title="<?= $access['add']; ?>"><i class="fas fa-plus" aria-hidden="true"></i> <?= $access['add']; ?></button>
<?php endif; ?>
<section class="card access-panel">
    <div class="card-header"><h2><?= $access['label']; ?> Directory</h2></div>
    <div class="card-body">
        <div class="access-context-note"><i class="fas <?= $isPermission ? 'fa-shield-alt' : 'fa-info-circle'; ?>" aria-hidden="true"></i><p><?= $access['note']; ?></p></div>
        <div class="table-responsive">
            <table class="table table-borderless table-striped access-directory-table <?= $canEdit ? '' : 'read-only'; ?>" id="<?= $access['route']; ?>" width="100%" cellspacing="0">
                <thead><tr><?php if ($isPermission): ?><th>ID</th><th>Permission Category</th><th>Permission Rule</th><?php else: ?><th><?= $access['label']; ?></th><th>Description</th><?php endif; ?><th class="access-actions-heading">Actions</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</section>
<?php if ($canAdd): ?>
<div class="modal fade access-modal" id="addModal" tabindex="-1" role="dialog" aria-labelledby="access-add-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="access-add-title"><?= $access['add']; ?></h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <form action="<?= site_url($access['route'].'/add'); ?>" method="post">
            <div class="modal-body">
                <div class="form-group"><label for="name"><?= $access['label']; ?> <small>Required</small></label><input type="text" name="name" id="name" class="form-control" placeholder="<?= $access['label']; ?>" required></div>
                <?php if ($isPermission): ?>
                <div class="form-group"><label for="category">Permission Category <small>Required</small></label><select name="category" id="category" class="form-control" required><option value="">Select a category</option><?php foreach ($this->steve->permission_categories() as $category): ?><option value="<?= (int) $category->perm_cat_id; ?>"><?= htmlspecialchars($category->perm_cat_name, ENT_QUOTES, 'UTF-8'); ?></option><?php endforeach; ?></select></div>
                <?php else: ?>
                <div class="form-group"><label for="description">Description <span class="access-optional">Optional</span></label><textarea name="description" id="description" class="form-control" rows="4" placeholder="Add a short description"></textarea></div>
                <?php endif; ?>
            </div>
            <div class="modal-footer"><button type="button" class="btn access-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn access-save"><i class="fas fa-plus" aria-hidden="true"></i> <?= $access['add']; ?></button></div>
        </form>
    </div></div>
</div>
<?php endif; ?>
<?php if ($isPermission && $canEdit): ?>
<div class="modal fade access-modal" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="access-delete-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="access-delete-title">Delete Permission Rule</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <form action="<?= site_url('permissions/delete'); ?>" method="post">
            <div class="modal-body"><p>Delete this permission rule? Features that depend on this rule may stop working.</p><input type="hidden" name="id" class="record_id"></div>
            <div class="modal-footer"><button type="button" class="btn access-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="access-action access-delete"><i class="fas fa-trash-alt" aria-hidden="true"></i> Delete</button></div>
        </form>
    </div></div>
</div>
<?php endif; ?>
