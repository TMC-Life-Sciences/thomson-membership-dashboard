<?php
require 'vendor/autoload.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'header.php';

if (isset($_GET['id'])) {
    $epat_id = $_GET['id'];
    $dpat_id = encryptor('decrypt', $epat_id);

    $sql = "SELECT * FROM `pat_tgp` WHERE `pat_id` = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Error preparing SQL: " . $conn->error);
        echo "<script>
            window.alert('Database error. Please try again later.');
          </script>";
        exit;
    }

    $stmt->bind_param("s", $dpat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result !== false && $result->num_rows > 0) {
        // Fetch data
        while ($row = $result->fetch_assoc()) {
            $pat_id = htmlspecialchars($row['pat_id']);
            $pat_name = htmlspecialchars($row['pat_name']);
            $pat_title = htmlspecialchars($row['pat_title']);
            $pat_nric = htmlspecialchars($row['pat_nric']);
            $pat_nat = htmlspecialchars($row['pat_nat']);
            $pat_dob = htmlspecialchars($row['pat_dob']);
            $pat_race = htmlspecialchars($row['pat_race']);
            $pat_phone = htmlspecialchars($row['pat_phone']);
            $pat_gender = htmlspecialchars($row['pat_gender']);
            $pat_addr = htmlspecialchars($row['pat_addr']);
            $pat_state = htmlspecialchars($row['pat_state']);
            $pat_postcode = htmlspecialchars($row['pat_postcode']);
            $pat_age = htmlspecialchars($row['pat_age']);
            $pat_city = htmlspecialchars($row['pat_city']);
            $pat_email = htmlspecialchars($row['pat_email']);
            $pat_memb = htmlspecialchars($row['pat_memb']);
            $pat_mrn = htmlspecialchars($row['pat_mrn']);
            $last_update = htmlspecialchars($row['last_update']);
            $user_update = htmlspecialchars($row['user_update']);
        }
    } else {
        echo "<script>
            window.alert('No records found');
          </script>";
    }

    if (isset($_POST['submit'])) {

        $pat_mrn = htmlspecialchars($_POST['pat_mrn']);
        $pat_title = htmlspecialchars($_POST['pat_title']);
        $pat_name = htmlspecialchars($_POST['pat_name']);
        $pat_nric = htmlspecialchars($_POST['pat_nric']);
        $pat_nat = htmlspecialchars($_POST['pat_nat']);
        $pat_dob = htmlspecialchars($_POST['pat_dob']);
        $pat_race = htmlspecialchars($_POST['pat_race']);
        $pat_gender = htmlspecialchars($_POST['pat_gender']);
        $pat_addr = htmlspecialchars($_POST['pat_addr']);
        $pat_state = htmlspecialchars($_POST['pat_state']);
        $pat_postcode = htmlspecialchars($_POST['pat_postcode']);
        $pat_age = htmlspecialchars($_POST['pat_age']);
        $pat_city = htmlspecialchars($_POST['pat_city']);
        $pat_email = htmlspecialchars($_POST['pat_email']);
        $pat_phone = htmlspecialchars($_POST['pat_phone']);
        $last_update = $timestamp;
        $user_update = htmlspecialchars($_SESSION['username']);
        $status = "Successfully Registered In SAP";

        $check_mrn = $conn->prepare("SELECT 1 FROM `pat_tgp` WHERE `pat_mrn` = ?");
        if ($check_mrn) {
            $check_mrn->bind_param("i", $pat_mrn);
            $check_mrn->execute();
            $check_mrn->store_result();
            if ($check_mrn->num_rows > 0) {
                echo "<script>alert('MRN has already existed with other records. Please try again!')</script>";
                echo "<script>window.history.back();</script>";
            } else {
                $update_tgp = $conn->prepare("UPDATE `pat_tgp` SET `pat_mrn`=?, `pat_title`=?, `pat_name`=?, 
                            `pat_gender`=?, `pat_race`=?, `pat_nric`=?, `pat_dob`=?, `pat_addr`=?, 
                            `pat_city`=?, `pat_postcode`=?, `pat_state`=?, `pat_phone`=?, 
                            `pat_email`=?, `status`=?, user_update=? WHERE pat_id=?");
                $update_tgp->bind_param(
                    "issssssssisissss",
                    $pat_mrn,
                    $pat_title,
                    $pat_name,
                    $pat_gender,
                    $pat_race,
                    $pat_nric,
                    $pat_dob,
                    $pat_addr,
                    $pat_city,
                    $pat_postcode,
                    $pat_state,
                    $pat_phone,
                    $pat_email,
                    $status,
                    $user_update,
                    $pat_id
                );

                if ($update_tgp->execute()) {
                    $mail = new PHPMailer(true);

                    try {
                        $mail->isSMTP();
                        $mail->Host = $smtpHost;
                        $mail->SMTPAuth = true;
                        $mail->Username = $smtpUsername;
                        $mail->Password = $smtpPassword;
                        $mail->SMTPSecure = $smtpSecure;
                        $mail->Port = $smtpPort;

                        $mail->setFrom($fromEmail, $fromName);
                        $mail->addAddress($pat_email, $pat_name);

                        $mail->Subject = 'THKD Membership Application: Registration Completed [Application ID: ' . $pat_id . ']';
                        $mail->isHTML(true);

                        $msg = file_get_contents("email_template/tgyp.html");
                        $msg = str_replace('{{$pat_mrn}}', $pat_mrn, $msg);
                        $msg = str_replace('{{$pat_email}}', $pat_email, $msg);
                        $msg = str_replace('{{$pat_name}}', $pat_name, $msg);
                        $msg = str_replace('{{$pat_nric}}', $pat_nric, $msg);

                        $mail->Body = $msg;

                        $mail->send();

                        echo "<script>alert('Registration complete! Confirmation email has been sent to customer.');</script>";
                        echo "<script>window.location.replace('pm_tgp.php');</script>";
                    } catch (Exception $e) {
                        error_log("Mailer Error: {$mail->ErrorInfo}");
                        echo "<script>alert('Failed to send confirmation email. Please check with system administrator.');</script>";
                    }
                } else {
                    error_log("Error: " . $update_tgp . "<br>" . $conn->error);
                    echo "<script>alert('Failed to update children information. Please check with system administrator.');</script>";
                    echo "<script>window.history.back();</script>";
                }
                $update_tgp->close();
                $conn->close();
            }
        }
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
                        <h2 class="card-title">Update <?php echo $pat_name . " [" . $dpat_id . "]"; ?> Information <code style="font-size:inherit;">(<?php echo 'Updated ' . $last_update . ' ' . $user_update; ?>)</code></h2>
                        <form class="forms-sample" method="post" action="">
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <label for="exampleInputPassword4">MRN<span style="color: red;">*</span></label>
                                    <?php

                                    if ($pat_mrn === "") {
                                        echo "<input type='text' class='form-control' name='pat_mrn' placeholder='MRN Not Available' required>";
                                    } elseif ($_SESSION['role'] === 'admin' && 'IT') {
                                        echo "<input type='text' class='form-control' name='pat_mrn' placeholder='Patient MRN' value='$pat_mrn' required>";
                                    } else {
                                        echo "<input type='text' class='form-control' name='pat_mrn' placeholder='Patient MRN' value='$pat_mrn' readonly>";
                                    }

                                    ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="exampleInputPassword4">Title<span style="color: red;">*</span></label>
                                    <select class="form-control" name="pat_title" style="color:black;" required>
                                        <option value="<?php echo $pat_title; ?>" selected><?php echo $pat_title; ?></option>
                                        <option value="Mr.">Mr.</option>
                                        <option value="Mrs.">Mrs.</option>
                                        <option value="Ms.">Ms.</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-sm-10">
                                    <label for="exampleInputPassword4">Full Name<span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" name="pat_name" placeholder="Full Name" value="<?php echo $pat_name; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="exampleInputPassword4">Gender<span style="color: red;">*</span></label>
                                    <select class="form-control" name="pat_gender" style="color:black;" required>
                                        <option value="<?php echo $pat_gender; ?>" selected><?php echo $pat_gender; ?></option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label for="exampleInputPassword4">Race<span style="color: red;">*</span></label>
                                    <select class="form-control" name="pat_race" style="color:black;" required>
                                        <option value="<?php echo $pat_race; ?>" selected><?php echo $pat_race; ?></option>
                                        <option value="Malay">Malay</option>
                                        <option value="China">China</option>
                                        <option value="China">Others</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label for="exampleInputPassword4">Date of Birth<span style="color: red;">*</span></label>
                                    <input type="date" class="form-control" name="pat_dob" placeholder="Date of Birth" id="dob" value="<?php echo $pat_dob; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="exampleInputPassword4">Age<span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" name="pat_age" placeholder="Age" id="age" value="<?php echo $pat_age; ?>" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="exampleInputPassword4">NRIC<span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" name="pat_nric" placeholder="NRIC" value="<?php echo $pat_nric; ?>" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="exampleInputPassword4">Nationality<span style="color: red;">*</span></label>
                                    <select class="form-control" name="pat_nat" style="color:black;" required>
                                        <option value="<?php echo $pat_nat; ?>" selected><?php echo $pat_nat; ?></option>
                                        <option value="AF">Afghanistan</option>
                                        <option value="AX">Aland Islands</option>
                                        <option value="AL">Albania</option>
                                        <option value="DZ">Algeria</option>
                                        <option value="AS">American Samoa</option>
                                        <option value="AD">Andorra</option>
                                        <option value="AO">Angola</option>
                                        <option value="AI">Anguilla</option>
                                        <option value="AQ">Antarctica</option>
                                        <option value="AG">Antigua and Barbuda</option>
                                        <option value="AR">Argentina</option>
                                        <option value="AM">Armenia</option>
                                        <option value="AW">Aruba</option>
                                        <option value="AU">Australia</option>
                                        <option value="AT">Austria</option>
                                        <option value="AZ">Azerbaijan</option>
                                        <option value="BS">Bahamas</option>
                                        <option value="BH">Bahrain</option>
                                        <option value="BD">Bangladesh</option>
                                        <option value="BB">Barbados</option>
                                        <option value="BY">Belarus</option>
                                        <option value="BE">Belgium</option>
                                        <option value="BZ">Belize</option>
                                        <option value="BJ">Benin</option>
                                        <option value="BM">Bermuda</option>
                                        <option value="BT">Bhutan</option>
                                        <option value="BO">Bolivia</option>
                                        <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
                                        <option value="BA">Bosnia and Herzegovina</option>
                                        <option value="BW">Botswana</option>
                                        <option value="BV">Bouvet Island</option>
                                        <option value="BR">Brazil</option>
                                        <option value="IO">British Indian Ocean Territory</option>
                                        <option value="BN">Brunei Darussalam</option>
                                        <option value="BG">Bulgaria</option>
                                        <option value="BF">Burkina Faso</option>
                                        <option value="BI">Burundi</option>
                                        <option value="KH">Cambodia</option>
                                        <option value="CM">Cameroon</option>
                                        <option value="CA">Canada</option>
                                        <option value="CV">Cape Verde</option>
                                        <option value="KY">Cayman Islands</option>
                                        <option value="CF">Central African Republic</option>
                                        <option value="TD">Chad</option>
                                        <option value="CL">Chile</option>
                                        <option value="CN">China</option>
                                        <option value="CX">Christmas Island</option>
                                        <option value="CC">Cocos (Keeling) Islands</option>
                                        <option value="CO">Colombia</option>
                                        <option value="KM">Comoros</option>
                                        <option value="CG">Congo</option>
                                        <option value="CD">Congo, Democratic Republic of the Congo</option>
                                        <option value="CK">Cook Islands</option>
                                        <option value="CR">Costa Rica</option>
                                        <option value="CI">Cote D'Ivoire</option>
                                        <option value="HR">Croatia</option>
                                        <option value="CU">Cuba</option>
                                        <option value="CW">Curacao</option>
                                        <option value="CY">Cyprus</option>
                                        <option value="CZ">Czech Republic</option>
                                        <option value="DK">Denmark</option>
                                        <option value="DJ">Djibouti</option>
                                        <option value="DM">Dominica</option>
                                        <option value="DO">Dominican Republic</option>
                                        <option value="EC">Ecuador</option>
                                        <option value="EG">Egypt</option>
                                        <option value="SV">El Salvador</option>
                                        <option value="GQ">Equatorial Guinea</option>
                                        <option value="ER">Eritrea</option>
                                        <option value="EE">Estonia</option>
                                        <option value="ET">Ethiopia</option>
                                        <option value="FK">Falkland Islands (Malvinas)</option>
                                        <option value="FO">Faroe Islands</option>
                                        <option value="FJ">Fiji</option>
                                        <option value="FI">Finland</option>
                                        <option value="FR">France</option>
                                        <option value="GF">French Guiana</option>
                                        <option value="PF">French Polynesia</option>
                                        <option value="TF">French Southern Territories</option>
                                        <option value="GA">Gabon</option>
                                        <option value="GM">Gambia</option>
                                        <option value="GE">Georgia</option>
                                        <option value="DE">Germany</option>
                                        <option value="GH">Ghana</option>
                                        <option value="GI">Gibraltar</option>
                                        <option value="GR">Greece</option>
                                        <option value="GL">Greenland</option>
                                        <option value="GD">Grenada</option>
                                        <option value="GP">Guadeloupe</option>
                                        <option value="GU">Guam</option>
                                        <option value="GT">Guatemala</option>
                                        <option value="GG">Guernsey</option>
                                        <option value="GN">Guinea</option>
                                        <option value="GW">Guinea-Bissau</option>
                                        <option value="GY">Guyana</option>
                                        <option value="HT">Haiti</option>
                                        <option value="HM">Heard Island and Mcdonald Islands</option>
                                        <option value="VA">Holy See (Vatican City State)</option>
                                        <option value="HN">Honduras</option>
                                        <option value="HK">Hong Kong</option>
                                        <option value="HU">Hungary</option>
                                        <option value="IS">Iceland</option>
                                        <option value="IN">India</option>
                                        <option value="ID">Indonesia</option>
                                        <option value="IR">Iran, Islamic Republic of</option>
                                        <option value="IQ">Iraq</option>
                                        <option value="IE">Ireland</option>
                                        <option value="IM">Isle of Man</option>
                                        <option value="IL">Israel</option>
                                        <option value="IT">Italy</option>
                                        <option value="JM">Jamaica</option>
                                        <option value="JP">Japan</option>
                                        <option value="JE">Jersey</option>
                                        <option value="JO">Jordan</option>
                                        <option value="KZ">Kazakhstan</option>
                                        <option value="KE">Kenya</option>
                                        <option value="KI">Kiribati</option>
                                        <option value="KP">Korea, Democratic People's Republic of</option>
                                        <option value="KR">Korea, Republic of</option>
                                        <option value="XK">Kosovo</option>
                                        <option value="KW">Kuwait</option>
                                        <option value="KG">Kyrgyzstan</option>
                                        <option value="LA">Lao People's Democratic Republic</option>
                                        <option value="LV">Latvia</option>
                                        <option value="LB">Lebanon</option>
                                        <option value="LS">Lesotho</option>
                                        <option value="LR">Liberia</option>
                                        <option value="LY">Libyan Arab Jamahiriya</option>
                                        <option value="LI">Liechtenstein</option>
                                        <option value="LT">Lithuania</option>
                                        <option value="LU">Luxembourg</option>
                                        <option value="MO">Macao</option>
                                        <option value="MK">Macedonia, the Former Yugoslav Republic of</option>
                                        <option value="MG">Madagascar</option>
                                        <option value="MW">Malawi</option>
                                        <option value="MY">Malaysia</option>
                                        <option value="MV">Maldives</option>
                                        <option value="ML">Mali</option>
                                        <option value="MT">Malta</option>
                                        <option value="MH">Marshall Islands</option>
                                        <option value="MQ">Martinique</option>
                                        <option value="MR">Mauritania</option>
                                        <option value="MU">Mauritius</option>
                                        <option value="YT">Mayotte</option>
                                        <option value="MX">Mexico</option>
                                        <option value="FM">Micronesia, Federated States of</option>
                                        <option value="MD">Moldova, Republic of</option>
                                        <option value="MC">Monaco</option>
                                        <option value="MN">Mongolia</option>
                                        <option value="ME">Montenegro</option>
                                        <option value="MS">Montserrat</option>
                                        <option value="MA">Morocco</option>
                                        <option value="MZ">Mozambique</option>
                                        <option value="MM">Myanmar</option>
                                        <option value="NA">Namibia</option>
                                        <option value="NR">Nauru</option>
                                        <option value="NP">Nepal</option>
                                        <option value="NL">Netherlands</option>
                                        <option value="AN">Netherlands Antilles</option>
                                        <option value="NC">New Caledonia</option>
                                        <option value="NZ">New Zealand</option>
                                        <option value="NI">Nicaragua</option>
                                        <option value="NE">Niger</option>
                                        <option value="NG">Nigeria</option>
                                        <option value="NU">Niue</option>
                                        <option value="NF">Norfolk Island</option>
                                        <option value="MP">Northern Mariana Islands</option>
                                        <option value="NO">Norway</option>
                                        <option value="OM">Oman</option>
                                        <option value="PK">Pakistan</option>
                                        <option value="PW">Palau</option>
                                        <option value="PS">Palestinian Territory, Occupied</option>
                                        <option value="PA">Panama</option>
                                        <option value="PG">Papua New Guinea</option>
                                        <option value="PY">Paraguay</option>
                                        <option value="PE">Peru</option>
                                        <option value="PH">Philippines</option>
                                        <option value="PN">Pitcairn</option>
                                        <option value="PL">Poland</option>
                                        <option value="PT">Portugal</option>
                                        <option value="PR">Puerto Rico</option>
                                        <option value="QA">Qatar</option>
                                        <option value="RE">Reunion</option>
                                        <option value="RO">Romania</option>
                                        <option value="RU">Russian Federation</option>
                                        <option value="RW">Rwanda</option>
                                        <option value="BL">Saint Barthelemy</option>
                                        <option value="SH">Saint Helena</option>
                                        <option value="KN">Saint Kitts and Nevis</option>
                                        <option value="LC">Saint Lucia</option>
                                        <option value="MF">Saint Martin</option>
                                        <option value="PM">Saint Pierre and Miquelon</option>
                                        <option value="VC">Saint Vincent and the Grenadines</option>
                                        <option value="WS">Samoa</option>
                                        <option value="SM">San Marino</option>
                                        <option value="ST">Sao Tome and Principe</option>
                                        <option value="SA">Saudi Arabia</option>
                                        <option value="SN">Senegal</option>
                                        <option value="RS">Serbia</option>
                                        <option value="CS">Serbia and Montenegro</option>
                                        <option value="SC">Seychelles</option>
                                        <option value="SL">Sierra Leone</option>
                                        <option value="SG">Singapore</option>
                                        <option value="SX">Sint Maarten</option>
                                        <option value="SK">Slovakia</option>
                                        <option value="SI">Slovenia</option>
                                        <option value="SB">Solomon Islands</option>
                                        <option value="SO">Somalia</option>
                                        <option value="ZA">South Africa</option>
                                        <option value="GS">South Georgia and the South Sandwich Islands</option>
                                        <option value="SS">South Sudan</option>
                                        <option value="ES">Spain</option>
                                        <option value="LK">Sri Lanka</option>
                                        <option value="SD">Sudan</option>
                                        <option value="SR">Suriname</option>
                                        <option value="SJ">Svalbard and Jan Mayen</option>
                                        <option value="SZ">Swaziland</option>
                                        <option value="SE">Sweden</option>
                                        <option value="CH">Switzerland</option>
                                        <option value="SY">Syrian Arab Republic</option>
                                        <option value="TW">Taiwan, Province of China</option>
                                        <option value="TJ">Tajikistan</option>
                                        <option value="TZ">Tanzania, United Republic of</option>
                                        <option value="TH">Thailand</option>
                                        <option value="TL">Timor-Leste</option>
                                        <option value="TG">Togo</option>
                                        <option value="TK">Tokelau</option>
                                        <option value="TO">Tonga</option>
                                        <option value="TT">Trinidad and Tobago</option>
                                        <option value="TN">Tunisia</option>
                                        <option value="TR">Turkey</option>
                                        <option value="TM">Turkmenistan</option>
                                        <option value="TC">Turks and Caicos Islands</option>
                                        <option value="TV">Tuvalu</option>
                                        <option value="UG">Uganda</option>
                                        <option value="UA">Ukraine</option>
                                        <option value="AE">United Arab Emirates</option>
                                        <option value="GB">United Kingdom</option>
                                        <option value="US">United States</option>
                                        <option value="UM">United States Minor Outlying Islands</option>
                                        <option value="UY">Uruguay</option>
                                        <option value="UZ">Uzbekistan</option>
                                        <option value="VU">Vanuatu</option>
                                        <option value="VE">Venezuela</option>
                                        <option value="VN">Viet Nam</option>
                                        <option value="VG">Virgin Islands, British</option>
                                        <option value="VI">Virgin Islands, U.s.</option>
                                        <option value="WF">Wallis and Futuna</option>
                                        <option value="EH">Western Sahara</option>
                                        <option value="YE">Yemen</option>
                                        <option value="ZM">Zambia</option>
                                        <option value="ZW">Zimbabwe</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <label for="exampleInputPassword4">Address<span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" name="pat_addr" placeholder="Home Address" value="<?php echo $pat_addr; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="exampleInputPassword4">City<span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" name="pat_city" placeholder="City" value="<?php echo $pat_city; ?>" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="exampleInputPassword4">Postcode<span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" name="pat_postcode" placeholder="Postcode" value="<?php echo $pat_postcode; ?>" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="exampleInputPassword4">State<span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" name="pat_state" placeholder="State" value="<?php echo $pat_state; ?>" required>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <label for="exampleInputPassword4">Mobile Phone / Telephone<span style="color: red;">*</span></label>
                                    <input type="numbers" class="form-control" name="pat_phone" placeholder="Mobile Phone / Telephone" value="<?php echo $pat_phone; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <label for="exampleInputPassword4">Personal Email<span style="color: red;">*</span></label>
                                    <input type="email" class="form-control" name="pat_email" placeholder="Personal Email" value="<?php echo $pat_email; ?>" required>
                                </div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-success mr-2">Submit</button>
                            <button class="btn btn-danger" onclick="klik()">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function klik() {
            event.preventDefault();
            window.location.replace('pm_tgp.php');
        }
    </script>

    <script>
        // Get the input fields
        const dobInput = document.getElementById('dob');
        const ageInput = document.getElementById('age');

        // Add event listener to the date of birth input field
        dobInput.addEventListener('change', calculateAge);

        // Function to calculate age
        function calculateAge() {
            const dob = new Date(dobInput.value); // Get the selected date of birth
            const today = new Date(); // Get the current date

            let age = today.getFullYear() - dob.getFullYear(); // Calculate the age

            // Check if the birthday hasn't occurred yet this year
            if (today < new Date(dob.getFullYear(), dob.getMonth(), dob.getDate())) {
                age--; // Subtract 1 year if birthday hasn't occurred yet
            }

            ageInput.value = age; // Populate the age in the age input field
        }
    </script>

    <?php
    include 'footer.php';
    ?>