<style type="text/css">
    .pagination>li>a{
        border-radius: 10px;
        /*background-color: #fff !important;*/
        /*color: #fff !important;*/
    }
.pagination > .active>a{
        background-color: #09073dff !important;
    }

    #incident_types_next>a{
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
    #incident_types_previous>a{
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
</style>

<div class="card shadow mb-4 tabradius">
    
    <div class="card-body">

        <div class="bg-white card-header py-3">
            <h6 class="bg-white m-0 font-weight-bold text-primary">List Of Incident Types
                <a class="float-right" href="#addModal" data-toggle="modal" data-target="#addModal"
                    title="Add new Incident type"><i class="fa fa-plus"></i> New Incident Type</a>
            </h6>
        </div>

        <div class="table-responsive">
            <table
                class="table table-borderless table-striped <?= ($this->user_model->has_perm("edit_incident_types") ? "" : "read-only"); ?> "
                id="incident_types" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="40%">Incident type</th>
                        <th width="50%">Description</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if ($this->user_model->has_perm("add_incident_types")) { ?>
<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Incident Type
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("incident_types/add"); ?>" method="post">
                <div class="modal-body row">

                    <?=$this->steve->form_group_label_input("text", "incident_type", "incident type name", "col-sm-12", 1, '', 30);?>

                    <?=$this->steve->form_group_label_textarea("description", "Description", "col-sm-12");?>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add incident Type</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>