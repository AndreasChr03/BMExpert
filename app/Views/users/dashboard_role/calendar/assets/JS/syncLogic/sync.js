$(document).ready(function () {

    $("input").attr("autocomplete", "off");
  
  
    var eventList = [];
  
    var calendar = "";
    if ($('#calendar').length > 0) {
        calendar = new FullCalendar.Calendar($('#calendar')[0], {
            initialView: 'dayGridMonth',
            events: [],
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
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
        $(".fc-daygrid-day-top").find(".fc-daygrid-day-number").off("click");
  
        $(".fc-daygrid-day-top").find(".fc-daygrid-day-number").on("click", function () {
            var date = formatDate($(this).attr("aria-label")) + " 00:00:00";
            var href = `eventRegister.php?startDate=${date}`;
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
        debugger;
        // alert("invoke");
        EventViewLogic();
    });
  
  
    $(".btnDelEvent").click(function () {
  
        deleteEvent()
    });
  
    $(".eventRegister").click(function () {
        insertEvent();
    });
  
  
    $(".btnEditEvent").click(function () {
        updateEvent();
    });
  
  
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
            $("#btnMonth").click();
            getEventByMonthAndYear();
        }
  
    }
  
  
    function getEventByMonthAndYear() {
  
        setTimeout(function () {
            debugger;
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
                $("#btnMonth").click();
  
                $.ajax({
                    url: "ajax_handler.php",
                    type: "POST",
                    data: {
                        action: "upComingEvent"
                    },
                    async: false, // Set async option to false for synchronous request
                    success: function (resp) {
                        debugger;
                        eventList = [];
                        if (resp != '') {
                            jsonResp = JSON.parse(resp);
                            var eventsByDate = {};
                            for (var i = 0; i < jsonResp.length; i++) {
                                var obj = jsonResp[i];
                                eventList.push({
                                    title: obj.name,
                                    start: obj.startDate,
                                    url: "calendar/eventUpdationAndDeletion.php?startDate=" + obj.startDate + "&name=" + obj.name + "&event_id=" + obj.event_id,
                                    backgroundColor: 'red'
                                });
                            }
                        }
                    }
                    ,
                    error: function (xhr, status, error) {
                        // Handle errors here
                        console.error(error);
                    }
                });
  
            }else{
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
                                eventList.push({
                                    title: obj.name,
                                    start: obj.startDate,
                                    url: "calendar/eventUpdationAndDeletion.php?startDate=" + obj.startDate + "&name=" + obj.name + "&event_id=" + obj.event_id,
                                    backgroundColor: 'red'
                                });
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
                                  debugger;
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
                debugger;
                // Handle the response here
                console.log(resp);
            },
            error: function (xhr, status, error) {
                // Handle errors here
                console.error(error);
            }
        });
  
  
        // alert("Event Inserted");
        window.location.href = "Index.php";
    }
  
  
  
    // SyncHolidays(2020, 2124);
  
  
  
  });
  
  function insertEvent(eventName = $("#eventName").val(), eventDate = $("#eventDate").val(), isPrimary = false) {
  
    $.ajax({
        url: "ajax_handler.php",
        type: "POST",
        data: {
            eventName: eventName,
            eventDate: eventDate,
            isPrimary: isPrimary,
            action: "insertEvent"
        },
        async: false, // Set async option to false for synchronous request
        success: function (resp) {
            // debugger;
            // Handle the response here
            console.log(resp);
        },
        error: function (xhr, status, error) {
            // Handle errors here
            console.error(error);
        }
    });
  
  
    // alert("Event Inserted");
    // window.location.href = "Index.php";
  }
  
  function updateEvent() {
  
    // debugger;
    var eventName = $("#eventName").val();
    var eventDate = $("#eventDate").val();
    var event_id = $("#event_id").val();
  
    $.ajax({
        url: "ajax_handler.php",
        type: "POST",
        data: {
            eventName: eventName,
            eventDate: eventDate,
            event_id: event_id,
            action: "updatetEvent"
        },
        async: false, // Set async option to false for synchronous request
        success: function (resp) {
            debugger;
            // Handle the response here
            console.log(resp);
        },
        error: function (xhr, status, error) {
            // Handle errors here
            console.error(error);
        }
    });
  
  
    // alert("Event Inserted");
    // window.location.href = "Index.php";
  }
  
  function SyncHolidays(startYear, endYear) {
    var currentYear = startYear;
    var interval = setInterval(function () {
        $.ajax({
            url: 'https://calendarific.com/api/v2/holidays',
            type: 'GET',
            async: true,
            dataType: 'json',
            data: {
                api_key: "De589pcDZ7m4yY663dxjnhASQf2xg58k",
                country: "CY",
                year: currentYear,
            },
            success: function (response) {
                if (response.meta.code === 200) {
                    var holidays = response.response.holidays;
                    var uniqueNames = {};
                    var uniqueHolidays = holidays.filter(function (holiday) {
                        if (!uniqueNames[holiday.name]) {
                            uniqueNames[holiday.name] = true;
                            return true;
                        }
                        return false;
                    });
  
                    uniqueHolidays.forEach(function (holiday) {
                        // console.log("Year: " + currentYear, "Name: " + holiday.name, "Date: " + holiday.date.iso);
                        insertEvent(holiday.name, holiday.date.iso + " 00:00:00", true);
                    });
  
                    // Display alert after syncing
                    if (currentYear === endYear) {
                        clearInterval(interval);
                        alert("Sync has been completed: PC HIBRENATE");
                    }
                }
            }
        });
  
        if (currentYear === endYear) {
            clearInterval(interval);
        } else {
            currentYear++;
        }
    }, 60000); 
  }
  
