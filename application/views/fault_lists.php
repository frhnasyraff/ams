<style type="text/css">
  .pagination>li>a{
        border-radius: 10px;
    }
    .pagination > .active>a{
        background-color: #0a073dff !important;
    }

    #wastage_types_next>a{
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
    #wastage_types_previous>a{
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
</style>

<div class="card shadow mb-4 tabradius">
    
    <div class="card-body">

        <div class="bg-white card-header py-3">
            <h6 class="bg-white m-0 font-weight-bold text-primary">List of  Fault lists
                <?php if ($this->user_model->has_perm("add_fault_lists")) { ?>
                <a class="float-right" href="#addModal" data-toggle="modal" data-target="#addModal"
                    title="Add new fault list"><i class="fa fa-plus"></i> New  fault list</a>
                <?php } ?>
            </h6>
        </div>

        <div class="table-responsive">
            <table
                class="table table-borderless table-striped <?= ($this->user_model->has_perm("edit_fault_lists") ? "" : "read-only"); ?>"
                id="fault_lists" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="30%"> Name</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($this->user_model->has_perm("add_fault_lists")) { ?>
<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New fault list
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("fault_lists/add"); ?>" method="post">
                <div class="modal-body row">

                    <?=$this->steve->form_group_label_input("text", "fault_name", "Fault name", "col-sm-12", 1, '', 30);?>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add fault list</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>