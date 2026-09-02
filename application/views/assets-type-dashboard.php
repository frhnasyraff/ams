<style>
/* Make all columns same height */
#asset-card-container {
    display: flex;
    flex-wrap: wrap;
}

/* Make each column a flex container */
.asset-card {
    display: flex;
    flex-direction: column;
}

/* Make all cards the same height */
.custom-card {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-height: 100%; /* Ensures full height */
}

/* Make card body fill remaining space */
.card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

</style>

<div class="asset-type-dashboard-page">
<div class="asset-dashboard-toolbar row mb-4">
    <div class="col-md-6">
        <div class="form-inline">
            <label class="mr-2" for="recordsPerPage">Show:</label>
            <select class="form-control form-control-sm" id="recordsPerPage">
                <option value="12">12</option>
                <option value="24">24</option>
                <option value="36">36</option>
                <option value="all">All</option>
            </select>
            <span class="ml-2">records</span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-inline float-right">
            <label class="mr-2" for="searchInput">Search Asset:</label>
            <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Enter asset type">
        </div>
    </div>
</div>

<div class="asset-dashboard-grid row" id="asset-card-container">
    <?php foreach ($this->steve->assets_type_dashboard() as $t): ?>
        <?php // Legacy direct status keys were replaced with null-coalescing fallbacks because missing statuses caused warnings. ?>
        <div class="col-md-3 mb-4 asset-card" data-asset-type="<?= strtolower($t['asset_type']); ?>" data-status-serviceable="<?= (($t['statuses']['SERVICEABLE'] ?? 0) > 0) ? 'true' : 'false' ?>" data-status-unserviceable="<?= (($t['statuses']['UNSERVICEABLE'] ?? 0) > 0) ? 'true' : 'false' ?>" data-status-maintenance="<?= (($t['statuses']['MAINTENANCE'] ?? 0) > 0) ? 'true' : 'false' ?>">

            <div class="custom-card shadow">
                <?php
                $pictureFilename = $t['asset_picture'] ?? '';
                $assetId = $t['asset_id'];
                $relativePath = 'storage/AssetType-' . $assetId . '/' . $pictureFilename;
                $absolutePath = FCPATH . $relativePath;

                $isThumbnail = empty($pictureFilename) || !file_exists($absolutePath);
                $backgroundUrl = $isThumbnail
                    ? base_url('design/img/thumbnail.png')
                    : base_url($relativePath);
                $backgroundClass = $isThumbnail ? 'asset-thumbnail-background' : 'asset-image-background';
                ?>


                <div class="asset-card-icon"><i class="fas fa-truck-loading"></i></div>
                <form method="post"
                    action="<?= site_url("assets_type_dashboard/asset_type_picture"); ?>"
                    class="dropzone <?= $backgroundClass ?>"
                    style="background-image: url('<?= $backgroundUrl ?>');">

                    <button type="button" class="asset-upload-pill" onclick="this.closest('form').querySelector('input[type=file]').click();"><i class="fas fa-cloud-upload-alt"></i> Upload</button>
                    <input type="hidden" name="id" readonly value="<?= $assetId; ?>">
                    <div class="fallback">
                        <input name="file" type="file" accept="image/*" />
                    </div>
                </form>


                <a href="<?= site_url('assets?type_filter=' . urlencode($t['asset_id'])) ?>" class="d-block text-decoration-none">
                    <div class="card-body text-center">
                        <h5 class="card-title main-title"><?= $t['asset_type']; ?></h5>
                        <!-- Legacy list layout replaced with 2x2 metric boxes to match requested dashboard card style.
                        <ul class="list-unstyled stats-list">
                            <li><span class="total-text">Total</span>: <?= $t['total']; ?></li>
                            <li><span class="serviceable-text">Serviceable</span>: <?= $t['statuses']['SERVICEABLE'] ?? 0; ?></li>
                            <li><span class="unserviceable-text">Unserviceable</span>: <?= $t['statuses']['UNSERVICEABLE'] ?? 0; ?></li>
                            <li><span class="maintenance-text">Maintenance</span>: <?= $t['statuses']['MAINTENANCE'] ?? 0; ?></li>
                        </ul>
                        -->
                        <div class="asset-metric-grid">
                            <div class="asset-metric-box"><span class="metric-icon total-text"><i class="fas fa-chart-bar"></i></span><span class="metric-label">Total</span><strong><?= $t['total']; ?></strong></div>
                            <div class="asset-metric-box"><span class="metric-icon serviceable-text"><i class="fas fa-check-circle"></i></span><span class="metric-label">Serviceable</span><strong><?= $t['statuses']['SERVICEABLE'] ?? 0; ?></strong></div>
                            <div class="asset-metric-box"><span class="metric-icon unserviceable-text"><i class="fas fa-times-circle"></i></span><span class="metric-label">Unserviceable</span><strong><?= $t['statuses']['UNSERVICEABLE'] ?? 0; ?></strong></div>
                            <div class="asset-metric-box"><span class="metric-icon maintenance-text"><i class="fas fa-wrench"></i></span><span class="metric-label">Maintenance</span><strong><?= $t['statuses']['MAINTENANCE'] ?? 0; ?></strong></div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    <?php endforeach; ?>
</div>

<div class="asset-dashboard-pagination text-center mt-3">
    <ul id="pagination" class="pagination justify-content-center">
        <li class="page-item disabled" id="previousPage">
            <a class="page-link" href="#" aria-label="Previous">
                <span>Previous</span>
            </a>
        </li>
        <li class="page-item disabled" id="nextPage">
            <a class="page-link" href="#" aria-label="Next">
                <span>Next</span>
            </a>
        </li>
    </ul>
</div>
</div>
