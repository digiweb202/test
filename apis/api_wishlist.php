<?php

// Include the connection file
include 'connection.php';

// Set headers to allow cross-origin resource sharing (CORS)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Get the product IDs and seller SKUs list
$productList = isset($_GET['productList']) ? $_GET['productList'] : '';

// Check if product list is provided
if (!empty($productList)) {
    // Convert product list string to array
    $productListArray = json_decode($productList, true);

    // Initialize an array to store the combined data
    $combinedData = [];

    // Loop through each product in the list
    foreach ($productListArray as $product) {
        $productId = $product['productId'];
        $sellerSKU = $product['sellerSKU'];

        // Query to fetch details from watches based on Product_ID and Seller_SKU
        $watchesQuery = "SELECT * FROM watches WHERE Product_ID = '$productId' AND Seller_SKU = '$sellerSKU'";
        $watchesResult = mysqli_query($con, $watchesQuery);

        // Check if the watches query was successful
        if ($watchesResult) {
            // Fetch data from watches
            $watch = mysqli_fetch_assoc($watchesResult);

            // Query to fetch details from watches_img based on Product_ID and Seller_SKU
            $watchesImgQuery = "SELECT * FROM watches_img WHERE Product_ID = '$productId' AND Seller_SKU = '$sellerSKU'";
            $watchesImgResult = mysqli_query($con, $watchesImgQuery);

            // Check if the watches_img query was successful
            if ($watchesImgResult) {
                // Fetch data from watches_img
                $watchesImgData = mysqli_fetch_all($watchesImgResult, MYSQLI_ASSOC);
                // Add watch data and image data to combined data array
                $combinedData[] = ['watch' => $watch, 'watches_img' => $watchesImgData];
            }
        }
    }

    // Return the combined data as JSON response
    echo json_encode(['success' => true, 'data' => $combinedData]);
} else {
    // Return an error response if product list is empty
    echo json_encode(['success' => false, 'message' => 'Product list is empty']);
}

// Close the database connection
mysqli_close($con);

?>
