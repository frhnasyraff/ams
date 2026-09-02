<?php
$this->load->helper('url');
function main_menu_item($name, $url, $icon='', $menu='', $root = 0) {
		return '<li class="nav-item' . ("/" . $url == $_SERVER['REDIRECT_QUERY_STRING'] || $url == $_SERVER['REDIRECT_QUERY_STRING'] || $menu == $url ? ' active' : '') . '"><a class="nav-link" href="' . ($root ? $url : site_url($url)) . '">' . ($icon ? '<i class="fas fa-fw fa-' . $icon . '"></i> ' : '') . '<span>' . $name . '</span></a></li>';
}

function sub_menu_item($name, $url, $menu='', $root = 0) {
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
	<title>PKSATM<?= (isset($title) ? " - " . strip_tags($title) : ''); ?></title>

	<!-- Bootstrap core CSS -->
	<link href="<?=site_url('design/vendor/fontawesome-free/css/all.min.css');?>" rel="stylesheet">
	<!-- Material Design Bootstrap -->
	<link
		href="https://fonts.googleapis.com/css?family=Lato:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
		rel="stylesheet">
	<!-- Your custom styles (optional) -->
	<link href="<?=site_url('design/css/sb-admin-2.min.css');?>" rel="stylesheet">
	<link rel="icon" href="<?= site_url("favicon.ico"); ?>" type="image/x-icon" />
	<link rel="shortcut icon" href="<?= site_url("favicon.ico"); ?>" type="image/x-icon" />

	<link href="<?=site_url('design/vendor/datatables/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet">
	<link href="<?=site_url('design/vendor/datatables/dataTables.bootstrap4.css'); ?>" rel="stylesheet">
	<link href="<?=site_url('design/css/bootstrap-toggle.min.css'); ?>" rel="stylesheet">
	<?php if (isset($styles)) {
foreach ($styles as $style) { ?>
	<link rel="stylesheet" href="<?= (preg_match("/http/", $style) ? $style : site_url($style . "?12" )); ?>">
	<?php }} ?>

	<link href="<?=site_url('design/css/styles.css?15');?>" rel="stylesheet">
	<?php if ($_SESSION['user']->default_font) { ?>
		<link
		href="https://fonts.googleapis.com/css?family=<?= $_SESSION['user']->default_font; ?>:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
		rel="stylesheet">
		<style>
		#wrapper { font-family: "<?= $_SESSION['user']->default_font; ?>"}
		</style>
		<?php } ?>
	<style id="override">
		<?php if ($_SESSION['user']->default_color) {
			?>
		.text-primary
		{
		color:
		<?=$_SESSION['user']->default_color;
		?>
		 !important;
		}
		.bg-gradient-primary, .dropdown-item.active, .dropdown-item:active
		{
		background-color:
		<?=$_SESSION['user']->default_color;
		?>;
		background-image:
		none;
		}
		table.dataTable thead tr {
			color: white;
		}
		.btn-primary, .bg-primary,
		.badge-primary, table.dataTable thead tr
		{
		background-color:
		<?=$_SESSION['user']->default_color;
		?> !important;
		border-color:
		<?=$_SESSION['user']->default_color;
		?>;
		}
		::-webkit-scrollbar-thumb
		{
		background-color:
		<?=$_SESSION['user']->default_color;
		?>;
		}
		.btn-primary.disabled,
		.btn-primary:disabled
		{
		background-color:
		<?=$_SESSION['user']->default_color;
		?>;
		border-color:
		<?=$_SESSION['user']->default_color;
		?>;
		}
		<?php
		}

		?>
	</style>
</head>

<body id="page-top">

	<!-- Page Wrapper -->
	<div id="wrapper">

		<!-- Sidebar -->
		<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

			<!-- Sidebar - Brand -->
			<a class="sidebar-brand d-flex align-items-center justify-content-left" href="index.html">
				<div class="sidebar-brand-icon mt-2">
				<img class="d-none d-md-inline full_logo"src="<?= site_url('design/img/logo-white.png'); ?>" />
					<img class="d-lg-none d-md-none half_logo"src="<?= site_url('design/img/spinner.png'); ?>" />

				</div>
				
			</a>

			<!-- Divider -->
			

			<?= main_menu_item("Home", "dashboard", 'home'); ?>
			
			<!-- Divider -->
			<hr class="sidebar-divider">			
			<?php if ($this->user_model->has_perm("list_service_requests")) { ?>
			<?= main_menu_item("Service requests", "service_requests", 'people-carry'); ?>
			<?php } ?>
			<?php if ($this->user_model->has_perm("list_vessel_visits")) { ?>
			<?= main_menu_item("Vessel schedule", "vessel_visits", 'ship'); ?>
			<?php } ?>
			<?php if ($this->user_model->has_perm("list_incidents_request")) { ?>
			<?= main_menu_item("Incidents", "Incidents", 'car-crash'); ?>
			<?php } ?>			
	
			<!-- Nav Item - Pages Collapse Menu Item Operations -->
			<?php if ($this->user_model->has_perm("list_operations")) { ?>
			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOperations"
					aria-expanded="true" aria-controls="collapseOperations">
					<i class="fas fa-fw fa-cogs"></i>
					<span>Operations</span>
				</a>
				<div id="collapseOperations" class="collapse" aria-labelledby="headingMasters"
					data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">						
						<?php if ($this->user_model->has_perm("list_operations")) { ?>
						<?= sub_menu_item("Operations Center", "operations_center"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_operations")) { ?>
						<?= sub_menu_item("Performance Center", "performance_center"); ?>						
						<?php } ?>
					</div>
				</div>
			</li>
			<?php } ?>
			
			<!-- Nav Item - Pages Collapse Menu Item Service Request -->
			<?php if ($this->user_model->has_perm("list_service_request")) { ?>
			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseServicerequest"
					aria-expanded="true" aria-controls="collapseOperations">
					<i class="fas fa-fw fa-people-carry"></i>
					<span>Service Request</span>
				</a>
				<div id="collapseServicerequest" class="collapse" aria-labelledby="headingMasters"
					data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">						
						<?php if ($this->user_model->has_perm("list_ssr_list")) { ?>
						<?= sub_menu_item("SSR list", "ssr_list"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_resource_allocation")) { ?>
						<?= sub_menu_item("Resource allocation", "resource_allocation"); ?>						
						<?php } ?>
					</div>
				</div>
			</li>
			<?php } ?>

			<!-- Nav Item - Pages Collapse Menu Item Workers--> 
			<?php if ($this->user_model->has_perm("list_workers")) { ?>
			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseWorkers"
					aria-expanded="true" aria-controls="collapseWorkers">
					<i class="fas fa-fw fa-user-cog"></i>
					<span>Workers</span>
				</a>
				<div id="collapseWorkers" class="collapse" aria-labelledby="headingWorkers"
					data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">
						<?= sub_menu_item("Workers list", "workers"); ?>
						<?php if ($this->user_model->has_perm("list_worker_groups")) {?>
						<?= sub_menu_item("Worker groups", "worker_groups"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_workers_availability")) {?>
						<?= sub_menu_item("Worker availability", "worker_availability"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_worker_group_allocation")) {?>
						<?= sub_menu_item("Worker groups allocation", "worker_group_allocation"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_worker_attendance")) { ?>
						<?= sub_menu_item("Attendance", "worker_attendance"); ?>						
						<?php } ?>
						<?php if ($this->user_model->has_perm("approve_overtime")) { ?>
						<?= sub_menu_item("Overtime approvals", "worker_attendance/overtime"); ?>						
						<?php } ?>
						<?= sub_menu_item("Public holidays", "worker_attendance/public_holidays"); ?>						
					</div>
				</div>
			</li>
			<?php } ?>
			
		<!-- Nav Item - Pages Collapse Menu Item equipment--> 
		<?php if ($this->user_model->has_perm("list_equipments")) { ?>
			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseequipments"
					aria-expanded="true" aria-controls="collapseequipments">
					<i class="fas fa-fw fa-tools"></i>
					<span>Equipments</span>
				</a>
				<div id="collapseequipments" class="collapse" aria-labelledby="headingequipments"
					data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">
						<?= sub_menu_item("Equipments list", "equipments"); ?>
						<?php if ($this->user_model->has_perm("list_equipment_groups")) {?>
						<?= sub_menu_item("Equipment groups", "equipment_groups"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_consumables")) { ?>
						<?= sub_menu_item("Consumables", "consumables"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_equipments_availability")) {?>
						<?= sub_menu_item("Equipment availability", "equipment_availability"); ?>
						<?php } ?>
					</div>
				</div>
			</li>
			<?php } ?>
				
		<!-- Nav Item - Pages Collapse Menu Item gear--> 
		<?php if ($this->user_model->has_perm("list_gears")) { ?>
			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsegears"
					aria-expanded="true" aria-controls="collapsegears">
					<i class="fas fa-fw fa-cogs"></i>
					<span>Gears</span>
				</a>
				<div id="collapsegears" class="collapse" aria-labelledby="headinggears"
					data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">
					<?= sub_menu_item("Gear list", "gears"); ?>
					<?= sub_menu_item("Gears stock", "gears/stock"); ?>
						<?php if ($this->user_model->has_perm("list_gear_groups")) {?>
						<?= sub_menu_item("Gear groups", "gear_groups"); ?>
						<?php } ?>
					</div>
				</div>
			</li>
			<?php } ?>
			
	
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
					<div class="bg-white py-2 collapse-inner rounded">						
						<?php if ($this->user_model->has_perm("list_equipments_list")) { ?>
						<?= sub_menu_item("Equipments list", "equipments_list"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_gear")) { ?>
						<?= sub_menu_item("Gear", "gear"); ?>						
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_consumables")) { ?>
						<?= sub_menu_item("Consumables", "consumables"); ?>	
						<?php if ($this->user_model->has_perm("list_maintenance_report")) { ?>
						<?= sub_menu_item("Maintenance report", "maintenance_report"); ?>
						<?php } ?>					
						<?php } ?>
					</div>
				</div>
			</li>
			<?php } ?>

		<!-- Nav Item - Pages Collapse Menu Item Billing--> 
		<?php if ($this->user_model->has_perm("list_finance_documents")) { ?>
			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBilling"
					aria-expanded="true" aria-controls="collapseBilling">
					<i class="fas fa-fw fa-file-invoice-dollar"></i>
					<span>Finance</span>
				</a>
				<div id="collapseBilling" class="collapse" aria-labelledby="headingMasters"
					data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">					
					<?php if ($this->user_model->has_perm("list_service_vouchers")) { ?>
						<?= sub_menu_item("Service vouchers", "finance/service_vouchers"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_invoices")) { ?>
						<?= sub_menu_item("Invoices", "finance/invoices"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_payroll_list")) { ?>
						<?= sub_menu_item("Payroll", "finance/payroll"); ?>		<?php } ?>
					</div>
				</div>
			</li>
			<?php } ?>

			<?php if ($this->user_model->has_perm("list_reports")) { ?>
			<?= main_menu_item("Reports", "reports", 'chart-bar'); ?>
			<?php } ?>
			
			<?php if ($this->user_model->has_perm("list_admin")) { ?>
			<!-- Divider -->
			<hr class="sidebar-divider">

			<!-- Heading -->
			<div class="sidebar-heading">
				Admin
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
					<div class="bg-white py-2 collapse-inner rounded">
						<h6 class="collapse-header">Vessels &amp; ports:</h6>

						<?php if ($this->user_model->has_perm("list_vessels")) { ?>
						<?= sub_menu_item("Vessels", "vessels"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_ports")) { ?>
						<?= sub_menu_item("Ports", "ports"); ?>
						<?php } ?>

						<h6 class="collapse-header">Operations:</h6>
						<?php if ($this->user_model->has_perm("list_operation_types")) { ?>
						<?= sub_menu_item("Operation types", "operation_types"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_delay_reasons")) { ?>
						<?= sub_menu_item("Delay reasons", "delay_reasons"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_tally_remarks")) { ?>
						<?= sub_menu_item("Tally remarks", "tally_remarks"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_rebundling_colours")) { ?>
						<?= sub_menu_item("Rebundling colours", "rebundling_colours"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_resource_types")) { ?>
						<?= sub_menu_item("Resource types", "resource_types"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_equipment_types")) { ?>
						<?= sub_menu_item("Equipment types", "equipment_types"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_gear_types")) { ?>
						<?= sub_menu_item("Gear types", "gear_types"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_manufacturers")) { ?>
						<?= sub_menu_item("Manufacturers", "manufacturers"); ?>
						<?php } ?>

						<h6 class="collapse-header">Cargo:</h6>
						<?php if ($this->user_model->has_perm("list_cargo_types")) { ?>
						<?= sub_menu_item("Cargo types", "cargo_types"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_cargo_packagings")) { ?>
						<?= sub_menu_item("Cargo packagings", "cargo_packagings"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_commodities")) { ?>
						<?= sub_menu_item("Commodities", "commodities"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_consumable_units")) { ?>
						<?= sub_menu_item("Consumable units", "consumable_units"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_wastage_types")) { ?>
						<?= sub_menu_item("Wastage types", "wastage_types"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_delay_reason")) { ?>
						<?= sub_menu_item("Delay Reason", "delay_reason"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_service_types")) { ?>
						<?= sub_menu_item("Service Types", "service_types"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_shifts")) { ?>
						<?= sub_menu_item("Shifts", "shifts"); ?>
						<?php } ?>					
						<?php if ($this->user_model->has_perm("list_maintenance_list")) { ?>
						<?= sub_menu_item("Maintenance list", "maintenance_list"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_locations")) { ?>
						<?= sub_menu_item("Locations", "locations"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_machinery_parts_list")) { ?>
						<?= sub_menu_item("Machinery parts list", "machinery_parts_list"); ?>
						<?php } ?>	
						<h6 class="collapse-header">Users &amp; permissions:</h6>
						<?php if ($this->user_model->has_perm("list_user_groups")) { ?>
						<?= sub_menu_item("User groups", "user_groups"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_designations")) { ?>
						<?= sub_menu_item("Designations", "designations"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_worker_locations")) { ?>
						<?= sub_menu_item("Worker locations", "worker_locations"); ?>
						<?php } ?>
						<?php if ($this->user_model->has_perm("list_permissions")) { ?>
						<?= sub_menu_item("Permissions", "permissions"); ?>
						<?php } ?>

						<h6 class="collapse-header">Incidents:</h6>
					
						<?= sub_menu_item("Incident types", "incident_types"); ?>
						<?= sub_menu_item("Companies", "masters_companies"); ?>
						

						
					</div>
				</div>
			</li>
			<?php } ?>
		
			
			<?php if ($this->user_model->has_perm("list_companies")) { ?>
			<?= main_menu_item("Companies", "companies", 'building'); ?>
			<?php } ?>
			<?php if ($this->user_model->has_perm("view_logs")) { ?>
			<?= main_menu_item("Logs", "log_viewer", 'film'); ?>
			<?php } ?>
			<?php } ?>

			<!-- Nav Item - Charts -->
			<li class="nav-item d-none">
				<a class="nav-link" href="charts.html">
					<i class="fas fa-fw fa-chart-area"></i>
					<span>Charts</span></a>
			</li>

			<!-- Divider -->
			<hr class="sidebar-divider d-none d-md-block">

			<!-- Sidebar Toggler (Sidebar) -->
			<div class="text-center d-none d-md-inline">
				<button class="rounded-circle border-0" id="sidebarToggle"></button>
			</div>
		</ul>
		<!-- End of Sidebar -->

		<!-- Content Wrapper -->
		<div id="content-wrapper" class="d-flex flex-column">

			<!-- Main Content -->
			<div id="content">

				<!-- Topbar -->
				<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
				<h5>GSS Port Services<?= ($_SESSION['user']->company_name ? " - " . $_SESSION['user']->company_name : ''); ?></h5>

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
						<!-- Nav Item - Alerts -->
						<li class="nav-item dropdown no-arrow mx-1">
							<a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
								data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fas fa-bell fa-fw"></i>
								<!-- Counter - Alerts -->
								<?php $alerts = $this->alerts->list($_SESSION['user']->user_id, $_SESSION['user']->active_branch); ?>
								<span class="badge badge-danger badge-counter"><?= count($alerts); ?></span>
							</a>
							<!-- Dropdown - Alerts -->
							<div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
								aria-labelledby="alertsDropdown">
								<h6 class="dropdown-header">
									Alerts Center
								</h6>
								<div class="alerts-box">
									<?php foreach($alerts as $alert) { ?>
									<a class="dropdown-item d-flex align-items-center" href="<?= $alert->url; ?>">
										<div class="mr-3">
											<div class="icon-circle bg-<?= $alert->color; ?>">
												<i class="fas fa-<?= $alert->icon; ?> text-white"></i>
											</div>
										</div>
										<div>
											<div class="small text-gray-500"><?= $this->steve->to_date_time($alert->alert_timestamp, 1); ?></div>
											<span class="font-weight-bold"><?= $alert->title; ?></span><br />
											<?= $alert->message; ?>
										</div>
									</a>
									<?php } ?>
									<span
										class="dropdown-item text-center small text-gray-500 disabled"><?= count($alerts) ? "That's all... for now! -Winnie" : "No alerts"; ?></span>
								</div>
							</div>
						</li>

						<?php $remarks = $this->alerts->list_remarks($_SESSION['user']->user_id, $_SESSION['user']->active_branch); ?>
						<li class="nav-item dropdown no-arrow mx-1">
							<a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
								data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fas fa-envelope fa-fw"></i>
								<!-- Counter - Messages -->
								<span class="badge badge-danger badge-counter"><?= count($remarks); ?></span>
							</a>
							<!-- Dropdown - Messages -->
							<div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
								aria-labelledby="messagesDropdown">
								<h6 class="dropdown-header">
									Message Center
								</h6>
								<div class="alerts-box">
									<?php foreach ($remarks as $remark) { ?>
									<a class="dropdown-item d-flex align-items-center remark_link" href="#"
										data-href="<?= site_url($remark->url); ?>" data-t="<?= $remark->t_updated; ?>"
										data-table="<?= $remark->table_name; ?>"
										data-record="<?= $remark->record_id; ?>">
										<div class="mr-3">
											<div class="icon-circle bg-<?= $remark->color; ?>">
												<i class="fas fa-<?= $remark->icon; ?> text-white"></i>
											</div>
										</div>
										<div>
											<div class="text-truncate"><?= $remark->remark; ?></div>
											<span class="font-weight-bold"><?= $remark->record_number; ?></span><br />
											<div class="small text-gray-500"><?= $remark->full_name; ?> ·
												<?= $this->steve->to_date_time($remark->t_updated, 1); ?></div>
										</div>
									</a>
									<?php } ?>
									<span
										class="dropdown-item text-center small text-gray-500 disabled"><?= count($remarks) ? "Showing replies from last seven days" : "No unread messages"; ?></span>
								</div>
							</div>
						</li>
<?php } ?>
						<div class="topbar-divider d-none d-sm-block"></div>

						<!-- Nav Item - User Information -->
						<li class="nav-item dropdown no-arrow">
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
									class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $_SESSION['full_name']; ?></span>
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

					<h1 class="h3 mb-2 text-gray-800"><?= $title; ?></h1>