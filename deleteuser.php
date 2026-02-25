<?php
include 'db.php';

$userid = $_GET['userid'];

// sql to delete a record
$sql = "DELETE FROM user WHERE userid=$userid";

if (mysqli_query($conn, $sql)) {
  echo "<script>
		window.alert('User information deleted successfully');
		</script>
		";
    echo "<script>window.location.replace('manageuser.php')</script>";
} else {
  echo "<script>
  		window.alert('There's something wrong to delete this information')
  		</script>";
}
?>