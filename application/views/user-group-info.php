          <div class="card shadow mb-4 tabradius">
              
              <div class="card-body">
                <div class="bg-white card-header py-3">
                    <h6 class="bg-white m-0 font-weight-bold text-primary">Edit user group</h6>
                </div>
                  <form class="form-horizontal" action="<?=site_url("user_groups/update");?>" method="post">
                      <div class="form-group col-12">
                          <label for="name">User group name</label>
                          <input type="text" name="name" class="form-control" id="name" placeholder="User group name" required
                              value="<?=$info->user_group_name;?>" />
                      </div>

                      <div class="form-group col-12">
                          <label for="description">Description</label>
                          <textarea name="description" class="form-control" id="description"
                              placeholder="Description"><?=$info->description;?></textarea>
                      </div>
                      <div class="text-center">
                          <input type="hidden" name="id" value="<?=$info->user_group_id;?>" />
                          <button type="submit" class="btn btn-primary">Save changes</button>
                          <a class="btn btn-secondary" data-dismiss="modal" href=".">Go back</a>
                      </div>
                  </form>
              </div>