<div class="card shadow mb-4 tabradius">
    
    <div class="card-body">
        <div class="bg-white card-header py-3">
            <h6 class="bg-white m-0 font-weight-bold text-primary">Edit incident types</h6>
        </div>
        <form class="form-horizontal" action="<?=site_url("incident_types/update");?>" method="post">
            <div class="form-group col-12">
                <label for="name">Incident Type</label>
                <input type="text" name="incident_type" class="form-control" id="incident_type" placeholder="Incident Type" 
                    value="<?=$info->incident_type;?>" required />
            </div>

            <div class="form-group col-12">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" id="description"
                    placeholder="Description"><?=$info->Description;?></textarea>
            </div>
            <div class="text-center">
                <input type="hidden" name="id" value="<?=$info->incident_type_id;?>" />
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a class="btn btn-secondary" data-dismiss="modal" href=".">Go back</a>
            </div>
        </form>
    </div>