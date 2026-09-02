<?php
$can_add_users = $this->user_model->has_perm('add_users');
$can_edit_users = $this->user_model->has_perm('edit_users');
$summary = $summary ?? ['total' => 0, 'active' => 0, 'inactive' => 0];
?>

<section class="identity-admin-page identity-admin-page--users" aria-labelledby="user-management-title">
  <header class="identity-hero">
    <div class="identity-hero__copy">
      <span class="identity-hero__icon"><i class="fas fa-user-shield"></i></span>
      <div>
        <span class="identity-eyebrow">Access Control</span>
        <h2 id="user-management-title">User Accounts</h2>
        <p>Manage who can access the system, review account status and open each profile to assign roles or permissions.</p>
      </div>
    </div>
    <div class="identity-hero__actions">
      <a class="identity-btn identity-btn--secondary" href="<?= site_url('user_roles'); ?>">
        <i class="fas fa-users-cog"></i><span>View Roles</span>
      </a>
      <?php if ($can_add_users) { ?>
        <button class="identity-btn identity-btn--primary" type="button" data-toggle="modal" data-target="#addModal">
          <i class="fas fa-user-plus"></i><span>Add User</span>
        </button>
      <?php } ?>
    </div>
  </header>

  <div class="identity-summary-grid" aria-label="User account summary">
    <article class="identity-summary-card identity-summary-card--blue">
      <span class="identity-summary-card__icon"><i class="fas fa-users"></i></span>
      <div><span>Total Accounts</span><strong id="identity-users-total"><?= intval($summary['total']); ?></strong><small>Registered system users</small></div>
    </article>
    <article class="identity-summary-card identity-summary-card--green">
      <span class="identity-summary-card__icon"><i class="fas fa-user-check"></i></span>
      <div><span>Active</span><strong id="identity-users-active"><?= intval($summary['active']); ?></strong><small>Can access the system</small></div>
    </article>
    <article class="identity-summary-card identity-summary-card--red">
      <span class="identity-summary-card__icon"><i class="fas fa-user-slash"></i></span>
      <div><span>Inactive</span><strong id="identity-users-inactive"><?= intval($summary['inactive']); ?></strong><small>Access currently disabled</small></div>
    </article>
  </div>

  <article class="identity-table-card">
    <div class="identity-table-card__head">
      <div>
        <span class="identity-eyebrow">Account Directory</span>
        <h3>Registered Users</h3>
        <p>Select Manage to review account details, roles and permission overrides.</p>
      </div>
      <div class="identity-status-filter" role="group" aria-label="Filter users by status">
        <button type="button" class="identity-filter-btn is-active" data-status="">All</button>
        <button type="button" class="identity-filter-btn" data-status="1"><i class="identity-filter-dot identity-filter-dot--active"></i>Active</button>
        <button type="button" class="identity-filter-btn" data-status="0"><i class="identity-filter-dot identity-filter-dot--inactive"></i>Inactive</button>
      </div>
    </div>
    <div class="identity-table-wrap">
      <table class="table identity-data-table <?= ($can_edit_users ? '' : 'read-only'); ?>" id="users" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>User</th>
            <th>User Code</th>
            <th>Email Address</th>
            <th>Status</th>
            <th>Manage</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </article>
</section>

<?php if ($can_add_users) { ?>
  <div class="modal fade identity-modal" tabindex="-1" role="dialog" id="addModal" aria-labelledby="add-user-title" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div class="identity-modal__title">
            <span class="identity-modal__icon"><i class="fas fa-user-plus"></i></span>
            <div><span class="identity-eyebrow">New Account</span><h5 class="modal-title" id="add-user-title">Add User</h5><p>Create login details and the user's access profile.</p></div>
          </div>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form class="form-horizontal" action="<?= site_url('users/add'); ?>" method="post">
          <div class="modal-body">
            <section class="identity-form-section">
              <div class="identity-form-section__head"><i class="fas fa-id-card"></i><div><h6>Account Details</h6><p>Information used to identify and sign in this user.</p></div></div>
              <div class="row">
                <?= $this->steve->form_group_label_input('text', 'full_name', 'Full name', 'col-md-6', 1); ?>
                <?= $this->steve->form_group_label_input('text', 'username', 'Username', 'col-md-6', 1); ?>
                <?= $this->steve->form_group_label_input('text', 'user_code', 'User code', 'col-md-6 uppercase'); ?>
                <?= $this->steve->form_group_label_input('email', 'email', 'Email address', 'col-md-6', 1); ?>
                <?= $this->steve->form_group_label_input('text', 'password', 'Temporary password', 'col-md-6', 1, $this->steve->random_str(10)); ?>
              </div>
            </section>

            <section class="identity-form-section">
              <div class="identity-form-section__head"><i class="fas fa-key"></i><div><h6>Access Profile</h6><p>Set the user's designation, group and mobile access.</p></div></div>
              <div class="row">
                <?= $this->steve->form_group_label_select('designation', 'Designation', $this->steve->designations(), 'designation_id', 'designation_name', 'col-md-6', '', 1); ?>
                <?= $this->steve->form_group_label_select('user_group', 'User group', $this->steve->user_groups(), 'user_group_id', 'user_group_name', 'col-md-6', '', 1); ?>
                <div class="form-group col-12 mb-0">
                  <label class="identity-check" for="form_mobile"><input type="checkbox" name="mobile" id="form_mobile" value="1"><span><strong>Allow mobile app access</strong><small>The account can also sign in through the mobile application.</small></span></label>
                </div>
              </div>
            </section>

            <section class="identity-form-section">
              <div class="identity-form-section__head"><i class="fas fa-address-book"></i><div><h6>Contact Information</h6><p>Optional address and phone information for this user.</p></div></div>
              <div class="row">
                <?= $this->steve->form_group_label_input('tel', 'phone', 'Mobile phone', 'col-md-6'); ?>
                <?= $this->steve->form_group_label_input('text', 'address_line_1', 'Address line 1', 'col-md-6'); ?>
                <?= $this->steve->form_group_label_input('text', 'address_line_2', 'Address line 2', 'col-md-6'); ?>
                <?= $this->steve->form_group_label_input('text', 'address_zip', 'ZIP code', 'col-md-3 uppercase', 0, '', 8); ?>
                <?= $this->steve->form_group_label_input('text', 'address_city', 'City', 'col-md-3'); ?>
                <?= $this->steve->form_group_label_input('text', 'address_state', 'State', 'col-md-3'); ?>
                <?= $this->steve->form_group_label_select('address_country', 'Country', $this->steve->countries(), 'code', 'countryname', 'col-md-3', 'MY', 1); ?>
              </div>
            </section>
          </div>
          <div class="modal-footer">
            <button type="button" class="identity-btn identity-btn--secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="identity-btn identity-btn--primary"><i class="fas fa-user-plus"></i>Create User</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php } ?>
