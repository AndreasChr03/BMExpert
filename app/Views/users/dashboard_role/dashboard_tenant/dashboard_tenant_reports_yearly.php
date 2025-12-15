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
    <link rel="icon" href="../../../../../public/img/logo.png">
    <title>Yearly Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../../../../../public/css/landing_page.css">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />  
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet"> 
</head>
<body class="sb-nav-fixed">
<style>
    .button-container {
        display: flex;
        justify-content: space-between;
        align-items: center; /* Ensures vertical alignment of children */
        width: 100%;
        padding: 0 20px;
    }
    .button-container div > select {
        cursor: pointer;
    }
    .button-container button,
    .button-container div > select {
        padding: 8px 16px; /* Uniform padding */
        font-size: 16px; /* Consistent font size */
        margin: 4px 2px; /* Margin for spacing */
        border: 2px solid #086972; /* Border color */
        background-color: #086972; /* Initial background color for buttons */
        color: white; /* Initial text color for better visibility */
        border-radius: 4px; /* Rounded corners */
        height: 40px; /* Uniform height */
        width: auto; /* Auto width to accommodate content */
        display: flex;
        align-items: center; /* Ensures text in buttons/selects is centered vertically */
        justify-content: center; /* Centers content horizontally */
    }
    .button-container > div > select {
        background-color: transparent; /* Specific background for select */
        color: #086972; /* Text color for select */
    }
    /* Adding hover effect for buttons */
    .button-container button:hover {
        background-color: black; /* Black background on hover */
        border-color: black; /* Border color changes to black on hover */
        color: white; /* Ensure text color remains white on hover */
    }
    /* Prevent buttons from becoming transparent when clicked */
    .button-container button:active {
        background-color: black !important; /* Same as non-hover state */
        border-color: #086972; /* Maintain border color */
        color: white !important;
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

<?php include_once '../dashboard_modules_header.php'; ?>
    
<div class="content-area" style="width: 100%; text-align: center; margin-top: 20px;">
    <div class="dropdown-area" style="margin-bottom: 20px;">
        <div class="button-container" style="display: flex; justify-content: space-between; align-items: center;">
            <button id="leftBtn" class="btn" style="margin-right: 10px;">Per Month</button>
            <div style="display: flex; align-items: center;">
                <select name="year" id="yearSelect" style="margin-right: 5px;">
                    <?php
                    $currentYear = date('Y'); // Get the current year
                    $startYear = 2020; // Set the start year to 2020
                    for ($year = $currentYear; $year >= $startYear; $year--) {
                        echo "<option value='{$year}'>{$year}</option>"; // Create an option for each year
                    }
                    ?>
                </select>
                <button onclick="fetchData()" class="btn">Show Selected Year</button>
            </div>
            <button id="rightBtn" class="btn">Per Year</button>
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
            <div class="card mb-6" id="card" >
                <div class="card-header" id="header">
                    <i class="fas fa-chart-area me-1"></i>
                    Monthly Data Report
                </div>
                <div class="card-body">
                    <canvas id="myChart1" style="height: 500px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
    </div>
    

    <script>
        function fetchData() {
            var year = $('#yearSelect').val(); // Correct variable name to fetch selected year
            $.ajax({
                url: 'process_reports_yearly.php',
                type: 'POST',
                data: {year: year}, // Correct data object key
                success: function(data) {
                    var chartData = JSON.parse(data); // Assume data is a JSON string
                    updateChart(chartData);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        function updateChart(chartData) {
            var ctx = document.getElementById('myChart1').getContext('2d');
            if (window.myBarChart) {
                window.myBarChart.destroy(); // Properly destroy the previous chart instance
            }
            window.myBarChart = new Chart(ctx, {
                type: 'line',
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
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    </script>
</body>
</html>
        