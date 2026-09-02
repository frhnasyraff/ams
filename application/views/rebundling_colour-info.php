          <div class="card shadow mb-4">
              <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Edit delay reason</h6>
              </div>
              <div class="card-body">

                  <form class="form-horizontal" action="<?=site_url("delay_reasons/update");?>" method="post">
                      <?=$this->steve->form_group_label_input("text", "name", "Delay reason name", "col-sm-12", 1, $info->delay_reason_name, 50);?>

                      <?=$this->steve->form_group_label_textarea("description", "Description", "col-sm-12", 0, $info->description);?>
                      
                      <div class="text-center">
                          <input type="hidden" name="id" value="<?=$info->delay_reason_id;?>" />
                          <button type="submit" class="btn btn-primary">Save changes</button>
                          <a class="btn btn-secondary" data-dismiss="modal" href=".">Go back</a>
                      </div>
                  </form>
              </div>
          </div>