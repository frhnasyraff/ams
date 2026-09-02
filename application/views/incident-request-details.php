<div class="row mb-4">
    <div class="col-sm-8">
        <nav>
            <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" id="nav-details-tab" data-toggle="tab" href="#nav-details"
                    role="tab" aria-controls="nav-details" aria-selected="true">Details</a>
                <a class="nav-item nav-link" id="nav-documents-tab" data-toggle="tab" href="#nav-documents" role="tab"
                    aria-controls="nav-documents" aria-selected="true">Documents</a>

            </div>
        </nav>

        <div class="tab-pane fade active show" id="nav-details" role="tabpanel">

            <div class="card shadow mb-4">

                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <?= $this->steve->form_group_label_input("text", "incident_datetime", "Incident Date & Time","datetime_picker",1,$incident->incident_datetime ,0, 1);?>

                        </div>

                        <div class="form-group col-sm-3">
                            <?= $this->steve->form_group_label_select_placeholder( "incident_type","Type of Incident",$this->steve->incident_types(), "incident_type_id", "incident_type","", $incident->incident_type_id, 0, 1); ?>

                        </div>
                        <div class="form-group col-sm-3">
                        <?= $this->steve->form_group_label_select_placeholder( "vessel_visit_id","Vessel/SCN",$this->steve->vessel_visit_id(), "vessel_visit_id","vessel_name,visit_eta","", $incident->vessel_visit_id,0,1);?>
                
                            <!--?= $this->steve->form_group_label_input( "text","vessel_visit_id","Vessel/SCN", "", 0,$incident->scn,0,1);?-->
                        </div>
                        <div class="form-group col-sm-3">
                            <?= $this->steve->form_group_label_select( "location","Location",$this->steve->worker_locations(), "worker_location_id", "worker_location_name","",$incident->location_id,0,1);?>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12 ">
                            <?= $this->steve->form_group_label_textarea("location_details","Location Details", "location_details",0,$incident->location_details,"0","1");?>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <?= $this->steve->form_group_label_select_placeholder("risk_rating","Risk Rating(1-10)", range(0, 10),"","","",$incident->risk_rating,0,1);?>
                        </div>
                        <div class="form-group col-sm-4">
                            <?= $this->steve->form_group_label_input("text","weather","Weather","",0,$incident->weather,"",1);?>
                        </div>
                        <div class="form-group col-sm-4">
                        <?= $this->steve->form_group_label_select_placeholder( "asset_person_dd","Asset/Person", ["asset", "person","both"],"","","",$incident->asset_person,0,1);?>                       
                        
                        </div>
                    </div>

                </div>
            </div>
            <div class="card shadow mb-4 person_add" id="person_form"style="margin:0 auto; <?= ($incident->asset_person == "asset") ? 'display:none;' : ""; ?>">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">PERSON</h6>
        </div>
        <div class="card-body ">
            <table class="persontable table">
                <thead>
                    <tr>
                        <th>Identity</th>
                        <th>Details</th>
                        <th>Injured &amp; Details</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                   
                <tbody>
                <?php foreach ($persondetails as $person) {
                  // print_r($person); ?>
                    <tr>
                        <td>
                            <?= $this->steve->form_group_label_input("text", "ic_passport[]", "Ic/Passport","",0,$person->ic_passport,"", 1) ?>
                            <?= $this->steve->form_group_label_select_placeholder( "company_name[]","Company Name", $this->steve->masters_companies(),"company_id", "company_name","", $person->company_id, 0, 1);?>
                            <?= $this->steve->form_group_label_select_placeholder("position[]","Position", $this->steve->designations(), "designation_id", "designation_name","",$person->postion_id,0,1);?>
                        
                        </td>
                        <td>
                            <?= $this->steve->form_group_label_input("text","name[]", "Name","",0,$person->name,"",1);?>
                            <?= $this->steve->form_group_label_select_placeholder("age[]","Age", range(18, 65),"","","",$person->age,0,1);?>
                            <?= $this->steve->form_group_label_input("text", "injured_part[]", "Injured part","",0,$person->injured_part,"",1);?>
                        </td>
                        <td>
                        <?= $this->steve->form_group_label_select_placeholder( "injured[]","Injured", ["Yes", "No"],"","","",$person->injured,"",1);?>
                        <?= $this->steve->form_group_label_input("text", "type_of_injury[]", "Type of Injury","",0,$person->type_of_injury,"",1);?>
                                                
                        <td>
                        <?= $this->steve->form_group_label_input("text", "cause[]", "Cause","",0,$person->cause,"",1);?>
                        <?= $this->steve->form_group_label_input("text", "object_cause_injury[]", "Object Cause Injury","",0,$person->object_cause_injury,"",1);?>
                        
                        </td>
                        <td>
                        </td>
                    </tr>
                
                <?php }?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow mb-4 asset_add" id="asset_form" style="margin:0 auto;  <?= ($incident->asset_person == "person") ? 'display:none;' : ""; ?>">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">ASSET</h6>
        </div>
        <div class="card-body ">
            <table class="table">
                <thead>
                    <tr>
                        <th>Asset &amp; Details</th>
                        <th>Damage &amp; Details</th>
                        <th>Status &amp;</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($assetdetails as $asset) {
                  //  print_r($asset); 
                  ?>
                    <tr>
                        <td>
                        <?= $this->steve->form_group_label_select_placeholder( "asset_type[]","asset_type", $this->steve->equipment_types(), "equipment_type_id", "equipment_type_name", "",$asset->asset_type_id,0,1);?>
                        <?= $this->steve->form_group_label_input("text", "owner", "Owner","",0,$asset->owner,"",1);?>
                        </td>
                        <td>
                            <?= $this->steve->form_group_label_input("text", "damage_part[]", "Damage Part", "",0,$asset->damage_part,"",1);?>
                            <?= $this->steve->form_group_label_input("text", "type_of_damage[]", "Type Of Damage","",0,$asset->type_of_damage,"",1);?>
                           
                        </td>
                        <td>
                            <?= $this->steve->form_group_label_input("text", "technical_status[]", "Technical Status","",0,$asset->technical_status,"",1);?>
                        
                        </td>
                        <td>
                        </td>
                    </tr>
                    <?php }?>
   
                </tbody>
            </table>
        </div>
    </div>
            
            

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">INFO</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <?= $this->steve->form_group_label_textarea("event_before","Event-Before","event_before",0,$incident->event_before,0,1);?>
                        </div>
                        <div class="form-group col-sm-6">
                            <?= $this->steve->form_group_label_textarea("event_during","Event-During","event_before",0,$incident->event_before,0,1);?>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <?= $this->steve->form_group_label_textarea("event_after",  "Event-After","event_after",0,$incident->event_after,"",1);?>
                        </div>
                        <div class="form-group col-sm-6">
                            <?= $this->steve->form_group_label_textarea("initial_finding", "Initial Finding","initial_finding",0,$incident->initial_finding,0,1);?>
                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-6">
                            <?= $this->steve->form_group_label_textarea("intermediate_action","Intermediate Action","intermediate_action",0,$incident->intermediate_action,0,1);?>
                        </div>

                    </div>


                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Documents</h6>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div style="margin:auto; display:table;">
                            <?php foreach ($attachments as $attachment) {?>


                            <?php if ($attachment->filename) {?>
                            <img width="200" height="200" class="img-fluid img-thumbnail"
                                <?php if ($attachment->filename) {?>
                                src="<?=site_url("storage/INCIDENT-" . $incident->incident_request_id . "/" . $attachment->filename);?>"
                                alt="<?=$attachment->filename;?>" title="<?=$attachment->filename;?>'s photo"
                                class="img" />
                            <?php }}?>


                            <?php }?>

                        </div>

                    </div>
                </div>
            </div>
        </div>





        <div class="tab-pane fade" id="nav-documents" role="tabpanel">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Supporting Documents</h6>
                </div>
                <div class="card-body">


                    <p>Click on the box or drag and drop your Bill of Lading and other documents in the box to upload.
                        Do note, only PDF and image formats will be accepted.</p>
                    <form action="<?=site_url("incidents/upload_document");?>" class="dropzone"
                        enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?=$incident->incident_request_id;?>" />
                        <div class="fallback">
                            <input name="file[]" type="file" multiple accept="image/*,.pdf" />
                        </div>
                    </form>

                    <ul class="list-group mt-4 attachment_files">
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Actions <span class="text-uppercase float-right"></span>
                </h6>
            </div>
            <div class="card-body text-center">

                <?php if ($incident->incident_request_status != "cancelled") { ?>
                <div class="btn-group" role="group" aria-label="Booking actions">
                    <?php if ($this->user_model->has_perm("approve_incident_requests") && $incident->incident_request_status != "approved") {?>
                    <a class="btn btn-success btn-sm tip text-white" title="Approve" data-toggle="modal"
                        data-target="#approveModal"><i class="fa fa-check"></i> Approve</a>
                    <?php } ?>
                    <?php if (($incident->incident_request_status != "approved")|| $this->user_model->has_perm("approve_incident_requests") && $incident->incident_request_status != "cancelled") {?>
                    <a class="btn btn-warning btn-sm tip text-white" title="Edit incident"
                        href="edit_view/?id=<?= $this->steve->id_encode($incident->incident_request_id); ?>"><i
                            class="fa fa-pencil-alt"></i> Edit</a><?php } ?>
                    
                </div>
                <?php } ?>
                <?php if ($this->user_model->has_perm("delete_incident_requests")) {?>
                

                <button type="button" class="btn btn-white btn-sm tip" title="Permanently Delete Incident"
                    data-toggle="modal" data-target="#deleteModal"><i class="fa fa-times"></i> Delete</button>
                    <?php } ?>
                <?php if ($incident->incident_request_status == "approved") {?>
                <br />
                <?php } ?>
            </div>

        </div>

        <div class="card shadow mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Remarks</h6>
            </div>
            <div class="card-body">
                <div class="panel panel-primary">

                    <div class="panel-collapse" id="collapseOne">
                        <div class="panel-body">
                            <ul class="chat">
                                <?php
                                $remark_style = "right";
                                foreach ($remarks as $remark) {
                                    if ($remark->user_id != $previous_user_id) {
                                        $remark_style = ($remark_style == "right" ? "left" : "right");
                                        $previous_user_id = $remark->user_id;
                                    }
                                    ?>
                                <li class="<?=$remark_style;?> clearfix">
                                    <span class="chat-img float-<?=$remark_style;?>">
                                        <?php if ($remark->profile_picture) {?>
                                        <img class="rounded-circle img-thumbnail profile_picture tip"
                                            src="<?=site_url("storage/User-" . $remark->user_id . "/" . $remark->profile_picture);?>"
                                            alt="<?=$remark->full_name;?>" title="<?=$remark->full_name;?>'s photo"
                                            class="img-circle" />
                                        <?php }?>
                                    </span>
                                    <div class="chat-body clearfix">
                                        <div class="header">
                                            <strong
                                                class="<?=($remark_style == "right" ? "float-right" : "");?> primary-font"><?=$remark->full_name;?></strong>
                                            <small
                                                class="<?=($remark_style == "right" ? "" : "float-right");?> text-muted mr-1 mt-1">
                                                <span class="fa fa-clock"></span>
                                                <?=$this->steve->to_date_time($remark->t_updated, 1);?></small>
                                        </div>
                                        <p>
                                            <?=$remark->remark;?>
                                        </p>
                                    </div>
                                </li>
                                <?php }?>
                                </li>

                            </ul>
                        </div>
                        <div class="panel-footer mt-3">
                            <form class="form-horizontal" action="<?=site_url("incidents/add_remark");?>" method="post">
                                <textarea id="btn-input" type="text" class="form-control input-sm" name="remark"
                                    placeholder="Type your remark here" required></textarea>
                                <div class="text-center mt-2">
                                    <input type="hidden" name="id" value="<?=$incident->incident_request_id;?>" />
                                    <button type="submit" class="btn btn-success btn-sm tip text-white" id="btn-chat">
                                        <i class="fa fa-paper-plane"></i> Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="approveModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve The Incident?
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?=site_url("incidents/approve");?>" method="post">
                <div class="modal-body">

                    <div class="row">
                        <div class="form-group col-sm-12 ">
                            <input type="hidden" name="incident_request_id" value="<?=$incident->incident_request_id;?>" />
                            <?= $this->steve->form_group_label_textarea("remarks","Approved the Incident","remarks",0,"","");?>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-success btn-sm tip text-white">Approve The Incident</button>
                    <button type="button" class="btn btn-info btn-sm tip text-white" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="cancelModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel The Incident?
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?=site_url("incidents/cancel");?>" method="post">
                <div class="modal-body">
                    Are you sure you would like to cancel the INCIDENT?
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" value="<?=$incident->incident_request_id;?>" />
                    <button type="submit" class="btn btn-danger">Cancel The INCIDENT</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="deleteModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Permanently Delete The INCIDENT?
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" action="<?=site_url("incidents/delete");?>" method="post">
                <div class="modal-body">
                    Are you sure you would like to delete the INCIDENT?
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" value="<?=$incident->incident_request_id;?>" />
                    <button type="submit" class="btn btn-danger btn-sm tip text-white">Delete The Incident</button>
                    <button type="button" class="btn btn-info btn-sm tip text-white" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>