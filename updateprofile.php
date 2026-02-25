<?php
include 'db.php';
include 'header.php';

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
        }
    } else {
        echo "<script>
            windows.alert('No records found')
          </script>";
    }
}

//Process Form
if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $staff_email = $_POST['staff_email'];
    $contact = $_POST['contact'];

    $sql = "UPDATE `user` SET `name`='$name', `username`='$username', `contact`='$contact'
                WHERE `userid`=$userid";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
            window.alert('Information Saved! You need to re-login')
            </script>";
        echo "<script>window.location.replace('logout.php')</script>";
    } else {
        echo "<script>
        window.alert('Error: " . $sql . "<br>" . $conn->error . "');
         </script>";
    }
    $conn->close();
}
?>

<head>
    <title>
        Update Profile
    </title>
</head>

<!-- body -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Account Information</h2>
                        <form class="forms-sample" method="post" action="<?php echo $_SERVER['PHP_SELF'] . "?userid=$userid"; ?>">
                            <div class="form-group">
                                <label for="exampleInputName1">Full Name<span style="color: red;">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="Full Name" value="<?php echo $name; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail3">Usernme<span style="color: red;">*</span></label>
                                <input type="email" class="form-control" name="staff_email" placeholder="Email" value="<?php echo $username; ?>" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail3">Contact Number<span style="color: red;">*</span></label>
                                <input type="text" class="form-control" name="contact" placeholder="Contact Number" value="<?php echo "0$contact"; ?>" required>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary mr-2">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include 'footer.php';
    ?>