          <div class="card shadow mb-4">
              <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Edit operation type</h6>
              </div>
              <div class="card-body">

                  <form class="form-horizontal" action="<?=site_url("operation_types/update");?>" method="post">
                  <div class="row">
                      <?=$this->steve->form_group_label_input("text", "name", "Operation type name", "col-sm-8", 1, $info->operation_type_name, 50);?>
<div class="col-sm-4">
<?= $this->steve->form_group_label_checkbox("no_cargo", "No cargo for this operation?", "mt-2", 0, 1, $info->no_cargo) ?>
<?= $this->steve->form_group_label_checkbox("no_commodity", "No commodity required for this operation's tally?", "mt-1", 0, 1, $info->no_commodity) ?>
<?= $this->steve->form_group_label_checkbox("no_stowage", "No stowage plan?", "mt-2", 0, 1, $info->no_stowage) ?>
                      </div>
                      <?=$this->steve->form_group_label_textarea("description", "Description", "col-sm-12", 0, $info->description);?>
                      </div>
                      <div class="text-center">
                          <input type="hidden" name="id" value="<?=$info->operation_type_id;?>" />
                          <button type="submit" class="btn btn-primary">Save changes</button>
                          <a class="btn btn-secondary" data-dismiss="modal" href=".">Go back</a>
                      </div>
                  </form>
              </div>
          </div>