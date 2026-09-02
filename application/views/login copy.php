<?php
$this->load->helper('url');

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Vinu">
  <title>Steve</title>

  <!-- Bootstrap core CSS -->
  <link href="<?=site_url('design/vendor/fontawesome-free/css/all.min.css');?>" rel="stylesheet">

  <!-- Material Design Bootstrap -->
  <link
    href="https://fonts.googleapis.com/css?family=Lato:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
    rel="stylesheet">

  <!-- Your custom styles (optional) -->
  <link href="<?=site_url('design/css/sb-admin-2.min.css');?>" rel="stylesheet">
</head>

<body class="bg-white">
  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-6">
                <div class="p-5">
                  <h1 class="h4 text-gray-900 mb-4 mt-4">User Login</h1>

                  <?php if ($this->input->get("error")) { ?>
                  <div class="alert alert-danger alert-dismissible fade show" role="danger">
                    <?= $this->input->get("error"); ?>
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
                  <form method="post" action="<?=site_url('user/login');?>">
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="InputUsername"
                        aria-describedby="usernameHelp" placeholder="Enter username" name="username">
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" id="exampleInputPassword"
                        placeholder="Enter password" name="password">
                    </div>
                    <div class="form-group">
                      <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="customCheck" name="remember_me"
                          value="1" checked>
                        <label class="custom-control-label" for="customCheck">Remember Me</label>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-user btn-block" style="background-color: #0067CA;">Login</button>
                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="#">Forgot Password?</a>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
            </div>

            <!-- Bootstrap core JavaScript-->
            <script src="<?=site_url('design/vendor/jquery/jquery.min.js');?>"></script>
            <script src="<?=site_url('design/vendor/bootstrap/js/bootstrap.bundle.min.js');?>"></script>

            <!-- Core plugin JavaScript-->
            <script src="<?=site_url('design/vendor/jquery-easing/jquery.easing.min.js');?>"></script>

            <!-- Custom scripts for all pages-->
            <script src="<?=site_url('design/js/sb-admin-2.min.js');?>"></script>

</body>

</html>