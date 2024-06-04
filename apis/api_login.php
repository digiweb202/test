<?php
require 'connection.php';

// Check if username and password are provided
if (!isset($_GET['username']) || !isset($_GET['password'])) {
    $response['status'] = 400;
    $response['message'] = "Error: Username and password are required";
    echo json_encode($response);
    exit;
}

$username = $_GET['username'];
$password = $_GET['password'];

// Check if the user with the provided username exists
$checkUserQuery = "SELECT id, username, email, password FROM nw_customer_app WHERE username = ?";
$stmtCheckUser = mysqli_prepare($con, $checkUserQuery);
mysqli_stmt_bind_param($stmtCheckUser, "s", $username);
mysqli_stmt_execute($stmtCheckUser);
mysqli_stmt_store_result($stmtCheckUser);

if (mysqli_stmt_num_rows($stmtCheckUser) > 0) {
    // User with the provided username exists, fetch user data
    mysqli_stmt_bind_result($stmtCheckUser, $id, $username, $email, $storedPassword);
    mysqli_stmt_fetch($stmtCheckUser);

    // Verify the provided password against the stored hashed password using password_verify
    if (password_verify($password, $storedPassword)) {
        // Passwords match, login successful
        $response['user'] = [
            'id' => $id,
            'username' => $username,
            'email' => $email,
        ];
        $response['status'] = "200";
        $response['message'] = "Login success";
    } else {
        // Incorrect password
        $response['user'] = (object)[];
        $response['status'] = "400";
        $response['message'] = "Wrong credentials";
    }
} else {
    // User with the provided username does not exist
    $response['user'] = (object)[];
    $response['status'] = "400";
    $response['message'] = "User does not exist";
}

mysqli_stmt_close($stmtCheckUser);
mysqli_close($con);

echo json_encode($response);
?>
