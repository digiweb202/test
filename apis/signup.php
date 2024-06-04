<?php
require 'connection.php';

// Check if all required fields are provided
if (!isset($_GET['username']) || !isset($_GET['first_name']) || !isset($_GET['email']) || !isset($_GET['password'])) {
    $response['status'] = 400; // Bad Request
    $response['message'] = "Error: Required parameters are missing";
    // Encode response array to JSON and send to client
    header('Content-Type: application/json');
    echo json_encode($response);
    exit; // Stop further execution
}

// Get data from GET parameters
$username = mysqli_real_escape_string($con, $_GET['username']);
$firstname = mysqli_real_escape_string($con, $_GET['first_name']);
$email = mysqli_real_escape_string($con, $_GET['email']);
$password = mysqli_real_escape_string($con, $_GET['password']);

// Check if email already exists
$emailCheckQuery = "SELECT * FROM `nw_customer` WHERE `email` = '$email'";
$emailCheckResult = mysqli_query($con, $emailCheckQuery);

if (mysqli_num_rows($emailCheckResult) > 0) {
    // Email already exists
    $response['status'] = 400; // Bad Request
    $response['message'] = "Error: Email already exists";
} else {
    // Create SQL query
    $sql = "INSERT INTO `nw_customer` (`username`, `first_name`, `email`, `password`) 
            VALUES ('$username', '$firstname', '$email', '$password')";

    // Execute the query
    if (mysqli_query($con, $sql)) {
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
}

// Encode response array to JSON and send to client
header('Content-Type: application/json');
echo json_encode($response);

// Close connection
mysqli_close($con);
?>
