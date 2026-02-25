<?php
if (isset($_SESSION['auth'])) {
}
include 'header.php';
include 'db.php';

//RETRIEVE DATA
if (isset($_GET['userid'])) {
    $userid = $_GET['userid'];
    $sql = "SELECT * FROM user WHERE userid=$userid";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            $userid = $row['userid'];
            $name = $row['name'];
            $email = $row['email'];
            $contact = $row['contact'];
            $department = $row['department'];
            $datecreate = $row['datecreate'];
        }
    } else {
        echo "<script>
            windows.alert('Error: Update not successful')
          </script>";
    }
}
?>

<head>
    <title>Add User</title>
</head>

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <div class="card-body">
                                <p class="mb-4">User Counter</p>
                                <p class="fs-30 mb-2">
                                    <?php

                                    $query = "SELECT userid FROM user ORDER BY userid";
                                    $query_run = mysqli_query($con, $query);

                                    $row = mysqli_num_rows($query_run);

                                    echo $row;

                                    ?>
                                </p>
                                <p>User Registered</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <p class="mb-4">Total Bookings</p>
                                <p class="fs-30 mb-2">61344</p>
                                <p>22.00% (30 days)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">User Registration Form</h2>
                        <form class="forms-sample" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="form-group">
                                <label for="exampleInputName1">Full Name</label>
                                <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail3">Email address</label>
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail3">Contact Number</label>
                                <input type="text" class="form-control" name="contact" placeholder="Contact Number" required>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control" id="role" required>
                                    <option disabled selected>Select Type</option>
                                    <option value="Admin">Admin</option>
                                    <option value="User">User</option>
                                </select>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary mr-2">Submit</button>
                            <button type="reset" class="btn btn-danger">Reset</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include 'footer.php';
    ?>