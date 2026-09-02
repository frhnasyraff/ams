<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap core CSS -->
    <link href="<?= site_url('design/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link
        href="https://fonts.googleapis.com/css?family=Lato:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- Your custom styles (optional) -->
    <link href="<?= site_url('design/css/sb-admin-2.min.css'); ?>" rel="stylesheet">
    <link rel="icon" href="<?= site_url("favicon.ico"); ?>" type="image/x-icon" />
    <link rel="shortcut icon" href="<?= site_url("favicon.ico"); ?>" type="image/x-icon" />

    <link href="<?= site_url('design/vendor/datatables/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet">
    <link href="<?= site_url('design/vendor/datatables/dataTables.bootstrap4.css'); ?>" rel="stylesheet">
    <link href="<?= site_url('design/css/bootstrap-toggle.min.css'); ?>" rel="stylesheet">
    <link href="<?= site_url("design/css/datepicker.css"); ?>" rel="stylesheet">
    <link href="<?= site_url('design/css/daily-summary.css'); ?>" rel="stylesheet">
    <link href="<?= site_url('design/css/schedule.css'); ?>" rel="stylesheet">
    <link href="<?= site_url('design/css/fullcalendar/full-calendar.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/solid.css"
        integrity="sha384-Rw5qeepMFvJVEZdSo1nDQD5B6wX0m7c5Z/pLNvjkB14W6Yki1hKbSEQaX9ffUbWe" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/fontawesome.css"
        integrity="sha384-GVa9GOgVQgOk+TNYXu7S/InPTfSDTtBalSgkgqQ7sCik56N9ztlkoTr2f/T44oKV" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= site_url('design/css/order-report.css') ?>">
    <link href="<?= site_url('design/css/orders-list.css'); ?>" rel="stylesheet">
    <link href="<?= site_url('design/css/order-report.css'); ?>" rel="stylesheet">
    <link href="<?= site_url('design/css/feedback-form.css'); ?>" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="<?= site_url('design/js/customer-center.js'); ?> "></script>


    <title>Report</title>

    <style>
    .feedbackButton {
        background-color: #fff;
        border: 1px solid #0186D0;
        height: 30px;
        /* border-radius: 50%;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%; */
        width: 30px;
        margin-left: 70%;
        position: absolute;
        border: 0px solid;
        cursor: pointer;
    }

    input[type="radio"] {
        width: 30px;
        height: 30px;
        border-radius: 0%;
        border: 2px solid #1FBED6;
        background-color: white;
        -webkit-appearance: none;
        /*to disable the default appearance of radio button*/
        -moz-appearance: none;
    }

    input[type="radio"]:focus {
        /*no need, if you don't disable default appearance*/
        outline: none;

        /*to remove the square border on focus*/
    }

    input[type="radio"]:checked {
        /*no need, if you don't disable default appearance*/
        background-color: #1FBED6;
    }

    input[type="radio"]:checked~span:first-of-type {
        color: white;

    }

    label span:first-of-type {
        position: relative;
        left: 10px;
        font-size: 15px;
        color: #1FBED6;

    }

    label span {
        position: relative;
        top: -30px;
        /* left: 8px;
        margin-left: 4px; */

    }
    </style>
</head>

<body>

    <div class="container">
        <div class="top">
            <h1>Feedback in PDF</h1>

            <button type="button" onclick="window.print();"><i class="fa fa-download"></i></button>
        </div>
        <div class="content">
            <!-- <textarea id="remarksText" style="width:100%;height:200px"></textarea> -->
            <div class="row" style="border:2px solid black;margin:7%;height:100px;">
                <div class="col-8">
                    <img src='/design/img/logo.png' width="200px" height="150px" alt='logo'
                        style="padding: 5px;margin-top: -13px;">
                </div>
                <div class="col-4" style="right:-13px">

                    <div class="row"
                        style="border:1px solid black; border-left:2px solid black;height:50px;font-size:smaller;color:black;padding:14px;">
                        <b>Issued Date: 01/01/2023</b>
                    </div>
                    <div class="row"
                        style="border:1px solid black;border-left:2px solid black;height:49px;font-size:smaller;color:black;padding-left:14px;padding-top:6px;border-bottom: 0">
                        <b>Controlled Form No. QF ‐09, Rev 01</b>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:-10px;margin-left:24%">
                <h5 style="color:black">Customer Satisfaction Survey</h5>
            </div>
            <div class="row">
                <div class="col-8" style="padding-left: 90px;padding-top: 30px;font-size: 14px;color: black;">
                    <p>Company Name: <span style="font-weight:600;" id="feedbackCompanyName"><?= $companyName ?></span>
                    </p>
                </div>
                <div class="col-4" style="padding-top: 30px;font-size: 14px;color: black;">
                    <p>Date: <span style="font-weight:600;" id="feedbackDate"><?= $feedbackDate ?></span></p>
                </div>
            </div>
            <div class="row" style="padding-left: 90px;font-size: 14px;color: black;">
                <p>Contact Person: </p><b id="feedbackContact"><?= $contactPerson ?></b>
            </div>
            <!-- <div class="row">
                <div class="col-8"></div>
                <div class="col-2">
                    <p style="font-size: 13px;color: black;">Excellent</p>
                </div>
                <div class="col-2">
                    <p style="font-size: 13px;color: black;">Poor</p>
                </div>
            </div> -->
            <div id="feedbackQ1" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8">
                    <p>1. Courtesy & friendliness of personnel </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans1; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>

            </div>

            <div id="feedbackQ2" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8">
                    <p>2. Reply to your enquiries in terms of: </p>
                </div>
                <div class="col-4">

                </div>

            </div>
            <div id="feedbackQ2a" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8" style="padding-left: 50px;">
                    <p>a. Promptness </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans2a; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>

            </div>
            <div id="feedbackQ2b" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8" style="padding-left: 50px;">
                    <p>b. Ability to answer all your queries </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans2b; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>

            </div>

            <div id="feedbackQ3" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8">
                    <p>3. Our level of performance in meeting the delivery lead time </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans3; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>

            </div>

            <div id="feedbackQ4" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8">
                    <p>4. Quality of our products/services in terms of: </p>
                </div>
                <div class="col-4">

                </div>

            </div>
            <div id="feedbackQ4a" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8" style="padding-left: 50px;">
                    <p>a. Reliability of Waste Collection </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans4a; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>

            </div>
            <div id="feedbackQ4b" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8" style="padding-left: 50px;">
                    <p>b. Driver Attitude </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans4b; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>

            </div>
            <div id="feedbackQ4c" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8" style="padding-left: 50px;">
                    <p>c. Truck cleanliness </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans4c; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>

            </div>

            <div id="feedbackQ5" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8">
                    <p>5. Competitiveness of our services in terms of price </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans5; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>
            </div>

            <div id="feedbackQ6" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8">
                    <p>6. Our ability to provide technical support to you </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans6; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>

            </div>

            <div id="feedbackQ7" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8">
                    <p>7. After sales support on </p>
                </div>
                <div class="col-4">

                </div>

            </div>
            <div id="feedbackQ7a" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8" style="padding-left: 50px;">
                    <p>a. Services </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans7a; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>

            </div>
            <div id="feedbackQ7b" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8" style="padding-left: 50px;">
                    <p>b. Product (Bin) </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans7b; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>

            </div>
            <div id="feedbackQ7c" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8" style="padding-left: 50px;">
                    <p>c. Documentation of Invoice / SAF / DO </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans7c; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>

            </div>

            <div id="feedbackQ8" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8">
                    <p>8. Your satisfaction in our product/services </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans8; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>

            </div>

            <div id="feedbackQ9" class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <div class="col-8">
                    <p>9. Overall satisfaction in UER RESOURCES S/B </p>
                </div>
                <div class="col-4" style="display: flex;margin-left:-25px">
                    <?php for ($i = 0; $i < $feedback->ans9; $i++) { ?>
                    <span style='font-size:20px;color:#1FBED6;'>&#9733;</span>
                    <?php } ?>
                </div>

            </div>

            <div class="row" style="padding-left: 80px;font-size: 13px;color: black;">

                <p>10. Is there any improvement you would like to see in UER RESOURCES S/B.? If so, please
                    comment/ suggest. </p>
                <textarea id="feedbackQ10" style="width:92%" disabled><?= $feedback->ans10 ?></textarea>
            </div>

            <!-- <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                            <a class="btn btn-primary feedback-modal-ok" href="#">Ok</a>
                        </div> -->
            <!-- <table>
                <tr>
                    <td>Order Number : <?= $order->order_num ?></td>
                    <td>Quote Number : <?= $order->quote_number ?></td>
                </tr>
                <tr>
                    <td>Client Name : <?= $order->company_name ?></td>
                    <td>Client Address: <?= $order->address_line_1 ?></td>
                </tr>
                <tr>
                    <td>Client PIC: --</td>
                    <td>Operation Type: --</td>
                </tr>
                <tr>
                    <td>Request On: <?= $order->created_at ?></td>
                    <td>Work Start: <?= $order->progress_at ?></td>
                </tr>
                <tr>
                    <td>Approve On: <?= $order->planned_at ?></td>
                    <td>Work Complete: <?= $order->completed_at ?></td>
                </tr>
                <tr>
                    <td>Driver Name: <?= $order->worker_name ?></td>
                    <td>Truck Number: <?= $order->equipment_registration ?></td>
                </tr>

                <tr>
                    <td>(IF Operation type waste disposal)</td>
                    <td>Truck Type: <?= $order->equipment_type_name ?></td>
                </tr>
                <tr>
                    <td>Tipping Fee (TF): --</td>
                    <td>Truck Number: <?= $order->equipment_registration ?></td>
                </tr>
                <tr>
                    <td colspan="2">TF Chit Number: --</td>
                </tr>
                <tr>
                    <td>Weight: <?= $order->equipment_safe_load ?></td>
                    <td>
                        <div class="vehicle-section">
                            <div class="vehicle-images">
                                <div class="image">
                                    <img src="<?= site_url('design/img/orders/truck.png'); ?>" alt="">
                                </div>
                                <div class="image">
                                    <img src="<?= site_url('design/img/orders/truck.png'); ?>" alt="">
                                </div>
                                <div class="image">
                                    <img src="<?= site_url('design/img/orders/truck.png'); ?>" alt="">
                                </div>
                                <div class="image">
                                    <img src="<?= site_url('design/img/orders/truck.png'); ?>" alt="">
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table> -->
        </div>
    </div>
</body>


</html>