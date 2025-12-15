$(document).ready(function () {

  function formatDate2(dateStr) {
    // Split date and time parts
    var parts = dateStr.split(" ");

    // Split date part into day, month, and year
    var datePartArr = parts[0].split("-");
    const day = datePartArr[0];
    const month = datePartArr[1];
    const year = datePartArr[2]; // Corrected index to get the year

    // Split time part into hours and minutes
    var timePartArr = parts[1].split(":");
    const hours = timePartArr[0];
    const minutes = timePartArr[1];

    // Construct the formatted date string
    return `${year}-${month}-${day}T${hours}:${minutes}:00`; // Adjusted format to omit milliseconds
  }

  $("input").attr("autocomplete", "off");

  if ($("#tempStartDate").length > 0 && $("#tempEndDate").length > 0) {

    var tempStartDateElement = flatpickr("#tempStartDate", {
      enableTime: true,
      time_24hr: true, // Enable 24-hour time format
      dateFormat: "d-m-Y H:i:S", // Set the desired date format
      onChange: function (selectedDates, dateStr, instance) {
        // When a date is selected, set the value of startDate input field
        $("#startDate").val(formatDate2(dateStr));
      }
    });



    var tempEndDateElement = flatpickr("#tempEndDate", {
      enableTime: true,
      time_24hr: true, // Enable 24-hour time format
      dateFormat: "d-m-Y H:i:S", // Set the desired date format
      onChange: function (selectedDates, dateStr, instance) {
        // When a date is selected, set the value of startDate input field
        $("#endDate").val(formatDate2(dateStr));
      }
    });

    if ($("#startDate").val() != "") {
      tempStartDateElement.setDate(new Date($("#startDate").val()));
    }

    if ($("#endDate").val() != "") {
      tempEndDateElement.setDate(new Date($("#endDate").val()));
    }



  }


  var eventList = [];

  var calendar = "";
  if ($('#calendar').length > 0) {
    calendar = new FullCalendar.Calendar($('#calendar')[0], {
      initialView: 'dayGridMonth',
      events: [],
      firstDay: 1,
      eventTimeFormat: {
        hour: 'numeric',
        minute: '2-digit',
        meridiem: 'short'
      },

      views: {
        week: {
          dayHeaderContent: function(arg) {
            var dayOfMonth = arg.date.getDate();
            var month = arg.date.getMonth() + 1;                                    
            var formattedDate = ('0' + dayOfMonth).slice(-2) + '/' + ('0' + month).slice(-2);
            var dayOfWeek = arg.date.toLocaleDateString('en-GB', { weekday: 'short' });
            return dayOfWeek + ' ' + formattedDate;
          }
        }
      },
      allDaySlot:false, 
      allDayText: '24 hours',
      allDayMaintainDuration: true,
      slotMinTime: '00:00:00', 
      slotMaxTime: '24:00:00',
      slotLabelFormat: {
        hour: 'numeric'
      },
      height: 'auto',
      contentHeight: 'auto',


      eventContent: function (arg) {
        // Check if event object and its start/end properties are valid
        if (arg && arg.event && arg.event.start && arg.event.end) {
          // Extract start and end dates from the event object
          var startDate = moment(arg.event.start).format('DD/MM/YYYY');
          var endDate = moment(arg.event.end).format('DD/MM/YYYY');

          // Create custom event content with start and end dates
          var content = '<div class="fc-daygrid-event-dot" style="border-color: ' + arg.event.backgroundColor + ';">' + '</div>';
          content += '<div class="fc-event-title">' + arg.event.title + '</div>';
          content += '<div class="fc-event-dates"><b>' + startDate + ' - ' + endDate + '</b></div>';

        } else if (arg && arg.event && arg.event.start) {
          // Return empty content if event object or its properties are invalid
          var startDate = moment(arg.event.start).format('DD/MM/YYYY');

          var content = '<div class="fc-daygrid-event-dot" style="border-color: ' + arg.event.backgroundColor + ';">' + '</div>';
          content += '<div class="fc-event-title">' + arg.event.title + '</div>';
          content += '<div class="fc-event-dates"><b>' + startDate + '</b></div>';
        }

        // Attach click event listener to the event content
        content += '<a class="d-none" href="' + arg.event.url + '">Click here</a>'; // Add URL to event content

        return { html: content };
      }

    });
    calendar.render();
  }

  $('#btnMonth').click(function () {
    calendar.changeView('dayGridMonth');
  });

  $('#btnDay').click(function () {
    calendar.changeView('timeGridDay');
  });

  $('#btnWeek').click(function () {
    calendar.changeView('dayGridWeek');
  });

  $('#btnYear').click(function () {
    calendar.changeView('multiMonthYear');
  });


  const monthYearHeading = $('.monthYearHeading');

  const viewDDL = $('.viewDDL');

  // calendar.fullCalendar({});

  const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

  const currentDate = new Date();

  const currentMonthIndex = currentDate.getMonth();

  const currentMonthName = months[currentMonthIndex];

  const weekdays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

  const currentDayIndex = currentDate.getDay();

  const currentDayAbbreviation = weekdays[currentDayIndex];

  const currentYear = currentDate.getFullYear();

  EventViewLogic();

  setInterval(function () {

    // debugger;
    // Get all td elements
    const tds = $(".fc-scrollgrid-sync-table").find("td");

    // Find the maximum height
    // let maxHeight = 0;
    // let maxWidth = 0;

    // tds.each(function () {
    //   // debugger;
    //   const height = $(this).height();
    //   const width = $(this).width();


    //   if (height > maxHeight) {
    //     maxHeight = height;
    //   }

    //   if (width > maxWidth) {
    //     maxWidth = width;
    //   }
    // });

    // Set all td elements to the maximum height
    // tds.each(function () {
    //   $(this).css("height", `${maxHeight}px`);
    //   $(this).css("width", `${maxWidth}px`);
    // });

    // console.log(calendar.el);
    // if (calendar.el != undefined) {
    //   calendar.render();
    // }


    $(".fc-daygrid-day-top").find(".fc-daygrid-day-number").off("click");

    $(".fc-daygrid-day-top").find(".fc-daygrid-day-number").on("click", function () {
      var startDate = formatDate($(this).attr("aria-label")) + " 00:00:00";
      var endDate = formatDate($(this).attr("aria-label")) + " 00:00:00";
      var href = `calendar/eventRegister.php?startDate=${startDate}&endDate=${endDate}`;
      $(this).attr("href", href);
    });
  }, 0);




  $(".fc-left").css("display", "none");

  $(".viewDDL").on("change", function () {

    $("table").find("div").css("display", "");



    var monthYearValue = $(".fc-left").find("h2").text();

    EventViewLogic();

  });
  $('.fc-toolbar-chunk').find("button").on("click", function () {

    // alert("invoke");
    EventViewLogic();
  });

/*
  $(".btnDelEvent").click(function () {

    deleteEvent()
  });

  $(".eventRegister").click(function () {
    insertEvent();
  });
$(".btnEditEvent").click(function () {
    updateEvent();
  });
*/


  $(".fc-button").click(function () {

    getEventByMonthAndYear();

  });

  if ($("#calendar").length > 0) {

    getEventByMonthAndYear();

  }

  function formatDate(inputDate) {
    var dateParts = inputDate.split(" ");
    var month = dateParts[0];
    var day = parseInt(dateParts[1].replace(",", ""));
    var year = dateParts[2];

    // Convert month to numeric value
    var monthNumber = {
      "January": "01", "February": "02", "March": "03", "April": "04",
      "May": "05", "June": "06", "July": "07", "August": "08",
      "September": "09", "October": "10", "November": "11", "December": "12"
    }[month];

    // Pad day with leading zero if needed
    day = day < 10 ? "0" + day : day;

    return year + "-" + monthNumber + "-" + day;
  }
  function EventViewLogic() {
    if (viewDDL.val() === "1") {
      $("#btnDay").click();
      getEventByMonthAndYear();

      // Setter(monthYearHeading, currentDayAbbreviation, false);
      // calendar.css("display", "none");
    }



    // ---SELECT---
    else if (viewDDL.val() === "2") {
      $("#btnMonth").click();
      getEventByMonthAndYear();
    }

    // Year View
    else if (viewDDL.val() === "3") {
      $("#btnYear").click();
      getEventByMonthAndYear();
    }

    // Events View
    else if (viewDDL.val() === "4") {
      $("#btnMonth").click();
      getEventByMonthAndYear();

      $(`td`).each(function () {
        var tdDate = $(this).attr("data-date");

        if (eventList.find(x => String(x.start).includes(tdDate)) === undefined && tdDate != undefined) {
          $(this).find("div").find(".fc-daygrid-day-top").css("display", "none");
        } else {
          $(this).find("div").find(".fc-daygrid-day-top").css("display", "");
        }

      });
    }


    // Empty Day View
    else if (viewDDL.val() === "5") {
      $("#btnMonth").click();
      getEventByMonthAndYear();

      $(`td`).each(function () {
        var tdDate = $(this).attr("data-date");
        $(this).find("div").find(".fc-daygrid-day-top").css("display", "block");

        if (eventList.find(x => String(x.start).includes(tdDate)) !== undefined && tdDate != undefined) {
          $(this).find("div").css("display", "none");
        } else {
          $(this).find("div").css("display", "");
        }

      });
    }

    // Week View
    else if (viewDDL.val() === "6") {
      $("#btnWeek").click();
      getEventByMonthAndYear();
    }

    // Upcoming events
    else if (viewDDL.val() === "7") {
      getEventByMonthAndYear();
    }

  }


  function getEventByMonthAndYear() {

    setTimeout(function () {

      // Get the current date
      var currentDate = calendar.getDate();

      // Get the year from the current date
      var currentYear = currentDate.getFullYear();

      // var arr = monthYearHeading.text().split(" ");
      // var arr = $("#fc-dom-1").text().split(" ");

      var month = 0;
      var year = currentYear;

      for (let i = 0; i < months.length; i++) {

        if (months[i] == month) {
          month = i + 1;
          break;
        }

      }

      var jsonResp = "";



      if (viewDDL.val() === "7") {
        calendar.changeView('listMonth');

        $.ajax({
          url: "ajax_handler.php",
          type: "POST",
          data: {
            action: "upComingEvent"
          },
          async: false, // Set async option to false for synchronous request
          success: function (resp) {

            eventList = [];
            if (resp != '') {
              jsonResp = JSON.parse(resp);
              var eventsByDate = {};
              for (var i = 0; i < jsonResp.length; i++) {
                var obj = jsonResp[i];
                if (obj.isPrimary == 1) {
                  eventList.push({
                    title: obj.name,
                    start: obj.startDate,
                    end: obj.endDate,
                    backgroundColor: 'green'
                  });
                } else {
                  eventList.push({
                    title: obj.name,
                    start: obj.startDate,
                    end: obj.endDate,
                    url: "calendar/eventUpdationAndDeletion.php?startDate=" + obj.startDate + "&endDate=" + obj.endDate + "&name=" + obj.name + "&event_id=" + obj.event_id,
                    backgroundColor: 'red'
                  });
                }
              }
            }
          }
          ,
          error: function (xhr, status, error) {
            // Handle errors here
            console.error(error);
          }
        });

      } else {
        $.ajax({
          url: "ajax_handler.php",
          type: "POST",
          data: {
            month: month,
            year: year,
            action: "getEventByMonthAndYear"
          },
          async: false, // Set async option to false for synchronous request
          success: function (resp) {
            eventList = [];
            if (resp != '') {
              jsonResp = JSON.parse(resp);
              var eventsByDate = {};
              for (var i = 0; i < jsonResp.length; i++) {
                var obj = jsonResp[i];

                if (obj.isPrimary == 1) {
                  eventList.push({
                    title: obj.name,
                    start: obj.startDate,
                    end: obj.endDate,
                    backgroundColor: 'green'
                  });
                } else {
                  eventList.push({
                    title: obj.name,
                    start: obj.startDate,
                    end: obj.endDate,
                    url: "calendar/eventUpdationAndDeletion.php?startDate=" + obj.startDate + "&endDate=" + obj.endDate + "&name=" + obj.name + "&event_id=" + obj.event_id,
                    backgroundColor: 'red'
                  });
                }



              }

              /*
              $.ajax({
                url: 'https://calendarific.com/api/v2/holidays',
                type: 'GET',
                async: false,
                dataType: 'json',
                data: {
                    api_key: "De589pcDZ7m4yY663dxjnhASQf2xg58",
                    country: "US",
                    year: currentYear, // Change this to the year you want to retrieve holiday data for
                },
                success: function(response) {

                    // Handle the response data
                    // console.log(response);
                    if (response.meta.code === 200) {
                        // Success, process the holiday data
                        var uniqueNames = {};

                        // Use reduce to filter unique names


                        var holidays = response.response.holidays;

                        var uniqueHolidays = holidays.reduce(function(acc, curr) {
                          if (!uniqueNames[curr.name]) {
                              uniqueNames[curr.name] = true;
                              acc.push(curr);
                          }
                          return acc;
                      }, []);

                        // Iterate over the holidays and do something with the data
                        uniqueHolidays.forEach(function(holiday) {
                          eventList.push({
                            title: holiday.name,
                            start: holiday.date.iso+" 00:00:00",
                            backgroundColor: 'green'
                            // url: "eventUpdationAndDeletion.php?startDate=" + obj.startDate + "&name=" + obj.name + "&event_id=" + obj.event_id

                          });
                        });
                    } else {
                        // Error handling
                        console.error('Error: ' + response.meta.error_detail);
                    }
                },
                error: function(xhr, status, error) {
                    // Error handling
                    console.error('Error: ' + error);
                }
            });
              */

              // Group events by date
              // for (var i = 0; i < jsonResp.length; i++) {
              //   var obj = jsonResp[i];
              //   var dateString = obj.startDate;

              //   if (!eventsByDate[dateString]) {
              //     eventsByDate[dateString] = [];
              //   }

              //   eventsByDate[dateString].push(obj);
              // }

              // Display events by date
              // for (var date in eventsByDate) {
              //   var events = eventsByDate[date];
              //   var parts = date.split('-');
              //   var day = parseInt(parts[2], 10);
              //   var body = "";

              //   for (var j = 0; j < events.length; j++) {
              //     var event = events[j];
              //     body += `<a href="eventUpdationAndDeletion.php?startDate=${event.startDate}&name=${event.name}&event_id=${event.event_id}">${event.name}</a><br>`;
              //   }

              //   $("td[data-date='" + date + "']").find("span").html(day + "<br>" + body);
              // }
            }
          }
          ,
          error: function (xhr, status, error) {
            // Handle errors here
            console.error(error);
          }
        });
      }
      calendar.getEventSources().forEach(function (source) {
        source.remove(); // Remove previous event sources
      });
      calendar.addEventSource(eventList); // Add new events

      // calendar = new FullCalendar.Calendar($('#calendar')[0], {
      //   initialView: 'dayGridMonth',
      //   events:eventList
      // });
      // calendar.render();

    }, 200);

  }
/*
  function deleteEvent() {

    var event_id = $("#event_id").val();


    $.ajax({
      url: "ajax_handler.php",
      type: "POST",
      data: {
        event_id: event_id,
        action: "deleteEvent"
      },
      async: false, // Set async option to false for synchronous request
      success: function (resp) {

        // Handle the response here
        console.log(resp);
      },
      error: function (xhr, status, error) {
        // Handle errors here
        console.error(error);
      }
    });


    // alert("Event Inserted");
    window.location.href = "calendar.php";
  }
*/
/*
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


      $.ajax({
        url: "ajax_handler.php",
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

          // Handle the response here
          console.log(resp);
        },
        error: function (xhr, status, error) {
          // Handle errors here
          console.error(error);
        }
      });


      // alert("Event Inserted");
      window.location.href = "calendar.php";
    }
  }
*/
  /*
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
    }
    else {
      $.ajax({
        url: "ajax_handler.php",
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
        success: function (resp) {

          // Handle the response here
          console.log(resp);
        },
        error: function (xhr, status, error) {
          // Handle errors here
          console.error(error);
        }
      });


      // alert("Event Inserted");
      window.location.href = "calendar.php";

    }
  }
*/

  function Setter(element, value, isValueType = true) {
    if (isValueType) {
      if ($(element).val() != value) {
        $(element).val(value);
      }
    }
    else {
      if ($(element).text() != value) {
        $(element).text(value);
      }
    }
  }


});
