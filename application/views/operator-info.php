<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit operator</h6>
    </div>
    <div class="card-body">

        <form class="form-horizontal" action="<?=site_url("operators/update");?>" method="post">
            <div class="row">
            <?=$this->steve->form_group_label_input("text", "code", "Operator code", "col-sm-12 uppercase", 1, $info->operator_code, 10);?>
            <?=$this->steve->form_group_label_input("text", "name", "Operator name", "col-sm-12", 0, $info->operator_name, 100);?>
            </div>
            <div class="text-center">
                <input type="hidden" name="id" value="<?=$info->operator_id;?>" />
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a class="btn btn-secondary" data-dismiss="modal" href=".">Go back</a>
            </div>
        </form>
    </div>
</div>