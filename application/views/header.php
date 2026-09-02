<?php
$this->load->helper('url');
function main_menu_item($name, $url, $icon = '', $menu = '', $root = 0)
{
    return '<li class="nav-item' . ("/" . $url == $_SERVER['REDIRECT_QUERY_STRING'] || $url == $_SERVER['REDIRECT_QUERY_STRING'] || $menu == $url ? ' active' : '') . '"><a class="nav-link" href="' . ($root ? $url : site_url($url)) . '">' . ($icon ? '<i class="fas fa-fw fa-' . $icon . '"></i> ' : '') . '<span>' . $name . '</span></a></li>';
}

function sub_menu_item($name, $url, $menu = '', $root = 0)
{
    return '<a class="collapse-item' . ("/" . $url == $_SERVER['REDIRECT_QUERY_STRING'] || $url == $_SERVER['REDIRECT_QUERY_STRING'] || $menu == $url ? ' active' : '') . '" href="' . ($root ? $url : site_url($url)) . '">' . '<span>' . $name . '</span></a>';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="ToadHut">
    <title>IMS<?= (isset($title) ? " - " . strip_tags($title) : ''); ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?= site_url('design/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <link
        href="https://fonts.googleapis.com/css?family=sora:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- Your custom styles (optional) -->
    <link href="<?= site_url('design/css/sb-admin-2.min.css'); ?>" rel="stylesheet">
    <link rel="icon" href="<?= site_url("favicon.ico"); ?>" type="image/x-icon" />
    <link rel="shortcut icon" href="<?= site_url("favicon.ico"); ?>" type="image/x-icon" />

    <link href="<?= site_url('design/vendor/datatables/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet">
    <link href="<?= site_url('design/vendor/datatables/dataTables.bootstrap4.css'); ?>" rel="stylesheet">
    <link href="<?= site_url('design/css/bootstrap-toggle.min.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <!-- Leaflet MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        span.select2-container {
            width: 100% !important;
        }
    </style>
    <style>
        .content,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        span,
        th {
            font-family: 'Montserrat', sans-serif !important;
        }
    </style>
    <?php if (isset($styles)) {
        foreach ($styles as $style) { ?>
            <link rel="stylesheet" href="<?= (preg_match("/http/", $style) ? $style : site_url($style . "?12")); ?>">
    <?php }
    } ?>

    <link href="<?= site_url('design/css/styles.css?15'); ?>" rel="stylesheet">
    <link href="<?= site_url('design/css/steve-dark-theme.css?76'); ?>" rel="stylesheet">
    <?php if ($_SESSION['user']->default_font) { ?>
        <link
            href="https://fonts.googleapis.com/css?family=<?= $_SESSION['user']->default_font; ?>:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <style>
            #wrapper {
                font-family: "<?= $_SESSION['user']->default_font; ?>"
            }
        </style>
    <?php } ?>
    <style id="override">
        <?php if ($_SESSION['user']->default_color) {
        ?>.text-primary {
            /* color: */
            <? // $_SESSION['user']->default_color;
            //
            ?>
            /* !important; */
        }

        .bg-gradient-primary,
        .dropdown-item.active,
        .dropdown-item:active {
            /* background-color: $_SESSION['user']->default_color */
            background-image: none;
        }

        /* table.dataTable thead tr {
			color: white;
		} */

        /* .btn-primary,
		.bg-primary,
		.badge-primary,
		table.dataTable thead tr {
			background-color:
				<? //$_SESSION['user']->default_color;
                ?> !important;
			border-color:
				<? //$_SESSION['user']->default_color;
                ?>;
		} */

        ::-webkit-scrollbar-thumb {
            /* background-color: //$_SESSION['user']->default_color; */

        }

        /* .btn-primary.disabled,
		.btn-primary:disabled {
			background-color:
				<? // $_SESSION['user']->default_color;
                ?>;
			border-color:
				<? // $_SESSION['user']->default_color;
                ?>;
		} */

        <?php
        }

        ?>

        /*added for all;*/
        .toggle-off,
        .toggle-group>label {
            border-radius: 20px !important;
            border-style: none !important;
        }

        .toggle-handle,
        hover {
            border-radius: 50px !important;
            margin-left: 90px;
            background-color: #fff !important;
        }

        .toggle-group,
        td>.toggle {
            background-color: #fff !important;
            border: none;
        }

        td>.btn-danger {
            border-radius: 90px;
        }

        .toggle-handle,
        hover {
            border-radius: 50px !important;
            margin-left: 90px;
            background-color: #D52A1A !important;
        }

        /**/


        #collapseWorkers>div {
            margin-left: -5px;
            color: #FAA202;
        }

        #collapseWorkers>div>a>span {
            color: #FAA202;
        }

        #collapseWorkers>div>a:before {
            color: #FAA202;
            content: '-- ';
        }

        #collapseasset>div {
            margin-left: -5px;
            color: #FAA202;
        }

        #collapseasset>div>a>span {
            color: #FAA202;
        }

        #collapseasset>div>a:before {
            color: #FAA202;
            content: '-- ';
        }

        #collapsedashboard>div {
            margin-left: -5px;
            color: #FAA202;
        }

        #collapsedashboard>div>a>span {
            color: #FAA202;
        }

        #collapsedashboard>div>a:before {
            color: #FAA202;
            content: '-- ';
        }


        #collapseorders>div {
            margin-left: -5px;
            color: #FAA202;
        }

        #collapseorders>div>a>span {
            color: #FAA202;
        }

        #collapseorders>div>a:before {
            color: #FAA202;
            content: '-- ';
        }

        #collapseschedule>div {
            margin-left: -5px;
            color: #FAA202;
        }

        #collapseschedule>div>a>span {
            color: #FAA202;
        }

        #collapseschedule>div>a:before {
            color: #FAA202;
            content: '-- ';
        }

        #collapseequipments>div {
            margin-left: 10px;
            color: #FAA202;
        }

        #collapseequipments>div>a>span {
            color: #FAA202;
        }

        #collapseequipments>div>a:before {
            color: #FAA202;
            content: ' --- ';
        }


        #collapsegears>div {
            margin-left: 10px;
            color: #FAA202;
        }

        #collapsegears>div>a>span {
            color: #FAA202;
        }

        #collapsegears>div>a:before {
            color: #FAA202;
            content: ' --- ';
        }


        #collapsereport>div {
            margin-left: 10px;
            color: #FAA202;
        }

        #collapsereport>div>a>span {
            color: #FAA202;
        }

        #collapsereport>div>a:before {
            color: #FAA202;
            content: ' --- ';
        }

        #collapseDrivers>div {
            margin-left: 10px;
            color: #FAA202;
        }

        #collapseDrivers>div>a>span {
            color: #FAA202;
        }

        #collapseDrivers>div>a:before {
            color: #FAA202;
            content: ' --- ';
        }

        #collapsetruck>div {
            margin-left: 10px;
            color: #FAA202;
        }

        #collapsetruck>div>a>span {
            color: #FAA202;
        }

        #collapsetruck>div>a:before {
            color: #FAA202;
            content: ' --- ';
        }
        #collapseBilling>div {
            margin-left: 10px;
            color: #FAA202;
        }

        #collapseBilling>div>a>span {
            color: #FAA202;
        }

        #collapseBilling>div>a:before {
            color: #FAA202;
            content: ' --- ';
        }

        #collapseMasters>div {
            margin-left: 10px;
            color: #FAA202;
        }

        #collapseMasters>div>a>span {
            color: #FAA202;
        }

        #collapseMasters>div>a:before {
            color: #FAA202;
            content: ' --- ';
        }

        #accordionSidebar>li>a>i {
            color: #FAA202;
        }

        #accordionSidebar>li>a:hover {
            color: #FAA202;
        }

        div {
            text-transform: capitalize;
        }

        .applygreen {
            background-color: #0f2d61ff !important;
            box-shadow: inset 0px 0px 20px 0px rgb(5 55 127);
        }

        .mapboxgl-popup {
            max-width: 400px;
        }

        body {
            margin: 0;
            padding: 0;
        }

        #map {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
        }

        .quake-info {
            position: absolute;
            font-family: sans-serif;
            margin-top: 5px;
            margin-left: 5px;
            padding: 5px;
            width: 30%;
            border: 2px solid black;
            font-size: 14px;
            color: #222;
            background-color: #fff;
            border-radius: 3px;
        }

        .sidebar.toggled {
            border-radius: 10px;
        }
        .search-container {
            display: flex;
            align-items: center;
            border-radius: 24px;
            background-color: #1E232A;
            box-shadow: inset 0px 0px 11px 0px rgb(243 36 36);
            padding: 8px 16px;
            width: 400px;
        }

        .search-input {
            border: none;
            outline: none;
            flex-grow: 1;
            font-size: 16px;
            color: #80A874;
            background-color: transparent;
        }

        .search-input::placeholder {
            color: #80A874;
        }

        .search-button {
            background-color: #FAA202;
            border: none;
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .search-button svg {
            fill: #fff;
            width: 24px;
            height: 24px;
        }

        .search-button:focus,
        .search-button:hover {
            background-color: rgb(31, 21, 9);
        }
    </style>
</head>

<?php
$master_module_controllers = [
    'assettypescolors', 'task', 'task_list', 'vendorpartnumber',
    'vendormanufacturingnumber', 'vendormanufacturingdrawingnumber',
    'managedbyadddata', 'maintenancetypecolorcode', 'faulttypecolorcode',
    'states', 'locations', 'item_type', 'dashboardstatuscolor', 'logoimage',
    'asset_groups', 'assettypes', 'assetstatus', 'itemstatus', 'storelocation',
    'disposalmethod', 'write_off_reasons', 'user_groups', 'designations',
    'worker_locations', 'permissions'
];
$current_controller = strtolower($this->router->fetch_class());
$current_method = strtolower($this->router->fetch_method());
$is_master_module = $current_method === 'index' && in_array($current_controller, $master_module_controllers, true);
?>
<body id="page-top" class="<?= ($is_master_module ? 'master-module-page' : ''); ?>" data-controller="<?= html_escape($current_controller); ?>" data-method="<?= html_escape($current_method); ?>" style="background-color: #fff;">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion latest_nav"
            style="margin-top: 90px; background-color: #133C81 !important; box-shadow: inset 0px 0px 20px 0px #133C81;"
            id="accordionSidebar">

            <br />
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-left mb-4" style="margin-top: -109px;"
                href="<?= base_url() ?>">
                <div class="sidebar-brand-icon mt-2">
                    <img class="d-none d-md-inline full_logo" src="<?= site_url('design/img/logo-new.png'); ?>" />
                    <img class="d-lg-none d-md-none half_logo"
                        src="<?= site_url('design/img/logo-new-small.png'); ?>" />

                </div>

            </a>
            <br />
            <!-- Divider -->
            <div class="sidebar-heading" style="color: #FBBF53;">
                <?= mb_strtoupper('Main Menu') ?>
            </div>
            <?php if ($this->user_model->current_user()->user_group == 9) { ?>
                <?= main_menu_item("Home", "customer_dashboard", 'home'); ?>
            <?php
            } else { ?>
            <?= main_menu_item("Home", "assets_type_dashboard", 'home');
            } ?>


            <?php if ($this->user_model->has_perm("list_assets") && $this->user_model->has_perm("view_maintenance_dashboard")) { // Require submenu permission supaya Dashboard dropdown tak render kosong ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsedashboard"
                        aria-expanded="true" aria-controls="collapsedashboard">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    <div id="collapsedashboard" class="collapse" aria-labelledby="headingequipments"
                        data-parent="#accordionSidebar">
                        <div class="applygreen py-2 collapse-inner rounded">


                            <?php if ($this->user_model->has_perm("view_maintenance_dashboard")) { ?>
                                <!-- <?= sub_menu_item("Assets ", "assets_type_dashboard"); ?> -->
                                <?= sub_menu_item("Component ", "items_type_dashboard"); ?>
                                <?= sub_menu_item("Asset Summary", "asset_summary_dashboard"); ?>
                                <?= sub_menu_item("PM", "preventive_maintenance"); ?>
                                <?= sub_menu_item("CM", "corrective_maintenance"); ?>
                                <?= sub_menu_item("Performance", "Performance"); ?>
                            <?php } ?>


                        </div>
                    </div>
                </li>
            <?php } ?>

            <?php if ($this->user_model->has_perm("list_assets")) { ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseasset"
                        aria-expanded="true" aria-controls="collapseasset">
                        <i class="fas fa-fw fa-th"></i>
                        <span>Assets</span>
                    </a>
                    <div id="collapseasset" class="collapse" aria-labelledby="headingequipments"
                        data-parent="#accordionSidebar">
                        <div class="applygreen py-2 collapse-inner rounded">
                            <?= sub_menu_item("General", "asset_dashboard"); ?>
                            <?= sub_menu_item("Inventory Summary", "InventorySummary"); ?>
                            <!-- <?= sub_menu_item("Corrective Summary", "CorrectiveMaintenanceSummary"); ?> -->

                            <?= sub_menu_item("Asset list", "assets"); ?>
                            <?= sub_menu_item("Component list", "items"); ?>

                            <?php if ($this->user_model->has_perm("issue_ticket_view")) { ?>
                                <?= sub_menu_item("Issue Ticket", "ticket"); ?>
                            <?php } ?>

                            <!-- <?php if ($this->user_model->has_perm("issue_item_ticket_view")) { ?>
                        <?= sub_menu_item("Issue Items Ticket", "items_ticket"); ?>
                        <?php } ?> -->

                            <?= sub_menu_item("Asset/Component C...", "Assets_Item_calibration"); ?>
                            <?= sub_menu_item("Asset/Component M...", "Assets_Item_maintenance?filter=corrective"); ?>
                            <?= sub_menu_item("Location Summary", "Location_summary", 'film'); ?>


                        </div>
                    </div>
                </li>
            <?php } ?>
            
            <?php if ($this->user_model->has_perm("list_assets")) { ?>
                <?= main_menu_item("Asset Consolidation", "asset_consolidation", 'compress'); ?>
            <?php } ?>


            <?php if ($this->user_model->has_perm("list_assets")) { ?>
                <?= main_menu_item("Manage Depreciation", "asset_depreciation", 'compress'); ?>
            <?php } ?>

            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('depreciation_summary'); ?>">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Depreciation Summary</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('asset_disposal_requests'); ?>">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Disposal Requests</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('asset_disposals'); ?>">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Disposals List</span>
                </a>
            </li>

            <!-- if -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsereport"
                    aria-expanded="true" aria-controls="collapsereport">
                    <i class="fas fa-fw fa-file"></i>
                    <span>Report</span>
                </a>
                <div id="collapsereport" class="collapse" aria-labelledby="headingreport"
                    data-parent="#accordionSidebar">
                    <div class="applygreen py-2 collapse-inner rounded">

                        <!-- <?= sub_menu_item("Bin Sales", "Bin_Performance"); ?> -->
                        <!-- <?= sub_menu_item("Asset Summary", "asset_performance"); ?> -->
                        <?= sub_menu_item("Asset Summary", "equipment_asset_summary_report"); ?>
                        <!-- <?= sub_menu_item("Item Summary", "item_summary_report"); ?>
                        <?= sub_menu_item("Asset Summ-Stats", "asset_summary_report"); ?>
                        <?= sub_menu_item("Asset & Item Faulty", "faulty_summary_report"); ?>
                        <?= sub_menu_item("Asset In Use", "AssetInUse_summary"); ?>
                        <?= sub_menu_item("Asset Maintenance", "AssetMaintenance_summary"); ?>-->
                        <?= sub_menu_item("Ticket Summary", "ticket_summary_report"); ?>
                        <?= sub_menu_item("Faulty Item List", "faulty_item_list_report"); ?>
                        <?= sub_menu_item("Maintenance Report", "maintenance_summary_report"); ?>
                        <?= sub_menu_item("MTBF", "Mean_time_between_failure_report"); ?>

                    </div>
            </li>
            <!-- end if -->

            <!-- Nav Item - Pages Collapse Menu Item Equipment-->
            <?php if ($this->user_model->has_perm("list_equipment")) { ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEquipment"
                        aria-expanded="true" aria-controls="collapseEquipment">
                        <i class="fas fa-fw fa-tools"></i>
                        <span>Equipments</span>
                    </a>
                    <div id="collapseEquipment" class="collapse" aria-labelledby="headingMasters"
                        data-parent="#accordionSidebar">
                        <div class="applygreen py-2 collapse-inner rounded">
                            <?php if ($this->user_model->has_perm("list_equipments_list")) { ?>
                                <?= sub_menu_item("Equipments list", "equipments_list"); ?>
                            <?php } ?>
                            <?php if ($this->user_model->has_perm("list_gear")) { ?>
                                <?= sub_menu_item("Gear", "gear"); ?>
                            <?php } ?>
                        </div>
                    </div>
                </li>
            <?php } ?>


            <!-- Nav Item - Pages Collapse Menu Item Billing-->
            <?php if ($this->user_model->has_perm("list_finance_documents")) { ?>
                <!-- <li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBilling"
					aria-expanded="true" aria-controls="collapseBilling">
					<i class="fas fa-fw fa-file-invoice-dollar"></i>
					<span>Finance</span>
				</a>
				<div id="collapseBilling" class="collapse" aria-labelledby="headingMasters"
					data-parent="#accordionSidebar">
					<div class="applygreen py-2 collapse-inner rounded">
					<?php if ($this->user_model->has_perm("list_payroll_list")) { ?>
						<?= sub_menu_item("Payroll", "finance/payroll"); ?>		<?php } ?>
					</div>
				</div>
			</li> -->
            <?php } ?>

            <?php if ($this->user_model->has_perm("list_reportss")) { ?>
                <?= main_menu_item("Reports", "reports", 'chart-bar'); ?>
            <?php } ?>

            <?php if ($this->user_model->has_perm("list_admin")) { ?>
                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <div style="color: #FBBF53;" class="sidebar-heading">
                    <?= mb_strtoupper('Admin') ?>
                </div>
                <?php if ($this->user_model->has_perm("list_users")) { ?>
                    <?= main_menu_item("Users", "users", 'user'); ?>
                <?php } ?>


                <?php if ($this->user_model->has_perm("list_user_roles")) { ?>
                    <?= main_menu_item("User roles", "user_roles", "users"); ?>
                <?php } ?>
                <?php if ($this->user_model->has_perm("list_masters")) { ?>
                    <li class="nav-item">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMasters"
                            aria-expanded="true" aria-controls="collapseMasters">
                            <i class="fas fa-fw fa-folder"></i>
                            <span>Masters</span>
                        </a>
                        <div id="collapseMasters" class="collapse" aria-labelledby="headingMasters"
                            data-parent="#accordionSidebar">
                            <div class="applygreen py-2 collapse-inner rounded">

                                <h6 class="collapse-header">Asset &amp; component</h6>
                                <?php if ($this->user_model->has_perm("list_asset_groups")) { ?>
                                    <?= sub_menu_item("Asset Groups", "asset_groups"); ?>
                                <?php } ?>
                                <?php if ($this->user_model->has_perm("list_assettypes")) { ?>
                                    <?= sub_menu_item("Asset Types", "assettypes"); ?>
                                <?php } ?>
                                <?= sub_menu_item("Component Types", "item_type", 'cubes'); ?>
                                <?= sub_menu_item("Asset Statuses", "AssetStatus"); ?>
                                <?= sub_menu_item("Component Statuses", "ItemStatus"); ?>
                                <?= sub_menu_item("Asset Type Colors", "AssetTypesColors"); ?>

                                <h6 class="collapse-header">Maintenance &amp; vendor</h6>
                                <?= sub_menu_item("Task Types", "Task"); ?>
                                <?= sub_menu_item("Maintenance Tasks", "Task_list"); ?>
                                <?= sub_menu_item("Vendor Part Numbers", "VendorPartNumber"); ?>
                                <?= sub_menu_item("Manufacturer Numbers", "VendorManufacturingNumber"); ?>
                                <?= sub_menu_item("Drawing Numbers", "VendorManufacturingDrawingNumber"); ?>
                                <?= sub_menu_item("Managed By", "ManagedByAddData"); ?>
                                <?= sub_menu_item("Maintenance Colors", "MaintenanceTypeColorCode"); ?>
                                <?= sub_menu_item("Fault Colors", "FaultTypeColorCode"); ?>

                                <h6 class="collapse-header">Location &amp; lifecycle</h6>
                                <?= sub_menu_item("States", "States", 'archway'); ?>
                                <?= sub_menu_item("Locations", "locations", 'map-marker-alt'); ?>
                                <?= sub_menu_item("Store Locations", "StoreLocation", 'warehouse'); ?>
                                <?= sub_menu_item("Disposal Methods", "DisposalMethod", 'recycle'); ?>
                                <?= sub_menu_item("Write Off Reasons", "write_off_reasons", 'trash-alt'); ?>

                                <h6 class="collapse-header">System appearance</h6>
                                <?= sub_menu_item("Dashboard Colors", "DashboardStatusColor", 'palette'); ?>
                                <?= sub_menu_item("Logo Image", "LogoImage", 'image'); ?>
                                <!-- <?= sub_menu_item("Fault Type", "FaultType"); ?>
                                <?= sub_menu_item("Maintenence Type", "MaintenenceType"); ?> -->

                                <!-- <?php if ($this->user_model->has_perm("list_wastage_types")) { ?>
                        <?= sub_menu_item("Wastage types", "wastage_types"); ?>
                        <?php } ?>

                        <?php if ($this->user_model->has_perm("list_banks")) { ?>
                        <?= sub_menu_item("Bank", "banks"); ?>
                        <?php } ?>

                        <?php if ($this->user_model->has_perm("list_fault_lists") || 1) { ?>
                        <?= sub_menu_item("Fault List", "fault_lists"); ?>
                        <?php } ?>

                        <?php if ($this->user_model->has_perm("list_branch_office_lists")) { ?>
                        <?= sub_menu_item("Branch Office List", "branch_office_lists"); ?>
                        <?php } ?>

                        <?php if ($this->user_model->has_perm("list_licence_types") || 1) { ?>
                        <?= sub_menu_item("Licence types", "licence_types"); ?>
                        <?php } ?>

                        <?php if ($this->user_model->has_perm("list_insurance_companies")) { ?>
                        <?= sub_menu_item("Insurance Companies", "insurance_companies"); ?>
                        <?php } ?> -->

                                <h6 class="collapse-header">Users &amp; permissions</h6>

                                <?php if ($this->user_model->has_perm("list_user_groups")) { ?>
                                    <?= sub_menu_item("User Groups", "user_groups"); ?>
                                <?php } ?>
                                <?php if ($this->user_model->has_perm("list_designations")) { ?>
                                    <?= sub_menu_item("Designations", "designations"); ?>
                                <?php } ?>
                                <?php if ($this->user_model->has_perm("list_worker_locations")) { ?>
                                    <?= sub_menu_item("Worker Locations", "worker_locations"); ?>
                                <?php } ?>
                                <?php if ($this->user_model->has_perm("list_permissions")) { ?>
                                    <?= sub_menu_item("Permissions", "permissions"); ?>
                                <?php } ?>
                                <!-- <h6 class="collapse-header">Incidents:</h6>

                        <?= sub_menu_item("Incident types", "incident_types"); ?> -->
                                <?php //sub_menu_item("Companies", "masters_companies");
                                ?>
                                <!-- <?= sub_menu_item("Land Fied", "land_field"); ?> -->

                            </div>
                        </div>
                    </li>
                <?php } ?>

                <?php if ($this->user_model->has_perm("view_logs")) { ?>
                    <?= main_menu_item("Logs", "log_viewer", 'film'); ?>
                <?php } ?>


            <?php } ?>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
             
        <div class="container my-auto">
            <div class="copyright text-center my-auto">
                <span style="color: #fff;">Version 1.17 – Bytespace Sdn Bhd</span>
            </div>
        </div>
    
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar static-top"
                    style="border-radius: 20px; margin: 0px 20px; margin-top: 10px;">

                    <!-- Undo: topbar logo placement removed per request.
                    <a class="topbar-steve-logo" href="<?= base_url() ?>" aria-label="SteVe Home">
                        <img src="<?= site_url('design/img/logo-new.png'); ?>" alt="SteVe" />
                    </a>
                    -->
                    <div class="topbar-page-title">
                        <?= mb_strtoupper($title ?? '') ?><?= ($_SESSION['user']->company_name ? " - " . $_SESSION['user']->company_name : ''); ?>
                    </div>
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>
                        <?php if (!$_SESSION['user']->company_id) { ?>

                            <li class="nav-item dropdown no-arrow mt-3">

                                <?php if (isset($_SESSION['logo_image_path']) && $_SESSION['logo_image_path']) { ?>
                                    <img class=" img-thumbnail profile_picture"
                                        src="<?= base_url($_SESSION['logo_image_path']); ?>" />
                                <?php } else { ?>

                                <?php } ?>
                            </li>

                        <?php } ?>
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow mt-3">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php if ($_SESSION['user']->profile_picture) { ?>
                                    <img class="rounded-circle img-thumbnail profile_picture"
                                        src="<?= site_url("storage/User-" . $_SESSION['user']->user_id . "/" . $_SESSION['user']->profile_picture); ?>" />
                                <?php } else { ?>
                                    <i class="fa fa-user"></i>
                                <?php } ?>
                                &nbsp;
                                <span
                                    class="mr-2 d-none d-lg-inline text-white small"><?= $_SESSION['full_name']; ?></span>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= site_url("user/settings"); ?>">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <h5 style="color:rgb(2, 2, 2); font-size: 1.8rem; font-weight: bold; letter-spacing: 1px;margin: 0px 20px;">
                    <?= mb_strtoupper($title ?? '') ?>
                    <?= ($_SESSION['user']->company_name ? " - " . $_SESSION['user']->company_name : ''); ?>
                </h5>


                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <?php if ($this->input->get("error")) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="danger">
                            <?= $this->input->get("error"); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        </div>
                    <?php } ?>
                    <?php if ($this->input->get("warning")) { ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="warning">
                            <?= $this->input->get("warning"); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        </div>
                    <?php } ?>
                    <?php if ($this->input->get("message")) { ?>
                        <div class="alert alert-success alert-dismissible fade show" role="success">
                            <?= $this->input->get("message"); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        </div>
                    <?php } ?>

                    <?php
                    if (isset($title2) && trim($title2) != "") {
                        
                    }
                    ?>
                    <h1 class="btn mb-3">&nbsp;</h1>











































