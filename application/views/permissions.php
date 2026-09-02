
<style type="text/css">
    .pagination>li>a{
        border-radius: 10px;
        /*background-color: #fff !important;*/
        /*color: #fff !important;*/
    }
.pagination > .active>a{
    	background-color: #073D11 !important;
    }

    #permissions_next>a{
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
    #permissions_previous>a{
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
</style>

<?php if ($this->user_model->has_perm("add_permissions")) { ?>
<button class="float-right text_successo btn btn_border" data-toggle="modal" data-target="#addModal" title="Add a new permission rule">  <i class="fa fa-plus"></i> Add Permission Rule</button>
<?php } ?>

<p class="mb-4 text_successo">Here is a list of actions in the system that necessitate permissions. Please don't change any values
    here unless you have to.</p>

<div class="card shadow mb-4 tabradius">
    <div class="card-body">
        <div class="table-responsive">
		<table class="table table-borderless table-striped <?= ($this->user_model->has_perm("edit_permissions") ? "" : "read-only"); ?>" id="permissions" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="bg-white text-dark font-weight-bold" >ID</th>
                        <th class="bg-white text-dark font-weight-bold" >Permission category</th>
                        <th class="bg-white text-dark font-weight-bold" >Permission rule</th>
                        <th width="10%" class="bg-white text-dark font-weight-bold" >Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($this->user_model->has_perm("add_permissions")) { ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="addModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New permission rule
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" action="<?= site_url("permissions/add"); ?>" method="post">
                    <div class="modal-body row">
                    <?= $this->steve->form_group_label_input("text", "name", "Permission rule name", "col-sm-12", 1); ?>

                            <?= $this->steve->form_group_label_select("category", "Permission category", $this->steve->permission_categories(), "perm_cat_id", "perm_cat_name", "col-sm-12", '', 1); ?>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Add permission rule</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php if ($this->user_model->has_perm("edit_permissions")) { ?>

    <div class="modal fade" tabindex="-1" role="dialog" id="deleteModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Delete the permission?
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form class="form-horizontal" action="<?= site_url("permissions/delete"); ?>" method="post">
				<div class="modal-body">
					Are you sure you would like to delete this permission item? Please be very careful when doing this. Some functionalities of system will stop working.
				</div>
				<div class="modal-footer">
					<input type="hidden" name="id" class="record_id" />
					<button type="submit" class="btn btn-danger">Delete</button>
					<button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
    <?php } ?>
