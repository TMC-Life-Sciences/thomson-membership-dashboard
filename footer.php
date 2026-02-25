<!-- partial:partials/_footer.html -->

<footer class="footer">

  <div class="d-sm-flex justify-content-center justify-content-sm-between">

    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © <?php echo date("Y"); ?>. All right reserved <a href="" target="_blank">Group IT THKD</a>.</span>

    <!--<span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="ti-heart text-danger ml-1"></i></span>-->

  </div>

</footer>



<!-- partial -->

</div>

<!-- main-panel ends -->

</div>

<!-- page-body-wrapper ends -->

</div>

</div>

<!-- container-scroller -->



<!-- plugins:js -->

<script src="vendors/js/vendor.bundle.base.js"></script>

<!-- endinject -->

<!-- Plugin js for this page -->

<script src="vendors/chart.js/Chart.min.js"></script>

<script src="vendors/datatables.net/jquery.dataTables.js"></script>

<script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>

<script src="js/dataTables.select.min.js"></script>



<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



<!-- End plugin js for this page -->



<!-- inject:js -->

<script src="js/off-canvas.js"></script>

<script src="js/hoverable-collapse.js"></script>

<script src="js/template.js"></script>

<script src="js/settings.js"></script>

<script src="js/todolist.js"></script>

<!-- endinject -->



<!-- Custom js for this page-->

<script src="js/dashboard.js"></script>

<script src="js/Chart.roundedBarCharts.js"></script>



<!-- Plugin js for select -->

<script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>

<script src="vendors/select2/select2.min.js"></script>

<script src="js/file-upload.js"></script>

<script src="js/typeahead.js"></script>

<script src="js/select2.js"></script>



<script>
  $('.modaldelete').click(function() {

    var userid = $(this).attr('data-a');

    $.ajax({

      url: "deletemodal.php?userid=" + userid,

      cache: false,

      success: function(result) {

        $(".modal-content").html(result);

      }

    });

  });
</script>



<!-- Form Checking -->

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



<!-- tooltip -->

<script>
  $(document).ready(function() {

    $('[data-toggle="tooltip"]').tooltip();

  });
</script>



<!-- password validation -->

<script>
  $('#password, #cfmpassword').on('keyup', function() {

    if ($('#password').val() == $('#cfmpassword').val()) {

      $('#message').html('Password Match').css('color', 'green');

    } else

      $('#message').html('Password Are Not Match').css('color', 'red');

  });
</script>

<script>
  $(document).ready(function() {
    $('#datatable').DataTable({
      "order": [
        [2, 'desc']
      ],
      pageLength: 2000,
      lengthMenu: [
        [5, 10, 20, 100, 500, 1000, 2000],
        [5, 10, 20, 100, 500, 1000, 2000]
      ] // This will sort the first column (index 0) in ascending order
    });
  });
</script>

<script>
  function exportTableToCSV() {

    var table = document.getElementById("datatable");

    var rows = table.getElementsByTagName("tr");

    var csvContent = "";



    // Get table headers

    var headers = [];

    var headerRow = rows[0];

    var headerCells = headerRow.getElementsByTagName("th");



    for (var h = 0; h < headerCells.length; h++) {

      headers.push(headerCells[h].innerText);

    }



    csvContent += '"' + headers.join('","') + '"' + "\n";



    // Get table data

    for (var i = 1; i < rows.length; i++) {

      var cells = rows[i].getElementsByTagName("td");

      var rowData = [];



      for (var j = 0; j < cells.length; j++) {

        rowData.push('"' + cells[j].innerText.replace(/"/g, '""') + '"');

      }



      csvContent += rowData.join(",") + "\n";

    }



    // Create a Blob with the CSV data

    var blob = new Blob([csvContent], {

      type: "text/csv;charset=utf-8"

    });



    // Prompt the user to save the file

    var link = document.createElement("a");

    link.href = URL.createObjectURL(blob);

    link.download = "export.csv";

    link.style.display = "none";

    document.body.appendChild(link);

    link.click();



    // Clean up

    document.body.removeChild(link);

    URL.revokeObjectURL(link.href);

  }
</script>



<script>
  function exportTableToCSVTGP() {

    var table = document.getElementById("tgpTable");

    var rows = table.getElementsByTagName("tr");

    var csvContent = "";



    // Get table headers

    var headers = [];

    var headerRow = rows[0];

    var headerCells = headerRow.getElementsByTagName("th");



    for (var h = 0; h < headerCells.length; h++) {

      headers.push(headerCells[h].innerText);

    }



    csvContent += '"' + headers.join('","') + '"' + "\n";



    // Get table data

    for (var i = 1; i < rows.length; i++) {

      var cells = rows[i].getElementsByTagName("td");

      var rowData = [];



      for (var j = 0; j < cells.length; j++) {

        rowData.push('"' + cells[j].innerText.replace(/"/g, '""') + '"');

      }



      csvContent += rowData.join(",") + "\n";

    }



    // Create a Blob with the CSV data

    var blob = new Blob([csvContent], {

      type: "text/csv;charset=utf-8"

    });



    // Prompt the user to save the file

    var link = document.createElement("a");

    link.href = URL.createObjectURL(blob);

    link.download = "pat-details-tgp.csv";

    link.style.display = "none";

    document.body.appendChild(link);

    link.click();



    // Clean up

    document.body.removeChild(link);

    URL.revokeObjectURL(link.href);

  }
</script>



<script>
  function exportTableToCSVTKC() {

    var table = document.getElementById("tkcTable");

    var rows = table.getElementsByTagName("tr");

    var csvContent = "";



    // Get table headers

    var headers = [];

    var headerRow = rows[0];

    var headerCells = headerRow.getElementsByTagName("th");



    for (var h = 0; h < headerCells.length; h++) {

      headers.push(headerCells[h].innerText);

    }



    csvContent += '"' + headers.join('","') + '"' + "\n";



    // Get table data

    for (var i = 1; i < rows.length; i++) {

      var cells = rows[i].getElementsByTagName("td");

      var rowData = [];



      for (var j = 0; j < cells.length; j++) {

        rowData.push('"' + cells[j].innerText.replace(/"/g, '""') + '"');

      }



      csvContent += rowData.join(",") + "\n";

    }



    // Create a Blob with the CSV data

    var blob = new Blob([csvContent], {

      type: "text/csv;charset=utf-8"

    });



    // Prompt the user to save the file

    var link = document.createElement("a");

    link.href = URL.createObjectURL(blob);

    link.download = "pat-details-tkc.csv";

    link.style.display = "none";

    document.body.appendChild(link);

    link.click();



    // Clean up

    document.body.removeChild(link);

    URL.revokeObjectURL(link.href);

  }
</script>



<script>
  var timeoutInMilliseconds = 900000; // 15 minutes



  var timeoutId;



  function startTimer() {
    timeoutId = setTimeout(redirectLogout, timeoutInMilliseconds);
  }



  function resetTimer() {
    clearTimeout(timeoutId);
    startTimer();
  }



  function redirectLogout() {
    window.location.href = 'logout.php?logout=1';
    // $(document).ready(function() {
    //   $("#idleModal").modal("show");
    // });
  }



  // Reset the timer on user activity events (e.g., mousemove, keydown)
  window.addEventListener('mousemove', resetTimer);
  window.addEventListener('keydown', resetTimer);



  // Start the timer when the page loads
  window.onload = startTimer;
</script>



<!-- End custom js for this page-->





</body>



</html>