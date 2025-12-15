<?php
session_start();

// Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role_id"]!== 3 )) {
    header("location: ../../../../../index.php");
    exit;
}
include_once '../../../../../config/config.php';
?>
    
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Monthly Reports</title>
        <link rel="icon" href="../../../../../public/img/logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="../../../../../public/css/landing_page.css">
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet"> 
        </head>
    <body> 
    <style>
    .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 10px;
        }

        .button-container>button,
        .button-container>form>button,
        .button-container>form>select {
            border: 1px solid #086972;
            padding: 6px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
            height: 40px;
            background-color: #086972;
            color: white;
            border: 2px solid #086972;
            /* Border color */
        }

        /* Add hover effect */
        .button-container>button:hover,
        .button-container>form>button:hover {
            background-color: black;
            /* Slightly darker shade for hover */
            border-color: #064f5c;
        }

        .button-container>form>select {
            background-color: transparent;
            color: #086972;
        }

        body {
            overflow-x: hidden;
        }
        #card {
            border: 2px solid #064f5c; 
        }
        #header {
            background-color: rgba(6, 79, 92, 0.9); /* Semi-transparent version of #064f5c */
            font-weight: bold; /* Makes the text bold */
            color: white; /* Sets the text color to white */
        }       
    </style>
    
    <script>
        window.onload = function() {
            var select = document.getElementById('monthSelect');
            var width = select.offsetWidth;
            select.style.width = width + 'px';
        };  
    </script>
    
    <?php include_once '../dashboard_modules_header.php'; ?>
          
        
    
    <div class="content-area" style="width: 100%; text-align: center;">
        <div class="dropdown-area" >
            <div class="select-month" style="padding: 8px 16px;">
                <div class="button-container">
                    <button id="leftBtn" class="btn btn-primary">Per Month</button>
                    <form id="monthForm" style="flex-grow: 1; display: flex; justify-content: center; align-items: center;">

                        <select name="month" id="monthSelect">
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <option value="05">May</option>
                            <option value="06">June</option>
                            <option value="07">July</option>
                            <option value="08">August</option>
                            <option value="09">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                        <button type="button" onclick="fetchData()" class="btn btn-primary" style="border: 1px solid #086972; background-color: #086972; color: white; height: 40px;">Show Selected Month</button>
                    </form>
                    <button id="rightBtn" class="btn btn-primary">Per Year</button>
                </div>
            </div>
        </div>
    </div>
                <script>
                    document.getElementById("leftBtn").addEventListener("click", function() {
                        window.location.href = 'dashboard_tenant_reports_monthly.php';
                    });
                    document.getElementById("rightBtn").addEventListener("click", function() {
                        window.location.href = 'dashboard_tenant_reports_yearly.php';
                    });
                </script>
                <div class="row" style="display: flex;justify-content:center;">
        <div class="col-xl-10">
            <div class="card mb-6" id="card">
                <div class="card-header" id="header">
                    <i class="fas fa-chart-area me-1"></i>
                    Monthly Data Report
                </div>
                <div class="card-body">
                    <canvas id="myChart" style="height: 500px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
            </div>
    

    <script>
        function fetchData() {
    var month = $('#monthSelect').val();
    $.ajax({
        url: 'process_reports_monthly.php',
        type: 'POST',
        data: {month: month},
        success: function(data) {
            var chartData = JSON.parse(data);
            updateChart(chartData);
        }
    });
}

function updateChart(chartData) {
    var canvas = document.getElementById('myChart');
    var ctx = canvas.getContext('2d');
    if (window.myBarChart) {
        window.myBarChart.destroy(); // Destroy the existing chart to avoid issues
    }
    window.myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Expenses for Each of the Last 6 Years',
                data: chartData.data,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true, // Make the chart responsive
            maintainAspectRatio: false, // Ignore the aspect ratio to fill the container
            scales: {
                y: {
                    beginAtZero: true // Start the y-axis at zero
                }
            }
        }
    });
}

    </script>
</body>
</html>
        