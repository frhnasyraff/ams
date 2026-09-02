<div class="card shadow mb-4 tabradius">

    <div class="card-body">

        <div class="bg-white card-header py-3">
            <h6 class="bg-white m-0 font-weight-bold text-primary">Edit Land Field Location</h6>
        </div>

        <form class="form-horizontal" action="<?= site_url("land_field/update"); ?>" method="post">
            <div class="form-group col-12">
                <label for="location_name">location_name</label>
                <input type="text" name="location_name" class="form-control" id="location_name" placeholder="Location Name" value="<?= $info->location_name; ?>" required />
            </div>
            <div class="form-group col-sm-12">
                <label for="">Branch Office</label>
                <select name="branch_id" class="form-control" id="">
                    <?php foreach ($branch_office as $branch) { ?>
                        <option value="<?= $branch->branch_id ?>" <?= $info->branch_id == $branch->branch_id ? 'selected' : '' ?>><?= $branch->branch_name ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-12">
                <label for="address">Address</label>
                <input type="text" name="address" class="form-control" id="address" placeholder="Address" value="<?= $info->address; ?>" />
            </div>
            <div class="form-group col-12">
                <label for="latitude">Latitude</label>
                <input type="text" name="latitude" class="form-control" id="latitude" placeholder="Latitude" value="<?= $info->latitude; ?>" />
            </div>
            <div class="form-group col-12">
                <label for="longitude">Longitude</label>
                <input type="text" name="longitude" class="form-control" id="longitude" placeholder="Longitude" value="<?= $info->longitude; ?>" />
            </div>
            <div class="text-center">
                <input type="hidden" name="id" value="<?= $info->land_field_id; ?>" />
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a class="btn btn-secondary" data-dismiss="modal" href=".">Go back</a>
            </div>
        </form>
    </div>