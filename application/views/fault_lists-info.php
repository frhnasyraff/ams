<div class="card shadow mb-4 tabradius">
              
              <div class="card-body">

                <div class="bg-white card-header py-3">
                    <h6 class="bg-white m-0 font-weight-bold text-primary">Edit fault list</h6>
                </div>
                  <form class="form-horizontal" action="<?=site_url("fault_lists/update");?>" method="post">
                  <div class="row">
                      <?=$this->steve->form_group_label_input("text", "fault_name", "Fault name", "col-sm-8", 1, $info->fault_name, 50);?>
                      </div>
                      <div class="text-center">
                          <input type="hidden" name="id" value="<?=$info->fault_id;?>" />
                          <button type="submit" class="btn btn-primary">Save changes</button>
                          <a class="btn btn-secondary" data-dismiss="modal" href=".">Go back</a>
                      </div>
                  </form>
              </div>
          </div>