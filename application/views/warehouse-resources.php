<?php $can_edit = 0;
if (!$ssr->planning_status || $ssr->planning_status == "new") {
  $can_edit = 1;
} else if (($this->user_model->has_perm("approve_resource_planner") &&  $ssr->planning_status == 'new') || $this->user_model->has_perm("edit_approved_resource_planner")) {
  $can_edit = 1;
}
?>
<div class="card shadow">
  <div class="card-body">
    <div class="row">
      <div class="col-sm-10">
        <h2><?= $ssr->vessel_name; ?></h2>
        <h4>Company: <?= $ssr->company_name; ?></h4>
        <h5>Location: <?= $ssr->location_id; ?></h5>
        <?= $this->steve->make_address($ssr); ?><br />
        Number of gangs:
                                <?= $ssr->number_gangs; ?>
        <div class="text-info">
        <i class="fa fa-utensils"></i>   <?= $ssr->work_meals ? '<span class="text-success">Working through meals.</span>' : '<span class="text-danger">Not working through meals.</span>'; ?>
        </div>
        <div class="row resources_counts">
          <div class="col-sm-4 small workers">
            <i class="fas fa-fw fa-user-cog"></i> Workers:
            <?php foreach ($resources_list as $r) { ?>
            <span class="badge badge-info" data-resource="<?= $r->resource_type_id; ?>"
              data-resource-count="<?= $r->quantity; ?>"
              style="background: <?=$r->resource_type_colour; ?>"><?= $r->resource_type_short_code; ?> -
              <?= $r->quantity; ?></span>
            <?php } ?>
          </div>
          <div class="col-sm-4 small equipments">
            <i class="fas fa-fw fa-tools"></i> Equipments:
            <?php foreach ($equipments as $r) { ?>
            <span class="badge badge-info" data-resource="<?= $r->equipment_type_id; ?>"
              data-resource-count="<?= $r->quantity; ?>"
              style="background: <?=$r->equipment_type_colour; ?>"><?= $r->equipment_type_short_code; ?> -
              <?= $r->quantity; ?></span>
            <?php } ?>
          </div>

          <div class="col-sm-4 small">
            <i class="fas fa-fw fa-cogs"></i>
            Gears:
            <?php foreach ($gears as $r) { ?>
            <span class="badge badge-info"
              style="background: <?=$r->gear_type_colour; ?>"><?= $r->gear_type_short_code ? $r->gear_type_short_code : $r->gear_type_name; ?>
              -
              <?= $r->quantity; ?></span>
            <?php } ?>
          </div>
        </div>
        <input type="hidden" name="vessel_id" id="vessel_id"
          value="<?= $this->steve->id_decode($this->input->get("vessel")); ?>" />
      </div>
    </div>
  </div>
</div>
<form class="form-horizontal" action="<?=site_url("companies/save_resources_plan");?>" method="post">
  <?php foreach ($dates as $date => $hatches) { ?>
  <div class="card date_box mt-2" id="date_<?= $date; ?>">
    <div class="card-header bg-primary text-white">
      <strong><?= $this->steve->to_full_format($date); ?></strong>
      <?php if ($can_edit && count($hatches)) { ?>
      <button type="button" class="float-right btn-light btn btn-sm clear_day m-0 text-primary tip mr-2 pt-0 pb-0"
        title="Clear the day">
        <i class="fa fa-trash"></i>
      </button>
      <button type="button"
        class="float-right btn-light btn btn-sm auto_assign m-0 text-primary tip mr-2 pt-0 pb-0 auto_assign"
        title="Clear & auto assign resources to day" href="#autoAssignModal" data-toggle="modal"
        data-target="#autoAssignModal" data-date="<?= $date; ?>">
        <i class="fa fa-magic"></i>
      </button>
      <a href="<?= site_url("companies/deployment_plan?date=" . $date . "&service_request_id=" . $this->steve->id_encode($ssr->service_request_id)); ?>" title="Download deployment plan for both shifts" class="float-right btn d-none btn-light btn-sm m-0 text-primary tip mr-2 pt-0 pb-0"><i class="fa fa-file-pdf"></i></a>
      <?php } ?>
    </div>

    <div class="card-body">
      <?php if (count($hatches)) { foreach ($hatches as $hatch) { foreach ($hatch as $operation) { ?>
      <span>
        <strong><a
            href="<?= site_url("service_requests/info?id=" . $this->steve->id_encode($operation->service_request_id)); ?>"><?= $operation->service_request_number; ?></a></strong>
        -
        <?= $operation->operation_type_name; ?><?php if ($operation->commodity_code) { ?> -
        <?= $operation->commodity_code; ?> <?php } ?><?php if ($operation->tonnage) { ?>
        <small>(<?= $operation->tonnage; ?> MT.)</small><?php } ?><?php if ($operation->quantity) { ?>
        <small>(<?= $operation->quantity; ?> units)</small><?php } ?>;
      </span>
      <?php } } ?>
      <div class="accordion row">
        <?php for($i=1;$i<=$ssr->number_gangs;$i++) { ?>
        <div class="col-md-12 col-lg-12">
          <div class="card mb-2 bg-dark" data-gang="<?= $i; ?>">
            <div class="card-header" id="heading<?= $date . $i; ?>">
              <h2 class="mb-0">
                <button class="btn btn-link collapsed btn-sm" type="button" data-toggle="collapse"
                  data-target="#collapse<?= $date . $i; ?>" aria-expanded="false"
                  aria-controls="collapse<?= $date . $i; ?>">
                  <strong>Gang <?= $i; ?></strong>
                  <?php if ($can_edit) { ?>
                  <button class="float-right btn btn-sm btn-link text-success add_resource" title="Add group or worker"
                    type="button" data-gang="<?= $i; ?>" data-date="<?= $date; ?>"><i class="fa fa-plus"></i> <i
                      class="fas fa-fw fa-user-cog"></i></button>
                  <?php } ?>
                </button>
              </h2>
            </div>
            <div id="collapse<?= $date . $i; ?>" class="collapse show" aria-labelledby="heading<?= $date . $i; ?>">
              <div class="card-body row">
                <?php for($shift=1;$shift<3;$shift++) { ?>
                <div class="col-md-6 pr-1 pl-1">
                  <div class="card" data-date="<?= $date; ?>" data-hatch="<?= $hatch_id; ?>" data-gang="<?= $i; ?>"
                    data-shift="<?= $shift; ?>">
                    <div class="card-header" id="heading<?= $date . $i; ?>-<?= $shift; ?>">
                      <h2 class="mb-0">
                        <button class="btn btn-link collapsed btn-sm" type="button" data-toggle="collapse"
                          data-target="#collapse<?= $date . $i; ?>-<?= $shift; ?>" aria-expanded="false"
                          aria-controls="collapse<?= $date . $i; ?>-<?= $shift; ?>">
                          <strong>SHIFT <?= $shift; ?></strong>
                        </button>
                        <?php if ($can_edit) { ?>
                        <button class="float-right btn btn-sm btn-link text-success add_gear" type="button"
                          title="Add gear" data-gang="<?= $i; ?>" data-shift="<?= $shift; ?>"
                          data-date="<?= $date; ?>"><i class="fa fa-plus"></i> <i class="fas fa-fw fa-cogs"></i>
                        </button>
                        <button class="float-right btn btn-sm btn-link text-success add_equipment" type="button"
                          title="Add equipment / group" data-gang="<?= $i; ?>" data-shift="<?= $shift; ?>"
                          data-date="<?= $date; ?>"><i class="fa fa-plus"></i> <i
                            class="fas fa-fw fa-tools"></i></button>
                        <?php } ?>
                      </h2>
                    </div>
                    <div id="collapse<?= $date . $i; ?>-<?= $shift; ?>" class="collapse show"
                      aria-labelledby="heading<?= $date . $i; ?>-<?= $shift; ?>">
                      <div class="card-body small" data-dateshift="<?= $date; ?>-<?= $shift; ?>">
                        <div class="resources draggable"><?php foreach ($warehouse_workers as $worker) { 
                        if ($worker->shift == $shift && $worker->gang == $i && $worker->operation_date == $date) { ?><small
                            class="badge badge-pill badge-info mr-1 d-inline-block"
                            data-worker="<?= $worker->worker_id; ?>"
                            style="background: <?= $worker->resource_type_colour; ?>"
                            data-worker-type="<?= $worker->resource_type_id; ?>"><i class="fas fa-fw fa-user-cog"></i>
                            <?= $worker->worker_name; ?>
                            <?php if ($can_edit) { ?>
                            <span class="delete"><i class="fa fa-trash"></i></span><?php } ?><input type="hidden"
                              name="workers[<?= $date; ?>][<?= $i; ?>][<?= $shift; ?>][]"
                              value="<?= $worker->worker_id; ?>" /></small><?php } } ?></div>
                        <hr />
                        <div class="equipments draggable" id="equipments_<?= $date; ?>-<?= $i; ?>-<?= $shift; ?>"><?php foreach ($warehouse_equipments as $equipment) {
                        if ($equipment->gang == $i && $equipment->operation_date == $date && $equipment->shift == $shift) { ?><small
                            class="badge badge-pill badge-info mr-1 d-inline-block"
                            data-equipment-type="<?= $equipment->equipment_type_id; ?>"
                            data-equipment="<?= $equipment->equipment_id; ?>"
                            style="background: <?= $equipment->equipment_type_colour; ?>"><i
                              class="fas fa-fw fa-tools"></i> <?= $equipment->equipment_type_short_code; ?> -
                            <?= $equipment->equipment_name; ?>
                            <?php if ($equipment->equipment_safe_load) { ?>-
                            <?= $equipment->equipment_safe_load; ?><?php } ?>
                            <?php if ($can_edit) { ?>
                            <span class="delete"><i class="fa fa-trash"></i></span><?php } ?><input type="hidden"
                              name="equipments[<?= $date; ?>][<?= $i; ?>][<?= $shift; ?>][]"
                              value="<?= $equipment->equipment_id; ?>" /></small><?php } } ?></div>
                        <hr />
                        <div class="gears draggable"><?php if ($warehouse_gears) { foreach ($warehouse_gears as $gear) {
                        if ($gear->gang == $i && $gear->operation_date == $date && $gear->shift == $shift) { ?><small
                            class="badge badge-pill badge-info mr-1 d-inline-block" data-gear="<?= $gear->gear_id; ?>"
                            style="background: <?= $gear->gear_type_colour; ?>"><i class="fas fa-fw fa-cogs"></i>
                            <?= $gear->gear_name; ?>
                            <?php if ($can_edit) { ?>
                            <span class="delete"><i class="fa fa-trash"></i></span><?php } ?><input type="hidden"
                              name="gears[<?= $date; ?>][<?= $i; ?>][<?= $shift; ?>][]"
                              value="<?= $gear->gear_id; ?>" /></small><?php } } } ?></div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
      <?php } else { ?>No activity for the day<?php } ?>
    </div>
  </div>
  <?php } ?>
  <?php if ($can_edit) { ?>
  <div class="text-center mt-3">
  <input type="hidden" name="id" value="<?=$ssr->service_request_id;?>" />
  <input type="hidden" name="ssr_id" value="<?= $ssr->service_request_id; ?>" />
    <?php if (!$ssr->planning_status || $ssr->planning_status != 'approved') { ?>
    <button type="submit" class="btn btn-info">Save changes</button>
    <?php } ?>
    <?php if (($this->user_model->has_perm("approve_resource_planner") &&  $ssr->planning_status == 'new') || $this->user_model->has_perm("edit_approved_resource_planner") && $warehouse_equipments && $warehouse_workers) { ?>
    <button type="submit" class="btn btn-success ml-5" name="save_approve" value="1">Update & approve</button>
    <?php } ?>
  </div>
  <?php } ?>
</form>
<?php if ($can_edit) { ?>
<div class="d-none">
  <div class="add_resource_box">
    <h4 class="loader text-center">
      <i class="fas fa-spin fa-hourglass-half"></i> <br />Loading
    </h4>
    <div class="form d-none">
      <?=$this->steve->form_group_label_select("worker_group_id", "Worker group", []);?>
      <button class="btn btn-success" onClick="add_resource_groups();" type="button">Add from group</button>
      <hr />
      <?=$this->steve->form_group_label_select("type", "Resource type", $this->steve->resource_types(), "resource_type_id", "resource_type_name");?>

      <div class="form-group">
        <label for="form_worker_id">Worker name</label><br />
        <select name="worker_id" class="form-control" multiple id="form_worker_id">
        </select>
      </div>
      <button class="btn btn-success" onClick="add_resource_groups();" type="button">Add worker(s)</button>
    </div>
  </div>
  <div class="add_equipment_box">
    <h4 class="loader text-center">
      <i class="fas fa-spin fa-hourglass-half"></i> <br />Loading
    </h4>
    <div class="form d-none">
      <?=$this->steve->form_group_label_select("equipment_group_id", "Equipment group", []);?>
      <div class="form-group">
        <label for="form_equipment_id">Equipment name</label><br />
        <select name="equipment_id" class="form-control" multiple id="form_equipment_id">
        </select>
      </div>
      <button class="btn btn-success" onClick="add_equipment_groups();" type="button">Add equipments to gang</button>
    </div>
  </div>
  <div class="add_gear_box">
    <h4 class="loader text-center">
      <i class="fas fa-spin fa-hourglass-half"></i> <br />Loading
    </h4>
    <div class="form d-none">
      <div class="form-group">
        <label for="form_gear_id">Gear name</label><br />
        <select name="gear_id" class="form-control" multiple id="form_gear_id">
        </select>
      </div>
      <button class="btn btn-success" onClick="add_gear_groups();" type="button">Add gear to gang</button>
    </div>
  </div>
</div>
<?php } ?>

<div class="modal fade" tabindex="-1" role="dialog" id="autoAssignModal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Worker & equipment groups to auto assign for <span class="date"></span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" action="<?=site_url("companies/auto_assign_resources");?>" method="post">
        <div class="modal-body">

          <div class="row">
            <div class="col-md-6">
              <strong>Worker groups</strong>
              <select multiple="multiple" id="worker_groups" name="worker_groups[]">
                <?php foreach ($worker_groups as $worker_group) {?>
                <option value="<?=$worker_group->worker_group_id;?>"
                  <?=(count($worker_groups) <= 2 ? 'selected' : '');?>>
                  <?=$worker_group->worker_group_name;?></option>
                <?php }?>
              </select>
            </div>
            <div class="col-md-6">
              <strong>Equipment groups</strong>
              <select multiple="multiple" id="equipment_groups" name="equipment_groups[]">
                <?php foreach ($equipment_groups as $equipment_group) {?>
                <option value="<?=$equipment_group->equipment_group_id;?>"
                  <?=(count($equipment_groups) <= 2 ? 'selected' : '');?>>
                  <?=$equipment_group->equipment_group_name;?></option>
                <?php }?>
              </select>
            </div>
          </div>
          <p class="text-center mt-3">Click the group names you would like to auto assign. Move them from left box to
            right box. Do note, existing assignment if present will be removed.</p>
        </div>

        <div class="modal-footer">
          <input type="hidden" name="id" value="<?=$ssr->service_request_id;?>" />
          <input type="hidden" name="date" value="" />
          <button type="submit" class="btn btn-success">Auto assign resources</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>