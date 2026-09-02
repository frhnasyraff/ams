          <div class="card shadow mb-4">
              <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Edit worker location</h6>
              </div>
              <div class="card-body">

                  <form class="form-horizontal" action="<?=site_url("worker_locations/update");?>" method="post">
                      <div class="form-group col-12">
                          <label for="name">Worker location name</label>
                          <input type="text" name="name" class="form-control" id="name" placeholder="Worker location name" required
                              value="<?=$info->worker_location_name;?>" />
                      </div>

                      <div class="form-group col-12">
                          <label for="description">Description</label>
                          <textarea name="description" class="form-control" id="description"
                              placeholder="Description"><?=$info->description;?></textarea>
                      </div>
                      <div class="text-center">
                          <input type="hidden" name="id" value="<?=$info->worker_location_id;?>" />
                          <button type="submit" class="btn btn-primary">Save changes</button>
                          <a class="btn btn-secondary" data-dismiss="modal" href=".">Go back</a>
                      </div>
                  </form>
              </div>