<?php
$display_name = trim($info->full_name ?: $info->username);
$name_parts = preg_split('/\s+/', $display_name);
$user_initials = strtoupper(substr($name_parts[0] ?? 'U', 0, 1) . (count($name_parts) > 1 ? substr(end($name_parts), 0, 1) : ''));
$assigned_role_count = count($user_in_roles);
$override_count = count($user_permission_overrides);
$inherited_permissions = is_array($role_permissions) ? array_unique($role_permissions) : [];
$inherited_count = count($inherited_permissions);
$profile_url = $info->profile_picture ? site_url("storage/User-" . $info->user_id . "/" . $info->profile_picture) : '';
$is_active = intval($info->active) === 1;
?>

<main class="user-detail-redesign" data-user-id="<?= intval($info->user_id); ?>">
    <section class="user-detail-hero">
        <div class="user-detail-identity">
            <div class="user-detail-avatar <?= $profile_url ? 'has-photo' : ''; ?>">
                <?php if ($profile_url) { ?>
                    <img src="<?= html_escape($profile_url); ?>" alt="Profile picture for <?= html_escape($display_name); ?>">
                <?php } else { ?>
                    <span><?= html_escape($user_initials); ?></span>
                <?php } ?>
            </div>
            <div>
                <span class="user-detail-eyebrow"><i class="fas fa-user-shield"></i> Account Management</span>
                <h2><?= html_escape($display_name); ?></h2>
                <p>Update account information, assigned roles and individual permission overrides.</p>
                <div class="user-detail-meta">
                    <span><i class="fas fa-at"></i><?= html_escape($info->username); ?></span>
                    <span><i class="fas fa-id-badge"></i><?= html_escape($info->user_code ?: 'No user code'); ?></span>
                    <span><i class="far fa-envelope"></i><?= html_escape($info->email); ?></span>
                </div>
            </div>
        </div>
        <div class="user-detail-hero-actions">
            <span class="user-detail-state <?= $is_active ? 'is-active' : 'is-inactive'; ?>">
                <i class="fas fa-circle"></i><?= $is_active ? 'Active Account' : 'Inactive Account'; ?>
            </span>
            <a class="user-detail-back-btn" href="<?= site_url('users'); ?>"><i class="fas fa-arrow-left"></i><span>Back to Users</span></a>
        </div>
    </section>

    <section class="user-detail-kpis" aria-label="Account summary">
        <article class="user-detail-kpi tone-blue">
            <span><i class="fas fa-user-tag"></i></span>
            <div><small>Assigned Roles</small><strong id="user-assigned-role-count"><?= $assigned_role_count; ?></strong><p>Access groups linked to this account</p></div>
        </article>
        <article class="user-detail-kpi tone-purple">
            <span><i class="fas fa-shield-alt"></i></span>
            <div><small>Role Permissions</small><strong><?= $inherited_count; ?></strong><p>Permissions inherited from roles</p></div>
        </article>
        <article class="user-detail-kpi tone-amber">
            <span><i class="fas fa-sliders-h"></i></span>
            <div><small>Overrides</small><strong id="user-override-count"><?= $override_count; ?></strong><p>Extra permissions set for this user</p></div>
        </article>
    </section>

    <nav class="user-detail-tabs" aria-label="User account sections">
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-details-tab" data-toggle="tab" href="#nav-details" role="tab" aria-controls="nav-details" aria-selected="true">
                <i class="fas fa-user-edit"></i><span>User Details</span>
            </a>
            <?php if ($this->user_model->has_perm("assign_user_roles")) { ?>
                <a class="nav-item nav-link" id="nav-association-tab" data-toggle="tab" href="#nav-association" role="tab" aria-controls="nav-association" aria-selected="false">
                    <i class="fas fa-user-tag"></i><span>User Roles</span><b id="user-role-tab-count"><?= $assigned_role_count; ?></b>
                </a>
            <?php } ?>
            <?php if ($this->user_model->has_perm("user_permissions_override")) { ?>
                <a class="nav-item nav-link" id="nav-permissions-tab" data-toggle="tab" href="#nav-permissions" role="tab" aria-controls="nav-permissions" aria-selected="false">
                    <i class="fas fa-key"></i><span>Permission Overrides</span><b id="user-permission-tab-count"><?= $override_count; ?></b>
                </a>
            <?php } ?>
        </div>
    </nav>

    <div class="tab-content user-detail-tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-details" role="tabpanel" aria-labelledby="nav-details-tab">
            <div class="user-detail-layout">
                <form class="user-detail-card user-profile-form" action="<?= site_url("users/update"); ?>" method="post">
                    <header class="user-detail-card__header">
                        <span class="user-detail-card__icon"><i class="fas fa-address-card"></i></span>
                        <div><span class="user-detail-eyebrow">Account Profile</span><h3>Personal & Contact Details</h3><p>Keep the user's identity, contact and access settings up to date.</p></div>
                    </header>

                    <div class="user-detail-form-section">
                        <div class="user-detail-section-title"><span><i class="fas fa-fingerprint"></i></span><div><h4>Account Identity</h4><p>Core details used to identify this user.</p></div></div>
                        <div class="row">
                            <?= $this->steve->form_group_label_input("text", "username", "Username", "col-md-6", 1, $info->username); ?>
                            <?= $this->steve->form_group_label_input("text", "user_code", "User code", "col-md-6 uppercase", 0, $info->user_code); ?>
                            <?= $this->steve->form_group_label_input("text", "full_name", "Full name", "col-md-6", 0, $info->full_name); ?>
                            <?= $this->steve->form_group_label_input("email", "email", "E-mail address", "col-md-6", 1, $info->email); ?>
                            <?= $this->steve->form_group_label_select("designation", "Designation", $this->steve->designations(), "designation_id", "designation_name", "col-md-6", $info->designation); ?>
                            <?= $this->steve->form_group_label_select("user_group", "User group", $this->steve->user_groups(), "user_group_id", "user_group_name", "col-md-6", $info->user_group); ?>
                        </div>
                    </div>

                    <div class="user-detail-form-section">
                        <div class="user-detail-section-title"><span><i class="fas fa-map-marker-alt"></i></span><div><h4>Address & Contact</h4><p>Location and phone information for operational contact.</p></div></div>
                        <div class="row">
                            <?= $this->steve->form_group_label_input("text", "address_line_1", "Address line 1", "col-md-6", 0, $info->address_line_1); ?>
                            <?= $this->steve->form_group_label_input("text", "address_line_2", "Address line 2", "col-md-6", 0, $info->address_line_2); ?>
                            <?= $this->steve->form_group_label_input("text", "address_zip", "ZIP code", "col-md-4 uppercase", 0, $info->address_zip, 8); ?>
                            <?= $this->steve->form_group_label_input("text", "address_city", "City", "col-md-4", 0, $info->address_city); ?>
                            <?= $this->steve->form_group_label_input("text", "address_state", "State", "col-md-4", 0, $info->address_state); ?>
                            <?= $this->steve->form_group_label_select("address_country", "Country", $this->steve->countries(), "code", "countryname", "col-md-6", $info->address_country, 1); ?>
                            <?= $this->steve->form_group_label_input("tel", "phone", "Mobile phone", "col-md-6", 0, $info->phone); ?>
                        </div>
                    </div>

                    <div class="user-detail-form-section user-detail-access-section">
                        <div class="user-detail-section-title"><span><i class="fas fa-mobile-alt"></i></span><div><h4>Access Preferences</h4><p>Choose which supporting services this account can use.</p></div></div>
                        <div class="user-detail-switch-grid">
                            <label class="user-detail-switch-card" for="form_mobile">
                                <span class="user-detail-switch-icon"><i class="fas fa-mobile-alt"></i></span>
                                <span><strong>Mobile app access</strong><small>Allow this user to sign in through the mobile application.</small></span>
                                <input type="checkbox" name="mobile" id="form_mobile" value="1" <?= $info->mobile ? 'checked' : ''; ?>>
                                <i class="user-detail-switch-ui"></i>
                            </label>
                            <?php if (!empty($supports_email_reminders)) { ?>
                                <label class="user-detail-switch-card" for="email_checkbox">
                                    <span class="user-detail-switch-icon"><i class="fas fa-bell"></i></span>
                                    <span><strong>Calibration reminders</strong><small>Send calibration reminder messages to this e-mail address.</small></span>
                                    <input type="checkbox" name="email_checkbox" id="email_checkbox" value="1" <?= (($info->email_check ?? 0) == '1' ? 'checked' : ''); ?>>
                                    <i class="user-detail-switch-ui"></i>
                                </label>
                            <?php } ?>
                        </div>
                    </div>

                    <input type="hidden" name="company_id" id="company_id" value="<?= intval($info->company_id); ?>">
                    <input type="hidden" name="id" value="<?= intval($info->user_id); ?>">
                    <footer class="user-detail-form-actions">
                        <a class="user-detail-secondary-btn" href="<?= site_url('users'); ?>"><i class="fas fa-times"></i><span>Cancel</span></a>
                        <button type="submit" class="user-detail-primary-btn"><i class="fas fa-save"></i><span>Save User Details</span></button>
                    </footer>
                </form>

                <aside class="user-detail-side-stack">
                    <section class="user-detail-card user-photo-card">
                        <header class="user-detail-card__header compact"><span class="user-detail-card__icon tone-cyan"><i class="fas fa-camera"></i></span><div><span class="user-detail-eyebrow">Profile Image</span><h3>Profile Picture</h3><p>Use a square image for the best result.</p></div></header>
                        <div class="user-photo-content">
                            <div class="user-photo-preview <?= $profile_url ? 'has-photo' : ''; ?>">
                                <?php if ($profile_url) { ?>
                                    <img src="<?= html_escape($profile_url); ?>" alt="Current profile picture">
                                <?php } else { ?>
                                    <span><?= html_escape($user_initials); ?></span><small>No photo uploaded</small>
                                <?php } ?>
                            </div>
                            <form action="<?= site_url("users/upload_picture"); ?>" class="dropzone user-photo-dropzone" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?= intval($info->user_id); ?>">
                                <div class="fallback"><input name="file" type="file" accept="image/*"></div>
                            </form>
                            <p class="user-photo-note"><i class="fas fa-info-circle"></i> JPG, PNG or WebP. The page refreshes after upload.</p>
                        </div>
                    </section>

                    <section class="user-detail-card user-password-card">
                        <header class="user-detail-card__header compact"><span class="user-detail-card__icon tone-purple"><i class="fas fa-lock"></i></span><div><span class="user-detail-eyebrow">Account Security</span><h3>Reset Password</h3><p>Create a new password for this account.</p></div></header>
                        <form class="user-password-form" action="<?= site_url("users/reset_password"); ?>" method="post">
                            <div class="form-group">
                                <label for="form_password">New password <sup>REQUIRED</sup></label>
                                <div class="user-password-field"><i class="fas fa-key"></i><input type="password" name="password" class="form-control" id="form_password" placeholder="Enter a new password" required minlength="8" autocomplete="new-password"><button type="button" data-toggle-password="form_password" aria-label="Show password"><i class="fas fa-eye"></i></button></div>
                            </div>
                            <div class="form-group">
                                <label for="form_confirm_password">Confirm password <sup>REQUIRED</sup></label>
                                <div class="user-password-field"><i class="fas fa-check-circle"></i><input type="password" name="confirm_password" class="form-control" id="form_confirm_password" placeholder="Repeat the new password" required minlength="8" autocomplete="new-password"><button type="button" data-toggle-password="form_confirm_password" aria-label="Show password"><i class="fas fa-eye"></i></button></div>
                            </div>
                            <p class="user-password-hint"><i class="fas fa-shield-alt"></i> Use at least 8 characters.</p>
                            <input type="hidden" name="id" value="<?= intval($info->user_id); ?>">
                            <button type="submit" class="user-detail-primary-btn full-width"><i class="fas fa-sync-alt"></i><span>Update Password</span></button>
                        </form>
                    </section>
                </aside>
            </div>
        </div>

        <?php if ($this->user_model->has_perm("assign_user_roles")) { ?>
            <div class="tab-pane fade" id="nav-association" role="tabpanel" aria-labelledby="nav-association-tab">
                <section class="user-detail-card user-role-card">
                    <header class="user-detail-card__header">
                        <span class="user-detail-card__icon tone-blue"><i class="fas fa-user-tag"></i></span>
                        <div><span class="user-detail-eyebrow">Role Assignment</span><h3>Assign User Roles</h3><p>Roles group related permissions and make account access easier to manage.</p></div>
                        <span class="user-detail-context"><i class="fas fa-link"></i><strong id="user-role-panel-count"><?= $assigned_role_count; ?></strong> assigned</span>
                    </header>
                    <form class="user-role-assignment-form" action="<?= site_url("users/assign_roles"); ?>" method="post">
                        <div class="user-role-toolbar">
                            <p><i class="fas fa-mouse-pointer"></i> Click a role to move it between the available and assigned lists.</p>
                            <div>
                                <button type="button" class="user-detail-utility-btn" id="assign-all-roles"><i class="fas fa-angle-double-right"></i><span>Assign All</span></button>
                                <button type="button" class="user-detail-utility-btn is-muted" id="clear-all-roles"><i class="fas fa-angle-double-left"></i><span>Clear Assigned</span></button>
                            </div>
                        </div>
                        <select multiple="multiple" id="roles" name="roles[]">
                            <?php foreach ($this->steve->user_roles() as $user_role) { ?>
                                <option value="<?= intval($user_role->role_id); ?>" <?= (in_array($user_role->role_id, $user_in_roles) ? 'selected' : ''); ?>><?= html_escape($user_role->role_name); ?></option>
                            <?php } ?>
                        </select>
                        <input type="hidden" name="id" value="<?= intval($info->user_id); ?>">
                        <footer class="user-detail-form-actions">
                            <a class="user-detail-secondary-btn" href="<?= site_url('users'); ?>"><i class="fas fa-arrow-left"></i><span>Back to Users</span></a>
                            <button type="submit" class="user-detail-primary-btn"><i class="fas fa-save"></i><span>Save Role Assignment</span></button>
                        </footer>
                    </form>
                </section>
            </div>
        <?php } ?>

        <?php if ($this->user_model->has_perm("user_permissions_override")) { ?>
            <div class="tab-pane fade" id="nav-permissions" role="tabpanel" aria-labelledby="nav-permissions-tab">
                <section class="user-detail-card user-permissions-card">
                    <header class="user-detail-card__header user-permissions-header permissions-header user-override-header">
                        <span class="user-detail-card__icon tone-purple"><i class="fas fa-key"></i></span>
                        <div><span class="user-detail-eyebrow">Fine-grained Access</span><h3>Permission Overrides</h3><p>Grant additional permissions directly to this user without changing their roles.</p></div>
                        <label class="role-permission-search user-override-search" for="permission-search">
                            <i class="fas fa-search"></i>
                            <input type="search" id="permission-search" placeholder="Search permissions..." autocomplete="off">
                        </label>
                    </header>

                    <?php
                    $user_permission_categories = $this->steve->permission_categories();
                    $user_category_permissions = array();
                    foreach ($user_permission_categories as $permission_cat) {
                        $user_category_permissions[$permission_cat->perm_cat_id] = $this->steve->permissions($permission_cat->perm_cat_id);
                    }
                    ?>
                    <form class="user-permission-form permissions role-permission-workspace user-override-workspace" action="<?= site_url("users/assign_permissions"); ?>" method="post">
                        <nav class="role-permission-tabs-wrap user-override-tabs-wrap" aria-label="Permission categories">
                            <div class="nav nav-tabs" id="user-permission-category-tabs" role="tablist">
                                <?php foreach ($user_permission_categories as $index => $permission_cat) { ?>
                                    <a class="nav-item nav-link <?= $index === 0 ? 'active' : ''; ?>" id="permission-category-<?= intval($permission_cat->perm_cat_id); ?>-tab" data-toggle="tab" href="#permission-category-<?= intval($permission_cat->perm_cat_id); ?>" role="tab" aria-controls="permission-category-<?= intval($permission_cat->perm_cat_id); ?>" aria-selected="<?= $index === 0 ? 'true' : 'false'; ?>">
                                        <span><?= html_escape($permission_cat->perm_cat_name); ?></span>
                                        <small><?= count($user_category_permissions[$permission_cat->perm_cat_id]); ?></small>
                                    </a>
                                <?php } ?>
                            </div>
                        </nav>

                        <div class="tab-content role-permission-content user-override-content">
                            <?php foreach ($user_permission_categories as $index => $permission_cat) { ?>
                                <?php $category_permissions = $user_category_permissions[$permission_cat->perm_cat_id]; ?>
                                <section class="tab-pane fade <?= $index === 0 ? 'show active' : ''; ?> user-permission-category" id="permission-category-<?= intval($permission_cat->perm_cat_id); ?>" role="tabpanel" aria-labelledby="permission-category-<?= intval($permission_cat->perm_cat_id); ?>-tab">
                                    <header class="role-permission-section-heading">
                                        <div>
                                            <span class="role-permission-category-icon"><i class="fas fa-folder-open"></i></span>
                                            <div><h4><?= html_escape($permission_cat->perm_cat_name); ?></h4><p>Select the direct access this user needs within this category.</p></div>
                                        </div>
                                        <div class="role-permission-bulk-actions user-permission-category-actions">
                                            <button type="button" data-filter="all" data-id="<?= intval($permission_cat->perm_cat_id); ?>"><i class="fas fa-check-double"></i><span>Select All</span></button>
                                            <button type="button" data-filter="none" data-id="<?= intval($permission_cat->perm_cat_id); ?>"><i class="fas fa-undo-alt"></i><span>Clear</span></button>
                                        </div>
                                    </header>

                                    <div class="role-permission-grid user-permission-grid">
                                        <?php foreach ($category_permissions as $permission) { ?>
                                            <?php
                                            $from_role = in_array($permission->perm_id, $inherited_permissions);
                                            $is_override = in_array($permission->perm_id, $user_permission_overrides);
                                            $permission_label = ucwords(str_replace('_', ' ', $permission->perm_name));
                                            ?>
                                            <label class="role-permission-option user-permission-option <?= $from_role ? 'has-inherited-access' : ''; ?>" for="permission-<?= intval($permission->perm_id); ?>" data-permission-name="<?= html_escape(strtolower($permission_label)); ?>">
                                                <input type="checkbox" value="<?= intval($permission->perm_id); ?>" id="permission-<?= intval($permission->perm_id); ?>" name="permissions[]" <?= $is_override ? 'checked' : ''; ?>>
                                                <span class="role-permission-check user-permission-check"><i class="fas fa-check"></i></span>
                                                <span class="role-permission-name user-permission-name"><strong><?= html_escape($permission_label); ?></strong><small><?= $is_override ? 'Direct override selected' : ($from_role ? 'Also available through assigned role' : 'Click to grant directly'); ?></small></span>
                                            </label>
                                        <?php } ?>
                                    </div>
                                    <div class="role-permission-empty user-permission-category-empty" hidden><i class="fas fa-search"></i><span>No matching permissions in this category.</span></div>
                                </section>
                            <?php } ?>

                            <div class="role-permission-empty user-permission-empty" hidden><i class="fas fa-search"></i><span>No matching permissions. Try a different search term.</span></div>
                        </div>

                        <input type="hidden" name="id" value="<?= intval($info->user_id); ?>">
                        <footer class="role-permission-footer user-override-footer">
                            <div class="user-override-summary">
                                <span><i class="fas fa-sliders-h"></i><strong id="permission-selected-count"><?= $override_count; ?></strong> direct overrides selected</span>
                                <span><i class="fas fa-shield-alt"></i><?= $inherited_count; ?> permissions inherited from assigned roles</span>
                                <span class="user-override-visible"><strong id="permission-visible-count">0</strong> shown</span>
                            </div>
                            <div class="user-override-footer-actions">
                                <a class="user-detail-secondary-btn" href="<?= site_url('users'); ?>"><i class="fas fa-arrow-left"></i><span>Back to Users</span></a>
                                <button type="submit" class="user-detail-primary-btn"><i class="fas fa-save"></i><span>Save Permission Overrides</span></button>
                            </div>
                        </footer>
                    </form>
                </section>
            </div>
        <?php } ?>
    </div>
</main>
