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

    <!-- firebase -->
    <script type="module">
        // Import the functions you need from the SDKs you need
        /*import {
          getAnalytics
        } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-analytics.js";
        import {
          initializeApp
        } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js";
        import {
          getDatabase,
          set,
          ref,
          update
        } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-database.js";
        import {
          getAuth,
          createUserWithEmailAndPassword,
          signInWithEmailAndPassword
        } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-auth.js";
        // TODO: Add SDKs for Firebase products that you want to use
        // https://firebase.google.com/docs/web/setup#available-libraries

        // Your web app's Firebase configuration
        // For Firebase JS SDK v7.20.0 and later, measurementId is optional
        const firebaseConfig = {
          apiKey: "AIzaSyBWrcPpZ7Yrn2dVQLpovWAQwttxjsmzNx8",
          authDomain: "pminventory-58e38.firebaseapp.com",
          databaseURL: "https://pminventory-58e38-default-rtdb.firebaseio.com",
          projectId: "pminventory-58e38",
          storageBucket: "pminventory-58e38.appspot.com",
          messagingSenderId: "117521079698",
          appId: "1:117521079698:web:5f14df35b46fae194e754b",
          measurementId: "G-2EJP605RQW"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const database = getDatabase(app);
        const auth = getAuth();


        signUp.addEventListener('click', (e) => {

          var email = document.getElementById('email').value;
          var password = document.getElementById('password').value;
          var fullname = document.getElementById('fullname').value;
          var contact = document.getElementById('contact').value;
          var role = document.getElementById('role').value;
          var datecreate = document.getElementById('datecreate').value;


          createUserWithEmailAndPassword(auth, email, password)
            .then((userCredential) => {
              // Signed in 
              const user = userCredential.user;

              set(ref(database, 'users/' + user.uid), {
                fullname: fullname,
                email: email,
                contact: contact,
                role: role,
                datecreate: datecreate,
              })

              alert('Register Successful');

              // ...
            })
            .catch((error) => {
              const errorCode = error.code;
              const errorMessage = error.message;

              alert(errorMessage);
              // ..
            });

        });
    </script>

    <?php
    include 'footer.php';
    ?>