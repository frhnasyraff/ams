<!-- <div class="row">
<div class="col-md-12">
    <?php if (!empty($alertMessage)): ?>
    <div class="alert alert-danger d-flex align-items-center" role="alert">
        <span class="fas fa-exclamation-triangle mr-4"></span>
        <div>
            <?php echo $alertMessage; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($itemalertMessage)): ?>
    <div class="alert alert-danger d-flex align-items-center" role="alert">
        <span class="fas fa-exclamation-triangle mr-4"></span>
        <div>
            <?php echo $itemalertMessage; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
</div> -->


<div class="calibration-workspace-page">
<section class="calibration-hero">
    <div class="calibration-hero-icon"><i class="fas fa-sliders-h"></i></div>
    <div class="calibration-hero-copy">
        <span>Compliance workspace</span>
        <h2>Calibration Control Centre</h2>
        <p>Review equipment due for calibration and complete updates from one focused queue.</p>
    </div>
    <div class="calibration-live-state"><i class="fas fa-circle"></i> Live schedule</div>
</section>

<section class="calibration-summary-grid" aria-live="polite">
    <article class="calibration-summary-card calibration-summary-due">
        <i class="fas fa-exclamation-circle"></i>
        <div><span>Due for action</span><strong id="calibration-due-count">0</strong><small>Within reminder window</small></div>
    </article>
    <article class="calibration-summary-card calibration-summary-scope">
        <i class="fas fa-box"></i>
        <div><span>Current scope</span><strong id="calibration-current-scope">Assets</strong><small>Switch using the tabs below</small></div>
    </article>
    <article class="calibration-summary-card calibration-summary-cycle">
        <i class="fas fa-sync-alt"></i>
        <div><span>Workflow</span><strong>Review &amp; Complete</strong><small>Updates are recorded instantly</small></div>
    </article>
</section>

<nav class="calibration-tabs-shell" aria-label="Calibration type">
    <div class="nav nav-tabs calibration-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="asset_faulty" data-toggle="tab" href="#nav-details" role="tab"
            aria-controls="nav-details" aria-selected="true"><i class="fas fa-box"></i> Asset Calibration</a>


        <a class="nav-item nav-link" id="item_faulty" data-toggle="tab" href="#nav-qr" role="tab"
            aria-controls="nav-fuel" aria-selected="false"><i class="fas fa-cubes"></i> Component Calibration</a>

    </div>
</nav>


<section class="project-tab calibration-content">
    <div class="row">
        <div class="col-12">
            <div class="tab-content">
                <div class="tab-pane show active">

                    <div class="calibration-table-card">
                    <div class="calibration-table-heading">
                        <div>
                            <span>Calibration queue</span>
                            <h3 id="calibration-queue-title">Assets Requiring Attention</h3>
                        </div>
                        <p>Records shown here have entered their reminder window.</p>
                    </div>
                    <form action="" id="downloadForm" method="POST">
                        <input type="hidden" name="download_type" id="download-type" value="pdf">
                        <div class="table-responsive">
                            <table class="table" id="faulty_summary" cellspacing="0">
                                <thead>
                                    <tr>

                                        <th>System Name</th>
                                        <th>Asset / Component Type</th>
                                        <th>Calibration Date</th>
                                        <th>Frequency</th>
                                        <th>Reminder</th>
                                        <th>Update Calibration</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows will be inserted dynamically here -->
                                </tbody>
                            </table>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calibration Modal -->

    <div class="modal fade calibration-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-check-circle"></i> Complete Calibration</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="calibrationForm">
                        <label for="calibration_date_edit">Calibration Date</label>
                        <input type="date" class="form-control" name="calibration_date" id="calibration_date_edit">
                        <input type="hidden"  id="edit_id" >
                        <input type="hidden" id="type">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn calibration-cancel-btn" data-dismiss="modal">Cancel</button>
                    <button type="button" id="complete-calibration" class="btn btn-primary"><i class="fas fa-check"></i> Complete</button>
                </div>
            </div>
        </div>
    </div>


   
</section>
</div>
