<?php
include 'header.php';
include 'db.php';

if (isset($_POST['submit'])) {

  $name = $_POST['name'];
  $username = $_POST['username'];
  $contact = $_POST['contact'];
  $department = $_POST['department'];
  $password = $_POST['password'];
  $cfmpassword = $_POST['cfmpassword'];
  $datecreate = date("d/m/y");
  $role = $_POST['role'];

  if ($password === $cfmpassword) {
    $password = md5($password);
    $sql = "INSERT INTO `user`(`name`, `username`, `contact`, `department`, `datecreate`, `status`, `role`, `password`) 
            VALUES ('$name', '$username', '$contact', '$department', '$datecreate', 'Active', '$role', '$password')";

    if ($conn->query($sql) === TRUE) {
      echo "<script>
        window.alert('$name registered successfully');
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
  <title>Register Account</title>
</head>

<!-- partial -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h2 class="card-title">Account Registration Form</h2>
            <form class="forms-sample" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
              <div class="form-group">
                <label for="exampleInputName1">Full Name<span style="color: red;">*</span></label>
                <input type="text" class="form-control" name="name" placeholder="Full Name" required>
              </div>
              <div class="form-group">
                <label for="exampleInputEmail3">Employee ID<span style="color: red;">*</span></label>
                <input type="text" class="form-control" name="username" placeholder="Employee ID" required>
              </div>
              <div class="form-group">
                <label for="exampleInputEmail3">Contact Number<span style="color: red;">*</span></label>
                <input type="text" class="form-control" name="contact" placeholder="Contact Number" required>
              </div>
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
                  <label>Password<span style="color: red;">*</span></label>
                  <input type="password" class="form-control" name="password" placeholder="Password" id="password" required>
                </div>
                <div class="col-sm-6">
                  <label>Confirm Password<span style="color: red;">*</span></label>
                  <input type="password" class="form-control" name="cfmpassword" placeholder="Confirm Password" id="cfmpassword" required>
                  <span id="message"></span>
                </div>
              </div>
              <div class="form-group">
                <label>Role<span style="color: red;">*</span></label>
                <select name="role" class="form-control" required>
                  <option disabled selected>Select Type</option>
                  <option value="user">User</option>
                  <option value="admin">Admin</option>
                  <option value="IT">IT</option>
                </select>
              </div>
              <button type="submit" name="submit" class="btn btn-primary mr-2">Register</button>
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