<?php if ($this->user_model->has_perm("add_operators")) { ?>
<button class="float-right btn btn-info btn-sm" data-toggle="modal" data-target="#addModal" title="Add new operator">Add operator</button>
<?php } ?>
          <p class="mb-4">Here is a list of operators registered in the system.</p>

          <div class="card shadow mb-4">
            <div class="card-header py-3">
			  <h6 class="m-0 font-weight-bold text-primary">List of operators</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
			  <table class="table table-bordered table-striped <?= ($this->user_model->has_perm("edit_operators") ? "" : "read-only"); ?>" id="operators" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Operator code</th>
                      <th>Operator name</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
				</table>
		  </div>
          </div>

		  <?php if ($this->user_model->has_perm("add_operators")) { ?>
          <div class="modal fade" tabindex="-1" role="dialog" id="addModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">New operator
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form class="form-horizontal" action="<?= site_url("operators/add"); ?>" method="post">
				<div class="modal-body row">

                <?=$this->steve->form_group_label_input("text", "code", "Operator code", "col-sm-6 uppercase", 1, '', 10);?>
                <?=$this->steve->form_group_label_input("text", "name", "Operator name", "col-sm-6", 0, '', 100);?>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-success">Add operator</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
		  <?php } ?>