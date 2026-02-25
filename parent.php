<?php
include 'header.php';

if (isset($_GET['id'])) {

    $pat_id = $_GET['id'];
    $pat_name = $_GET['name'];
    $id = encryptor('decrypt', $pat_id);
    $name = encryptor('decrypt', $pat_name);

    $query = "SELECT child.*, pat_tkc.* FROM pat_tkc INNER JOIN child ON pat_tkc.pat_id = child.pat_id WHERE child.pat_id = '$id'";
    $result_union = $conn->query($query);



?>

    <head>
        <title>Dashboard</title>

    </head>

    <!-- main page -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title mb-3"><?php echo "Application under <span class='text-primary'>" . $name . "</span>"; ?></p>
                            <div class="table-responsive">
                                <table id="datatable" class="display expandable-table table table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>MRN</th>
                                            <th>Child's Name</th>
                                            <th>Date Registered</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        if ($result_union->num_rows > 0) {
                                            // output data of each row
                                            while ($row = $result_union->fetch_assoc()) {
                                                $child_id = $row['child_id'];
                                                $child_mrn = $row['child_mrn'];
                                                $child_name = $row['child_name'];
                                                $child_dob = $row['child_dob'];
                                                $child_gen = $row['child_gen'];
                                                $status = $row['status'];
                                                $child_timestamp = $row['child_timestamp'];
                                                $pat_name = $row['pat_name'];

                                                $date = new DateTime($child_dob);
                                                $child_dob = $date->format('d/m/Y');

                                                $date2 = new DateTime($child_timestamp);
                                                $child_timestamp = $date->format('d/m/Y H:m:s');

                                                $echild_id = encryptor('encrypt', $row['child_id']);

                                                echo "<tr>";
                                                if ($child_mrn === "" || $child_mrn === NULL) {
                                                    echo "<td class='text-danger'>Not Available</td>";
                                                } else {
                                                    echo "<td>$child_mrn</td>";
                                                }
                                                echo strtoupper("<td class='font-weight-bold'>$child_name</td>");
                                                echo "<td>$child_timestamp</td>";
                                                if ($status === "Pending Registration") {
                                                    echo "<td><label class='badge badge-warning'>Pending Registration</label></td>";
                                                } elseif ($status === "Successfully Registered In SAP") {
                                                    echo "<td><label class='badge badge-success'>Successfully Registered In SAP</label></td>";
                                                }
                                                echo "<td>
                                  <a href='pat_details_tkc.php?id=$echild_id' data-toggle='tooltip' data-placement='top' title='Details'><i class='far fa-solid fa-circle-info text-primary'></i> </a>
                                  <a href='update_pat_tkc.php?id=$echild_id' data-toggle='tooltip' data-placement='top' title='Update'><i class='far fa-edit text-primary'></i> </a>
                                </td>";
                                                echo "</tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        <?php
    } else {
        echo "<script>alert('Parameter not found! Please try again')</script>";
        echo "<script>window.history.back()</script>";
    }

    include 'footer.php';
        ?>