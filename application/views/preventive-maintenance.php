<style>
    .section-title {
        color: #2c3e50;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .table th {
        background-color: #2c3e50;
        color: #ffffff;
    }

    .table tbody tr:hover {
        background-color: #f2f4f7;
    }
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



<div class="asset-type-dashboard-page preventive-maintenance-page">
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

                <div class="asset-dashboard-grid row preventive-dashboard-grid" id="asset-card-container">
                    <?php foreach ($this->steve->preventive_dashboard() as $t): ?>
                        <?php
                        // Normalize new status keys to old frontend labels
                        $complete     = $t['statuses']['complete'] ?? 0;
                        $pending   = $t['statuses']['Pending'] ?? 0;
                        $maintenance     = $t['statuses']['Maintenance'] ?? 0;
                        ?>
                        <div class="col-md-3 mb-4 asset-card"
                            data-asset-type="<?= strtolower($t['asset_type']); ?>"
                            data-status-serviceable="<?= $complete > 0 ? 'true' : 'false' ?>"
                            data-status-unserviceable="<?= $pending > 0 ? 'true' : 'false' ?>"
                            data-status-maintenance="<?= $maintenance > 0 ? 'true' : 'false' ?>">

                            <div class="custom-card shadow">
                                <?php
                                $assetId = $t['asset_id'];
                                $assetPicture = $t['asset_picture'] ?? '';
                                $relativePath = 'storage/AssetType-' . $assetId . '/' . $assetPicture;
                                $absolutePath = FCPATH . $relativePath;

                                $isThumbnail = empty($assetPicture) || !file_exists($absolutePath);
                                $backgroundUrl = $isThumbnail
                                    ? base_url('design/img/thumbnail.png')
                                    : base_url($relativePath);
                                $backgroundClass = $isThumbnail
                                    ? 'asset-thumbnail-background'
                                    : 'asset-image-background';
                                ?>


                                <div class="asset-card-icon"><i class="fas fa-truck-loading"></i></div>
                                <form method="post"
                                    action="<?= site_url("assets_type_dashboard/asset_type_picture"); ?>"
                                    class="dropzone <?= $backgroundClass ?>"
                                    style="background-image: url('<?= $backgroundUrl ?>');">

                                    <?php /* old upload button inside dropzone commented: <button type="button" class="asset-upload-pill" onclick="this.closest('form').querySelector('input[type=file]').click();"><i class="fas fa-cloud-upload-alt"></i> Upload</button> */ ?>
                                    <input type="hidden" name="id" readonly value="<?= $assetId; ?>">
                                    <div class="fallback">
                                        <input name="file" type="file" accept="image/*" />
                                    </div>
                                </form>
                                <button type="button" class="asset-upload-pill preventive-upload-trigger" onclick="event.preventDefault(); event.stopPropagation(); var dz=this.parentElement.querySelector('.dropzone'); if(dz && dz.dropzone && dz.dropzone.hiddenFileInput){ dz.dropzone.hiddenFileInput.click(); } else { var fi=this.parentElement.querySelector('input[type=file]'); if(fi){ fi.click(); } } return false;"><i class="fas fa-cloud-upload-alt"></i> Upload</button>


                                <a href="#" class="d-block text-decoration-none apply-type-filter" data-type-filter="<?= $t['asset_id'] ?>">

                                    <div class="asset_id_filter" style="display:none;">
                                        <button class="btn btn-primary" data-filter="">Filter</button>
                                    </div>

                                    <div class="card-body text-center">
                                        <h5 class="card-title main-title"><?= $t['asset_type']; ?></h5>
                                        <div class="asset-metric-grid preventive-metric-grid">
                                            <div class="asset-metric-box">
                                                <span class="metric-icon total-text"><i class="fas fa-chart-bar"></i></span>
                                                <span class="metric-label">Total</span>
                                                <strong><?= $t['total']; ?></strong>
                                            </div>
                                            <div class="asset-metric-box">
                                                <span class="metric-icon serviceable-text"><i class="fas fa-check-circle"></i></span>
                                                <span class="metric-label">Complete</span>
                                                <strong><?= $complete; ?></strong>
                                            </div>
                                            <div class="asset-metric-box">
                                                <span class="metric-icon unserviceable-text"><i class="fas fa-times-circle"></i></span>
                                                <span class="metric-label">Pending</span>
                                                <strong><?= $pending; ?></strong>
                                            </div>
                                            <div class="asset-metric-box">
                                                <span class="metric-icon maintenance-text"><i class="fas fa-wrench"></i></span>
                                                <span class="metric-label">Maintenance</span>
                                                <strong><?= $maintenance; ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>


                <div class="text-center mt-3">
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
                <div class="modal fade" id="preventiveModal" tabindex="-1" aria-labelledby="preventiveModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table" id="preventive" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Equipment Name</th>
                                                <th>Location</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
</div>


