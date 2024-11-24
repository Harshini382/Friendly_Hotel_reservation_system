<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Hoteldb";

// Initialize variables
$reservation_id = null;
$errors = [];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize inputs
function sanitize_input($data, $conn) {
    return htmlspecialchars(stripslashes(trim($conn->real_escape_string($data))));
}

// Function to calculate rate based on room type
function calculate_rate($room_type_id) {
    switch ($room_type_id) {
        case 1: // Single
            return 2500.00;
        case 2: // Deluxe
            return 3500.00;
        case 3: // Bridal Suite
            return 5000.00;
        default:
            return 0.00; // Default if room type not found
    }
}

// Validate and sanitize inputs
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = sanitize_input($_POST['customer_name'], $conn);
    $room_type_id = intval($_POST['room_type_id']); // Ensure integer value
    $booking_type = sanitize_input($_POST['booking_type'], $conn);
    $from_date = sanitize_input($_POST['from_date'], $conn);
    $to_date = sanitize_input($_POST['to_date'], $conn);
    $number_of_people = intval($_POST['number_of_people']); // Ensure integer value

    // Basic validation
    if (empty($customer_name) || empty($from_date) || empty($to_date)) {
        $errors[] = "Customer Name, From Date, and To Date are required fields.";
    } elseif ($from_date >= $to_date) {
        $errors[] = "To Date must be after From Date.";
    } else {
        // Calculate rate based on room type
        $rate = calculate_rate($room_type_id);

        // Check if rate is retrieved successfully
        if ($rate == 0.00) {
            $errors[] = "Invalid room type.";
        } else {
            // Insert or update Customer_Details
            $stmt = $conn->prepare("INSERT INTO Customer_Details (First_Name, Last_Name) VALUES (?, ?) ON DUPLICATE KEY UPDATE First_Name = VALUES(First_Name), Last_Name = VALUES(Last_Name)");
            $names = explode(' ', $customer_name);
            $stmt->bind_param("ss", $names[0], $names[1]);
            $stmt->execute();
            $stmt->close();

            // Retrieve or insert Customer_Number
            $stmt = $conn->prepare("SELECT Customer_Number FROM Customer_Details WHERE First_Name = ? AND Last_Name = ?");
            $stmt->bind_param("ss", $names[0], $names[1]);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $customer_number = $row['Customer_Number'];

                // Prepare and bind parameters for Reservation table
                $stmt = $conn->prepare("INSERT INTO Reservation (Customer_Number, Room_Type_ID, Booking_Type, From_Date, To_Date, Number_of_People, Rate) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iissssd", $customer_number, $room_type_id, $booking_type, $from_date, $to_date, $number_of_people, $rate);

                // Execute the statement
                if ($stmt->execute()) {
                    // Retrieve inserted reservation ID
                    $reservation_id = $stmt->insert_id;
                    $stmt->close();

                    // Redirect to confirmation page
                    header("Location: confirmation.php?id=" . $reservation_id);
                    exit();
                } else {
                    $errors[] = "Error: " . $stmt->error;
                }
            } else {
                $errors[] = "Failed to retrieve Customer_Number.";
            }
        }
    }
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation Form</title>
    <style>
        body {
            background: url('resv.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white */
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37); /* Box shadow for depth */
            backdrop-filter: blur(10px); /* Blur effect */
            -webkit-backdrop-filter: blur(10px); /* Blur effect for Safari */
            padding: 20px;
            max-width: 500px;
            width: 90%;
            text-align: center; /* Center align content */
            color: black; /* Text color */
            margin-top: 20px; /* Adjust margin top as needed */
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        form {
            display: block; /* Always show the form */
            margin-top: 20px; /* Space between title and form */
        }
        label {
            display: block;
            margin-top: 10px;
            color: #333;
        }
        input[type="text"],
        input[type="date"],
        select,
        input[type="number"] {
            width: calc(100% - 20px);
            padding: 8px;
            font-size: 16px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-family: inherit;
        }
        select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="rgba(0, 0, 0, 0.54)" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position-x: calc(100% - 10px);
            background-position-y: 50%;
            padding-right: 25px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error-message {
            color: red;
            margin-top: 10px;
            text-align: left; /* Align error messages to the left */
        }
        button {
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            margin-right: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reservation Form</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" value="<?php echo isset($customer_name) ? htmlspecialchars($customer_name) : ''; ?>" required>
            
            <label for="room_type_id">Room Type:</label>
            <select id="room_type_id" name="room_type_id" required>
                <option value="1">Single - $2500.00</option>
                <option value="2">Deluxe - $3500.00</option>
                <option value="3">Bridal Suite - $5000.00</option>
            </select>
            
            <label for="booking_type">Booking Type:</label>
            <select id="booking_type" name="booking_type" required>
                <option value="Confirmed">Confirmed</option>
                <option value="Provisional">Provisional</option>
            </select>
            
            <label for="from_date">From Date:</label>
            <input type="date" id="from_date            > name="from_date" name="from_date" value="<?php echo isset($from_date) ? htmlspecialchars($from_date) : ''; ?>" required>
            
            <label for="to_date">To Date:</label>
            <input type="date" id="to_date" name="to_date" value="<?php echo isset($to_date) ? htmlspecialchars($to_date) : ''; ?>" required>
            
            <label for="number_of_people">Number of People:</label>
            <input type="number" id="number_of_people" name="number_of_people" min="1" max="4" value="<?php echo isset($number_of_people) ? htmlspecialchars($number_of_people) : ''; ?>" required>
            
            <input type="submit" value="Confirm Reservation">
        </form>

        <button onclick="window.location.href='index.html'">Homepage</button>
    </div>
</body>
</html>

