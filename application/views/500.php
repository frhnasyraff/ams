<?php
$this->load->helper('url');
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>Pollstar - Internal error</title>
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/solid.css" integrity="sha384-Rw5qeepMFvJVEZdSo1nDQD5B6wX0m7c5Z/pLNvjkB14W6Yki1hKbSEQaX9ffUbWe" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/fontawesome.css" integrity="sha384-GVa9GOgVQgOk+TNYXu7S/InPTfSDTtBalSgkgqQ7sCik56N9ztlkoTr2f/T44oKV" crossorigin="anonymous">

	<!-- Bootstrap core CSS -->
	<link href="<?= site_url('design/css/bootstrap.min.css'); ?>" rel="stylesheet">
	<!-- Material Design Bootstrap -->
	<link href="<?= site_url('design/css/mdb.min.css'); ?>" rel="stylesheet">
	<!-- Your custom styles (optional) -->
	<link href="<?= site_url('design/css/style.css'); ?>" rel="stylesheet">
</head>

<body class="login">
	<div class="flex-center flex-column">
		<div class="login_box animated fadeInDown">
			<div class="logo text-center"><img src="<?= site_url('design/img/logo-white.png'); ?>" /></div>
			<div class="login_form text-center">
				<i class="fa fa-exclamation-triangle fa-5x" aria-hidden="true"></i>

				<h3 class="top-20">Ayyoh!</h3>
				<h4 class="text-info top-20">Something went wrong. Please go back and try again after a few seconds.</h4>
			</div>
		</div>
		<div class="footer animated fadeInDown">© Infinity
			<?= date("Y"); ?>
		</div>
	</div>

	<script type="text/javascript" src="<?= site_url('design/js/jquery-3.2.1.min.js'); ?>"></script>
	<!-- Bootstrap core JavaScript -->
	<script type="text/javascript" src="<?= site_url('design/js/bootstrap.min.js'); ?>"></script>
	<!-- MDB core JavaScript -->
	<script type="text/javascript" src="<?= site_url('design/js/mdb.min.js'); ?>"></script>

</body>

</html>
