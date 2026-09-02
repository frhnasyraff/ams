          <div class="card shadow mb-4">
              <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Edit equipment type</h6>
              </div>
              <div class="card-body">

                  <form class="form-horizontal" action="<?=site_url("equipment_types/update");?>" method="post">
                      <div class="row">
                          <?=$this->steve->form_group_label_input("text", "name", "Equipment type name", "col-sm-4", 1, $info->equipment_type_name, 50);?>


                          <?=$this->steve->form_group_label_input("text", "short_code", "Equipment type short code", "col-sm-4 uppercase", 1, $info->equipment_type_short_code, 6);?>

                          <?= $this->steve->form_group_label_select("resource_type", "Operated by", $this->steve->resource_types(), 'resource_type_id', 'resource_type_name', 'col-sm-4', $info->operator_id); ?>

                          <?=$this->steve->form_group_label_textarea("description", "Description", "col-sm-4", 0, $info->description);?>

                          <div class="text-center colorwheel col-sm-4">
                              <input id="color-block" type="text" value="<?= $info->equipment_type_colour; ?>"
                                  data-wheelcolorpicker="" data-wcp-format="css" name="colour" data-wcp-layout="block"
                                  data-wcp-sliders="wsvp" data-wcp-cssclass="color-block" data-wcp-autoresize="false" />
                          </div>


                          <div class="col-sm-4">
                              <label class="m-0">Cost price / unit / day</label>
                              <?=$this->steve->form_group_input_suffix('cost', 'Cost price', "mt-2", 'RM', 0, $info->equipment_type_cost);?>

                              <label class="m-0 mt-3">Fuel cost / unit</label>
                              <?=$this->steve->form_group_input_suffix('fuel_cost', 'Fuel cost / unit', "mt-2", 'RM', 0, $info->equipment_type_fuel_cost);?>

                          </div>

                      </div>

                      <div class="text-center">
                          <input type="hidden" name="id" value="<?=$info->equipment_type_id;?>" />
                          <button type="submit" class="btn btn-primary">Save changes</button>
                          <a class="btn btn-secondary" data-dismiss="modal" href=".">Go back</a>
                      </div>
                  </form>
              </div>
          </div>