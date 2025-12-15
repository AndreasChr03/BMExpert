<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css' rel='stylesheet' />
<link href="assets/CSS/calendarCss.css" rel='stylesheet' />

<!-- Replace slim version with full version of jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js'></script>
<script src="assets/JS/calendarJS.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


<?php
session_start();

include_once '../../../../../config/config.php';

$event_id = $_GET['event_id'];
$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];

$pattern = '/\d+\s*(Hours?|Minutes?)/';
$name = trim(preg_replace($pattern, '', $_GET['name']));
?>


<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>Event Updation / Deletion</title>
    <!-- Tab Icon -->
    <link rel="icon" href="../../../../../public/img/logo.png">

</head>

<body>


  <div class="modal fade" id="deleteAlert" tabindex="-1" role="dialog" aria-labelledby="deleteAlertLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="deleteAlertLabel">Deletion Alert</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p class="text-danger">Are you sure you want to delete this event?</p>
          <p class="text-muted">This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btnDelEvent">Yes, Delete Event</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <div class="container my-5">

    <div class="card">
      <div class="card-header">
        <h2>Event Updation / Deletion</h2>

      </div>
      <div class="card-body">

        <input id="event_id" class="d-none" value="<?php echo $event_id ?>">

        <label for="eventName" class="font-weight-bold">Event Name</label>
        <input id="eventName" class="form-control mb-3" value="<?php echo $name ?>">


        <label for="startDate" class="font-weight-bold">Start Date</label>
        <input id="tempStartDate" class="form-control mb-3" value="<?php echo $startDate ?>">
        <input style="display:none" id="startDate" class="form-control mb-3" type="datetime-local" value="<?php echo $startDate ?>">


        <label for="endDate" class="font-weight-bold">End Date</label>
        <input id="tempEndDate" class="form-control mb-3" value="<?php echo $startDate ?>">
        <input style="display:none" id="endDate" class="form-control mb-3" type="datetime-local" value="<?php echo $endDate ?>">


        <label for="duration" class="font-weight-bold">Duration</label>
        <input id="duration" class="form-control mb-3" value="" readonly="readonly">

        <?php if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2) { ?>


          <label for="userList" class="font-weight-bold">User</label>
          <div class="userListDiv">

          </div>



          <label for="emailMsg" class="font-weight-bold">Email Message</label>
          <textarea id="emailMsg" class="form-control mb-3" maxlength="2500"></textarea>
          <div id="charCount" class="text-right">0 / 2500</div>
          <div id="charLimitMsg" class="text-danger d-none">You have reached the maximum characters allowed.</div>


        <?php } ?>


        <?php
        // Admin or owner role.
        if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2) {

        ?>
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAlert">Delete</button>
          <!-- <button class="btn btn-danger btnDelEvent">Delete</button> -->
          <button class="btn btn-primary btnEditEvent">Save</button>
        <?php
        }
        ?>
        <a href="<?php echo BASE_URL . 'app/Views/users/dashboard_role/dashboard_admin.php' ?>" class="btn btn-default btn-secondary">Cancel</a>

      </div>

    </div>


  </div>


</body>

</html>


<script>
  $(document).ready(function() {

    var userList = [];

    var event_id = $("#event_id").val();

    $.ajax({
      url: "<?php echo BASE_URL . 'app/Views/users/dashboard_role/ajax_handler.php' ?>",
      type: "POST",
      data: {
        action: "getAllUsers"
      },
      async: false,
      success: function(resp) {
        eventList = [];
        if (resp != '') {
          userList = JSON.parse(resp);
        }
      }
    });

    var userSelect = "<select id='userList' class='form-control mb-3'>";

    userSelect += `<option value=''>`;
    userSelect += "---SELECT---";
    userSelect += "</option>";

    for (var i = 0; i < userList.length; i++) {
      var userID = userList[i].user_id;
      var name = userList[i].name;
      var email = userList[i].email;

      var str = userID + 'Æ' + email;

      userSelect += `<option value='${str}'>`;
      userSelect += name + " (" + email + ")";
      userSelect += "</option>";
    }
    userSelect += "</select>";

    $(".userListDiv").html(userSelect);



    $.ajax({
      url: "<?php echo BASE_URL . 'app/Views/users/dashboard_role/ajax_handler.php' ?>",
      type: "POST",
      data: {
        month: "",
        year: "",
        event_id: $("#event_id").val(),
        action: "getEventByMonthAndYear"
      },
      async: false, // Set async option to false for synchronous request
      success: function(resp) {

        if (resp != '') {
          var obj = JSON.parse(resp)[0];

          $("#userList").val(obj.userID + "Æ" + obj.email);
          $("#emailMsg").val(obj.emailMsg);
        }
      }

    });

    $.ajax({
      url: "<?php echo BASE_URL . 'app/Views/users/dashboard_role/ajax_handler.php' ?>",
      type: "POST",
      data: {
        event_id: event_id,
        action: "getEventByID"
      },
      async: false,
      success: function(resp) {
        if (resp != '') {
          var jsonResp = JSON.parse(resp)[0];

          var userID = jsonResp.userID;
          userID = userID == null ? 0 : userID;

          userID = Number(userID) > 0 ? userID + "Æ" + jsonResp.email : "";

          var name = jsonResp.name;


          var emailMsg = jsonResp.emailMsg;
          $("#userList").val(userID);
          $("#emailMsg").val(emailMsg);
          $("#eventName").val(name);

        }
      }
    });

    setInterval(function() {
      if ($("#userList").val() == "") {
        $("#emailMsg").val("");
        $("#emailMsg").prop("disabled", true);
      } else {
        $("#emailMsg").prop("disabled", false);
      }
    }, 0);

    function calculateTimeDifference(startDate, endDate) {
      // Convert the string dates to Date objects
      var start = new Date(startDate);
      var end = new Date(endDate);

      // Calculate the difference in milliseconds
      var difference = Math.abs(end - start);

      // Convert milliseconds to hours and minutes
      var hours = Math.floor(difference / (1000 * 60 * 60));
      var minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));

      // Define pluralization helper function
      function pluralize(count, noun) {
        return count === 1 ? noun : noun + "s";
      }

      // Construct the result string
      var result = "";
      if (hours > 0) {
        result += hours + " " + pluralize(hours, "Hour") + " ";
      }
      if (minutes > 0) {
        result += minutes + " " + pluralize(minutes, "Minute");
      }

      return result.trim(); // Trim any leading/trailing whitespace
    }

    // Test the function
    var startDate = new Date($("#startDate").val());
    var endDate = new Date($("#endDate").val());

    var timeDifference = calculateTimeDifference(startDate, endDate);
    // console.log("Time Difference:", timeDifference);
    $("#duration").val(timeDifference);

    $(".btnEditEvent").click(function() {
      updateEvent();
    });

    function updateEvent() {

      var event_id = $("#event_id").val();

      var arr = $("#userList").val().split("Æ");

      var event_id = $("#event_id").val();
      var eventName = $("#eventName").val();
      var startDate = $("#startDate").val();
      var endDate = $("#endDate").val();
      var userID = arr[0];
      var email = arr[1];
      var emailMsg = $("#emailMsg").val();

      var flag = true;

      var alertMsg = "";

      if (eventName.trim() == "") {
        alertMsg = "Event Name Cannot Be Empty";
        flag = false;
      }

      if (new Date(startDate) > new Date(endDate)) {
        if (alertMsg == "")
          alertMsg = "\Start Date Cannot Be Greater Than End Date";
        else
          alertMsg += "\nStart Date Cannot Be Greater Than End Date";


        flag = false;
      }

      if (!flag) {
        alert(alertMsg);
      } else {
        $.ajax({
          url: "<?php echo BASE_URL . 'app/Views/users/dashboard_role/ajax_handler.php' ?>",
          type: "POST",
          data: {
            event_id: event_id,
            eventName: eventName,
            startDate: startDate,
            endDate: endDate,
            userID: userID,
            email: email,
            emailMsg: emailMsg,
            action: "updateEvent"
          },
          async: false, // Set async option to false for synchronous request
          success: function(resp) {

            if (resp == 'Event updated successfully') {
              window.location.href = '<?php echo BASE_URL . 'app/Views/users/dashboard_role/dashboard_admin.php' ?>';
            } else {
              alert('Some fields have errors.');
            }

            // Handle the response here
            console.log(resp);
          },
          error: function(xhr, status, error) {
            // Handle errors here
            console.error(error);
            alert('Some fields have errors.');
          }
        });

      }
    }


    $(".btnDelEvent").click(function() {

      deleteEvent()
    });

    function deleteEvent() {

      var event_id = $("#event_id").val();

      $.ajax({
        url: "<?php echo BASE_URL . 'app/Views/users/dashboard_role/ajax_handler.php' ?>",
        type: "POST",
        data: {
          event_id: event_id,
          action: "deleteEvent"
        },
        async: false, // Set async option to false for synchronous request
        success: function(resp) {
          // Handle the response here
          console.log(resp);
        },
        error: function(xhr, status, error) {
          // Handle errors here
          console.error(error);
          alert('Some fields have errors.');
        }
      });

      window.location.href = '<?php echo BASE_URL . 'app/Views/users/dashboard_role/dashboard_admin.php' ?>';
    }
    // Character count functionality
    $("#emailMsg").on('input', function() {
      var charCount = $(this).val().length;
      $("#charCount").text(charCount + " / 2500");

      if (charCount >= 2500) {
        $("#charLimitMsg").removeClass("d-none");
      } else {
        $("#charLimitMsg").addClass("d-none");
      }
    });





  });
</script>