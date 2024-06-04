<?php

// API credentials
$apiUrl = "https://api.example.com/login";
$apiUsername = "your_api_username";
$apiPassword = "your_api_password";

// User credentials to verify
$userInputUsername = "user_input_username";
$userInputPassword = "user_input_password";

// Prepare data for the API request
$data = [
    'username' => $userInputUsername,
    'password' => $userInputPassword,
];

// Initialize cURL session
$ch = curl_init($apiUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_USERPWD, "$apiUsername:$apiPassword");

// Execute cURL session
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    die('Curl error: ' . curl_error($ch));
}

// Close cURL session
curl_close($ch);

// Process API response
$responseData = json_decode($response, true);

if ($responseData && isset($responseData['success']) && $responseData['success']) {
    echo "Login successful";
    // You may perform additional actions here, like storing user information or setting session variables.
} else {
    echo "Login failed";
}

?>
