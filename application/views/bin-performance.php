<style type="text/css">
.pagination>li>a {
    border-radius: 10px;
    /*background-color: #fff !important;*/
    /*color: #fff !important;*/
}

.pagination>.active>a {
    background-color: #073D11 !important;
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
        <span>Bin Sales Performance Summary</span>
    </p>
</div>


    <div class="d-flex align-items-center">
        <div class="form-group mr-3">
            <label for="">Month</label>
            <select name="month" id="month">
                <option value="<?php  $currentMonth = date('F'); echo $currentMonth?>" selected> <?php  $currentMonth = date('F'); echo $currentMonth?>
           
        </option>
                <?php
                $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Auguest', 'September', 'October', 'November', 'December'];
                foreach ($months as $month) { ?>
                <option value="<?= $month ?>" <?= $this->input->get('month') == $month ? 'selected' : '' ?>>
                    <?= $month ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="">Year</label>
            <select name="year" id="year">
                <option value="2024" selected>2024</option>
                <?php
                $years = [2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030, 2031, 2032, 2033];
                foreach ($years as $year) { ?>
                <option value="<?= $year ?>" <?= $this->input->get('year') == $year ? 'selected' : '' ?>><?= $year ?>
                </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group ml-4 pb-1">
            <button class="btn btn-primary btn-sm" id="datefilter">Filter</button>
        </div>
    </div>

    


<section class="project-tab">
    <div class="row">
        <div class="col-12">
            <div class="tab-content">
                <div class="tab-pane show active">
                    <div class="mb-2">
                        <button type="submit" form="downloadForm" class="btn btn-sm btn-primary"><i
                                class="fa fa-download"></i></button>
                        <select id="download_type_select">
                            <option value="pdf">PDF</option>
                            <option value="excel">EXCEL</option>
                        </select>
                    </div>
                    <form action="/Bin_Performance/downloadRecord" id="downloadForm" method="POST">
                        <input type="hidden" name="download_type" id="downlaod-type" value="pdf">

                        <div class="table-responsive">
                        <table class="table" id="bin-table" cellspacing="0">
                            <thead>
                                <tr>
                                <th><a href="javascript:void(0)" class="text-primary" id="select_all_checkboxes" >Select All</a></th>

                                    <th><a href="<?= sortColumnNew('customer_name', 'Bin_Performance') ?>">Customer Name<?= sortColumnIcon('customer_name') ?></a></th>
                                    <th><a href="<?= sortColumnNew('asset_type', 'Bin_Performance') ?>">Asset Type<?= sortColumnIcon('asset_type') ?></a></th>
                                    <th><a href="<?= sortColumnNew('service_date', 'Bin_Performance') ?>">Service Date<?= sortColumnIcon('service_date') ?></a></th>
                                    <th><a href="<?= sortColumnNew('total_qr_codes', 'Bin_Performance') ?>">Total Qty Sold<?= sortColumnIcon('total_qr_codes') ?></a></th>
                                    <th><i class="fa fa-eye"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
<!-- ORDER BIN QR MODAL -->
<div class="modal fade orderModal" tabindex="-1"
                                                id="OrderBinQRModal" data-backdrop="static">
                                                <div class="modal-dialog modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            Registration Numbers
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                           <!-- Data From Ajax -->
                                                            <div id="bin-ajax-response" class="mt-3"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                        

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>