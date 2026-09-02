<div class="header-second">
    <p>
        <i class="far fa-calendar"></i>
        <span>Asset In Use Summary</span>
    </p>

    <form action="<?= site_url('driver_performance/index') ?>" method="GET" id="performance-form">
        <div class="d-flex align-content-center">
            <select name="branch" class="form-select mr-3"
                onchange="document.getElementById('performance-form').submit();">
                <option value="">All</option>
                <?php foreach ($branches as $branch) { ?>
                <option value="<?= $branch->branch_id ?>"
                    <?= $this->input->get('branch') == $branch->branch_id ? 'selected' : '' ?>>
                    <?= $branch->branch_name ?></option>
                <?php } ?>
            </select>
            <div class="input-group">
                <!-- <input type="date" name="date" class="form-control" value="<?= $_GET['date'] ?>"> -->
                <button class="btn btn-primary rounded-0 btn-sm" type="submit">Search</button>
            </div>
        </div>
    </form>
</div>

<section class="project-tab">
    <div class="row">
        <div class="col-12">
            <div class="tab-content">
                <div class="tab-pane show active">
                    <!-- <div class="mb-2">
                        <button type="submit" form="downloadForm" id="downloadPdfBtn" class="btn btn-sm btn-primary"><i
                                class="fa fa-download"></i></button>
                        <select id="download_type_select">
                            <option value="pdf">PDF</option>
                            <option value="excel">EXCEL</option>
                        </select>
                    </div> -->
                    <form action="/AssetMaintenance_summary/downloadRecord" id="downloadForm" method="POST">
                    <div class="mb-2">
                        <button type="submit" form="downloadForm" id="downloadPdfBtn" class="btn btn-sm btn-primary"><i
                                class="fa fa-download"></i></button>
                        <select id="download_type_select" name="download_type">
                            <option value="pdf">PDF</option>
                            <option value="excel">EXCEL</option>
                        </select>
                        
                    </div>
                        <input type="hidden" name="download_type" id="download-type" value="pdf">
                        <div class="table-responsive">
                            <table class="table" id="assetMaintenance_summary" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>
                                            <a href="javascript:void(0)" class="text-primary" id="select_all_checkboxes"
                                                <?= count($workers) == 0 ? 'disabled' : '' ?>>Select All</a>
                                        </th>
                                        <th>Equipment Name</th>
                                        <th>Equipment Type</th>
                                        <th>Maintenance Cost</th>
                                        <th>Maintenance Date</th>
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
</section>