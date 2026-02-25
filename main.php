<?php
include 'header.php';

$yesterday = date("j F, Y", strtotime("yesterday"));
$today = date("j F, Y");

$sql = "SELECT * FROM pat_tkc UNION SELECT * from pat_tkc";
$result = $conn->query($sql);

?>

<head>
  <title>Dashboard</title>

</head>

<!-- main page -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="row">
        <!-- statistic -->
        <div class="col-md-6 mb-4 stretch-card transparent">
          <div class="card">
            <div class="card-body">
              <p class="card-title">Thomson Golden Years Privilege Applications</p>
              <div class="row pt-3">
                <div class="col-sm-4">
                  <?php
                  $date = date("Y-m-d");
                  $query = "SELECT `pat_id` FROM `pat_tgp` WHERE `status`='Pending Registration'";
                  $query_run = mysqli_query($conn, $query);
                  $row = mysqli_num_rows($query_run);
                  ?>
                  <h3 class="fs-30 font-weight-medium"><a href="#" class="text-primary"><?php echo $row; ?></a></h3>
                  <span class="font-weight-bold text-danger">Pending</span>
                </div>
                <div class="col-sm-4">
                  <?php
                  $date = date("Y-m-d");
                  $query = "SELECT `pat_id` FROM `pat_tgp` WHERE `status`='Successfully Registered In SAP'";
                  $query_run = mysqli_query($conn, $query);
                  $row = mysqli_num_rows($query_run);
                  ?>
                  <h3 class="fs-30 font-weight-medium"><a href="#" class="text-primary"><?php echo $row; ?></a></h3>
                  <span class="font-weight-bold text-success">Completed</span>
                </div>
                <div class="col-sm-4">
                  <?php
                  $date = date("Y-m-d");
                  $query = "SELECT `pat_id` FROM `pat_tgp`";
                  $query_run = mysqli_query($conn, $query);
                  $row = mysqli_num_rows($query_run);
                  ?>
                  <h3 class="fs-30 font-weight-medium"><a href="#" class="text-primary"><?php echo $row; ?></a></h3>
                  <span class="font-weight-bold text-info">Total</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4 stretch-card transparent">
          <div class="card">
            <div class="card-body">
              <p class="card-title">Thomson Kids Club Applications</p>
              <div class="row pt-3">
                <div class="col-sm-4">
                  <?php
                  $date = date("Y-m-d");
                  $query = "SELECT `child_id` FROM `child` WHERE `child_status`='Pending Registration'";
                  $query_run = mysqli_query($conn, $query);
                  $row = mysqli_num_rows($query_run);
                  ?>
                  <h3 class="fs-30 font-weight-medium"><a href="#" class="text-primary"><?php echo $row; ?></a></h3>
                  <span class="font-weight-bold text-danger">Pending</span>
                </div>
                <div class="col-sm-4">
                  <?php
                  $date = date("Y-m-d");
                  $query = "SELECT `child_id` FROM `child` WHERE `child_status`='Successfully Registered In SAP'";
                  $query_run = mysqli_query($conn, $query);
                  $row = mysqli_num_rows($query_run);
                  ?>
                  <h3 class="fs-30 font-weight-medium"><a href="#" class="text-primary"><?php echo $row; ?></a></h3>
                  <span class="font-weight-bold text-success">Completed</span>
                </div>
                <div class="col-sm-4">
                  <?php
                  $date = date("Y-m-d");
                  $query = "SELECT `child_id` FROM `child`";
                  $query_run = mysqli_query($conn, $query);
                  $row = mysqli_num_rows($query_run);
                  ?>
                  <h3 class="fs-30 font-weight-medium"><a href="#" class="text-primary"><?php echo $row; ?></a></h3>
                  <span class="font-weight-bold text-success">Total</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- top products -->
        <div class="col-md-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <p class="card-title mb-3">Membership Listing</p>
              <div class="table-responsive">
                <table id="datatable" class="display expandable-table table table-hover" style="width:100%">
                  <thead>
                    <tr>
                      <th>MRN</th>
                      <th>Patient's Name</th>
                      <th>Date Registered</th>
                      <th>Membership</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php

                    $sql_union = "SELECT * FROM (
                SELECT * FROM `pat_tkc` 
                UNION ALL 
                SELECT * FROM `pat_tgp`
             ) AS combined
             ORDER BY pat_register DESC";

                    $result_union = $conn->query($sql_union);

                    if ($result_union->num_rows > 0) {
                      // output data of each row
                      while ($row = $result_union->fetch_assoc()) {
                        $pat_mrn = $row['pat_mrn'];
                        $pat_name = $row['pat_name'];
                        $pat_register = $row['pat_register'];
                        $pat_memb = $row['pat_memb'];
                        $status = $row['status'];
                        $pat_id = $row['pat_id'];

                        $date = new DateTime($pat_register);
                        $pat_register = $date->format('d/m/Y H:i:s');



                        switch ($pat_memb) {
                          case 'TKC':
                            $pat_member = "Thomson Kids Club";
                            break;
                          case 'TGYP':
                            $pat_member = "Thomson Golden Years Privilege";
                            break;
                        }

                        echo "<tr>";

                        if ($pat_mrn === "" || $pat_mrn === NULL) {
                          echo "<td class='text-danger'>Not Available</td>";
                        } else {
                          echo "<td>$pat_mrn</td>";
                        }

                        if ($pat_memb === "TKC") {
                          $id = encryptor('encrypt', $pat_id);
                          $name = encryptor('encrypt', $pat_name);
                          echo "<td class='text-success'><a href='parent.php?id=$id&name=$name' data-toggle='tooltip' data-placement='right' title='child list'>$pat_name</a></td>";
                        } else {
                          echo strtoupper("<td class='font-weight-bold'>$pat_name</td>");
                        }

                        echo "<td>{$row['pat_register']}</td>
                                <td>$pat_member</td>";

                        if ($status === "Pending Registration") {
                          echo "<td><label class='badge badge-warning'>Pending Registration</label></td>";
                        } elseif ($status === "Successfully Registered In SAP") {
                          echo "<td><label class='badge badge-success'>Successfully Registered In SAP</label></td>";
                        } elseif ($status === "Rejected") {
                          echo "<td><label class='badge badge-danger'>Rejected</label></td>";
                        }

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