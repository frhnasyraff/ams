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

    <link href="<?= site_url('design/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="<?= site_url('design/css/sb-admin-2.min.css'); ?>" rel="stylesheet">
    <link href="<?= site_url('design/css/login.css?v=9'); ?>" rel="stylesheet">
</head>

<body>
<div class="login-shell neon-login-shell">
    <div class="ambient-ring ring-left"></div>
    <div class="ambient-ring ring-right"></div>
    <div class="ambient-dots"></div>

    <section class="login-panel">
        <div class="login-card-wrap">
            <form method="post" action="<?= site_url('user/login'); ?>" class="login-card-modern neon-login-card">
                <div class="login-card-glow"></div>

                <div class="login-brand neon-login-brand custom-steve-logo"><span class="steve-mark" aria-hidden="true"></span><span>SteVe</span></div>

                <div class="login-heading-block">
                    <h1>Welcome back</h1>
                    <p>All in One Inventory Management System</p>
                </div>

                <?php if ($this->input->get("error")) { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="danger">
                        <?= $this->input->get("error"); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php } ?>

                <?php if ($this->input->get("message")) { ?>
                    <div class="alert alert-success alert-dismissible fade show" role="success">
                        <?= $this->input->get("message"); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php } ?>

                <div class="login-field-group">
                    <label for="InputUsername">Username</label>
                    <div class="input-shell">
                        <i class="far fa-user"></i>
                        <input type="text" id="InputUsername" name="username" placeholder="Enter your username" autocomplete="username" required>
                    </div>
                </div>

                <div class="login-field-group">
                    <label for="exampleInputPassword">Password</label>
                    <div class="input-shell">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="exampleInputPassword" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                        <span class="input-eye"><i class="far fa-eye"></i></span>
                    </div>
                </div>

                <div class="login-actions-row">
                    <span></span>
                    <a class="forgot-link" href="javascript:void(0)">Forgot password?</a>
                </div>

                <button type="submit" class="login-submit-btn">
                    <span>Log In</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </section>

    <section class="login-hero neon-login-hero">
        <div class="hero-copy">
            <h2>Inventory<br>Management<br><span>System</span></h2>
            <p>Streamline operations, track inventory in real-time, and make smarter decisions with confidence.</p>
        </div>

        <div class="hero-visual neon-hero-visual">
            <div class="hero-wave wave-one"></div>
            <div class="hero-wave wave-two"></div>
            <div class="warehouse-scene">
                <div class="plant"><span></span><span></span><span></span></div>
                <div class="trolley"><i class="fas fa-dolly"></i></div>
                <div class="box box-a"><i class="fas fa-box"></i></div>
                <div class="box box-b"><i class="fas fa-box-open"></i></div>
                <div class="box box-c"><i class="fas fa-archive"></i></div>

                <div class="dashboard-mockup">
                    <div class="mockup-top">
                        <strong>Dashboard</strong>
                        <span></span><span></span><span></span>
                    </div>
                    <div class="mockup-stats">
                        <div><small>Total Items</small><strong>8,450</strong><i class="fas fa-cube"></i></div>
                        <div><small>Low Stock</small><strong>320</strong><i class="fas fa-exclamation-triangle"></i></div>
                        <div><small>Orders</small><strong>1,245</strong><i class="fas fa-shopping-cart"></i></div>
                        <div><small>Suppliers</small><strong>64</strong><i class="fas fa-users"></i></div>
                    </div>
                    <div class="mockup-lower">
                        <div class="mini-chart">
                            <span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <div class="mockup-donut"></div>
                    </div>
                </div>

                <div class="check-board">
                    <i class="fas fa-check-square"></i>
                    <i class="fas fa-check-square"></i>
                    <i class="fas fa-check-square"></i>
                </div>
                <div class="shield-badge"><i class="fas fa-check"></i></div>
            </div>
        </div>

        <div class="hero-features">
            <div><i class="fas fa-cube"></i><strong>Real-time Tracking</strong><small>Always know what you have</small></div>
            <div><i class="fas fa-chart-bar"></i><strong>Smart Insights</strong><small>Data-driven stock decisions</small></div>
            <div><i class="fas fa-shield-alt"></i><strong>Secure & Reliable</strong><small>Enterprise-grade security</small></div>
        </div>
    </section>
</div>

<script src="<?= site_url('design/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?= site_url('design/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?= site_url('design/vendor/jquery-easing/jquery.easing.min.js'); ?>"></script>
<script src="<?= site_url('design/js/sb-admin-2.min.js'); ?>"></script>
</body>
</html>






