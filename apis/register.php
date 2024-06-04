<?php

require 'connection.php';

// Get data based on the request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = $_GET;
} else {
    // Handle unsupported request methods
    $response['status'] = "400";
    $response['message'] = "Unsupported request method.";
    echo json_encode($response);
    exit();
}

// Check if the required keys are set
if (isset($data['username'], $data['email'], $data['password'])) {
    $username = $data['username'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_BCRYPT);

    $response = [];

    // Validate input (you can add more validation as needed)
    if (empty($username) || empty($email) || empty($data['password'])) {
        $response['status'] = "400";
        $response['message'] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['status'] = "400";
        $response['message'] = "Invalid email format.";
    } else {
        // Check if the user with the provided email exists
        $checkUserQuery = "SELECT * FROM nw_customer WHERE LOWER(email) = LOWER(?)";
        $stmtCheckUser = mysqli_prepare($con, $checkUserQuery);
        mysqli_stmt_bind_param($stmtCheckUser, "s", $email);
        mysqli_stmt_execute($stmtCheckUser);
        mysqli_stmt_store_result($stmtCheckUser);

        if (mysqli_stmt_num_rows($stmtCheckUser) > 0) {
            $response['status'] = "403";
            $response['message'] = "User with this email already exists.";
        } else {
            // Insert new user into the database
            $insertQuery = "INSERT INTO nw_customer (
                username, 
                first_name, 
                last_name, 
                email, 
                address, 
                post_code, 
                shipping_address, 
                additional_information, 
                password, 
                customer_number, 
                user_gst_no
            ) VALUES (?, '', '', ?, '', '', '', '', ?, '', '')";
            $stmtInsertUser = mysqli_prepare($con, $insertQuery);
            mysqli_stmt_bind_param($stmtInsertUser, "sss", 
                $username, 
                $email, 
                $password
            );

            $result = mysqli_stmt_execute($stmtInsertUser);

            if ($result) {
                $response['status'] = "200";
                $response['message'] = "Registration successful!";
            } else {
                $response['status'] = "400";
                $response['message'] = "Registration failed.";
            }

            mysqli_stmt_close($stmtInsertUser);
        }
    }

    mysqli_stmt_close($stmtCheckUser);
    mysqli_close($con);

    echo json_encode($response);
} else {
    // Handle missing keys.............
    $response['status'] = "400";
    $response['message'] = "Missing required data.";
    echo json_encode($response);
}

?>
