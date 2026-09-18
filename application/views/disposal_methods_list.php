<?php $isEditing = $mode === 'edit' && !empty($method); ?>
<div class="container-fluid dm-workspace" id="disposal-method-workspace">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success" role="status"><?= htmlspecialchars($this->session->flashdata('success'), ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <div class="dm-layout">
        <section class="dm-card dm-list" aria-labelledby="dm-list-title">
            <div class="dm-card-heading">
                <div><span class="dm-eyebrow">Reference List</span><h2 id="dm-list-title">Approved Methods</h2></div>
                <span class="dm-count"><?= count($methods ?? []); ?> methods</span>
            </div>
            <div class="table-responsive dm-table-scroll">
                <table class="table table-borderless table-hover dm-table">
                    <thead><tr><th>ID</th><th>Method Name</th><th>Description</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php if (!empty($methods)): ?>
                        <?php foreach ($methods as $m): ?>
                        <tr>
                            <td class="dm-id"><?= (int) $m->id; ?></td>
                            <td class="dm-name"><?= htmlspecialchars($m->disposal_method, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="dm-description"><?= htmlspecialchars(($m->description ?? '') ?: '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><div class="dm-row-actions">
                                <a href="<?= site_url('DisposalMethod/index/edit/'.(int) $m->id); ?>" class="dm-action dm-action-edit" title="Edit disposal method" aria-label="Edit"><i class="fas fa-pen" aria-hidden="true"></i> Edit</a>
                                <a href="<?= site_url('DisposalMethod/delete/'.(int) $m->id); ?>" class="dm-action dm-action-delete" title="Delete disposal method" aria-label="Delete" onclick="return confirm('Delete this record?')"><i class="fas fa-trash-alt" aria-hidden="true"></i> Delete</a>
                            </div></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="dm-empty">No disposal methods yet. Add your first method using the form.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="dm-card dm-form-card" aria-labelledby="dm-form-title">
            <div class="dm-card-heading">
                <div><span class="dm-eyebrow"><?= $isEditing ? 'Update Record' : 'New Record'; ?></span><h2 id="dm-form-title"><?= $isEditing ? 'Edit Disposal Method' : 'Add Disposal Method'; ?></h2></div>
            </div>
            <form class="dm-form" action="<?= site_url('DisposalMethod/save'); ?>" method="POST">
                <input type="hidden" name="id" value="<?= $isEditing ? (int) $method->id : ''; ?>">
                <div class="form-group">
                    <label for="dm-method-name">Method Name <span class="dm-required">Required</span></label>
                    <input id="dm-method-name" type="text" name="disposal_method" class="form-control" required placeholder="e.g. Recycling" value="<?= $isEditing ? htmlspecialchars($method->disposal_method, ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="dm-description">Description <span class="dm-optional">Optional</span></label>
                    <textarea id="dm-description" name="description" class="form-control" rows="4" placeholder="Describe when this method should be used."><?= $isEditing ? htmlspecialchars($method->description ?? '', ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
                </div>
                <div class="dm-form-actions">
                    <button type="submit" class="btn dm-save"><i class="fas fa-save" aria-hidden="true"></i> <?= $isEditing ? 'Save Changes' : 'Add Method'; ?></button>
                    <?php if ($isEditing): ?><a href="<?= site_url('DisposalMethod'); ?>" class="dm-cancel">Cancel</a><?php endif; ?>
                </div>
            </form>
        </section>
    </div>
</div>
