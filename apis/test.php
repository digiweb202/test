<?php

include 'connection.php'; // Include the database connection file

// Check if 'count' parameter is provided
if (!isset($_GET['count'])) {
    $response['status'] = 400;
    $response['message'] = "Number of data page is missing";
    echo json_encode($response);
    exit;
}

$countdata = $_GET['count'];

// Validate 'count' as a positive integer
$countdata = filter_var($countdata, FILTER_VALIDATE_INT);

if ($countdata === false || $countdata <= 0) {
    $response['status'] = 400;
    $response['message'] = "Invalid count parameter";
    echo json_encode($response);
    exit;
}

// Constants for pagination
$limit = 10; // Number of records to fetch per request
$page = isset($_GET['page']) ? intval($_GET['page']) : $countdata; // Current page

// Ensure that $page is a positive integer
$page = max(1, $page);

// Calculate the offset based on the current page and limit
$offset = ($page - 1) * $limit;

// Define the query with LEFT JOIN to combine data from watches and watches_img
$query = "SELECT watches.Seller_SKU, watches.Product_ID, watches.Item_Name, 
                 watches.Product_Description, watches.Your_Price, watches_img.Main_Image_URL
          FROM watches
          LEFT JOIN watches_img ON watches.Product_ID = watches_img.Product_ID 
                              AND watches.Seller_SKU = watches_img.Seller_SKU
          LIMIT $limit OFFSET $offset";

// Execute the query
$result = mysqli_query($con, $query);

// Check if the query was successful
if ($result) {
    // Fetch the data and convert it into an associative array
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Output the data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    // If the query fails, output an error message
    $response['status'] = 500;
    $response['message'] = "Internal Server Error: " . mysqli_error($con);
    echo json_encode($response);
}

// Close the connection (optional since PHP automatically closes it at the end of the script)
// mysqli_close($con);

?>
