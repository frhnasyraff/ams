<style type="text/css">
    .pagination>li>a {
        border-radius: 10px;
    }

    .pagination>.active>a {
        background-color: #07073dff !important;
    }

    #orders_next>a {
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    #orders_previous>a {
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }

    .hidden {
        display: none;
    }
</style>
<div class="header-second">
    <p>
        <i class="far fa-calendar"></i>
        <span>Asset Deployed</span>
    </p>

    <form action="<?= site_url('asset_performance/index') ?>" method="GET">
        <div class="input-group">
            <input type="date" name="date" class="form-control" value="<?= $this->input->get('date') ?>">
            <button class="btn btn-primary rounded-0 btn-sm" type="submit">Search</button>
        </div>
    </form>
</div>

<form class="mb-3" action="<?= site_url('asset_performance/index') ?>" id="performance-form" method="GET">
    <div class="d-flex align-items-center">
        <div class="form-group mr-3">
            <label for="">Month</label>
            <select name="month" onchange="document.getElementById('performance-form').submit();">
                <option value="">All</option>
                <?php
                $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                foreach ($months as $month) { ?>
                    <option value="<?= $month ?>" <?= $this->input->get('month') == $month ? 'selected' : '' ?>><?= $month ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="">Year</label>
            <select name="year" onchange="document.getElementById('performance-form').submit();">
                <option value="">All</option>
                <?php
                $years = range(2022, 2033);
                foreach ($years as $year) { ?>
                    <option value="<?= $year ?>" <?= $this->input->get('year') == $year ? 'selected' : '' ?>><?= $year ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</form>

<section class="project-tab">
    <div class="row">
        <div class="col-12">
            <div class="tab-content">
                <div class="tab-pane show active">
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-primary" id="downloadBtn"><i class="fa fa-download"></i></button>
                        <select id="download_type_select">
                            <option value="pdf">PDF</option>
                            <option value="excel">EXCEL</option>
                        </select>
                    </div>
                    <form action="/asset_performance/downloadRecord" id="downloadForm" method="POST">
                        <input type="hidden" name="download_type" id="download-type" value="pdf">
                        <input type="hidden" name="date" value="<?= $this->input->get('date') ?>">
                        <input type="hidden" name="month" value="<?= $this->input->get('month') ?>">
                        <input type="hidden" name="year" value="<?= $this->input->get('year') ?>">

                        <div class="table-responsive">
                            <table class="table" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th><a href="javascript:void(0)" class="text-primary" id="select_all_checkboxes" <?= count($assets) == 0 ? 'disabled' : '' ?>>Select All</a></th>
                                        <th>#</th>
                                        <th><a href="<?= sortColumnNew('asset_type', 'Asset_Performance') ?>">Asset Type<?= sortColumnIcon('asset_type') ?></a></th>
                                        <th><a href="<?= sortColumnNew('total_qr_codes', 'Asset_Performance') ?>">Quantity<?= sortColumnIcon('total_qr_codes') ?></a></th>
                                        <th>Registration</th>
                                        <th><a href="<?= sortColumnNew('date_deployed', 'Asset_Performance') ?>">Date Deployed<?= sortColumnIcon('date_deployed') ?></a></th>
                                        <th><a href="<?= sortColumnNew('customer_name', 'Asset_Performance') ?>">Customer Name<?= sortColumnIcon('customer_name') ?></a></th>
                                        <th><a href="<?= sortColumnNew('customer_location', 'Asset_Performance') ?>">Customer Location<?= sortColumnIcon('customer_location') ?></a></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($assets) > 0) {
                                        foreach ($assets as $key => $asset) { ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="checkbox-select" name="record[]" value="<?= urlencode(json_encode([
                                                                                                                                'asset_type_name' => $asset->asset_type_name,
                                                                                                                                'total_qr_codes' => $asset->total_qr_codes,
                                                                                                                                'created_at' => $asset->created_at,
                                                                                                                                'company_name' => $asset->company_name,
                                                                                                                                'address_line_1' =>  $asset->address_line_1
                                                                                                                            ])) ?>" />
                                                </td>
                                                <td><?= $key + 1 ?></td>
                                                <td><?= $asset->asset_type_name ?></td>
                                                <td><?= $asset->total_qr_codes ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#OrderBinQRModal<?= $key ?>"><i class="fa fa-eye"></i></button>
                                                    <div class="modal fade orderModal" tabindex="-1" id="OrderBinQRModal<?= $key ?>" data-backdrop="static">
                                                        <div class="modal-dialog modal-dialog-scrollable">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    Registration Numbers
                                                                    <button type="button" class="close" data-dismiss="modal">
                                                                        <span>&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <?php
                                                                    $bin_qr_codes = $this->db->select('order_equipment_bin_qr_codes.reg_no')
                                                                        ->from('order_equipment_bin_qr_codes')
                                                                        ->join('orders', 'order_equipment_bin_qr_codes.order_id = orders.order_id')
                                                                        ->join('companies', 'orders.company_id=companies.company_id')
                                                                        ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id')
                                                                        ->join('asset_types', 'asset_types.asset_id = order_equipment_bin_qr_codes.asset_type_id')
                                                                        ->where('orders.company_address_id', $asset->company_address_id)
                                                                        ->get()
                                                                        ->result();

                                                                    foreach ($bin_qr_codes as $j => $bin) {
                                                                    ?>
                                                                        <div class='input'>
                                                                            <label for=''> <?= ($j + 1) ?> Asset Regno</label>
                                                                            <div class='bin_qr_code'>
                                                                                <div class='qr_code'>
                                                                                    <input type='text' class='field' value='<?= $bin->reg_no ?>' readonly>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= $asset->created_at ?></td>
                                                <td><?= $asset->company_name ?></td>
                                                <td><?= $asset->address_line_1 ?></td>
                                            </tr>
                                    <?php }
                                    } else {
                                        echo "<tr><td colspan='100%' class='text-center'>No records found</td></tr>";
                                    } ?>
                                    <tr>
                                        <td>
                                            <a id="prevLink" href="#" class="btn-sm btn-primary previous-btn">Prev</a>
                                            <a id="nextLink" href="#" class="btn-sm btn-primary next-btn">Next</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select id="limit-asset" name="limit-asset" class="select-box" onchange="changeLimit()">
                                                <option value="10" <?= $_GET['limit'] == 10 ? 'selected' : '' ?>>10</option>
                                                <option value="30" <?= $_GET['limit'] == 30 ? 'selected' : '' ?>>30</option>
                                                <option value="50" <?= $_GET['limit'] == 50 ? 'selected' : '' ?>>50</option>
                                                <option value="100" <?= $_GET['limit'] == 100 ? 'selected' : '' ?>>100</option>
                                            </select>
                                            <a href="#" onclick="goToPage(0)" class="btn-sm btn-danger go-btn">Go</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function getQueryParams() {
        const urlParams = new URLSearchParams(window.location.search);
        return {
            order: urlParams.get('order') || 'planned',
            search: urlParams.get('search') || '',
            startdate: urlParams.get('startdate') || '',
            enddate: urlParams.get('enddate') || '',
            ordertype: urlParams.get('ordertype') || '',
            limit: urlParams.get('limit') || '10',
            offset: parseInt(urlParams.get('offset')) || 0
        };
    }

    function updateLinks() {
        const params = getQueryParams();
        const prevOffset = Math.max(0, params.offset - parseInt(params.limit));
        const nextOffset = params.offset + parseInt(params.limit);

        document.getElementById('prevLink').href = `?order=${params.order}&search=${params.search}&startdate=${params.startdate}&enddate=${params.enddate}&ordertype=${params.ordertype}&limit=${params.limit}&offset=${prevOffset}`;
        document.getElementById('nextLink').href = `?order=${params.order}&search=${params.search}&startdate=${params.startdate}&enddate=${params.enddate}&ordertype=${params.ordertype}&limit=${params.limit}&offset=${nextOffset}`;
    }

    function goToPage(newOffset) {
        const params = getQueryParams();
        console.log('goToPage called with newOffset:', newOffset); // Debug log
        console.log('Current params:', params); // Debug log
        window.location.href = `?order=${params.order}&search=${params.search}&startdate=${params.startdate}&enddate=${params.enddate}&ordertype=${params.ordertype}&limit=${params.limit}&offset=${newOffset}`;
    }

    function changeLimit() {
        const params = getQueryParams();
        params.limit = document.getElementById('limit-asset').value;
        params.offset = 0; // Reset offset when limit changes
        updateURL(params);
    }

    function updateURL(params) {
        console.log('updateURL called with params:', params); // Debug log
        const newUrl = `?order=${params.order}&search=${params.search}&startdate=${params.startdate}&enddate=${params.enddate}&ordertype=${params.ordertype}&limit=${params.limit}&offset=${params.offset}`;
        window.location.href = newUrl;
    }

    // Initialize links on page load
    updateLinks();

    // Handle download button click
    document.getElementById('downloadBtn').addEventListener('click', function() {
        var downloadType = document.getElementById('download_type_select').value;
        document.getElementById('download-type').value = downloadType;
        document.getElementById('downloadForm').submit();
    });
</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
