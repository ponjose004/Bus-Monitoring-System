 <?php
// Connect to database
$con = new mysqli("127.0.0.1", "root", "", "bus");

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$query = "select * from bus_number_detection";
$result = mysqli_query($con,$query);

// Close MySQL connection
mysqli_close($con);


?> 
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <meta http-equiv="refresh" content="3">
    <title>Bus Monitoring System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>

    </style>
</head>

<body>
    <input type="checkbox" name="menu-toggle" id="menu-toggle">
    <div class="sidebar">
        <div class="side-header">
            <h3><span></span>
                <small class="las la-user-alt"></small>
                <span>Profile</span>
            </h3>
        </div>
        <div class="side-content">
            <div class="side-menu">
                <ul>
                    <li>
                        <a href="" class="active">
                            <span class="las la-poll"></span>
                            <small>Add Form</small>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <span class="las la-chart-bar"></span>
                            <small>Form Data Show</small>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="main-content">
        <header>
            
            <div class="header-content">
                <label for="menu-toggle">
                    <span class="las la-bars"></span>
                </label>
                <h2>Bus Monitoring System</h2>
                <div class="header-menu">
                    <div class="user">
                        <a href="">
                            <span class="las la-power-off"></span>
                            <span>Logout</span></a>
                    </div>
                </div>
            </div>
        </header>
        <main>
            <br><br><br><br><br><br><div class="page-content">

                    <div id="main">
                        <table width="100%" id="table-body">
        <thead>
            <tr>
                <th></th>
                <th><span class="las la-sort"></span>id</th>
                <th><span class="las la-sort"></span>Bus_Number</th>
                <th><span class="las la-sort"></span>Licence_plate_Number</th>
                <th><span class="las la-sort"></span>In_time</th>
                <th><span class="las la-sort"></span>In_date</th>
                <th><span class="las la-sort"></span>Out_time</th>
                <th><span class="las la-sort"></span>Out_Date</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $rows = array();
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }

            // Reverse the order of the rows
            $rows = array_reverse($rows);

            foreach ($rows as $row) {
                echo "<tr id='" . $row["id"] . "'>";
                echo "<td></td>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["bus_number"] . "</td>";
                echo "<td>" . $row["licence_plate_number"] . "</td>";
                echo "<td>" . $row["In_time"] . "</td>";
                echo "<td>" . $row["In_date"] . "</td>";
                echo "<td>" . $row["Out_time"] . "</td>";
                echo "<td>" . $row["Out_Date"] . "</td>";
                echo "</tr>";
}
?>
        </tbody>
    </table>

                </div>
        </main>
    </div>


</body>

</html>