<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Details</title>
<style>
body {
font-family: Arial, sans-serif;
background-image: url('sear.jpg'); /* Replace 'sear.jpg' with your actual image path */
background-size: cover;
background-repeat: no-repeat;
background-position: center;
margin: 0;
padding: 0;
transition: background-image 0.5s ease; /* Smooth transition effect */
}
.container {
width: 80%;
max-width: 800px;
margin: 50px auto;
background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white background */
padding: 20px;
border-radius: 8px;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
h2 {
text-align: center;
}
table {
width: 100%;
border-collapse: collapse;
margin-top: 20px;
}
table, th, td {
33
border: 1px solid #ccc;
padding: 10px;
text-align: left;
}
.back-link {
margin-top: 10px;
text-align: center;
text-decoration: none;
color: #333;
display: block;
}
.back-link:hover {
color: #555;
}
</style>
</head>
<body>
<div class="container">
<h2>Customer Details</h2>
<?php
// Check if the form was submitted with valid data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Retrieve input values
$lastName = $_POST['lastName'];
$contactNumber = $_POST['contactNumber'];
// Establish database connection (replace with your database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Hoteldb";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL statement based on input
if (!empty($lastName)) {
// Search by Last Name
$sql = "SELECT cd.Customer_Number, cd.First_Name, cd.Last_Name, cd.Address1 AS Address, cd.Contact_Number,
r.Room_Type_ID, r.Booking_Type, r.From_Date, r.To_Date, r.Number_of_People, r.Rate
FROM Customer_Details cd
LEFT JOIN Reservation r ON cd.Customer_Number = r.Customer_Number
WHERE cd.Last_Name LIKE ?";
$stmt = $conn->prepare($sql);
$search_term = "%" . $lastName . "%";
$stmt->bind_param("s", $search_term);
} elseif (!empty($contactNumber)) {
// Search by Contact Number
$sql = "SELECT cd.Customer_Number, cd.First_Name, cd.Last_Name, cd.Address1 AS Address, cd.Contact_Number,
r.Room_Type_ID, r.Booking_Type, r.From_Date, r.To_Date, r.Number_of_People, r.Rate
FROM Customer_Details cd
LEFT JOIN Reservation r ON cd.Customer_Number = r.Customer_Number
WHERE cd.Contact_Number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $contactNumber);
} else {
// No valid input provided
echo "<p>Please enter either Last Name or Contact Number.</p>";
exit();
}
// Execute SQL statement
$stmt->execute();
$result = $stmt->get_result();
// Check if there are results
if ($result->num_rows > 0) {
// Display results in a table
echo "<table>";
echo "<tr><th>Customer Number</th><th>Name</th><th>Address</th><th>Contact Number</th>
<th>Room Type ID</th><th>Booking Type</th><th>From Date</th><th>To Date</th>
<th>Number of People</th><th>Rate</th></tr>";

while ($row = $result->fetch_assoc()) {
echo "<tr>";
echo "<td>" . htmlspecialchars($row["Customer_Number"]) . "</td>";
echo "<td>" . htmlspecialchars($row["First_Name"] . " " .$row["Last_Name"]) . "</td>";
echo "<td>" . htmlspecialchars($row["Address"]) . "</td>";
echo "<td>" . htmlspecialchars($row["Contact_Number"]) . "</td>";
echo "<td>" . htmlspecialchars($row["Room_Type_ID"]) . "</td>";
echo "<td>" . htmlspecialchars($row["Booking_Type"]) . "</td>";
echo "<td>" . htmlspecialchars($row["From_Date"]) . "</td>";
echo "<td>" . htmlspecialchars($row["To_Date"]) . "</td>";
echo "<td>" . htmlspecialchars($row["Number_of_People"]) . "</td>";
echo "<td>" . htmlspecialchars($row["Rate"]) . "</td>";
echo "</tr>";
}
echo "</table>";
} else {
echo "<p>No results found.</p>";
}
// Close statement and connection
$stmt->close();
$conn->close();
} else {
// If accessed directly without POST data, redirect to search page
header("Location: index.html");
exit();
}
?>
<a href="index.html" class="back-link">Homepage</a>
</div>
</body>
</html>