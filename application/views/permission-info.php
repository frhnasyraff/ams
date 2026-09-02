<div class="card shadow mb-4">
<div class="card-header py-3">
<h6 class="m-0 font-weight-bold text-primary">Edit permission</h6>
</div>
<div class="card-body">

<form class="form-horizontal" action="<?=site_url("permissions/update");?>" method="post">

<?php if (!$info->system) { ?>
<?= $this->steve->form_group_label_input("text", "name", "Permission rule name", "col-sm-12", 1, $info->perm_name); ?>
<?php } ?>

<?= $this->steve->form_group_label_select("category", "Permission category", $this->steve->permission_categories(), "perm_cat_id", "perm_cat_name", "col-sm-12", $info->perm_cat_id, 1); ?>


<div class="text-center">
<input type="hidden" name="id" value="<?=$info->perm_id;?>" />
<button type="submit" class="btn btn-primary">Save changes</button>
</div>
</form>
</div>
</div>