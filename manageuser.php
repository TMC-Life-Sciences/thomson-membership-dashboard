<?php
if (isset($_SESSION['auth'])) {
}
include 'header.php';

$sql = "SELECT * FROM user";
$result = $conn->query($sql);

if (isset($_POST['submit'])) {

  $name = $_POST['name'];
  $username = $_POST['username'];
  $contact = $_POST['contact'];
  $department = $_POST['department'];
  $password = $_POST['password'];
  $cfmpassword = $_POST['cfmpassword'];
  $datecreate = $timestamp;

  if ($password === $cfmpassword) {
    $password = md5($password);
    $sql = "INSERT INTO user (`name`, `username`, `department`, `password`, `datecreate`, `status`)
                VALUES ('$name', '$username', '$department', '$password', '$datecreate', 'Active')";

    if ($conn->query($sql) === TRUE) {
      echo "<script>
        window.alert('New user registered successfully');
        </script>";
      echo "<script>
        window.location.replace('manageuser.php');
        </script>";
    } else {
      echo "<script>
        window.alert('Error: " . $sql . "<br>" . $conn->error . "');
         </script>";
    }
  } else {
    echo "<script>
            window.alert('Password does not match!');
            </script>";
  }
  $conn->close();
}
?>

<head>
  <title>Manage User</title>
</head>

<!-- partial -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card position-relative">
          <div class="card-body">
            <div id="detailedReports" class="carousel slide detailed-report-carousel position-static pt-2" data-ride="carousel">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <div class="row">
                    <div class="col-md-12 col-xl-12">
                      <div class="row ml-4">
                        <p class="card-title">User Statistic</p>
                      </div>
                      <div class="row">
                        <div class="col-md-4 border-right">
                          <div class="ml-xl-4 mt-3 mb-3">
                            <p class="card-title badge badge-success" style="color:white;">User</p>
                            <h1 class="text-primary mt-5">
                              <?php

                              $query = "SELECT `userid` FROM `user` WHERE `role`= 'User' ORDER BY `userid`";
                              $query_run = mysqli_query($conn, $query);

                              $row = mysqli_num_rows($query_run);

                              echo $row;

                              ?>
                            </h1>
                            <h3 class="font-weight-500 mb-xl-2 text-primary">Registered User</h3>
                          </div>
                        </div>
                        <div class="col-md-4 border-right">
                          <div class="ml-xl-4 mt-3 mb-3">
                            <p class="card-title badge badge-success" style="color:white;">Admin</p>
                            <h1 class="text-primary mt-5">
                              <?php

                              $query = "SELECT `userid` FROM `user` WHERE `role`= 'Admin' ORDER BY `userid`";
                              $query_run = mysqli_query($conn, $query);

                              $row = mysqli_num_rows($query_run);

                              echo $row;

                              ?>
                            </h1>
                            <h3 class="font-weight-500 mb-xl-2 text-primary">Registered User</h3>

                          </div>
                        </div>
                        <div class="col-md-4 borderless">
                          <div class="ml-xl-4 mt-3 mb-3">
                            <p class="card-title badge badge-success" style="color:white;">Technical</p>
                            <h1 class="text-primary mt-5">
                              <?php

                              $query = "SELECT `userid` FROM `user` WHERE `role`= 'IT' ORDER BY `userid`";
                              $query_run = mysqli_query($conn, $query);

                              $row = mysqli_num_rows($query_run);

                              echo $row;

                              ?>
                            </h1>
                            <h3 class="font-weight-500 mb-xl-2 text-primary">Registered User</h3>

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
      </div>
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-12 mb-4 stretch-card transparent">
            <button type="button" class="btn btn-success btn-lg btn-block" data-toggle="modal" data-target="#registeruser">
              <i class="far fa-plus-square" style="padding-right:10px;"></i>
              Register User
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <p class="card-title">Manage User</p>
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table id="datatable" class="display expandable-table table table-hover" style="width:100%">
                    <thead>
                      <tr>

                        <th>Staff Name</th>
                        <th>Employee ID</th>
                        <th>Department</th>
                        <th>Registered on</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if ($result->num_rows > 0) {
                        // output data of each row
                        while ($row = $result->fetch_assoc()) {
                          $userid = $row['userid'];
                          $name = $row['name'];
                          $username = $row['username'];
                          // $contact = $row['contact'];
                          $department = $row['department'];
                          $datecreate = $row['datecreate'];
                          $status = $row['status'];
                          $last_login = $row['last_login'];
                          echo "<tr>
                                    
                                    <td>$name</td>
                                    <td><a class='text-primary' href='mailto:'$username'>$username</a></td>
                                    <td>$department</td>
                                    <td>$datecreate</td>";

                          if ($status == 'Active') {
                            echo "<td><label class='badge badge-success'>Active</label></td>";
                          }
                          if ($status == 'Inactive') {
                            echo "<td><label class='badge badge-danger'>Inactive</label></td>";
                          }
                          echo "<td>$last_login</td>
                                <td>
                                  <a href='updateuser.php?userid=$userid' data-toggle='tooltip' data-placement='top' title='Edit Info'><i class='far fa-edit text-primary'></i> </a>
                                  <a href='deleteuser.php?userid=$userid' data-toggle='tooltip' data-placement='top' title='Delete'> <i class='far fa-trash-alt text-primary'></i></a>
                                </td>
                              </tr>";
                        }
                      } else {
                        echo "0 results";
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
  </div>

  <!-- Register Supplier -->
  <div class="modal fade" id="registeruser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Register User</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="forms-sample" method="post" action="">
            <div class="form-group">
              <label for="exampleInputName1">Full Name<span style="color: red;">*</span></label>
              <input type="text" class="form-control" name="name" placeholder="Full Name" required>
            </div>
            <div class="form-group">
              <label for="exampleInputEmail3">Employee ID<span style="color: red;">*</span></label>
              <input type="text" class="form-control" name="username" placeholder="Employee ID" required>
            </div>
            <!-- <div class="form-group">
              <label for="exampleInputEmail3">Contact Number<span style="color: red;">*</span></label>
              <input type="text" class="form-control" name="contact" placeholder="Contact Number" required>
            </div> -->
            <div class="form-group">
              <label for="exampleInputPassword4">Department<span style="color: red;">*</span></label>
              <select class="form-control" name="department" required>
                <option disabled selected>Select Department</option>
                <option value="Front Office">Front Office</option>
                <option value="Marketing">Marketing</option>
                <option value="IT">IT</option>
              </select>
            </div>
            <div class="form-group row">
              <div class="col-sm-6">
                <label for="exampleInputPassword4">Password<span style="color: red;">*</span></label>
                <input type="password" class="form-control" name="password" placeholder="Password" id="password" required>
              </div>
              <div class="col-sm-6">
                <label for="exampleInputPassword4">Confirm Password<span style="color: red;">*</span></label>
                <input type="password" class="form-control" name="cfmpassword" placeholder="Confirm Password" id="cfmpassword" required>
                <span id="message"></span>
              </div>
            </div>
            <hr>
            <button type="submit" name="submit" class="btn btn-primary mr-2">Submit</button>
            <button type="reset" class="btn btn-danger">Reset</button>
          </form>
        </div>
      </div>
    </div>
  </div>



  <?php
  include 'footer.php';
  ?>