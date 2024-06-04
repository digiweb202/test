<?php

include 'connection.php'; // Include the file with the database connection

$query = "SELECT * FROM `ContentTable`";
$result = mysqli_query($con, $query);

$response = array(); // Initialize an empty array to store the data

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $imageURL = $row['image_url'];
        $contentText = $row['content_text'];

        // Create an associative array for each row
        $data = array(
            'id' => $id,
            'imageURL' => $imageURL,
            'contentText' => $contentText
        );

        // Append the data to the response array
        $response[] = $data;
    }

    // Convert the response array to JSON and echo it
    echo json_encode($response);
} else {
    // Echo an error message in JSON format
    echo json_encode(array('error' => 'Error executing query: ' . mysqli_error($con)));
}

mysqli_close($con); // Close the database connection
?>
