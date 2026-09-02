<?php
$can_add_roles = $this->user_model->has_perm('add_user_roles');
$can_edit_roles = $this->user_model->has_perm('edit_user_roles');
$summary = $summary ?? ['total' => 0, 'active' => 0, 'inactive' => 0];
?>

<section class="identity-admin-page identity-admin-page--roles" aria-labelledby="role-management-title">
  <header class="identity-hero identity-hero--roles">
    <div class="identity-hero__copy">
      <span class="identity-hero__icon"><i class="fas fa-users-cog"></i></span>
      <div>
        <span class="identity-eyebrow">Permission Structure</span>
        <h2 id="role-management-title">User Roles</h2>
        <p>Organise permissions into clear roles, then assign those roles to the appropriate user accounts.</p>
      </div>
    </div>
    <div class="identity-hero__actions">
      <a class="identity-btn identity-btn--secondary" href="<?= site_url('users'); ?>">
        <i class="fas fa-users"></i><span>View Users</span>
      </a>
      <?php if ($can_add_roles) { ?>
        <button class="identity-btn identity-btn--primary" type="button" data-toggle="modal" data-target="#addModal">
          <i class="fas fa-plus"></i><span>Add Role</span>
        </button>
      <?php } ?>
    </div>
  </header>

  <div class="identity-summary-grid" aria-label="User role summary">
    <article class="identity-summary-card identity-summary-card--violet">
      <span class="identity-summary-card__icon"><i class="fas fa-layer-group"></i></span>
      <div><span>Total Roles</span><strong id="identity-roles-total"><?= intval($summary['total']); ?></strong><small>Permission groups created</small></div>
    </article>
    <article class="identity-summary-card identity-summary-card--green">
      <span class="identity-summary-card__icon"><i class="fas fa-shield-alt"></i></span>
      <div><span>Active</span><strong id="identity-roles-active"><?= intval($summary['active']); ?></strong><small>Available for assignment</small></div>
    </article>
    <article class="identity-summary-card identity-summary-card--red">
      <span class="identity-summary-card__icon"><i class="fas fa-ban"></i></span>
      <div><span>Inactive</span><strong id="identity-roles-inactive"><?= intval($summary['inactive']); ?></strong><small>Unavailable for new assignment</small></div>
    </article>
  </div>

  <aside class="identity-guidance-card">
    <span><i class="fas fa-lightbulb"></i></span>
    <div><strong>How roles work</strong><p>A role groups related permissions together. Open Manage Role to review its users and permissions, or visit User Accounts to assign roles per user.</p></div>
  </aside>

  <article class="identity-table-card">
    <div class="identity-table-card__head">
      <div>
        <span class="identity-eyebrow">Role Directory</span>
        <h3>Configured Roles</h3>
        <p>Review what each role is for and whether it is available for use.</p>
      </div>
      <div class="identity-status-filter" role="group" aria-label="Filter roles by status">
        <button type="button" class="identity-filter-btn is-active" data-status="">All</button>
        <button type="button" class="identity-filter-btn" data-status="1"><i class="identity-filter-dot identity-filter-dot--active"></i>Active</button>
        <button type="button" class="identity-filter-btn" data-status="0"><i class="identity-filter-dot identity-filter-dot--inactive"></i>Inactive</button>
      </div>
    </div>
    <div class="identity-table-wrap">
      <table class="table identity-data-table <?= ($can_edit_roles ? '' : 'read-only'); ?>" id="user_roles" width="100%" cellspacing="0">
        <thead><tr><th>Role</th><th>Description</th><th>Status</th><th>Manage</th></tr></thead>
        <tbody></tbody>
      </table>
    </div>
  </article>
</section>

<?php if ($can_add_roles) { ?>
  <div class="modal fade identity-modal" tabindex="-1" role="dialog" id="addModal" aria-labelledby="add-role-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div class="identity-modal__title">
            <span class="identity-modal__icon"><i class="fas fa-plus"></i></span>
            <div><span class="identity-eyebrow">Permission Group</span><h5 class="modal-title" id="add-role-title">Add User Role</h5><p>Create a role first, then configure its users and permissions.</p></div>
          </div>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form class="form-horizontal" action="<?= site_url('user_roles/add'); ?>" method="post">
          <div class="modal-body">
            <section class="identity-form-section mb-0">
              <div class="identity-form-section__head"><i class="fas fa-shield-alt"></i><div><h6>Role Details</h6><p>Use a clear name and explain the responsibility covered by this role.</p></div></div>
              <div class="form-group">
                <label for="name">Role name <sup>Required</sup></label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Example: Asset Supervisor" required>
              </div>
              <div class="form-group mb-0">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" id="description" rows="4" placeholder="Describe what this role is responsible for..."></textarea>
              </div>
            </section>
          </div>
          <div class="modal-footer">
            <button type="button" class="identity-btn identity-btn--secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="identity-btn identity-btn--primary"><i class="fas fa-plus"></i>Create Role</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php } ?>
