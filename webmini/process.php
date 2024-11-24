<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Function to validate if a string is a valid date in DD/MM/YYYY format
function validateDate($date)
{
$d = DateTime::createFromFormat('j/n/Y', $date); // Allow single-digit day (j) and month (n)
return $d && $d->format('j/n/Y') === $date;
}
// Function to calculate age based on date of birth
function calculateAge($dob)
{
$today = new DateTime();
$birthdate = DateTime::createFromFormat('j/n/Y', $dob); // Use same format as validateDate
$age = $today->diff($birthdate)->y;
return $age;
}
// Function to check if a string is numeric
function isNumeric($value)
{
return is_numeric($value);
}
// Function to sanitize input data
function sanitizeInput($data)
{
return htmlspecialchars(stripslashes(trim($data)));
}
// Validate form submission and process registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Initialize error array
$errors = array();
// Sanitize and validate first name
$firstName = sanitizeInput($_POST["firstName"]);
if (strlen($firstName) < 2) {
    $errors[] = "First Name should be at least 2 characters long.";
}
// Sanitize and validate last name
$lastName = sanitizeInput($_POST["lastName"]);
if (strlen($lastName) < 2) {
$errors[] = "Last Name should be at least 2 characters long.";
}
// Validate date of birth
$dob = sanitizeInput($_POST["dob"]);
echo "Date of Birth input: " . $dob . "<br>"; // Debugging output
if (!validateDate($dob)) {
$errors[] = "Date of Birth should be in DD/MM/YYYY format.";
} elseif (new DateTime() <= DateTime::createFromFormat('j/n/Y', $dob)) {
$errors[] = "Date of Birth should be less than today's date.";
} elseif (calculateAge($dob) < 18) {
$errors[] = "Age must be at least 18 years old.";
}
// Sanitize and validate address fields, city, pin code, and contact number
$address1 = sanitizeInput($_POST["address1"]);
$address2 = sanitizeInput($_POST["address2"]);
$city = sanitizeInput($_POST["city"]);
$pinCode = sanitizeInput($_POST["pinCode"]);
if (!isNumeric($pinCode) || strlen($pinCode) !== 6) {
$errors[] = "Pin code should be numeric and consist of 6 digits.";
}
$contactNumber = sanitizeInput($_POST["contactNumber"]);
if (!isNumeric($contactNumber)) {
$errors[] = "Contact number should be numeric.";
}
// Database connection details (modify as needed)
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your database password if any
$dbname = "Hoteldb"; // Replace with your database name
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection

if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
// Check if there is already a record with the same Last Name and Contact Number
$sqlCheck = "SELECT * FROM Customer_Details WHERE Last_Name = '$lastName' AND Contact_Number = '$contactNumber'";
$resultCheck = $conn->query($sqlCheck);
if ($resultCheck->num_rows > 0) {
$errors[] = "A record with the same Last Name and Contact Number already exists.";
}
// If no errors, insert into database and redirect to reservation page
if (empty($errors)) {
// Generate customer number (example: unique ID generation)
$customerNumber = uniqid('CUS');
// Insert data into Customer_Details table
$sqlInsert = "INSERT INTO Customer_Details (Customer_Number, First_Name, Last_Name, Date_of_Birth, Address1, Address2, City, Pin_Code, Contact_Number)
VALUES ('$customerNumber', '$firstName', '$lastName', STR_TO_DATE('$dob', '%d/%m/%Y'), '$address1', '$address2', '$city', '$pinCode', '$contactNumber')";
if ($conn->query($sqlInsert) === TRUE) {
// Redirect to reservation page with customer number
header("Location: reservation.php?customerNumber=$customerNumber");
exit();
} else {
echo "Error: " . $sqlInsert . "<br>" . $conn->error;
}
}
// Close database connection
$conn->close();
if (!empty($errors)) {
echo "<h3>Errors:</h3><ul>";
foreach ($errors as $error) {
echo "<li>$error</li>";
}
echo "</ul>";
}
}
?>