<?php
require 'connection.php';

// Check if all required parameters are provided
if (
    !isset($_GET['email']) || !isset($_GET['username']) || !isset($_GET['password']) ||
    !isset($_GET['fullname']) || !isset($_GET['number']) || !isset($_GET['address']) ||
    !isset($_GET['pincode']) || !isset($_GET['nickname'])
) {
    $response['status'] = 400;
    $response['message'] = "Error: All required parameters are needed for user update";
    echo json_encode($response);
    exit;
}

$email = $_GET['email'];
$username = $_GET['username'];
$password = password_hash($_GET['password'], PASSWORD_DEFAULT); // Hash the password
$fullname = $_GET['fullname'];
$number = $_GET['number'];
$address = $_GET['address'];
$pincode = $_GET['pincode'];
$nickname = $_GET['nickname'];

// Check if the user with the provided email exists
$checkUserQuery = "SELECT id FROM nw_customer_app WHERE email = ?";
$stmtCheckUser = mysqli_prepare($con, $checkUserQuery);
mysqli_stmt_bind_param($stmtCheckUser, "s", $email);
mysqli_stmt_execute($stmtCheckUser);
mysqli_stmt_store_result($stmtCheckUser);

if (mysqli_stmt_num_rows($stmtCheckUser) > 0) {
    // User with the provided email exists, perform the update
    $updateUserQuery = "UPDATE nw_customer_app SET username=?, password=?, fullname=?, number=?, address=?, pincode=?, nickname=? WHERE email=?";
    $stmtUpdateUser = mysqli_prepare($con, $updateUserQuery);

    // Check if prepare was successful
    if (!$stmtUpdateUser) {
        die('Error in preparing statement: ' . mysqli_error($con));
    }

    // Bind parameters
    mysqli_stmt_bind_param($stmtUpdateUser, "ssssssss", $username, $password, $fullname, $number, $address, $pincode, $nickname, $email);

    // Check if execute was successful
    if (mysqli_stmt_execute($stmtUpdateUser)) {
        // User information updated successfully
        $response['status'] = "200";
        $response['message'] = "User information updated successfully";
    } else {
        // Error occurred during the update
        $response['status'] = "500";
        $response['message'] = "Error updating user information: " . mysqli_error($con);
    }

    // Close the update statement
    mysqli_stmt_close($stmtUpdateUser);
} else {
    // User with the provided email does not exist
    $response['status'] = "400";
    $response['message'] = "User does not exist";
}

// Close the check user statement
mysqli_stmt_close($stmtCheckUser);
mysqli_close($con);

echo json_encode($response);
?>
