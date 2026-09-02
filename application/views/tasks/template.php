<?php
/**
 * @author mult1mate
 * Date: 21.12.15
 * Time: 0:29
 */
$menu = array(
    'index' => 'Tasks list',
    'taskEdit' => 'Add new/edit task',
    'taskLog' => 'Logs',
    'export' => 'Import/Export',
    'tasksReport' => 'Report',
);
?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Scheduled tasks</h6>
    </div>
    <div class="card-body">
    <ul class="nav nav-tabs">
        <?php foreach ($menu as $m => $text):
            $class = (isset($_GET['m']) && ($_GET['m'] == $m)) ? 'active' : '';
            ?>
            <li class="nav-item"><a href="<?= site_url("scheduled_tasks/$m"); ?>" class="nav-link <?= $class ?>"><?= $text ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>