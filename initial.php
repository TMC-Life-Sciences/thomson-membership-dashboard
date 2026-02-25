<?php
require __DIR__ . '/config/config.inc.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Change Password</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/feather/feather.css">
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/thkd/thkd-butterfly.png" />
    <!-- font awesome -->
    <script src="https://kit.fontawesome.com/9dbb640724.js" crossorigin="anonymous"></script>
</head>

<style>
    .background {
        background-image: url('images/thkd/bg.jpg');
        background-size: cover;
        max-width: 100%;
        height: auto;
    }

    .float-on-hover {
        display: inline-block;
        vertical-align: middle;
        -webkit-transform: perspective(1px) translateZ(0);
        transform: perspective(1px) translateZ(0);
        box-shadow: 0 0 1px rgba(0, 0, 0, 0);
        -webkit-transition-duration: 0.3s;
        transition-duration: 0.3s;
        -webkit-transition-property: transform;
        transition-property: transform;
        -webkit-transition-timing-function: ease-out;
        transition-timing-function: ease-out;
    }

    .float-on-hover:hover,
    .float-on-hover:focus,
    .float-on-hover:active {
        -webkit-transform: translateY(-8px);
        transform: translateY(-8px);
    }

    .overlay {
        position: fixed;
        display: none;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 2;
    }

    .btn-blue {
        color: #fff;
        background-color: #025930;
        border-color: #025930;
    }

    .btn-blue span {
        cursor: pointer;
        display: inline-block;
        position: relative;
        transition: 0.5s;
    }

    .btn-blue span:after {
        content: '\f2f6';
        font-family: FontAwesome;
        font-weight: 400;
        position: absolute;
        opacity: 0;
        top: 0;
        right: -20px;
        transition: 0.5s;
    }

    .btn-blue:hover span {
        padding-right: 25px;
        color: white;
    }

    .btn-blue:hover span:after {
        opacity: 1;
        right: 0;
    }

    .btn-blue:focus,
    .btn-blue.focus {
        color: #fff;
        background-color: #025930;
        border-color: #025930;
        box-shadow: 0 0 0 0.2rem rgba(69, 156, 253, 0.5);
    }

    .btn-blue.disabled,
    .btn-blue:disabled {
        color: #fff;
        background-color: #025930;
        border-color: #025930;
    }

    .btn-blue:not(:disabled):not(.disabled):active,
    .btn-blue:not(:disabled):not(.disabled).active,
    .show>.btn-blue.dropdown-toggle {
        color: #fff;
        background-color: #025930;
        border-color: #025930;
    }

    .btn-blue:not(:disabled):not(.disabled):active:focus,
    .btn-blue:not(:disabled):not(.disabled).active:focus,
    .show>.btn-blue.dropdown-toggle:focus {
        box-shadow: 0 0 0 0.2rem rgba(69, 156, 253, 0.5);
    }

    .text-blue,
    .list-wrapper .completed .remove {
        color: #025930 !important;
    }

    a.text-blue:hover,
    .list-wrapper .completed a.remove:hover,
    a.text-blue:focus,
    .list-wrapper .completed a.remove:focus {
        color: #343276 !important;
    }
</style>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0 background overlay">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div id="successContainer" class="mb-3"></div>
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5 rounded shadow-lg">
                            <?php

                            if (isset($_GET['username'])) {

                                $u = $_GET['username'];
                                $i = encryptor('decrypt', $u);

                                $sql = "SELECT * FROM `user` WHERE username='$i'";
                                $result = mysqli_query($conn, $sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {

                                        if ($_SERVER["REQUEST_METHOD"] === "POST") {
                                            $password = mysqli_real_escape_string($conn, $_POST['password']);
                                            $cfmpassword = mysqli_real_escape_string($conn, $_POST['cfmpassword']);

                                            if ($password === $cfmpassword) {
                                                $hashedPassword = hash('sha256', $password);

                                                $initial = 0;
                                                $sql = "UPDATE `user` SET `password` = ?, `initial` = ? WHERE `username` = ?";
                                                $stmt = $conn->prepare($sql);

                                                if ($stmt) {
                                                    $stmt->bind_param("sss", $hashedPassword, $initial, $i);

                                                    // Execute the query
                                                    if ($stmt->execute()) {
                                                        echo "<script>
                                                                    alert('Password changed successfully! Redirecting to login page.');
                                                                    window.location.replace('index.php');
                                                                </script>";
                                                    } else {
                                                        error_log("Error updating password: " . $stmt->error);
                                                        echo "<script>alert('Error updating password! Please contact your system administrator')</script>";
                                                        echo "<script>window.history.back()</script>";
                                                    }
                                                } else {
                                                    error_log("Error preparing UPDATE query: " . $conn->error);
                                                    echo "<script>alert('Error preparing query! Please contact your system administrator')</script>";
                                                    echo "<script>window.history.back()</script>";
                                                }
                                            } else {
                                                echo "<div class='alert alert-danger col-sm-12'>
                                                <strong>Password does not match!</strong> Retry again.
                                            </div>";
                                            }
                                        }
                                    }
                                } else {
                                    echo "<script>alert('User not found! Please contact your system administrator')</script>";
                                    echo "<script>window.location.replace('index.php')</script>";
                                }
                            } else {
                                echo "<script>alert('Unable to fetch request! Please contact your system administrator')</script>";
                                echo "<script>window.location.replace('index.php')</script>";
                            }

                            ?>

                            <form class="pt-3" method="post">
                                <div class="form-group">
                                    <h4 class="font-weight-bold mb-2">Change Password</h4>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend bg-transparent">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="fa-solid fa-user text-blue"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="userid" class="form-control form-control-lg border-left-0" placeholder="User ID" value="<?php echo $i; ?>" readonly required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend bg-transparent">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="fas fa-lock text-blue"></i>
                                            </span>
                                        </div>
                                        <input type="password" name="password" class="form-control form-control-lg border-left-0" placeholder="Password" id="password" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend bg-transparent">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="fas fa-lock text-blue"></i>
                                            </span>
                                        </div>
                                        <input type="password" name="cfmpassword" class="form-control form-control-lg border-left-0" placeholder="Confirm Password" id="cfmpassword" required>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" name="submit" class="btn btn-block btn-blue btn-lg font-weight-medium auth-form-btn"><span>Update Password </span></button>
                                    <button class="btn btn-block btn-danger btn-lg font-weight-medium auth-form-btn" onclick="window.location.replace('index.php')"><span>Back to Login </span></button>
                                </div>
                            </form>
                            <div id="alertContainer" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Alert!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="text-danger">Please change your password before proceed</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Continue</button>
                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                </div>
            </div>
        </div>
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    <!-- endinject -->
    <script>
        $('#password, #cfmpassword').on('keyup', function() {
            if ($('#password').val() == $('#cfmpassword').val()) {
                showBootstrapAlert('Password Match.', 'success');
            } else {
                showBootstrapAlert('Passwords Do Not Match.', 'danger');
            }
        });

        function showBootstrapAlert(message, alertType) {
            // Create a Bootstrap alert element dynamically using jQuery
            var alertContainer = $('#alertContainer');
            var alertDiv = $('<div>').addClass('alert alert-' + alertType).html('<strong>' + message + '</strong>');
            alertContainer.empty(); // Clear previous alerts
            alertContainer.append(alertDiv);
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#exampleModal').modal('show');
        });
    </script>

</body>

</html>