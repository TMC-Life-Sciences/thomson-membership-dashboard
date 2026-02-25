<?php
include 'header.php';

$yesterday = date("j F, Y", strtotime("yesterday"));
$today = date("j F, Y");

if (isset($_GET['id'])) {
  $eparam = $_GET['id'];
  $param = encryptor('decrypt', $eparam);
} else {
  echo "<script>alert('Error while fetching data! Please try again')</script>";
  echo "<script>window.history.back()</script>";
}

?>

<head>
  <title>Report</title>
</head>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 borderless align-middle">
                <div class="mt-3 mb-3">
                  <p class='card-title'>
                    <?php
                    switch ($param) {
                      case '1':
                      case '3':
                        echo "Thomson Golden Years Privilege";
                        break;

                      case '2':
                      case '4':
                        echo "Thomson Kids Club";
                        break;
                    }
                    ?>
                    <span class="badge badge-warning">Today's Pending Registration</span>
                  </p>
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
                      $currentdate = date("Y-m-d");

                      function displayRow($data, $isChild = false)
                      {
                        $id = $data[$isChild ? 'child_id' : 'pat_id'];
                        $mrn = $data[$isChild ? 'child_mrn' : 'pat_mrn'];
                        $name = $data[$isChild ? 'child_name' : 'pat_name'];
                        $timestamp = $data[$isChild ? 'child_timestamp' : 'pat_register'];
                        $status = $data[$isChild ? 'child_status' : 'status'];

                        $encrypted_id = encryptor('encrypt', $id);

                        $date = new DateTime($timestamp);
                        $formatted_date = $date->format('d/m/Y H:i:s');

                        echo "<tr>";

                        if (empty($mrn)) {
                          echo "<td class='text-danger'>Not Available</td>";
                        } else {
                          echo "<td>$mrn</td>";
                        }

                        echo "<td class='font-weight-bold'>" . strtoupper($name) . "</td>";

                        echo "<td>$formatted_date</td>";

                        if ($status === "Pending Registration") {
                          echo "<td><label class='badge badge-warning'>Pending Registration</label></td>";
                        } elseif ($status === "Successfully Registered In SAP") {
                          echo "<td><label class='badge badge-success'>Successfully Registered In SAP</label></td>";
                        }

                        $details_link = $isChild ? "pat_details_tkc.php" : "pat_details_tgp.php";
                        $update_link = $isChild ? "update_pat_tkc.php" : "update_pat_tgp.php";

                        echo "<td>
                              <a href='$details_link?id=$encrypted_id' data-toggle='tooltip' data-placement='top' title='Details'>
                                  <i class='far fa-solid fa-circle-info text-primary'></i>
                              </a>
                              <a href='$update_link?id=$encrypted_id' data-toggle='tooltip' data-placement='top' title='Update'>
                                  <i class='far fa-edit text-primary'></i>
                              </a>
                            </td>";
                        echo "</tr>";
                      }

                      switch ($param) {
                        case '1':
                          $query = "SELECT `pat_id`, `pat_mrn`, `pat_name`, `pat_register`, `status` 
                                    FROM `pat_tgp` 
                                    WHERE `status` = 'Pending Registration' AND DATE(`pat_register`) = ?";

                          $stmt = $conn->prepare($query);
                          $stmt->bind_param("s", $currentdate);
                          $stmt->execute();
                          $result = $stmt->get_result();

                          if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              displayRow($row, false);
                            }
                          }
                          break;

                        case '2':
                          $query = "SELECT `child_id`, `child_mrn`, `child_name`, `child_timestamp`, `child_status` 
                                    FROM `child` 
                                    WHERE `child_status` = 'Pending Registration' AND DATE(`child_timestamp`) = ?";

                          $stmt = $conn->prepare($query);
                          $stmt->bind_param("s", $currentdate);
                          $stmt->execute();
                          $result = $stmt->get_result();

                          if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              displayRow($row, true);
                            }
                          }
                          break;

                        case '3':
                          $query = "SELECT `pat_id`, `pat_mrn`, `pat_name`, `pat_register`, `status` 
                                    FROM `pat_tgp` 
                                    WHERE `status` = 'Successfully Registered In SAP' AND DATE(`pat_register`) = ?";

                          $stmt = $conn->prepare($query);
                          $stmt->bind_param("s", $currentdate);
                          $stmt->execute();
                          $result = $stmt->get_result();

                          if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              displayRow($row, false);
                            }
                          }
                          break;

                        case '4':
                          $query = "SELECT `child_id`, `child_mrn`, `child_name`, `child_timestamp`, `child_status` 
                                    FROM `child` 
                                    WHERE `child_status` = 'Successfully Registered In SAP' AND DATE(`child_timestamp`) = ?";

                          $stmt = $conn->prepare($query);
                          $stmt->bind_param("s", $currentdate);
                          $stmt->execute();
                          $result = $stmt->get_result();

                          if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              displayRow($row, true);
                            }
                          }
                          break;
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