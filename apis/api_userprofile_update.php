<?php
require 'connection.php';

// Check if required fields are provided
if (!isset($_GET['email'])) {
    $response['status'] = 400; // Bad Request
    $response['message'] = "Error: Required parameters are missing";
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Get data from GET parameters
$username = mysqli_real_escape_string($con, $_GET['username']);
$email = mysqli_real_escape_string($con, $_GET['email']);
$fullname = mysqli_real_escape_string($con, $_GET['fullname']);
$nickname = mysqli_real_escape_string($con, $_GET['nickname']);
$number = mysqli_real_escape_string($con, $_GET['number']);
$address = mysqli_real_escape_string($con, $_GET['address']);
$pincode = mysqli_real_escape_string($con, $_GET['pincode']);
$password = isset($_GET['password']) ? mysqli_real_escape_string($con, $_GET['password']) : null;

// Validate email format
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 400; // Bad Request
    $response['message'] = "Error: Invalid email format";
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Check if user exists
$userCheckQuery = "SELECT * FROM `nw_customer_app` WHERE `email` = ?";
$stmt = mysqli_prepare($con, $userCheckQuery);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    // Update user profile
    $updateQuery = "UPDATE `nw_customer_app` SET `email`=?, `fullname`=?, `nickname`=?, `number`=?, `address`=?, `pincode`=?"
        . ($password ? ", `password`=?": "") . " WHERE `email`=?";
    $updateStmt = mysqli_prepare($con, $updateQuery);

    if ($password) {
        $hashedPassword = $password;
        mysqli_stmt_bind_param($updateStmt, "ssssssss", $email, $fullname, $nickname, $number, $address, $pincode, $hashedPassword, $email);
    } else {
        mysqli_stmt_bind_param($updateStmt, "sssssss", $email, $fullname, $nickname, $number, $address, $pincode, $email);
    }

    if (mysqli_stmt_execute($updateStmt)) {
        // Profile updated successfully
        $response['status'] = 200;
        $response['message'] = "Profile updated successfully";
    } else {
        // Error occurred
        $response['status'] = 500; // Internal Server Error
        $response['message'] = "Error updating profile: " . mysqli_error($con);
    }

    // Close the update statement
    mysqli_stmt_close($updateStmt);
} else {
    // User not found
    $response['status'] = 404; // Not Found
    $response['message'] = "Error: User not found";
}

// Close the user check statement
mysqli_stmt_close($stmt);

// Encode response array to JSON and send to client
header('Content-Type: application/json');
echo json_encode($response);

// Close connection
mysqli_close($con);
?>
