<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>THKD Membership Dashboard</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="vendors/feather/feather.css">
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <link rel="shortcut icon" href="images/thkd/thkd-butterfly.png" />
    <script src="https://kit.fontawesome.com/9dbb640724.js" crossorigin="anonymous"></script>
    <!-- <script src="https://kit.fontawesome.com/9e7d2c1449.js" crossorigin="anonymous"></script> -->

    <!-- Internal Styles -->
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
            transform: perspective(1px) translateZ(0);
            box-shadow: 0 0 1px rgba(0, 0, 0, 0);
            transition: transform 0.3s ease-out;
        }

        .float-on-hover:hover,
        .float-on-hover:focus,
        .float-on-hover:active {
            transform: translateY(-8px);
        }

        .overlay {
            position: fixed;
            display: none;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
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

        .btn-blue:focus {
            box-shadow: 0 0 0 0.2rem rgba(69, 156, 253, 0.5);
        }

        .text-blue {
            color: #025930 !important;
        }

        a.text-blue:hover {
            color: #343276 !important;
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0 background overlay">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5 rounded shadow-lg">
                            <div class="brand-logo">
                                <img src="images/thkd/thkd-logo.png" alt="logo" style="width: 50%;">
                            </div>
                            <h4>Hi!</h4>
                            <h6 class="font-weight-light">Sign in to continue.</h6>
                            <form class="pt-3" method="post" action="login.php">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend bg-transparent">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="fas fa-user text-blue"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="email" class="form-control form-control-lg border-left-0" placeholder="Username">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend bg-transparent">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="fas fa-lock text-blue"></i>
                                            </span>
                                        </div>
                                        <input type="password" name="pass" class="form-control form-control-lg border-left-0" placeholder="Password">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-block btn-blue btn-lg font-weight-medium auth-form-btn">
                                        <span>Sign In </span>
                                    </button>
                                </div>
                                <?php
                                if (isset($_GET['logout'])) {
                                    $parameters = $_GET['logout'];
                                    switch ($parameters) {
                                        case '1':
                                            echo "<div class='alert alert-danger mt-3' role='alert'><strong>Warning! Logout due to inactivity</strong></div>";
                                            break;
                                        case '2':
                                            echo "<div class='alert alert-success mt-3' role='alert'>Logout successful!</div>";
                                            break;
                                        case '3':
                                            echo "<div class='alert alert-success mt-3' role='alert'>We have logged you out from another device. Please proceed to login</div>";
                                            break;
                                        case '4':
                                            echo "<div class='alert alert-warning mt-3' role='alert'>Please logout your account from another device before proceeding to login</div>";
                                            break;
                                    }
                                }
                                ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
</body>

</html>