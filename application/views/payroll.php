<style type="text/css">
    .tabsclass1{
        background-color: #EEF2EF;
        margin: 10px 5px;
        border-radius: 10px;
    }
    .cardtitle_tabclass1{
        background-color: #09073dff;
        width: 90%;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        color: #fff;
    }
</style>

<a class="float-right btn text-warning text_colorb" href="<?= site_url("finance/payroll_spreadsheets"); ?>">
    <i class="fa fa-download"></i> 
    Download payroll spreadsheets
</a>
    

<div class="card shadow mb-4 tabradius">
    <div class="card-body text-center">
    <form class="form-horizontal" action="<?=site_url("finance/operationgroup_spreadsheet");?>" id="operationgroup" method="get">
   
        <div class="row">
            <div class="col-sm-4 text_successo"> 
                
                <?= $this->steve->form_group_label_input("text", "month_picker", "Date Range", "h4 col-md-12 md-3", 0, '', '', 0, ''); ?>
                <input id="start" name="start" type="hidden" value="<?= date("Y-m-d", strtotime("-1 month")); ?>" />
                <input id="end" name="end" type="hidden" value="<?= date("Y-m-d"); ?>" />
            </div>
            <div class="col-sm-4 text_successo" style="border-left: 1px solid #FEECCC;">
                    
                    <?= $this->steve->form_group_label_select_placeholder( "worker_group_id","Type of Operation",$this->steve->worker_groups_operations(), "worker_group_id", "worker_group_name","h4 col-md-12 md-3",0);?>
            </div>
            <div class="col-sm-4 ">
                
            <button type="submit" class="btn btn-success col-sm-10 groupbutton" name="operationgroup" value="1"><i class="fa fa-download"></i>Download spreadsheet</button>
                
            </div>   
        </div>
       
    </form>     
        
        <hr />
        <div class="row justify-content-center">
            <div class="col-sm-2 tabsclass1">
                <center><p class="card-title cardtitle_tabclass1">Hours worked</p></center>
                <h4 class="text_successb"><span id="pay_hours">0</span> hrs</h4>
            </div>
            <div class="col-sm-3 tabsclass1">
                <center><p class="card-title cardtitle_tabclass1">OT hours</p></center>
                <h4 class="text_successb"><span id="ot_hours">0</span> hrs</h4>
            </div>
            <div class="col-sm-3 tabsclass1">
                <center><p class="card-title cardtitle_tabclass1">Pay loss hours</p></center>
                <h4 class="text_dangerb"><span id="lop_hours">0</span> hrs</h4>
            </div>
            <div class="col-sm-2 tabsclass1">
                <center><p class="card-title cardtitle_tabclass1">Total hours</p></center>
                <h4 class="text_successb"><span id="total_hours">0</span> hrs</h4>
            </div>
        </div>
<hr />
        <div class="row justify-content-center">
            <div class="col-sm-2 tabsclass1">
                <p class="card-title cardtitle_tabclass1">Basic pay</p>
                <h4 class="text_successb">RM <span id="pay_amount">0.00</span></h4>
            </div>
            <div class="col-sm-3 tabsclass1">
                <p class="card-title cardtitle_tabclass1">Overtime pay</p>
                <h4 class="text_successb">RM <span id="ot_amount">0.00</span></h4>
            </div>
            <div class="col-sm-3 tabsclass1">
                <p class="card-title cardtitle_tabclass1">Pay loss</p>
                <h4 class="text_dangerb">RM <span id="lop_amount">0.00</span></h4>
            </div>
            <div class="col-sm-2 tabsclass1">
                <p class="card-title cardtitle_tabclass1">Total pay</p>
                <h4 class="text_successb">RM <span id="total_amount">0.00</span></h4>
            </div>
        </div>
    </div>
</div>