<?php
$permissionCategories = $this->steve->permission_categories();
$categoryPermissions = [];
$totalPermissions = 0;

foreach ($permissionCategories as $permissionCategory) {
  $permissionsForCategory = $this->steve->permissions($permissionCategory->perm_cat_id);
  $categoryPermissions[$permissionCategory->perm_cat_id] = $permissionsForCategory;
  $totalPermissions += count($permissionsForCategory);
}

$assignedUsersCount = count($role_users);
$availableUsersCount = max(0, count($users) - $assignedUsersCount);
$selectedPermissionsCount = count($role_permissions);
$isRoleActive = !isset($info->active) || intval($info->active) === 1;
?>

<main class="role-detail-redesign">
  <section class="role-detail-hero">
    <div class="role-detail-hero__main">
      <span class="role-detail-hero__icon"><i class="fas fa-user-shield"></i></span>
      <div>
        <span class="role-detail-eyebrow">Permission structure</span>
        <h2><?= htmlspecialchars($info->role_name, ENT_QUOTES, 'UTF-8'); ?></h2>
        <p>Manage the role profile, assigned users and access permissions from one workspace.</p>
      </div>
    </div>
    <div class="role-detail-hero__actions">
      <span class="role-detail-state <?= $isRoleActive ? 'is-active' : 'is-inactive'; ?>">
        <i class="fas fa-circle"></i><?= $isRoleActive ? 'Active role' : 'Inactive role'; ?>
      </span>
      <a href="<?= site_url('user_roles'); ?>" class="role-detail-back-btn"><i class="fas fa-arrow-left"></i><span>Back to Roles</span></a>
    </div>
  </section>

  <section class="role-detail-kpis" aria-label="Role summary">
    <article class="role-detail-kpi tone-blue">
      <span><i class="fas fa-users"></i></span>
      <div><small>Assigned Users</small><strong id="role-assigned-count"><?= $assignedUsersCount; ?></strong><p>Accounts using this role</p></div>
    </article>
    <article class="role-detail-kpi tone-cyan">
      <span><i class="fas fa-user-plus"></i></span>
      <div><small>Available Users</small><strong id="role-available-count"><?= $availableUsersCount; ?></strong><p>Can be assigned</p></div>
    </article>
    <article class="role-detail-kpi tone-purple">
      <span><i class="fas fa-key"></i></span>
      <div><small>Permissions Enabled</small><strong id="role-permission-count"><?= $selectedPermissionsCount; ?></strong><p>Of <?= $totalPermissions; ?> permissions</p></div>
    </article>
    <article class="role-detail-kpi tone-amber">
      <span><i class="fas fa-layer-group"></i></span>
      <div><small>Permission Groups</small><strong><?= count($permissionCategories); ?></strong><p>Access categories</p></div>
    </article>
  </section>

  <div class="role-detail-layout">
    <?php if ($this->user_model->has_perm("assign_users")) { ?>
      <section class="role-detail-card role-assignment-card">
        <header class="role-detail-card__header">
          <div>
            <span class="role-detail-eyebrow">User assignment</span>
            <h3>Who has this role?</h3>
            <p>Click a user to move them between available and assigned lists.</p>
          </div>
          <span class="role-detail-context"><i class="fas fa-exchange-alt"></i> Click to move</span>
        </header>
        <form class="form-horizontal role-assignment-form" action="<?= site_url("user_roles/assign_users"); ?>" method="post">
          <select multiple="multiple" id="users" name="users[]">
            <?php foreach ($users as $user) { ?>
              <option value="<?= $user->user_id; ?>" <?= (in_array($user->user_id, $role_users) ? 'selected' : ''); ?>><?= htmlspecialchars($user->full_name, ENT_QUOTES, 'UTF-8'); ?> (<?= htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8'); ?>)</option>
            <?php } ?>
          </select>

          <div class="role-detail-form-actions">
            <input type="hidden" name="id" value="<?= $info->role_id; ?>" />
            <button type="submit" class="role-detail-primary-btn"><i class="fas fa-save"></i><span>Save User Assignment</span></button>
          </div>
        </form>
      </section>
    <?php } ?>

    <aside class="role-detail-card role-profile-card">
      <header class="role-detail-card__header">
        <div>
          <span class="role-detail-eyebrow">Role profile</span>
          <h3>Role Information</h3>
          <p>Keep the name and purpose easy to understand.</p>
        </div>
        <span class="role-profile-icon"><i class="fas fa-id-badge"></i></span>
      </header>
      <form class="form-horizontal role-profile-form" action="<?= site_url("user_roles/update"); ?>" method="post">
        <div class="role-field">
          <label for="name"><span>Role Name</span><small>Required</small></label>
          <div class="role-input-wrap"><i class="fas fa-tag"></i><input type="text" name="name" class="form-control" id="name" placeholder="Role name" required value="<?= htmlspecialchars($info->role_name, ENT_QUOTES, 'UTF-8'); ?>" /></div>
        </div>

        <div class="role-field">
          <label for="description"><span>Description</span><small>Optional</small></label>
          <div class="role-input-wrap role-textarea-wrap"><i class="fas fa-align-left"></i><textarea name="description" class="form-control" id="description" placeholder="Explain what this role is used for"><?= htmlspecialchars($info->description ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></div>
        </div>
        <input type="hidden" name="id" value="<?= $info->role_id; ?>" />
        <button type="submit" class="role-detail-primary-btn role-profile-save"><i class="fas fa-save"></i><span>Save Role Details</span></button>
      </form>
    </aside>
  </div>

  <?php if ($this->user_model->has_perm("assign_permissions")) { ?>
    <section class="role-detail-card role-permissions-card">
      <header class="role-detail-card__header permissions-header">
        <div>
          <span class="role-detail-eyebrow">Access control</span>
          <h3>Role Permissions</h3>
          <p>Choose exactly what users with this role are allowed to access.</p>
        </div>
        <label class="role-permission-search">
          <i class="fas fa-search"></i>
          <input type="search" id="role-permission-search" placeholder="Search permissions..." autocomplete="off">
        </label>
      </header>

      <div class="permissions role-permission-workspace small">
        <form class="form-horizontal" action="<?= site_url("user_roles/assign_permissions"); ?>" method="post">
          <nav class="role-permission-tabs-wrap">
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <?php foreach ($permissionCategories as $index => $permissionCat) { ?>
                <a class="nav-item nav-link <?= $index === 0 ? 'active' : ''; ?>" id="nav-permissions-<?= $permissionCat->perm_cat_id; ?>-tab" data-toggle="tab" href="#nav-permissions-<?= $permissionCat->perm_cat_id; ?>" role="tab" aria-controls="nav-permissions-<?= $permissionCat->perm_cat_id; ?>" aria-selected="<?= $index === 0 ? 'true' : 'false'; ?>">
                  <span><?= htmlspecialchars($permissionCat->perm_cat_name, ENT_QUOTES, 'UTF-8'); ?></span>
                  <small><?= count($categoryPermissions[$permissionCat->perm_cat_id]); ?></small>
                </a>
              <?php } ?>
            </div>
          </nav>

          <div class="tab-content role-permission-content">
            <?php foreach ($permissionCategories as $index => $permissionCat) { ?>
              <div class="tab-pane fade <?= $index === 0 ? 'show active' : ''; ?>" id="nav-permissions-<?= $permissionCat->perm_cat_id; ?>" role="tabpanel" aria-labelledby="nav-permissions-<?= $permissionCat->perm_cat_id; ?>-tab">
                <div class="role-permission-section-heading">
                  <div>
                    <span class="role-permission-category-icon"><i class="fas fa-folder-open"></i></span>
                    <div><h4><?= htmlspecialchars($permissionCat->perm_cat_name, ENT_QUOTES, 'UTF-8'); ?></h4><p>Select the access available within this category.</p></div>
                  </div>
                  <div class="role-permission-bulk-actions" role="group" aria-label="Permission selection actions">
                    <button type="button" data-filter="all" data-id="<?= $permissionCat->perm_cat_id; ?>"><i class="fas fa-check-double"></i><span>Select All</span></button>
                    <button type="button" data-filter="none" data-id="<?= $permissionCat->perm_cat_id; ?>"><i class="fas fa-undo-alt"></i><span>Clear</span></button>
                  </div>
                </div>
                <div class="role-permission-grid" id="permission_<?= $permissionCat->perm_cat_id; ?>">
                  <?php foreach ($categoryPermissions[$permissionCat->perm_cat_id] as $permission) { ?>
                    <label class="role-permission-option" for="permission_<?= $permission->perm_id; ?>">
                      <input type="checkbox" value="<?= $permission->perm_id; ?>" id="permission_<?= $permission->perm_id; ?>" name="permissions[]" <?= (in_array($permission->perm_id, $role_permissions) ? 'checked' : ''); ?>>
                      <span class="role-permission-check"><i class="fas fa-check"></i></span>
                      <span class="role-permission-name"><?= htmlspecialchars($permission->perm_name, ENT_QUOTES, 'UTF-8'); ?></span>
                    </label>
                  <?php } ?>
                </div>
                <div class="role-permission-empty" hidden><i class="fas fa-search"></i><span>No permissions match your search.</span></div>
              </div>
            <?php } ?>
          </div>

          <footer class="role-permission-footer">
            <div><i class="fas fa-info-circle"></i><span><strong id="role-permission-footer-count"><?= $selectedPermissionsCount; ?></strong> permissions selected for this role.</span></div>
            <input type="hidden" name="id" value="<?= $info->role_id; ?>" />
            <button type="submit" class="role-detail-primary-btn"><i class="fas fa-shield-alt"></i><span>Save Permissions</span></button>
          </footer>
        </form>
      </div>
    </section>
  <?php } ?>
</main>
