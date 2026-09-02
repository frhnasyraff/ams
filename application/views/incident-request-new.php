<form class="form-horizontal" action="<?=site_url("incidents/add_new_incident");?>" id="incident_request" method="post">
    <div class="card shadow mb-4 tabradius">
        
        <div class="card-body">    
            <div class="row">
                <div class="form-group col-sm-3">
                    <?= $this->steve->form_group_label_input("text", "incident_datetime", "Incident Date & Time", "datetime_picker",1);?>
                    
                </div>
            
                <div class="form-group col-sm-3">
                    <?= $this->steve->form_group_label_select_placeholder( "incident_type","Type of Incident",$this->steve->incident_types(), "incident_type_id", "incident_type",0,0,1);?>
                
                </div>
                <div class="form-group col-sm-3">
                
                    						
                    <?= $this->steve->form_group_label_select_placeholder( "vessel_visit_id","Vessel/SCN",$this->steve->vessel_visit_id(), "vessel_visit_id", "vessel_name,visit_eta",0,0);?>
                
                    <!--?= $this->steve->form_group_label_input( "text","scn","Vessel/SCN","",0);?-->
                </div> 
                <div class="form-group col-sm-3">
                    <?= $this->steve->form_group_label_select_placeholder("location","Location",$this->steve->worker_locations(),"worker_location_id","worker_location_name","","",1);?>
                </div>                            
            </div>
            <div class="row">
                <div class="form-group col-sm-12 ">
                    <?= $this->steve->form_group_label_textarea("location_details","Location Details", "location_details",0);?>
                </div>
        
            </div>
            <div class="row">                            
                <div class="form-group col-sm-4">
                    <?= $this->steve->form_group_label_select_placeholder("risk_rating","Risk Rating(1-10)", range(0, 10),"","","","",1);?>
                </div>
                <div class="form-group col-sm-4">
                    <?= $this->steve->form_group_label_input("text","weather","Weather","",0);?>
                </div>  
                <div class="form-group col-sm-4">
                    <?= $this->steve->form_group_label_select_placeholder( "asset_person_dd","Asset/Person", ["asset", "person","both"],"","","","",1);?>                    
                </div>                            
            </div>
        
        </div>
    </div>
    <div class="card shadow mb-4 tabradius person_add" id="person_form"style="margin:0 auto; display:none;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">PERSON</h6>
        </div>
        <div class="card-body ">
            <table class="persontable table">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-center"><button class="btn btn-success btn-sm tip text-white add_person"
                                title="Add Another Person" type="button"><i class="fa fa-plus"> </i> Add Another Person</button>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?= $this->steve->form_group_label_input("text", "ic_passport[]", "Ic/Passport", 1);?>
                            <?= $this->steve->form_group_label_select_placeholder( "company_name[]","Company Name", $this->steve->masters_companies(), "company_id", "company_name", $info->masters_companies);?>
                            <?= $this->steve->form_group_label_select_placeholder("position[]","Position", $this->steve->designations(), "designation_id", "designation_name", $info->designation);?>
                        
                        </td>
                        <td>
                            <?= $this->steve->form_group_label_input("text","name[]", "Name", 1);?>
                            <?= $this->steve->form_group_label_select_placeholder("age[]","Age", range(18, 65),"","",1,1);?>
                            <?= $this->steve->form_group_label_input("text", "injured_part[]", "Injured Part", 0);?>
                        </td>
                        <td>
                        <?= $this->steve->form_group_label_select_placeholder( "injured[]","Injured",["Yes","No"],"","",1,1);?>
                        <?= $this->steve->form_group_label_input("text", "type_of_injury[]", "Type of Injury", 0);?>
                                                
                        <td>
                        <?= $this->steve->form_group_label_input("text", "cause[]", "Cause", 0);?>
                        <?= $this->steve->form_group_label_input("text", "object_cause_injury[]", "Object Cause Injury", 0);?>
                        </td>
                        <td></td>
                        
                    </tr>
                </tbody>
            </table>
        </div>
    </div>



    


    <div class="card shadow mb-4 tabradius asset_add" id="asset_form" style="margin:0 auto; display:none;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">ASSET</h6>
        </div>
        <div class="card-body ">
            <table class="assettable table">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-center"><button class="btn btn-success btn-sm tip text-white add_asset"
                                title="Add Another Asset" type="button"><i class="fa fa-plus"></i> Add Another Asset</button>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                        <?= $this->steve->form_group_label_select_placeholder( "asset_type[]","Asset Type", $this->steve->equipment_types(), "equipment_type_id", "equipment_type_name", $info->equipment_types);?>
                        <?= $this->steve->form_group_label_input("text", "type_of_damage[]", "Type Of Damage", 1);?>
                        </td>
                        <td>
                            <?= $this->steve->form_group_label_input("text", "damage_part[]", "Damage Part", 1);?>
                            
                        </td>
                        <td>
                            <?= $this->steve->form_group_label_input("text", "technical_status[]", "Technical Status", 1);?>
                        </td>
                        <td>
                        <?= $this->steve->form_group_label_input("text", "owner[]", "Owner", 0);?>
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

   

    <div class="card shadow mb-4 tabradius">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">REMARKS</h6>
        </div>
        <div class="card-body">
            
            <div class="row">
                <div class="form-group col-sm-12 ">
                    <?= $this->steve->form_group_label_textarea("remarks","Remarks", 30);?>
                </div>
        
            </div>
        </div>
    </div>

    <div class="card shadow mb-4 tabradius">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">INFO</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="form-group col-sm-6">
                    <?= $this->steve->form_group_label_textarea("event_before","Event Before", 0);?>
                </div>
                <div class="form-group col-sm-6">
                    <?= $this->steve->form_group_label_textarea("event_during","Event During", 0);?>
                </div>
                         
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <?= $this->steve->form_group_label_textarea("event_after",  "Event After","event_after",0);?>
                </div>
                <div class="form-group col-sm-6">
                    <?= $this->steve->form_group_label_textarea("initial_finding", "Initial Finding",  0);?>
                </div>
                         
            </div>
            
            <div class="row">
                <div class="form-group col-sm-6">
                    <?= $this->steve->form_group_label_textarea("intermediate_action","Intermediate Action", 0);?>
                </div>
                          
            </div>
            <div class="row">
            <div class="form-group col-sm-8">
                   
                
                </div> 
                <div class="form-group col-sm-2">
                    <button type="submit" class="btn btn-info col-sm-12" name="back" onclick="history.back()" value="1">Back</button><br />
                   
                
                </div> 
                <div class="form-group col-sm-2">
         
                    <button type="submit" class="btn btn-success col-sm-12" name="add_incident_request" value="1">Submit</button>
                
                </div>            
            </div>
            
        </div>
    </div>

    
    
</form>

