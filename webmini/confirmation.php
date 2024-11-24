<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Hoteldb";

// Initialize variables
$reservation_id = null;
$errors = [];

// Check if reservation ID is passed in URL
if (isset($_GET['id'])) {
    $reservation_id = intval($_GET['id']);
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL query to retrieve reservation details
    $stmt = $conn->prepare("SELECT r.Reservation_Number, c.First_Name, c.Last_Name, rt.Room_Type_Name, r.Booking_Type, r.From_Date, r.To_Date, r.Number_of_People, r.Rate
                            FROM Reservation r
                            JOIN Customer_Details c ON r.Customer_Number = c.Customer_Number
                            JOIN Room_Type rt ON r.Room_Type_ID = rt.Room_Type_ID
                            WHERE r.Reservation_Number = ?");
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if reservation exists
    if ($result->num_rows > 0) {
        $reservation = $result->fetch_assoc();
    } else {
        $errors[] = "Reservation not found.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    $errors[] = "Reservation ID not provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #f2f2f2;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
            text-align: center;
        }
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .error-message {
            color: red;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reservation Confirmation</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <table>
                <tr>
                    <th>Reservation Number</th>
                    <td><?php echo htmlspecialchars($reservation['Reservation_Number']); ?></td>
                </tr>
                <tr>
                    <th>Customer Name</th>
                    <td><?php echo htmlspecialchars($reservation['First_Name'] . ' ' . $reservation['Last_Name']); ?></td>
                </tr>
                <tr>
                    <th>Room Type</th>
                    <td><?php echo htmlspecialchars($reservation['Room_Type_Name']); ?></td>
                </tr>
                <tr>
                    <th>Booking Type</th>
                    <td><?php echo htmlspecialchars($reservation['Booking_Type']); ?></td>
                </tr>
                <tr>
                    <th>From Date</th>
                    <td><?php echo htmlspecialchars($reservation['From_Date']); ?></td>
                </tr>
                <tr>
                    <th>To Date</th>
                    <td><?php echo htmlspecialchars($reservation['To_Date']); ?></td>
                </tr>
                <tr>
                    <th>Number of People</th>
                    <td><?php echo htmlspecialchars($reservation['Number_of_People']); ?></td>
                </tr>
                <tr>
                    <th>Rate</th>
                    <td><?php echo htmlspecialchars('$' . number_format($reservation['Rate'], 2)); ?></td>
                </tr>
            </table>
        <?php endif; ?>

        <button onclick="window.location.href='index.html'">Homepage</button>
    </div>
</body>
</html>
