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
    <link href="<?= site_url('design/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet">

    <!-- Material Design Bootstrap -->
    <link
        href="https://fonts.googleapis.com/css?family=Lato:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Your custom styles (optional) -->
    <link href="<?= site_url('design/css/sb-admin-2.min.css'); ?>" rel="stylesheet">
    <link href="<?= site_url('design/css/login.css'); ?>" rel="stylesheet">

    <?php if ($this->input->get("error") || $this->input->get("message")) { ?>
    <style>
    .form-fields {
        margin: 10% auto 0 auto !important
    }
    </style>
    <?php } ?>

</head>

<body>

    <div class="main-container">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-8 px-0">
                <div class="card login-card">
                    <div class="rectangle17">
                        <div class="text-area">
                            <div class="welcomeText">Welcome</div>
                            <div class="loginText">Log In</div>
                        </div>
                    </div>
                    <div class="form-container">
                        <?php if ($this->input->get("error")) { ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-0" role="danger">
                            <?= $this->input->get("error"); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        </div>
                        <?php } ?>
                        <?php if ($this->input->get("message")) { ?>
                        <div class="alert alert-success alert-dismissible fade show mb-0" role="success">
                            <?= $this->input->get("message"); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        </div>
                        <?php } ?>
                        <form method="post" action="<?= site_url('user/login'); ?>">
                            <div class="form-fields">
                                <div class="form-group">
                                    <label for="">Username</label>
                                    <input type="text" class="form-control-user-2" id="InputUsername"
                                        aria-describedby="usernameHelp" name="username">
                                </div>
                                <div class="form-group">
                                    <label for="">Password</label>
                                    <input type="password" class="form-control-user-2" id="exampleInputPassword"
                                        name="password">
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck"
                                            name="remember_me" value="1" checked>
                                        <label class="custom-control-label" for="customCheck">Remember Me</label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="btn-user">Log In</button>
                                <p class="text-center mt-2 mb-0">version 1.20</p>
                            </div>
                            <div class="text-right mt-3">
                                <img src="<?= site_url('design/img/logo-dark-login.png') ?>" width="100" alt="">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8 px-0 position-relative">
                <div class="img-wrapper">
                    <img src="<?= site_url('design/img/login-bg.png') ?>" alt="">
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap core JavaScript-->
    <script src="<?= site_url('design/vendor/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= site_url('design/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <!-- Core plugin JavaScript-->
    <script src="<?= site_url('design/vendor/jquery-easing/jquery.easing.min.js'); ?>"></script>
    <!-- Custom scripts for all pages-->
    <script src="<?= site_url('design/js/sb-admin-2.min.js'); ?>"></script>
</body>

</html>