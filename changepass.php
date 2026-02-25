<?php
include 'header.php';


//Process Form
if (isset($_POST['submit'])) {

    $password = $_POST['password'];
    $cfmpassword = $_POST['cfmpassword'];

    if ($password === $cfmpassword) {
        $password = hash('sha256', data: $password);
        $sql = "UPDATE `user` SET `password`='$password'
                WHERE `userid`=$userid";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
            window.alert('Your password has been saved! You need to re-login')
            </script>";
            echo "<script>window.location.replace('index.php')</script>";
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
    <title>
        Change Password
    </title>
</head>

<!-- body -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Change Password</h2>
                        <form class="forms-sample" method="post" action="<?php echo $_SERVER['PHP_SELF'] . "?userid=$userid"; ?>">
                            <!--<div class="form-group">
                                <label for="exampleInputName1">Current Password<span style="color: red;">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="Current Password" required>
                            </div>
                            <hr>-->
                            <div class="form-group">
                                <label>New Password<span style="color: red;">*</span></label>
                                <input type="password" class="form-control" name="password" placeholder="Password" id="password" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password<span style="color: red;">*</span></label>
                                <input type="password" class="form-control" name="cfmpassword" placeholder="Confirm Password" id="cfmpassword" required>
                                <span id="message"></span>
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