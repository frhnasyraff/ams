<style type="text/css">
  .pagination>li>a{
        border-radius: 10px;
        /*background-color: #fff !important;*/
        /*color: #fff !important;*/
    }
.pagination > .active>a{
    	background-color: #073D11 !important;
    }

    #users_next>a{
        margin-left: 10px;
        border-radius: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
    #users_previous>a{
        border-radius: 10px;
        margin-right: 10px;
        background-color: #fff !important;
        color: grey !important;
    }
</style>
<?php if ($this->user_model->has_perm("add_users")) { ?>
<button class="float-right text_successo btn btn-default btn_border" data-toggle="modal" data-target="#addModal" title="Add new user"><i class="fa fa-plus"></i> Add
  user</button>
<?php } ?>

<p class="mb-4 text_successo">Here is a list of all users registered for access to the System. The roles of each user defines their permission to perform actions in the system.</p>

<div class="card shadow mb-4 tabradius">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-borderless table-striped <?= ($this->user_model->has_perm("edit_users") ? "" : "read-only"); ?>" id="users" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th class="bg-white text-dark font-weight-bold">Username</th>
            <th class="bg-white text-dark font-weight-bold">Full name</th>
            <th class="bg-white text-dark font-weight-bold">User code</th>
            <th class="bg-white text-dark font-weight-bold">E-mail address</th>
            <th class="bg-white text-dark font-weight-bold">Actions</th>
          </tr>
        </thead>
        <tbody>

        </tbody>
      </table>
    </div>
  </div>



  <?php if ($this->user_model->has_perm("add_users")) { ?>
  <div class="modal fade" tabindex="-1" role="dialog" id="addModal">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">New user account
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form class="form-horizontal" action="<?=site_url("users/add");?>" method="post">
          <div class="modal-body row">

            <?= $this->steve->form_group_label_input("text", "username", "Username", "col-sm-6", 1); ?>

            <?= $this->steve->form_group_label_input("text", "user_code", "User code", "col-sm-6 uppercase"); ?>

            <?= $this->steve->form_group_label_input("text", "full_name", "Full name", "col-sm-6",1); ?>

            <?= $this->steve->form_group_label_input("email", "email", "E-mail address", "col-sm-6", 1); ?>

            <?= $this->steve->form_group_label_input("text", "password", "Password", "col-sm-6", 1, $this->steve->random_str(10)); ?>

            <?= $this->steve->form_group_label_select("designation", "Designation", $this->steve->designations(), "designation_id", "designation_name", "col-sm-6", 1); ?>

            <?= $this->steve->form_group_label_select("user_group", "User group", $this->steve->user_groups(), "user_group_id", "user_group_name", "col-sm-6", 1); ?>

            <?= $this->steve->form_group_label_input("text", "address_line_1", "Address line 1", "col-sm-6"); ?>

            <?= $this->steve->form_group_label_input("text", "address_line_2", "Address line 2", "col-sm-6"); ?>

            <?= $this->steve->form_group_label_input("text", "address_zip", "ZIP code", "col-sm-6 uppercase", 0, "", 8); ?>

            <?= $this->steve->form_group_label_input("text", "address_city", "City", "col-sm-6"); ?>

            <?= $this->steve->form_group_label_input("text", "address_state", "State", "col-sm-6"); ?>

            <?= $this->steve->form_group_label_select("address_country", "Country", $this->steve->countries(), "code", "countryname", "col-sm-6", "MY", 1); ?>

            <?=$this->steve->form_group_label_input("tel", "phone", "Mobile phone", "col-sm-6");?>
            
            <?= $this->steve->form_group_label_input("text", "company_name", "Company name", "col-sm-6"); ?>

            <input type="hidden" name="company_id" id="company_id" />

            <?=$this->steve->form_group_label_checkbox("mobile", "Mobile app access (user cannot login to mobile app without this checkbox being checked)", "col-sm-6", 0, 1, $info->mobile);?>

          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Add user</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php } ?>
