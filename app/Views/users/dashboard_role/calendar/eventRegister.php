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

// Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role_id"] == 3)) {
  header("location: ../../../../../../index.php");
  exit;
}

$loginError = '';
if ($_SESSION["loggedin"] == false) {
  header("Location: index.php");
  exit();
}
$role_id = $_SESSION["role_id"];

if (isset($_GET["startDate"])) {
  $startDate = $_GET["startDate"];
} else {
  $startDate = "";
}

if (isset($_GET["endDate"])) {
  $endDate = $_GET["endDate"];
} else {
  $endDate = "";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Event Register</title>
    <!-- Tab Icon -->
    <link rel="icon" href="../../../../../public/img/logo.png">



</head>

<body>

  <div class="container my-5">

    <div class="card">
      <div class="card-header">
        <h2>Event Registration</h2>

      </div>
      <div class="card-body text-start">


        <label for="eventName" class="font-weight-bold">Event Name</label>
        <input id="eventName" class="form-control mb-3">

        <div class="row">

          <div class="col-12 col-lg-6">
            <label for="startDate" class="font-weight-bold">Start Date</label>
            <input id="tempStartDate" class="form-control mb-3" value="<?php echo $startDate ?>">
            <input style="display:none" id="startDate" class="form-control mb-3" type="datetime-local" value="<?php echo $startDate ?>">
          </div>

          <div class="col-12 col-lg-6">
            <label for="endDate" class="font-weight-bold">End Date</label>
            <input id="tempEndDate" class="form-control mb-3" value="<?php echo $startDate ?>">
            <input style="display:none" id="endDate" class="form-control mb-3" type="datetime-local" value="<?php echo $endDate ?>">
          </div>

        </div>

        <label for="userList" class="font-weight-bold">User</label>
        <div class="userListDiv text-start">

        </div>



        <label for="emailMsg" class="font-weight-bold">Email Message</label>
        <textarea id="emailMsg" class="form-control mb-3" maxlength="2500" disabled></textarea>
        <div id="charCount" class="text-right">0 / 2500</div>
        <div id="charLimitMsg" class="text-danger d-none">You have reached the maximum characters allowed.</div>



        <?php
        // Admin or owner role.
        if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2) {

        ?>
          <button class="btn btn-primary eventRegister">Save</button>
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

setInterval(function() {
  if ($("#userList").val() == "") {
    $("#emailMsg").val("");
    $("#emailMsg").prop("disabled", true);
  } else {
    $("#emailMsg").prop("disabled", false);
  }
}, 0);

$(".eventRegister").click(function () {
  insertEvent();
});

function insertEvent() {
  var arr = $("#userList").val().split("Æ");

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
  }
  else {
    console.log(eventName);

    $.ajax({
      url: "<?php echo BASE_URL . 'app/Views/users/dashboard_role/ajax_handler.php' ?>",
      type: "POST",
      data: {
        eventName: eventName,
        startDate: startDate,
        endDate: endDate,
        userID: userID,
        email: email,
        emailMsg: emailMsg,
        action: "insertEvent"
      },
      async: false, // Set async option to false for synchronous request
      success: function (resp) {
        if(!isNaN(resp)) {
          window.location.href = '<?php echo BASE_URL . 'app/Views/users/dashboard_role/dashboard_admin.php' ?>';
        } else {
          alert('Some fields have errors.');
        }

        console.log(resp);
      },
      error: function (xhr, status, error) {
        // Handle errors here
        console.error(error);
        alert('Some fields have errors.');
      }
    });
  }
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