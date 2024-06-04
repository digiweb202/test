<?php

// Include the connection file
include 'connection.php';

// Set headers to allow cross-origin resource sharing (CORS)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Check if the search query parameter is provided
$searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 10; // Set the number of items per page

// Calculate the offset for pagination
$offset = ($page - 1) * $itemsPerPage;

// Query to fetch details from watches based on the search query and pagination
$watchesQuery = "SELECT * FROM watches WHERE Item_Name LIKE '%$searchQuery%' OR Product_ID = '$searchQuery' LIMIT $offset, $itemsPerPage";
$watchesResult = mysqli_query($con, $watchesQuery);

// Check if the watches query was successful
if ($watchesResult) {
    // Fetch data from watches and convert to JSON
    $watchesData = mysqli_fetch_all($watchesResult, MYSQLI_ASSOC);

    // Check if any data is found in watches table
    if ($watchesData) {
        // Initialize an array to store the combined data
        $combinedData = [];

        // Loop through each row in watches data
        foreach ($watchesData as $watch) {
            // Fetch corresponding data from watches_img based on Product_ID and Seller_SKU
            $productId = $watch['Product_ID'];
            $sellerSKU = $watch['Seller_SKU'];

            $watchesImgQuery = "SELECT * FROM watches_img WHERE Product_ID = '$productId' AND Seller_SKU = '$sellerSKU'";
            $watchesImgResult = mysqli_query($con, $watchesImgQuery);

            // Check if the watches_img query was successful
            if ($watchesImgResult) {
                // Fetch data from watches_img and add it to the combined data
                $watchesImgData = mysqli_fetch_all($watchesImgResult, MYSQLI_ASSOC);
                $combinedData[] = ['watches' => $watch, 'watches_img' => $watchesImgData];
            }
        }

        // Return the combined data as JSON response
        echo json_encode(['success' => true, 'data' => $combinedData]);
    } else {
        // Return an error response if no data is found in watches table
        echo json_encode(['success' => false, 'message' => 'No data found matching the search criteria']);
    }
} else {
    // Return an error response if the watches query fails
    echo json_encode(['success' => false, 'message' => 'Error fetching data from the watches table']);
}

// Close the database connection
mysqli_close($con);

?>
