<?php

include 'config/config.inc.php';
include 'config/session.php';

secure_session_start($conn);

if (!validate_session($conn)) {
  // Redirect to login page
  echo "<script>window.alert('You are not authenticated for this page. Please log in first')</script>";
  echo "<script>window.location.replace('index.php')</script>";
  exit();
}


// include 'db.php';

if (isset($_SESSION['userid']) && isset($_SESSION['name']) && isset($_SESSION['role']) && isset($_SESSION['department'])) {



  date_default_timezone_set('Asia/Kuala_Lumpur');

  $timestamp = date('Y-m-d H:i:s', strtotime('+6 hours'));



  $last_login = $timestamp;

  $userid = $_SESSION['userid'];



  $sql = "UPDATE `user` SET last_login='$last_login' WHERE userid=$userid";



  if ($conn->query($sql) === TRUE) {
  }



  $currentTime = time();



  if (isset($_SESSION['last_login'])) {

    $idle = $currentTime - $_SESSION['last_login'];



    if ($idle > (10 * 60)) {

      header('Location: logout.php');

      exit();
    }
  }



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

    <link rel="shortcut icon" href="images/thkd/thkd-butterfly.png" />

    <!-- font awesome -->
    <!-- <script src="https://kit.fontawesome.com/9dbb640724.js" crossorigin="anonymous"></script> -->
    <script src="https://kit.fontawesome.com/9e7d2c1449.js" crossorigin="anonymous"></script>



  </head>



  <style>
    @font-face {

      font-family: headerfont;

      src: url(fonts/Nunito/Nunito-SemiBold.ttf);

    }



    .icon-menu {

      padding-right: 20px;

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

      content: '\f2f5';

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
  </style>



  <body>

    <div class="container-scroller">

      <!-- partial:partials/_navbar.html -->

      <nav class="navbar bg-primary col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">

          <a class="navbar-brand brand-logo mr-5" href="main.php"><img src="images/thkd/thkd-logo.png" style="width:100%;"
              class="mr-2" alt="logo" /></a>

          <a class="navbar-brand brand-logo-mini" href="main.php"><img src="images/thkd/thkd-butterfly.png"
              alt="logo" /></a>

        </div>

        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

          <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">

            <span class="icon-menu"></span>

          </button>

          <span class="text-muted text-center text-sm-left d-block d-sm-inline-block"><a href="" target="_blank"
              style="color:#82328C;font-weight:500;margin-left:50px;">Thomson Hospital Membership Portal</a></span>

          <ul class="navbar-nav navbar-nav-right">

            <li class="nav-item nav-profile dropdown">

              <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">

                <i class="fas fa-user text-blue" alt="profile"></i>

                <?php echo $_SESSION['name']; ?>

              </a>

              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">

                <?php

                if ($_SESSION['userid'] && $_SESSION['role'] === 'IT') {



                  echo

                  "<a class='dropdown-item' href='updateprofile.php?userid=$userid'>

                  <i class='fas fa-user-cog text-blue'></i>

                  Your Profile

                </a>

                <a class='dropdown-item' href='changepass.php?userid=$userid'>

                  <i class='fas fa-key text-blue'></i>

                  Change Password

                </a>";
                }



                ?>

                <a class="dropdown-item" data-toggle="modal" data-target="#logoutModal">

                  <i class="fas fa-sign-out-alt text-blue"></i>

                  Logout

                </a>

              </div>

            </li>

          </ul>

          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">

            <span class="icon-menu"></span>

          </button>

        </div>

      </nav>

      <!-- partial -->

      <div class="container-fluid page-body-wrapper">

        <!--sidebar-->

        <nav class="sidebar sidebar-offcanvas" id="sidebar">

          <ul class="nav">

            <li class="nav-item">

              <a class="nav-link" href="main.php">

                <i class="fa-solid fa-chart-simple icon-menu"></i>

                <span class="menu-title">Dashboard</span>

              </a>

            </li>

            <li class="nav-item">

              <a class="nav-link" href="pm_tgp.php">

                <i class="fa-solid fa-person-cane icon-menu"></i>

                <span class="menu-title">Golden Privilege</span>

              </a>

            </li>

            <li class="nav-item">

              <a class="nav-link" href="pm_tkc.php">

                <i class="fa-solid fa-child icon-menu"></i>

                <span class="menu-title">Kids Club</span>

              </a>

            </li>

            <!--admin view-->

            <?php

            if ($_SESSION['role'] == 'IT') {

              echo "<li class='nav-item'>

              <a class='nav-link' href='manageuser.php'>

                <i class='fas fa-user-circle icon-menu'></i>

                <span class='menu-title'>Account List</span>

              </a>

            </li>";
            }

            ?>

          </ul>

        </nav>



        <!-- Logout Modal-->

        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
          aria-hidden="true">

          <div class="modal-dialog" role="document">

            <div class="modal-content">

              <div class="modal-header">

                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>

                <button class="close" type="button" data-dismiss="modal" aria-label="Close">

                  <span aria-hidden="true">×</span>

                </button>

              </div>

              <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>

              <div class="modal-footer">

                <button class="btn btn-danger" type="button" data-dismiss="modal">Cancel</button>

                <a class="btn btn-blue" href="logout.php?logout=2"><span>Logout</span></a>

              </div>

            </div>

          </div>

        </div>



        <!--Idle Alert-->

        <div class="modal fade" id="idleModal" tabindex="-1" role="dialog" aria-labelledby="idleModalLabel"
          data-backdrop="static" data-keyboard="false">

          <div class="modal-dialog" role="document">

            <div class="modal-content">

              <div class="modal-header bg-warning">

                <h5 class="modal-title" id="idleModalLabel"><i class="fas fa-exclamation-triangle mr-2"></i>Session Expiring Soon</h5>

              </div>

              <div class="modal-body text-center">

                <p>You have been idle. Your session will expire in:</p>

                <h2 id="idleCountdown" style="font-size:3rem;font-weight:700;color:#dc3545;">60</h2>

                <p>seconds</p>

                <p class="text-muted small">Click "Stay Logged In" to continue your session.</p>

              </div>

              <div class="modal-footer justify-content-center">

                <button class="btn btn-blue" id="stayLoggedIn"><span>Stay Logged In</span></button>

                <a class="btn btn-danger" href="logout.php"><span>Logout Now</span></a>

              </div>

            </div>

          </div>

        </div>

      <?php



    } else {

      echo "<script>window.alert('You are not authenticate for this page. Please log in first')</script>";

      echo "<script>window.location.replace('index.php')</script>";

      exit();
    }

      ?>