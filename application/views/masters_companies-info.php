<div class="card shadow mb-4 tabradius">
    
    <div class="card-body">

        <div class="bg-white card-header py-3">
            <h6 class="bg-white m-0 font-weight-bold text-primary">Edit Company</h6>
        </div>
        
        <form class="form-horizontal" action="<?=site_url("masters_companies/update");?>" method="post">
            <div class="form-group col-12">
                <label for="registration_id">Registration Id</label>
                <input type="text" name="registration_id" class="form-control" id="registration_id" placeholder="Registration Id" 
                    value="<?=$info->registration_id;?>"required />
            </div>
            <div class="form-group col-12">
                <label for="company_name">Company Name</label>
                <input type="text" name="company_name" class="form-control" id="company_name" placeholder="Company Name" 
                    value="<?=$info->company_name;?>" required/>
            </div>
            <div class="form-group col-12">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" class="form-control" id="contact_person" placeholder="Contact Person" 
                    value="<?=$info->contact_person;?>" />
            </div>

            <div class="form-group col-12">
                <label for="contact_email">Contact Email</label>
                <input type="text" name="contact_email" class="form-control" id="contact_email" placeholder="Email" 
                    value="<?=$info->contact_email;?>" />
                            
            </div>
            <div class="form-group col-12">
                <label for="business_type">Bussiness Type</label>
                <input type="text" name="business_type" class="form-control" id="business_type" placeholder="Business Type" 
                    value="<?=$info->business_type;?>" />
                            
            </div>
            <div class="text-center">
            <input type="hidden" name="id" value="<?=$info->company_id;?>" />
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a class="btn btn-secondary" data-dismiss="modal" href=".">Go back</a>
            </div>
        </form>
    </div>