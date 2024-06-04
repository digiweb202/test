<?php

// Include the database connection file
include 'connection.php';

// Query to fetch data from the table
$query = "SELECT * FROM `all_browse_categories`";

// Perform the query
$result = mysqli_query($con, $query);

// Check if the query was successful
if ($result) {
    // Fetch data as an associative array
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Encode the data as JSON and output it
    echo json_encode($data);
} else {
    // If the query fails, output an error message
    echo "Error fetching data: " . mysqli_error($con);
}

// Close the database connection
mysqli_close($con);

?>



