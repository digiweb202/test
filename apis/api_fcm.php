<?php

include 'connection.php';

// Check if the fcm_id parameter is present in the URL
if(isset($_GET['fcm_id'])) {
    // Sanitize the input to prevent SQL injection
    $fcm_id = mysqli_real_escape_string($con, $_GET['fcm_id']);

    // Check if the FCM ID is empty
    if(!empty($fcm_id)) {
        // Check if the FCM ID already exists in the database
        $check_query = "SELECT * FROM fcm WHERE fcm_id = '$fcm_id'";
        $check_result = mysqli_query($con, $check_query);

        if(mysqli_num_rows($check_result) > 0) {
            // FCM ID already exists, do not insert again
            echo "FCM ID already exists";
        } else {
            // FCM ID does not exist, insert it
            $insert_query = "INSERT INTO fcm (fcm_id) VALUES ('$fcm_id')";
            if(mysqli_query($con, $insert_query)) {
                echo "FCM ID inserted successfully";
            } else {
                echo "Error: " . $insert_query . "<br>" . mysqli_error($con);
            }
        }
    } else {
        echo "FCM ID is empty";
    }
} else {
    echo "FCM ID not provided";
}

?>
