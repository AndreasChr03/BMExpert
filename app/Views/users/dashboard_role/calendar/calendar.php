<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css' rel='stylesheet' />
<link href="calendar/assets/CSS/calendarCss.css" rel='stylesheet' />

<!-- Replace slim version with full version of jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js'></script>
<script src="calendar/assets/JS/calendarJS.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <div class="my-5">

    <div class="card">
      <div class="card-header">
        <?php
        // Admin or owner role.
        if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2) {

        ?>
            <button type="button" class="btn btn-danger btnz float-end" data-toggle="modal" data-target="#sendEmailsModal">Send Emails</button>
            <a href="calendar/eventRegister.php" class="btn btn-primary btnz me-2 float-end">Add Event</a>
        <?php
        }
        ?>

        <h2 class='heading mb-0'>Calendar</h2>

      </div>

      <div class="card-body">

        <div class="header mb-3">
          <h2 class="monthYearHeading"></h2>
          <div class="header-controls">

            <select class="viewDDL select form-control ml-3">
              <option value="2">---SELECT---</option>
              <option value="1">Day View</option>
              <option value="6">Week View</option>
              <option value="2">Month View</option>
              <option value="3">Year View</option>
              <option value="4">Events View</option>
              <!-- <option value="5">Empty Day View</option> -->
              <option value="7">Upcoming events</option>
            </select>
          </div>


        </div>
        <button id="btnMonth" style="display:none">Month View</button>
        <button id="btnDay" style="display:none">Day View</button>
        <button id="btnWeek" style="display:none">Week View</button>
        <button id="btnYear" style="display:none">Year View</button>
        <div id="calendar"></div>

      </div>

    </div>



    <div class="modal fade" id="sendEmailsModal" tabindex="-1" role="dialog" aria-labelledby="deleteAlertLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="deleteAlertLabel">Send Email Alert</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p class="text-danger">Are you sure you want to send emails to the users where event end date have remaining only 24 hours or less?</p>
            <p class="text-muted">This action cannot be undone.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger sendEmailsBtn">Yes, Send Emails</button>
            <button type="button" class="btn btn-secondary sendEmailCancel" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>


<script>
$(document).ready(function() {
    $(".sendEmailsBtn").on("click", function() {
        $.get("<?php echo BASE_URL . 'app/Views/users/dashboard_role/ajax_handler.php' ?>?action=sendEmailsForUpcomingEvents", function(data, status) {
            console.log(status);
            if (status === "success") {
                console.log("Emails sent successfully.");
                $(".sendEmailCancel").click();
            }
        });
    });
});
</script>
