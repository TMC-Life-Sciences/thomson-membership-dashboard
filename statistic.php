<?php
include 'db.php';

$yesterday = date("j F, Y", strtotime("yesterday"));
$today = date("j F, Y");
$sql = "SELECT * FROM `inventory` ORDER BY `quantity` DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/feather/feather.css">
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/logo/favicon.png" />
    <!-- font awesome -->
    <script src="https://kit.fontawesome.com/f2a6503275.js" crossorigin="anonymous"></script>


</head>

<body style="background-color: white;">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="col-md-12 grid-margin stretch-card">
                                <div class="card position-relative">
                                    <div class="card-body">
                                        <div id="detailedReports" class="carousel slide detailed-report-carousel position-static pt-2" data-ride="carousel">
                                            <div class="carousel-inner">
                                                <div class="carousel-item active">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xl-12">

                                                            <div class="row">
                                                                <div class="col-md-6 border-right">
                                                                    <div class="ml-xl-4 mt-3 mb-3">
                                                                        <p class="card-title">Stock In</p>
                                                                        <h1 class="text-primary mt-5">
                                                                            <?php

                                                                            $date = date("d/m/y");

                                                                            $query = "SELECT in_id FROM `stock_in` WHERE `date`='$date' ORDER BY in_id";
                                                                            $query_run = mysqli_query($con, $query);

                                                                            $row = mysqli_num_rows($query_run);

                                                                            echo $row;

                                                                            ?>
                                                                        </h1>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="carousel-item">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xl-12">
                                                            <div class="row">
                                                                <div class="col-md-6 borderless">
                                                                    <div class="ml-xl-4 mt-3 mb-3">
                                                                        <p class="card-title">Stock Out</p>
                                                                        <h1 class="text-primary mt-5">
                                                                            <?php

                                                                            $date = date("d/m/y");

                                                                            $query = "SELECT transaction_id FROM `stock_out` WHERE `date`='$date'  ORDER BY transaction_id";
                                                                            $query_run = mysqli_query($con, $query);

                                                                            $row = mysqli_num_rows($query_run);

                                                                            echo $row;

                                                                            ?>
                                                                        </h1>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <a class="carousel-control-prev" href="#detailedReports" role="button" data-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                            <a class="carousel-control-next" href="#detailedReports" role="button" data-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- partial -->
    </div>
    <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="vendors/chart.js/Chart.min.js"></script>
    <script src="vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
    <script src="js/dataTables.select.min.js"></script>

    <!-- End plugin js for this page -->

    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    <!-- endinject -->

    <!-- Custom js for this page-->
    <script src="js/dashboard.js"></script>
    <script src="js/Chart.roundedBarCharts.js"></script>

    <!-- Plugin js for select -->
    <script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
    <script src="vendors/select2/select2.min.js"></script>
    <script src="js/file-upload.js"></script>
    <script src="js/typeahead.js"></script>
    <script src="js/select2.js"></script>

    <!-- tooltip -->
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>


</body>

</html>