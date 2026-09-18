<?php $isPermission = $access['route'] === 'permissions'; ?>
<section class="card access-editor">
    <div class="access-editor-heading"><span>Users &amp; Permissions</span><h2>Edit <?= $access['title']; ?></h2><p>Update the details below and save your changes.</p></div>
    <form action="<?= site_url($access['route'].'/update'); ?>" method="post" class="access-edit-form">
        <input type="hidden" name="id" value="<?= (int) $info->{$access['id']}; ?>">
        <?php if (!$isPermission || !$info->system): ?>
        <div class="form-group"><label for="name"><?= $access['label']; ?> <small>Required</small></label><input type="text" name="name" id="name" class="form-control" required value="<?= htmlspecialchars($info->{$access['name']}, ENT_QUOTES, 'UTF-8'); ?>"></div>
        <?php else: ?>
        <div class="access-context-note"><i class="fas fa-lock" aria-hidden="true"></i><p>System rule: <strong><?= htmlspecialchars($info->perm_name, ENT_QUOTES, 'UTF-8'); ?></strong>. The rule name cannot be changed.</p></div>
        <?php endif; ?>
        <?php if ($isPermission): ?>
        <div class="form-group"><label for="category">Permission Category <small>Required</small></label><select name="category" id="category" class="form-control" required><?php foreach ($this->steve->permission_categories() as $category): ?><option value="<?= (int) $category->perm_cat_id; ?>" <?= (int) $info->perm_cat_id === (int) $category->perm_cat_id ? 'selected' : ''; ?>><?= htmlspecialchars($category->perm_cat_name, ENT_QUOTES, 'UTF-8'); ?></option><?php endforeach; ?></select></div>
        <?php else: ?>
        <div class="form-group"><label for="description">Description <span class="access-optional">Optional</span></label><textarea name="description" id="description" class="form-control" rows="4"><?= htmlspecialchars($info->description ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></div>
        <?php endif; ?>
        <div class="access-form-actions"><button type="submit" class="btn access-save"><i class="fas fa-save" aria-hidden="true"></i> Save Changes</button><a href="<?= site_url($access['route']); ?>" class="btn access-secondary"><i class="fas fa-arrow-left" aria-hidden="true"></i> Go Back</a></div>
    </form>
</section>
