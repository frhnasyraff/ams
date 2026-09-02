<?php /* Not using - 22/08/21
if ( $this->user_model->has_perm("generate_invoices") && count($pending)) { ?>
<div class="card-deck">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Service vouchers pending invoicing</h6>
        </div>
        <div class="card-body">
            <div class="card-deck">
                <?php foreach ($pending as $company_svs) { ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $company_svs[0]->company_name; ?> <a
                                class="small float-right small"
                                href="<?= site_url("companies/info?id=" . $this->steve->id_encode($company_svs[0]->company_id)); ?>"><i
                                    class="fa fa-external-link-alt"></i></a></h6>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" action="<?=site_url("finance/generate_invoice");?>" method="post">
                            <select multiple="multiple" class="service_vouchers" name="service_vouchers[]">
                                <?php foreach ($company_svs as $sv) { ?>
                                <option value="<?= $sv->service_voucher_id; ?>"> <?= $sv->operation_date; ?> -
                                    <?= $sv->shift; ?> - <?= $sv->vessel_name; ?></option>
                                <?php } ?>
                            </select>
                            <small>Select the Service vouchers you would like to combine for the invoice</small>
                            <div class="text-center mb-2">
                                <button class="btn btn-success">Generate invoice</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php } */?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">List of invoices</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="invoices" width="100%" cellspacing="0">
                <thead>
                    <tr>
                    <?php if ($this->user_model->has_perm("delete_invoices")) { ?>
                    <th class="delete_invoice">&nbsp;</th>
                    <?php } ?>
                        <th>Number</th>
                        <th>Customer</th>
                        <th>Value</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>