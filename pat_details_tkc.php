<?php
include 'header.php';

if (isset($_GET['id'])) {
    $child_idd = $_GET['id'];
    $child_id = encryptor('decrypt', $child_idd);

    $sql = "SELECT child.*, pat_tkc.* FROM `child` INNER JOIN `pat_tkc` ON child.pat_id = pat_tkc.pat_id WHERE `child_id`='$child_id';";
    $result = $conn->query($sql);
    if ($result !== false && $result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            $pat_id = $row['pat_id'];
            $pat_name = $row['pat_name'];
            $pat_title = $row['pat_title'];
            $pat_nric = $row['pat_nric'];
            $pat_nat = $row['pat_nat'];
            $pat_dob = $row['pat_dob'];
            $pat_race = $row['pat_race'];
            $pat_phone = $row['pat_phone'];
            $pat_gender = $row['pat_gender'];
            $pat_addr = $row['pat_addr'];
            $pat_state = $row['pat_state'];
            $pat_postcode = $row['pat_postcode'];
            $pat_age = $row['pat_age'];
            $pat_city = $row['pat_city'];
            $pat_email = $row['pat_email'];
            $pat_memb = $row['pat_memb'];
            $pat_mrn = $row['pat_mrn'];
            $last_update = $row['last_update'];
            $user_update = $row['user_update'];
            $child_mrn = $row['child_mrn'];
            $child_name = $row['child_name'];
            $child_gen = $row['child_gen'];
            $child_age = $row['child_age'];
            $child_dob = $row['child_dob'];
            $child_id = $row['child_id'];
        }
    } else {
        echo "<script>
            windows.alert('No records found')
          </script>";
    }
}

?>

<head>
    <title>Patient Details</title>
</head>

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <!--<h2 class="card-title">Update<code style="font-size:inherit;"><?php echo $pat_name; ?></code>Information</h2>-->
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Child's Details <code style="font-size:inherit;">(<?php echo 'Updated ' . $last_update . ' ' . $user_update; ?>)</code></h4>
                                    <div class="table-responsive pt-3">
                                        <table class="table table-bordered" id="tkcTable">
                                            <thead class="">
                                                <tr style="background-color: #82328C;color:white;">
                                                    <th>
                                                        Parent's Information
                                                    </th>
                                                    <th>
                                                        Details
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        MRN
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($pat_mrn === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $pat_mrn;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Name
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($pat_name === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $pat_title . " " . $pat_name;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Gender
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($pat_gender === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $pat_gender;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Age
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($pat_age === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $pat_age;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Date of Birth
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($pat_dob === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $pat_dob;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        NRIC
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($pat_nric === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $pat_nric;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Race
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($pat_race === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $pat_race;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Nationality
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($pat_nat === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $pat_nat;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Address
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($pat_addr === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $pat_addr . ", " . $pat_postcode . ", " . $pat_city . ", " . $pat_state;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Mobile Phone
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($pat_phone === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo "<a href='callto:$pat_phone'>$pat_phone</a>";
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Email Address
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($pat_email === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo "<a href='mailto:$pat_email'>$pat_email</a>";
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr style="background-color: #82328C;color:white;">
                                                    <td>
                                                        <b>Child's Information</b>
                                                    </td>
                                                    <td>
                                                        <b>Details</b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        MRN
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($child_mrn === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $child_mrn;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Name
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($child_name === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $child_name;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Gender
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($child_gen === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $child_gen;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Age
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($child_age === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $child_age;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Date of Birth
                                                    </td>
                                                    <td class="uppercase">
                                                        <?php
                                                        if ($child_dob === "") {
                                                            echo "Not Available";
                                                        } else {
                                                            echo $child_dob;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <button type="submit" name="export" class="btn btn-success mr-2" onclick="exportTableToCSVTKC()">Export</button>
                                    <button class="btn btn-danger" onclick="klik()">Back</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function klik() {
            event.preventDefault();
            window.location.replace('pm_tkc.php');
        }
    </script>

    <?php
    include 'footer.php';
    ?>