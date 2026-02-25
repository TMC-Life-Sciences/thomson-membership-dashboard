<?php
include 'header.php';

$yesterday = date("j F, Y", strtotime("yesterday"));
$today = date("j F, Y");
$sql = "SELECT * FROM pat_tgp";
$result = $conn->query($sql);

if (isset($_POST['register'])) {
  $pat_title = $_POST['pat_title'];
  $pat_name = $_POST['pat_name'];
  $pat_nric = $_POST['pat_nric'];
  $pat_nat = $_POST['pat_nat'];
  $pat_dob = $_POST['pat_dob'];
  $pat_race = $_POST['pat_race'];
  $pat_phone = $_POST['pat_phone'];
  $pat_gender = $_POST['pat_gender'];
  $pat_addr = $_POST['pat_addr'];
  $pat_state = $_POST['pat_state'];
  $pat_postcode = $_POST['pat_postcode'];
  $pat_age = $_POST['pat_age'];
  $pat_city = $_POST['pat_city'];
  $pat_email = $_POST['pat_email'];
  $pat_phone = $_POST['pat_phone'];
  $pat_memb = "Thomson Golden Privilege";
  $pat_reg = $date;
  $pat_reg_time = $time;
  $last_update = $timestamp;
  $user_update = $user_update;
  $status = "Pending Registration";

  $sql = "INSERT INTO `pat_tgp` (`pat_title`, `pat_name`, `pat_nric`, `pat_dob`, `pat_nat`, `pat_race`, `pat_gender`, `pat_addr`, `pat_state`, `pat_age`, `pat_city`, `pat_email`, `pat_phone`, `pat_memb`, `pat_reg`, `user_update`, `pat_reg_time`, `last_update`, `pat_postcode`, `status`) 
            VALUES ('$pat_title', '$pat_name', '$pat_nric', '$pat_dob', '$pat_nat', '$pat_race', '$pat_gender', '$pat_addr', '$pat_state', '$pat_age', '$pat_city', '$pat_email', '$pat_phone', '$pat_memb', '$pat_reg', '$user_update', '$pat_reg_time', '$last_update', '$pat_postcode', '$status')";

  if ($conn->query($sql) === TRUE) {
    echo "<script>window.alert('Patient Registration Successful')</script>";
    echo "<script>window.location.replace('pm_tgp.php')</script>";
  } else {
    echo "<script>window.alert('Error: " . $sql . "<br>" . $conn->error . "'); </script>";
  }
}
?>

<head>
  <title>Thomson Golden Years Privilege</title>
</head>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-3 mb-4 stretch-card transparent">
        <div class="card">
          <div class="card-body">
            <p class="card-title">Today Pending</p>
            <div class="mr-5 mt-3">
              <?php
              $date = date("Y-m-d");
              $query = "SELECT `pat_id` FROM `pat_tgp` WHERE `status`='Pending Registration' AND DATE(`pat_register`) = '$date' ORDER BY `pat_id`";
              $query_run = mysqli_query($conn, $query);
              $row = mysqli_num_rows($query_run);
              $param = encryptor('encrypt', '1');
              ?>
              <h3 class="fs-30 font-weight-medium"><a href="<?php echo "report.php?id=$param"; ?>" class="text-danger"><?php echo $row; ?></h3></a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4 stretch-card transparent">
        <div class="card">
          <div class="card-body">
            <p class="card-title">Today Completion</p>
            <div class="mr-5 mt-3">
              <?php
              $date = date("Y-m-d");
              $query = "SELECT `pat_id` FROM `pat_tgp` WHERE `status`='Successfully Registered In SAP	' AND DATE(`pat_register`) = '$date' ORDER BY `pat_id`";
              $query_run = mysqli_query($conn, $query);
              $row = mysqli_num_rows($query_run);
              $param = encryptor('encrypt', '3');
              ?>
              <h3 class="fs-30 font-weight-medium"><a href="<?php echo "report.php?id=$param"; ?>" class="text-success"><?php echo $row; ?></a></h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4 stretch-card transparent">
        <div class="card">
          <div class="card-body">
            <p class="card-title">Total Pending</p>
            <div class="mr-5 mt-3">
              <?php
              $query = "SELECT `pat_id` FROM `pat_tgp` WHERE `status`='Pending Registration' ORDER BY `pat_id`";
              $query_run = mysqli_query($conn, $query);
              $row = mysqli_num_rows($query_run);

              ?>
              <h3 class="fs-30 font-weight-medium"><a href="#" class="text-danger"><?php echo $row; ?></a></h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4 stretch-card transparent">
        <div class="card">
          <div class="card-body">
            <p class="card-title">Total Completion</p>
            <div class="mr-5 mt-3">
              <?php
              $query = "SELECT `pat_id` FROM `pat_tgp` WHERE `status`='Successfully Registered In SAP' ORDER BY `pat_id`";
              $query_run = mysqli_query($conn, $query);
              $row = mysqli_num_rows($query_run);

              ?>
              <h3 class="fs-30 font-weight-medium"><a class="text-success"><?php echo $row; ?></a></h3>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 borderless align-middle">
                <div class="mt-3 mb-3">
                  <?php
                  if ($_SESSION['role'] === 'admin' or 'IT') {
                    echo "<p class='card-title'>Thomson Golden Years Privilege Listing <button style='margin-left: 3px;' class='btn btn-success btn-sm mr-1' data-toggle='modal' data-target='#request'><i class='fa-solid fa-user-plus' data-toggle='toptooltip' data-placement='top' title='New Registration'></i>Register</button></p>";
                  } elseif ($_SESSION['role'] === 'user') {
                    echo "<p class='card-title'>Thomson Golden Years Privilege Listing</p>";
                  }
                  ?>
                </div>
              </div>
              <div class="col-md-6 borderless text-right">
                <div class="ml-xl-4 mt-3 mb-3">
                  <button type="button" onclick="exportTableToCSV()" class="btn btn-success btn-sm" data-toggle="modal" data-target="#registeruser">
                    <i class="fa-solid fa-download" style="padding-right:10px;"></i>Export List
                  </button>
                </div>
              </div>
              <div class="col-12">
                <div class="table-responsive">
                  <table id="datatable" class="display expandable-table table table-hover" style="width:100%">
                    <thead>
                      <tr>
                        <th>MRN</th>
                        <th>Patient's Name</th>
                        <th>Date Registered</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if ($result->num_rows > 0) {
                        // output data of each row 
                        while ($row = $result->fetch_assoc()) {
                          $pat_id = $row['pat_id'];
                          $pat_mrn = $row['pat_mrn'];
                          $pat_name = $row['pat_name'];
                          $date = $row['pat_register'];
                          $status = $row['status'];
                          $dpat_id = urlencode(encryptor('encrypt', $pat_id));

                          $pat_register = new DateTime($date);
                          $pat_register = $pat_register->format('d/m/Y H:i:s');

                          if ($pat_mrn === "" || $pat_mrn === NULL) {
                            echo "<td class='text-danger'>Not Available</td>";
                          } else {
                            echo "<td>$pat_mrn</td>";
                          }
                          echo strtoupper("<td class='font-weight-bold'>$pat_name</td>");
                          echo "<td>$pat_register</td>";
                          if ($status === "Pending Registration") {
                            echo "<td><label class='badge badge-warning'>Pending Registration</label></td>";
                          } elseif ($status === "Successfully Registered In SAP") {
                            echo "<td><label class='badge badge-success'>Successfully Registered In SAP</label></td>";
                          }
                          echo "<td><a href='pat_details_tgp.php?id=$dpat_id' data-toggle='tooltip' data-placement='top' title='Details'><i class='far fa-solid fa-circle-info text-primary'></i></a>
                                    <a href='update_pat_tgp.php?id=$dpat_id' data-toggle='tooltip' data-placement='top' title='Update'><i class='far fa-edit text-primary'></i></a>
                                </td>";
                          echo "</tr>";
                        }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php
    include 'footer.php';
    ?>
  </div>
</div>