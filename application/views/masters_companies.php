<style type="text/css">
    .pagination>li>a{
        border-radius: 10px;
        /*background-color: #fff !important;*/
        /*color: #fff !important;*/
    }
.pagination > .active>a{
    	background-color: #09073dff !important;
    }
    
    #masters_companies_next>a{
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
    #masters_companies_previous>a{
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
</style>

<a class="float-right text_successo btn btn_border" href="#addModal" data-toggle="modal" data-target="#addModal"
                title="Add new Company"><i class="fa fa-plus"></i> New Company</a>

<div class="card shadow mb-4 tabradius">
    <div class="card-body">
        <div class="table-responsive">
            <table
                class="table table-borderless table-striped <?= ($this->user_model->has_perm("edit_masters_companies") ? "" : "read-only"); ?>"
                id="masters_companies" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="bg-white text-dark font-weight-bold" >Registration Id</th>
                        <th class="bg-white text-dark font-weight-bold" >Company Name</th>
                        <th class="bg-white text-dark font-weight-bold" >contact person</th>
                        <th class="bg-white text-dark font-weight-bold" >contact email</th>
                        <th class="bg-white text-dark font-weight-bold" >business type</th>
                        <th width="10%" class="bg-white text-dark font-weight-bold" >Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if ($this->user_model->has_perm("add_masters_companies")) { ?>
<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Company
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?= site_url("masters_companies/add"); ?>" method="post">
                <div class="modal-body row">

                    <?=$this->steve->form_group_label_input("text", "registration_id", "registration id", "col-sm-12", 1, '', 30);?>
                    
                    <?=$this->steve->form_group_label_input("text", "company_name", "company name", "col-sm-12", 1, '', 30);?>
                    <?=$this->steve->form_group_label_input("text", "contact_person", "contact person", "col-sm-12", 1, '', 30);?>
                    <?=$this->steve->form_group_label_input("text", "contact_email", "contact email", "col-sm-12", 1, '', 30);?>
                    <?=$this->steve->form_group_label_input("text", "business_type", "business type", "col-sm-12", 1, '', 30);?>

                   

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Company</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>