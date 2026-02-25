<?php
include 'header.php';
include 'db.php';

//Display Data
if (isset($_GET['userid'])) {
    $userid = $_GET['userid'];
    $sql = "SELECT * FROM user WHERE userid=$userid";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            $userid = $row['userid'];
            $name = $row['name'];
            $username = $row['username'];
            $contact = $row['contact'];
            $department = $row['department'];
        }
    } else {
        echo "<script>
            windows.alert('No records found')
          </script>";
    }
}

//Process Form
if (isset($_POST['save'])) {

    $name = $_POST['name'];
    $username = $_POST['username'];
    $contact = $_POST['contact'];
    $department = $_POST['department'];

    $sql = "UPDATE user SET
            name='$name', 
            username='$username',
            contact='$contact',
            department='$department'
            WHERE userid=$userid";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
            window.alert('Information Saved')
            </script>";
        echo "<script>window.location.replace('manageuser.php')</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}
?>

<head>
    <title>Access Information</title>
</head>

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Update<code style="font-size:inherit;"><?php echo $name; ?>'s</code>Information</h2>
                        <form class="forms-sample" method="post" action="<?php echo $_SERVER['PHP_SELF'] . "?userid=$userid"; ?>">
                            <div class="form-group">
                                <label for="exampleInputName1">Full Name<span style="color: red;">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="Full Name" value="<?php echo $name; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail3">Email Address<span style="color: red;">*</span></label>
                                <input type="email" class="form-control" name="username" placeholder="Email" value="<?php echo $username; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail3">Contact Number<span style="color: red;">*</span></label>
                                <input type="text" class="form-control" name="contact" placeholder="Contact Number" value="0<?php echo $contact; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Department<span style="color: red;">*</span></label>
                                <select name="department" class="form-control" value="<?php echo $department; ?>" required>
                                    <option value="">Select Type</option>
                                    <option value="Admin" <?php if($row['department'] == 'Admin') { echo "selected";} ?>>Admin</option>
                                    <option value="Account">Account</option>
                                    <option value="Operation">Operation</option>
                                    <option value="Marketing">Marketing</option>
                                </select>
                            </div>
                            <!--<hr>
                            <div class="form-group">
                                <h4 class="mb-4 card-title">Confirm your password</h4>
                                <labh6el for="exampleInputPassword4">Please enter your password in order to update the Information.</label>
                                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                            </div>-->
                            <button type="submit" name="save" class="btn btn-primary mr-2">Save</button>
                            <button type="reset" class="btn btn-danger" onclick="location.href='manageuser.php'">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include 'footer.php';
    ?>