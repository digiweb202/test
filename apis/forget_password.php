<?php
require 'connection.php';

// Function to generate OTP
function generateOTP() {
    return rand(100000, 999999); // Generate a 6-digit OTP
}

// Get email from POST parameters
$email = isset($_POST['email']) ? mysqli_real_escape_string($con, $_POST['email']) : '';

// Check if email exists
$emailCheckQuery = "SELECT * FROM `nw_customer` WHERE `email` = '$email'";
$emailCheckResult = mysqli_query($con, $emailCheckQuery);

if (mysqli_num_rows($emailCheckResult) > 0) {
    // Email exists, generate OTP
    $otp = generateOTP();

    // Send email with OTP
    $to = $email;
    $subject = 'Forgot Password OTP';
    $message = 'Your OTP for resetting the password is: ' . $otp;
    $headers = 'From: your_email@example.com' . "\r\n" .
               'Reply-To: your_email@example.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

    if (mail($to, $subject, $message, $headers)) {
        // Email sent successfully
        $response['status'] = 200;
        $response['message'] = "OTP sent successfully to $email";
    } else {
        // Error occurred while sending email
        $response['status'] = 500; // Internal Server Error
        $response['message'] = "Error: Failed to send OTP email";
    }
} else {
    // Email does not exist
    $response['status'] = 400; // Bad Request
    $response['message'] = "Error: Email address not found";
}

// Encode response array to JSON and send to client
header('Content-Type: application/json');
echo json_encode($response);

// Close connection
mysqli_close($con);
?>
