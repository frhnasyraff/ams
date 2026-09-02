<form class="form-horizontal" action="<?=site_url("incidents/updateincident");?>" id="incident_request" method="post">
    <div class="card shadow mb-4">

        <div class="card-body">
            <div class="row">
                <div class="form-group col-sm-3">
                    <?= $this->steve->form_group_label_input("text", "incident_datetime", "Incident Date & Time", "datetime_picker",1,$incident->incident_datetime , 0, 0 );?>

                </div>

                <div class="form-group col-sm-3">
                    <?= $this->steve->form_group_label_select_placeholder( "incident_type","Type of Incident",$this->steve->incident_types(), "incident_type_id", "incident_type","", $incident->incident_type_id, 0, 0); ?>

                </div>
                <div class="form-group col-sm-3">
                <?= $this->steve->form_group_label_select_placeholder( "vessel_visit_id","Vessel/SCN",$this->steve->vessel_visit_id(), "vessel_visit_id","vessel_name,visit_eta","", $incident->vessel_visit_id,0,0);?>
                
                    <!--?= $this->steve->form_group_label_input( "text","scn","Vessel/SCN", "", 0,$incident->scn);?-->
                </div>
                <div class="form-group col-sm-3">
                    <?= $this->steve->form_group_label_select_placeholder( "location","Location",$this->steve->worker_locations(), "worker_location_id", "worker_location_name","",$incident->location_id,0,0);?>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-12 ">
                    <?= $this->steve->form_group_label_textarea("location_details","Location Details", "location_details",0,$incident->location_details);?>
                </div>

            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <?= $this->steve->form_group_label_select_placeholder("risk_rating","Risk Rating(1-10)", range(0, 10),"","","",$incident->risk_rating,0,0);?>
                </div>
                <div class="form-group col-sm-4">
                    <?= $this->steve->form_group_label_input("text","weather","Weather","",0,$incident->weather);?>
                </div>
                <div class="form-group col-sm-4">
                <?= $this->steve->form_group_label_select_placeholder( "asset_person_dd","Asset/Person", ["asset", "person","both"],"","","",$incident->asset_person,0,0);?>                       
                        
                   </div>
            </div>

        </div>
    </div>
    <div class="card shadow mb-4" id="person_form" style="margin:0 auto; <?= ($incident->asset_person == "asset") ? 'display:none;' : ""; ?>">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">PERSON</h6>
        </div>
        <div class="card-body">
        <table class="persontable table">
            
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-center"><button class="btn btn-success btn-sm tip text-white edit_add_person"
                                title="Add Another Person" type="button"><i class="fa fa-plus"></i> Add Another Person</button>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <?php foreach ($persondetails as $person) {
                   // print_r($person); ?>
       
                <tbody>                   
                    <tr>                    
                        <td>               
                            <?= $this->steve->form_group_label_input("text", "ic_passport[]", "Ic/Passport","",0,$person->ic_passport,"") ?>
                            <?= $this->steve->form_group_label_select_placeholder( "company_name[]","Company Name", $this->steve->masters_companies(),"company_id", "company_name","", $person->company_id, 0);?>
                            <?= $this->steve->form_group_label_select_placeholder("position[]","Position", $this->steve->designations(), "designation_id", "designation_name", "",$person->postion_id,0);?>
                            
                        </td>
                        <td>
                            <?= $this->steve->form_group_label_input("text","name[]", "Name","",0,$person->name,"");?>
                            <?= $this->steve->form_group_label_select_placeholder("age[]","Age", range(18, 65),"","","",$person->age,0);?>
                            <?= $this->steve->form_group_label_input("text", "injured_part[]", "Injured Part","",0,$person->injured_part,"");?>
                        </td>
                        <td>
                        <?= $this->steve->form_group_label_select( "injured[]","Injured", ["Yes", "No"],"","","",$person->injured,"");?>
                        <?= $this->steve->form_group_label_input("text", "type_of_injury[]", "Type of Injury","",0,$person->type_of_injury,"");?>
                                                
                        <td>
                        
                        <?= $this->steve->form_group_label_input("text", "cause[]", "Cause","",0,$person->cause,"");?>
                        <?= $this->steve->form_group_label_input("text", "object_cause_injury[]", "Object Cause Injury","",0,$person->object_cause_injury,"");?>
                        <?= $this->steve->form_group_label_input("hidden", "incident_request_person_details_id[]", "","",0,$person->incident_request_person_details_id,"") ?>
                        
                        </td>
                        <td>
                        <button
                                class="float-right delete_box btn btn-sm btn-text text-danger incident_person_details_delete"
                                data-id="<?= $person->incident_request_person_details_id; ?>" type="button" href="#"><i
                                    class="fa fa-times"></i></button>
                        </td>
                        
                    </tr>
                
                <?php }
                
                if (empty($persondetails)) {?>
                <tr>
                    
                    <td>
           
                        <?= $this->steve->form_group_label_input("text", "ic_passport[]", "Ic/Passport","",0,$person->ic_passport,"") ?>
                        <?= $this->steve->form_group_label_select_placeholder( "company_name[]","Company Name", $this->steve->masters_companies(),"company_id", "company_name","", $person->company_id, 0);?>
                        <?= $this->steve->form_group_label_select_placeholder("position[]","Position", $this->steve->designations(), "designation_id", "designation_name", "",$person->postion_id,0);?>
                        
                    </td>
                    <td>
                        <?= $this->steve->form_group_label_input("text","name[]", "Name","",0,$person->name,"");?>
                        <?= $this->steve->form_group_label_select_placeholder("age[]","Age", range(18, 65),"","","",$person->age,0);?>
                        <?= $this->steve->form_group_label_input("text", "injured_part[]", "Injured part","",0,$person->injured_part,"");?>
                    </td>
                    <td>
                    <?= $this->steve->form_group_label_select( "injured[]","Injured", ["Yes", "No"],"","","",$person->injured,"");?>
                    <?= $this->steve->form_group_label_input("text", "type_of_injury[]", "Type of Injury","",0,$person->type_of_injury,"");?>
                                            
                    <td>
                    
                    <?= $this->steve->form_group_label_input("text", "cause[]", "Cause","",0,$person->cause,"");?>
                    <?= $this->steve->form_group_label_input("text", "object_cause_injury[]", "Object Cause Injury","",0,$person->object_cause_injury,"");?>
                    <?= $this->steve->form_group_label_input("hidden", "incident_request_person_details_id[]", "","",0,$person->incident_request_person_details_id,"") ?>
                    
                    </td>
                    <td>
                        
                    </td>
                    
                </tr>
            <?php }?>
                </tbody>
            </table>

        </div>
    </div>
    <div class="card shadow mb-4" id="asset_form"style="margin:0 auto; <?= ($incident->asset_person == "person") ? 'display:none;' : ""; ?>">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">ASSET</h6>
        </div>
        <div class="card-body">
        <table class="assettable table">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-center"><button class="btn btn-success btn-sm tip text-white edit_add_asset"
                                title="Add Another Asset" type="button"><i class="fa fa-plus"></i> Add Another Asset</button>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($assetdetails as $asset) {
                  // print_r("Hi"); 
                  ?>
                    <tr>
                        <td>
                        <?= $this->steve->form_group_label_select_placeholder( "asset_type[]","Asset Type", $this->steve->equipment_types(), "equipment_type_id", "equipment_type_name", "",$asset->asset_type_id,0);?>
                        <?= $this->steve->form_group_label_input("text", "type_of_damage[]", "Type Of Damage","",0,$asset->type_of_damage,"");?>
                        </td>
                        <td>
                            <?= $this->steve->form_group_label_input("text", "damage_part[]", "Damage Part", "",0,$asset->damage_part,"");?>
                            
                           
                        </td>
                        <td>
                            <?= $this->steve->form_group_label_input("text", "technical_status[]", "Technical Status","",0,$asset->technical_status,"");?>
                            <?= $this->steve->form_group_label_input("hidden", "incident_request_asset_details_id[]", "","",0,$asset->incident_request_asset_details_id,"") ?>
                        
                        </td>
                        <td>
                        <?= $this->steve->form_group_label_input("text", "owner[]", "Owner","",0,$asset->owner,"");?>
                        </td>
                        <td>
                        <button
                                class="float-right delete_box btn btn-sm btn-text text-danger incident_asset_details_delete"
                                data-id="<?= $asset->incident_request_asset_details_id ?>"  type="button" href="#"><i
                                    class="fa fa-times"></i></button>
                        </td>
                    </tr>
                    <?php }
                    
                    if (empty($assetdetails)) {?>
                        <tr>
                        <td>
                        <?= $this->steve->form_group_label_select_placeholder( "asset_type[]","asset_type", $this->steve->equipment_types(), "equipment_type_id", "equipment_type_name", "",$asset->asset_type_id,0);?>
                        <?= $this->steve->form_group_label_input("text", "type_of_damage[]", "Type Of Damage","",0,$asset->type_of_damage,"");?>
                        </td>
                        <td>
                            <?= $this->steve->form_group_label_input("text", "damage_part[]", "Damage Part", "",0,$asset->damage_part,"");?>
                            
                           
                        </td>
                        <td>
                            <?= $this->steve->form_group_label_input("text", "technical_status[]", "Technical Status","",0,$asset->technical_status,"");?>
                            <?= $this->steve->form_group_label_input("hidden", "incident_request_asset_details_id[]", "","",0,$asset->incident_request_asset_details_id,"") ?>
                        
                        </td>
                        <td>
                        <?= $this->steve->form_group_label_input("text", "owner[]", "Owner","",0,$asset->owner,"");?>
                        </td>
                        <td></td>
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
                    <?= $this->steve->form_group_label_textarea("event_before","Event-Before","event_before",0,$incident->event_before,0);?>
                </div>
                <div class="form-group col-sm-6">
                    <?= $this->steve->form_group_label_textarea("event_during","Event-During","event_before",0,$incident->event_before,0);?>
                </div>

            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <?= $this->steve->form_group_label_textarea("event_after",  "Event-After","event_after",0,$incident->event_after);?>
                </div>
                <div class="form-group col-sm-6">
                    <?= $this->steve->form_group_label_textarea("initial_finding", "Initial Finding","initial_finding",0,$incident->initial_finding,0);?>
                </div>

            </div>

            <div class="row">
                <div class="form-group col-sm-6">
                    <?= $this->steve->form_group_label_textarea("intermediate_action","Intermediate Action","intermediate_action",0,$incident->intermediate_action,0);?>
                </div>

            </div>
            <div class="row">
                <div class="form-group col-sm-8">


                </div>
                <div class="form-group col-sm-2">
                    <button type="submit" class="btn btn-info col-sm-12" name="back" onclick="history.back()"
                        value="1">Back</button><br />


                </div>
                <div class="form-group col-sm-2">
                    <input type="hidden" name="id" value="<?=$incident->incident_request_id;?>" />
                    <button type="submit" class="btn btn-success col-sm-12 " name="updateincident"
                        value="1">Update</button>

                </div>
            </div>

        </div>
    </div>



</form>