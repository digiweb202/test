<?php

// Include the database connection configuration
include 'connection.php';

// Get parameters from the request (you can modify this based on your API input method)
$productID = isset($_GET['product_id']) ? $_GET['product_id'] : null;
$sellerSKU = isset($_GET['seller_sku']) ? $_GET['seller_sku'] : null;

// Validate input
if (empty($productID) || empty($sellerSKU)) {
    http_response_code(400); // Set HTTP response code to 400 Bad Request
    echo json_encode(["error" => "Product ID and Seller SKU are required."]);
    exit;
}

// Prepare and execute SQL query with JOIN
$sql = "SELECT watches.*, watches_img.* FROM `watches`
        LEFT JOIN `watches_img` ON watches.Product_ID = watches_img.Product_ID
        WHERE watches.`Product_ID` = ? AND watches.`Seller_SKU` = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("ss", $productID, $sellerSKU);
$stmt->execute();

// Get result set
$result = $stmt->get_result();

// Check if any rows are returned
if ($result->num_rows > 0) {
    // Fetch data and convert to JSON
    $data = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($data);
} else {
    // No matching records found
    echo json_encode(["message" => "No records found for the given Product ID and Seller SKU."]);
}

// Close statement
$stmt->close();

// The connection will be closed when the script finishes execution

?>
