<div class="container-fluid">

    <h4>Disposal Methods</h4>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <?= $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>

    <div class="row">

        <!-- LEFT – LIST VIEW -->
        <div class="col-md-8">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Method Name</th>
                        <th>Description</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($methods)): ?>
                    <?php foreach ($methods as $m): ?>
                        <tr>
                            <td><?= $m->id ?></td>
                            <td><?= htmlspecialchars($m->disposal_method) ?></td>
                            <td><?= htmlspecialchars($m->description) ?></td>
                            <td>
                                <a href="<?= site_url('DisposalMethod/index/edit/'.$m->id) ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="<?= site_url('DisposalMethod/delete/'.$m->id) ?>"
                                   onclick="return confirm('Delete this record?')"
                                   class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No records found</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>


        <!-- RIGHT – FORM (ADD | EDIT) -->
        <div class="col-md-4">

            <h5>
                <?= ($mode === 'edit') ? 'Edit Disposal Method' : 'Add Disposal Method' ?>
            </h5>

            <form action="<?= site_url('DisposalMethod/save') ?>" method="POST">

                <input type="hidden" name="id"
                       value="<?= ($mode === 'edit') ? $method->id : '' ?>">

                <div class="form-group mb-3">
                    <label>Disposal Method</label>
                    <input type="text"
                           name="disposal_method"
                           class="form-control"
                           required
                           value="<?= ($mode === 'edit') ? $method->disposal_method : '' ?>">
                </div>

                <div class="form-group mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control"><?= ($mode === 'edit') ? $method->description : '' ?></textarea>
                </div>

                <button class="btn btn-primary">
                    <?= ($mode === 'edit') ? 'Update' : 'Save' ?>
                </button>

                <?php if ($mode === 'edit'): ?>
                    <a href="<?= site_url('DisposalMethod') ?>" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>

            </form>
        </div>

    </div>
</div>

