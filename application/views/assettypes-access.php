<section class="ams-access-denied" aria-labelledby="assettypes-access-title">
    <span class="ams-access-icon"><i class="fas fa-lock" aria-hidden="true"></i></span>
    <span class="ams-eyebrow">Master data / Asset types</span>
    <h1 id="assettypes-access-title">Asset Types access required</h1>
    <p>This page defines asset categories and their maintenance requirements. Asset Type Colors only controls display colors.</p>
    <p>Your account does not currently have permission to open this page. Ask an administrator to check your assigned role.</p>
    <div class="ams-access-help"><strong>Administrator setup</strong><p>Enable <code>list_assettypes</code>, <code>add_assettypes</code> and <code>edit_assettypes</code> for the Admin role. If these permissions are missing, apply <code>patch_assettypes_admin_access.sql</code>.</p></div>
    <div class="ams-access-actions">
        <?php if ($this->user_model->has_perm('list_user_roles')): ?><a class="btn btn-primary" href="<?= site_url('user_roles') ?>">Open User Roles</a><?php endif; ?>
        <a class="btn btn-outline-info" href="<?= site_url('order_summary') ?>">Back to Summary</a>
    </div>
</section>
