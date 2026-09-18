<a class="btn" href="#addModal" data-toggle="modal" data-target="#addModal" title="Upload logo image"><i class="fas fa-upload" aria-hidden="true"></i> Upload Image</a>

<section class="card mb-4 logo-manager" id="logo-image-panel" data-logo-count="<?= empty($image_path) ? 0 : 1; ?>">
    <div class="card-header"><h2>Current Logo</h2></div>
    <div class="card-body logo-layout">
        <div class="logo-preview-panel">
            <div class="logo-preview-heading"><span>Image Preview</span><span class="logo-preview-tag"><?= empty($image_path) ? 'Not Set' : 'Current'; ?></span></div>
            <div class="logo-preview-stage">
                <?php if (!empty($image_path)): ?>
                    <img class="logo-preview-image" src="<?= htmlspecialchars(base_url($image_path), ENT_QUOTES, 'UTF-8'); ?>" alt="Current organisation logo">
                <?php else: ?>
                    <div class="logo-empty"><i class="far fa-image" aria-hidden="true"></i><p>No logo uploaded yet</p><span>Upload an image to personalise the application header.</span></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="logo-details">
            <span class="logo-eyebrow">System Appearance</span>
            <h2><?= empty($image_path) ? 'Add Your Organisation Logo' : 'Organisation Logo'; ?></h2>
            <p>This image is displayed in the application header.</p>
            <?php if (!empty($image_path)): ?>
                <div class="logo-file-info"><i class="far fa-file-image" aria-hidden="true"></i><div><span>Current File</span><strong><?= htmlspecialchars(basename($image_path), ENT_QUOTES, 'UTF-8'); ?></strong></div></div>
            <?php endif; ?>
            <div class="logo-actions">
                <button type="button" class="logo-action logo-action-edit" data-toggle="modal" data-target="#addModal"><i class="fas <?= empty($image_path) ? 'fa-upload' : 'fa-pen'; ?>" aria-hidden="true"></i> <?= empty($image_path) ? 'Upload Image' : 'Replace Image'; ?></button>
                <?php if (!empty($image_path)): ?>
                    <form action="<?= site_url('LogoImage/delete'); ?>" method="post">
                        <input type="hidden" name="image_path" value="<?= htmlspecialchars($image_path, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="logo-action logo-action-delete" onclick="return confirm('Are you sure you want to delete this image?');"><i class="fas fa-trash-alt" aria-hidden="true"></i> Delete</button>
                    </form>
                <?php endif; ?>
            </div>
            <p class="logo-help">Supported formats: JPG, PNG and GIF.</p>
        </div>
    </div>
</section>

<div class="modal fade logo-upload-modal" tabindex="-1" role="dialog" id="addModal" aria-labelledby="logo-upload-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logo-upload-title">Upload Logo Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="<?= site_url('LogoImage/add'); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <p>Select an image for your organisation's logo. Uploading a new image replaces the current logo.</p>
                    <div class="logo-file-picker">
                        <label for="logoImage"><i class="fas fa-cloud-upload-alt" aria-hidden="true"></i> Select Image</label>
                        <input type="file" name="logoImage" id="logoImage" class="form-control" accept="image/jpeg,image/png,image/gif" required aria-describedby="logo-format-help">
                        <small id="logo-format-help">JPG, PNG or GIF. A clear image with a transparent background works well.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="logo-cancel" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn logo-upload-submit"><i class="fas fa-upload" aria-hidden="true"></i> Upload Image</button>
                </div>
            </form>
        </div>
    </div>
</div>
