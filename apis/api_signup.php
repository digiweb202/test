<?php
require 'connection.php';

// Check if all required fields are provided
if (!isset($_GET['username']) || !isset($_GET['email']) || !isset($_GET['password'])) {
    $response['status'] = 400; // Bad Request
    $response['message'] = "Error: Required parameters are missing";
    // Encode response array to JSON and send to client
    header('Content-Type: application/json');
    echo json_encode($response);
    exit; // Stop further execution
}

// Get data from GET parameters
$username = mysqli_real_escape_string($con, $_GET['username']);
$email = mysqli_real_escape_string($con, $_GET['email']);
$password = mysqli_real_escape_string($con, $_GET['password']);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 400; // Bad Request
    $response['message'] = "Error: Invalid email format";
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Check if email already exists
$emailCheckQuery = "SELECT * FROM `nw_customer_app` WHERE `email` = ?";
$stmt = mysqli_prepare($con, $emailCheckQuery);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    // Email already exists
    $response['status'] = 400; // Bad Request
    $response['message'] = "Error: Email already exists";
    mysqli_stmt_close($stmt);
} else {
    // Hash the password before storing
    $hashedPassword = $password;

    // Create SQL query with prepared statement
    $sql = "INSERT INTO `nw_customer_app` (`username`, `email`, `password`) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPassword);

    // Execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        // Record added successfully
        $userId = mysqli_insert_id($con);
        $user = array(
            "id" => $userId,
            "username" => $username,
            "email" => $email
        );
        $response['status'] = 200;
        $response['message'] = "Record added successfully";
        $response['user'] = $user;
    } else {
        // Error occurred
        $response['status'] = 500; // Internal Server Error
        $response['message'] = "Error: " . mysqli_error($con);
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
}

// Encode response array to JSON and send to client
header('Content-Type: application/json');
echo json_encode($response);

// Close connection
mysqli_close($con);
?>
