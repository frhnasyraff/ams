<div class="row">
    <div class="col-md-9 row">
        <?php foreach ($worker_groups as $group) { ?>
        <div class="col-sm-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $group->worker_group_name; ?></h6>
                </div>
                <div class="card-body row">
                    <?php if (!$group->payroll_start) { ?>
                    <div class="alert alert-info m-4">Payroll start date not set for this group. <a
                            href="<?= site_url("worker_groups/info?id=" . $this->steve->id_encode($group->user_group_id)); ?>">Go
                            to worker group</a></div>
                    <?php } else { 
                for($i=1; $i<=12;$i++) { 
                    $start = strtotime(date("Y-m-" . $group->payroll_start) . " -" . $i . " months");
                    $end = strtotime(date("Y-m-d", $start) . " +1 months -1 days");
                    ?>
                    <div class="col-md-4 col-lg-6 mb-2">
                        <div class="card p-1">
                            <strong><?= date("F y", $end); ?>
                                <?php if ($this->user_model->has_perm("download_payroll")) { ?><a class="float-right"
                                    href="<?= site_url("finance/payroll_spreadsheet?group=" . $group->worker_group_id . "&start=" . date("Y-m-d", $start)); ?>"><i
                                        class="fa fa-download"></i></a><?php } ?></strong>
                            <small><?= date("d-m-y", $start); ?> -
                                <?= date("d-m-y", $end); ?></small>
                        </div>
                    </div>
                    <?php } } ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="col-md-3">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Detailed payroll by month</h6>
            </div>
            <div class="card-body">
            <div class="panel-group" id="accordion">
                    <?php for($i=0; $i<=11;$i++) { 
                        $start = strtotime(date("Y-m-1") . " -" . $i . " months");
                    ?>
                <div class="panel panel-default acc-panel">
                    <div class="panel-heading">
                        <h4 class="panel-title card-body row">
                            <a class="acr" data-toggle="collapse" data-parent="#accordion" href="#collapseOne<?= $start ; ?>">
                                <span class="glyphicon glyphicon-folder-close">
                                <strong class=""><?= date("F y", $start); ?></strong></span></a>
                        </h4>
                    </div>
                    <div id="collapseOne<?= $start ; ?>" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <table class="table">
                            <?php foreach ($worker_teams as $team) { ?>
    
                                <tr>
                                    <td>
                                        <span class="glyphicon glyphicon-pencil text-primary">
                                        <?= $team->worker_group_name; ?>

                                        <?php if ($this->user_model->has_perm("download_payroll")) { ?><a class="float-right"
                                            href="<?= site_url("finance/detailed_payroll_spreadsheet?team=".$team->worker_group_id."&start=" . date("Y-m-d", $start)); ?>"><i
                                                class="fa fa-download"></i></a><?php } ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php }?> 
                            </table>
                        </div>
                    </div>
                </div>
                
                <?php } ?>
            </div>
               
            </div>
        </div>
    </div>
</div>