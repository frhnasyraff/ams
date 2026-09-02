<style type="text/css">
    .pagination>li>a{
        border-radius: 10px;
        /*background-color: #fff !important;*/
        /*color: #fff !important;*/
    }
.pagination > .active>a{
    	background-color: #09073dff !important;
    }

    #designations_next>a{
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
    #designations_previous>a{
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
</style>

<?php if ($this->user_model->has_perm("add_designations")) { ?>
<button class="float-right text_successo btn btn_border" data-toggle="modal" data-target="#addModal"
	title="Add a new designation"> <i class="fa fa-plus"></i> Add Designation</button>
<?php } ?>

<p class="mb-4 text_successo">Here is a list of designations that can be assigned to users and other contacts.</p>

<div class="card shadow mb-4 tabradius">
	<div class="card-body">
		<div class="table-responsive">
		<table class="table table-borderless table-striped <?= ($this->user_model->has_perm("edit_designations") ? "" : "read-only"); ?>" id="designations" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th class="bg-white text-dark font-weight-bold">Designation</th>
						<th class="bg-white text-dark font-weight-bold">Description</th>
						<th width="10%" class="bg-white text-dark font-weight-bold">Actions</th>
					</tr>
				</thead>
				<tbody>


				</tbody>
			</table>
		</div>
	</div>

	<?php if ($this->user_model->has_perm("add_designations")) { ?>
	<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">New designation
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form class="form-horizontal" action="<?= site_url("designations/add"); ?>" method="post">
					<div class="modal-body row">

						<div class="form-group col-12">
							<label for="name">Designation</label>
							<input type="text" name="name" class="form-control" id="name" placeholder="Designation"
								required />
						</div>

						<div class="form-group col-12">
							<label for="description">Description</label>
							<textarea name="description" class="form-control" id="description"
								placeholder="Description"></textarea>
						</div>
					</div>

					<div class="modal-footer">
						<button type="submit" class="btn btn-success">Add designation</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php } ?>
