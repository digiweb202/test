<?php
// Include the database connection file (replace 'connection.php' with your actual file name)
require 'connection.php';

// Check if required parameters are provided in either $_GET or $_POST
if (
    !isset($_REQUEST['userid']) ||
    !isset($_REQUEST['email']) ||
    !isset($_REQUEST['product_id']) ||
    !isset($_REQUEST['select_sku']) ||
    !isset($_REQUEST['quantity']) ||
    !isset($_REQUEST['total_amount'])
) {
    $response['status'] = 400; // Bad Request
    $response['message'] = "Error: Required parameters are missing";
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Get data from either $_GET or $_POST parameters
$userid = mysqli_real_escape_string($con, $_REQUEST['userid']);
$email = mysqli_real_escape_string($con, $_REQUEST['email']);
$product_id = mysqli_real_escape_string($con, $_REQUEST['product_id']);
$select_sku = mysqli_real_escape_string($con, $_REQUEST['select_sku']);
$quantity = mysqli_real_escape_string($con, $_REQUEST['quantity']);
$total_amount = mysqli_real_escape_string($con, $_REQUEST['total_amount']);
$order_date = date("Y-m-d H:i:s"); // Assuming you want to use the current timestamp

// Insert data into the order_app table
$insertQuery = "INSERT INTO `order_app` (userid, email, product_id, select_sku, quantity, total_amount, order_date) VALUES (?, ?, ?, ?, ?, ?, ?)";
$insertStmt = mysqli_prepare($con, $insertQuery);

mysqli_stmt_bind_param($insertStmt, "issisds", $userid, $email, $product_id, $select_sku, $quantity, $total_amount, $order_date);

$response = array();

if (mysqli_stmt_execute($insertStmt)) {
    // Order inserted successfully
    $response['status'] = 200;
    $response['message'] = "Order inserted successfully";
} else {
    // Error occurred
    $response['status'] = 500; // Internal Server Error
    $response['message'] = "Error inserting order: " . mysqli_error($con);
}

// Close the insert statement
mysqli_stmt_close($insertStmt);

// Encode response array to JSON and send to client
header('Content-Type: application/json');
echo json_encode($response);

// Close connection
mysqli_close($con);
?>
