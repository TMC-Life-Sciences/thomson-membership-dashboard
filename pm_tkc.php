<?php
include 'header.php';

$yesterday = date("j F, Y", strtotime("yesterday"));
$today = date("j F, Y");

if (isset($_POST['submit'])) {
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
  $pat_memb = "Thomson Kids Club";
  $pat_reg = $date;
  $pat_reg_time = $time;
  $last_update = $timestamp;
  $status = "Pending Registration";

  $sql = "INSERT INTO `pat_tkc` (`pat_title`, `pat_name`, `pat_nric`, `pat_dob`, `pat_nat`, `pat_race`, `pat_gender`, `pat_addr`, `pat_state`, `pat_age`, `pat_city`, `pat_email`, `pat_phone`, `pat_memb`, `pat_reg`, `pat_reg_time`, `last_update`, `pat_postcode`, `status`) 
            VALUES ('$pat_title', '$pat_name', '$pat_nric', '$pat_dob', '$pat_nat', '$pat_race', '$pat_gender', '$pat_addr', '$pat_state', '$pat_age', '$pat_city', '$pat_email', '$pat_phone', '$pat_memb', '$pat_reg', '$pat_reg_time', '$last_update', '$pat_postcode', '$status')";

  if ($conn->query($sql) === TRUE) {
    echo "";
  } else {
    echo "<script>window.alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
  }

  $child_name = $_POST['child_name'];
  $child_gen = $_POST['child_gen'];
  $child_age = $_POST['child_age'];
  $child_dob = $_POST['child_dob'];
  $child_reg = $date;
  $child_reg_time = $time;
  $last_update_child = $timestamp;
  $child_status = 'Pending Registration';
  $nok_name = $_POST['pat_name'];

  $sql_check = "SELECT * FROM child WHERE child_name = '$child_name'";
  $result = $conn->query($sql_check);

  if ($result->num_rows > 0) {
    echo "<script>alert('Records Already Exist');</script>";
    echo "<script>window.location.replace('tkc.php');</script>";
  } else {
    $sql1 = "INSERT INTO `child` (`child_name`, `child_gen`, `child_age`, `child_dob`, `child_reg`, `child_reg_time`, `last_update_child`, `child_status`, `nok_name`) 
                 VALUES ('$child_name', '$child_gen', '$child_age', '$child_dob', '$child_reg', '$child_reg_time', '$last_update_child', '$child_status', '$nok_name')";

    if ($conn->query($sql1) === TRUE) {
      echo "<script>window.location.replace('pm_tkc.php')</script>";
    }
  }
}
?>

<head>
  <title>Thomson Kids Club</title>
</head>

<!-- content -->
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
              $query = "SELECT `child_id` FROM `child` WHERE `child_status`='Pending Registration' AND DATE(`child_timestamp`) = '$date' ORDER BY `child_id`";
              $query_run = mysqli_query($conn, $query);
              $row = mysqli_num_rows($query_run);
              $param = encryptor('encrypt', '2');
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
              $query = "SELECT `child_id` FROM `child` WHERE `child_status`='Successfully Registered In SAP' AND DATE(`child_timestamp`) = '$date' ORDER BY `child_id`";
              $query_run = mysqli_query($conn, $query);
              $row = mysqli_num_rows($query_run);
              $param = encryptor('encrypt', '4');
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
              $query = "SELECT `child_id` FROM `child` WHERE `child_status`='Pending Registration' ORDER BY `child_id`";
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
              $query = "SELECT `child_id` FROM `child` WHERE `child_status`='Successfully Registered In SAP' ORDER BY `child_id`";
              $query_run = mysqli_query($conn, $query);
              $row = mysqli_num_rows($query_run);

              ?>
              <h3 class="fs-30 font-weight-medium"><a href="#" class="text-success"><?php echo $row; ?></a></h3>
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
                    echo "<p class='card-title'>Thomson Kids Club Listing <button style='margin-left: 3px;' class='btn btn-success btn-sm' data-toggle='modal' data-target='#request'><i class='fa-solid fa-user-plus mr-1' data-toggle='toptooltip' data-placement='top' title='New Registration'></i> Register</button></p>";
                  } else {
                    echo "<p class='card-title'>Thomson Kids Club Listing</p>";
                  }
                  ?>
                </div>
              </div>
              <div class="col-md-6 borderless text-right">
                <div class="ml-xl-4 mt-3 mb-3">
                  <button type="button" onclick="exportTableToCSV()" class="btn btn-success btn-sm" data-toggle="modal" data-target="#registeruser">
                    <i class="fa-solid fa-download" style="padding-right:10px;"></i> Export List
                  </button>
                </div>
              </div>
              <div class="col-12">
                <div class="table-responsive">
                  <table id="datatable" class="display expandable-table table table-hover" style="width:100%">
                    <thead>
                      <tr>
                        <th>MRN</th>
                        <th>Child's Name</th>
                        <th>Date Registered</th>
                        <th>Parent's Name</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT child.*, pat_tkc.* FROM `child` INNER JOIN `pat_tkc` ON child.pat_id = pat_tkc.pat_id";
                      $result = $conn->query($sql);
                      if ($result !== false && $result->num_rows > 0) {
                        // output data of each row
                        while ($row = $result->fetch_assoc()) {
                          $pat_id = $row['pat_id'];
                          $pat_name = $row['pat_name'];
                          $pat_mrn = $row['pat_mrn'];
                          $pat_title = $row['pat_title'];
                          $pat_nric = $row['pat_nric'];
                          $pat_nat = $row['pat_nat'];
                          $pat_dob = $row['pat_dob'];
                          $pat_race = $row['pat_race'];
                          $pat_phone = $row['pat_phone'];
                          $pat_gender = $row['pat_gender'];
                          $pat_addr = $row['pat_addr'];
                          $pat_state = $row['pat_state'];
                          $pat_postcode = $row['pat_postcode'];
                          $pat_age = $row['pat_age'];
                          $pat_city = $row['pat_city'];
                          $pat_email = $row['pat_email'];
                          $pat_memb = $row['pat_memb'];
                          $last_update = $row['last_update'];
                          $user_update = $row['user_update'];
                          $child_mrn = $row['child_mrn'];
                          $child_name = $row['child_name'];
                          $child_gen = $row['child_gen'];
                          $child_age = $row['child_age'];
                          $child_dob = $row['child_dob'];
                          $child_id = $row['child_id'];
                          $child_register = $row['child_timestamp'];
                          $child_status = $row['child_status'];

                          $echild_id = encryptor('encrypt', $child_id);

                          $date = new DateTime($child_register);
                          $newchildregister = $date->format('d/m/Y H:i:s');

                          echo "<tr>";
                          if ($child_mrn === "" || $child_mrn === NULL) {
                            echo "<td class='text-danger'>Not Available</td>";
                          } else {
                            echo "<td>$child_mrn</td>";
                          }
                          echo strtoupper("<td class='font-weight-bold'>$child_name</td>");
                          echo "<td>$newchildregister</td>
                                                          <td>$pat_name</td>";
                          if ($child_status === "Pending Registration") {
                            echo "<td><label class='badge badge-warning'>Pending Registration</label></td>";
                          } elseif ($child_status === "Successfully Registered In SAP") {
                            echo "<td><label class='badge badge-success'>Successfully Registered In SAP</label></td>";
                          } elseif ($child_status === "Rejected") {
                            echo "<td><label class='badge badge-danger'>Rejected</label></td>";
                          }

                          echo "<td>
                                  <a href='pat_details_tkc.php?id=$echild_id' data-toggle='tooltip' data-placement='top' title='Details'><i class='far fa-solid fa-circle-info text-primary'></i> </a>
                                  <a href='update_pat_tkc.php?id=$echild_id' data-toggle='tooltip' data-placement='top' title='Update'><i class='far fa-edit text-primary'></i> </a>
                                </td>";
                          echo "</tr>";
                        }
                      } else {
                        echo "<script>windows.alert('No records found')</script>";
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
    <?php include 'footer.php'; ?>