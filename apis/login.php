    <?php

    require 'connection.php';

    // Check if email, username, and password are provided
    if (!isset($_GET['email']) || !isset($_GET['password'])) {
        $response['status'] = 400;
        $response['message'] = "Error: Email and password are required";
        echo json_encode($response);
        exit;
    }

    $email = $_GET['email'];
    $password = $_GET['password'];

    // Check if the user with the provided email exists
    $checkUserQuery = "SELECT id, username, email, password FROM nw_customer WHERE email = ?";
    $stmtCheckUser = mysqli_prepare($con, $checkUserQuery);
    mysqli_stmt_bind_param($stmtCheckUser, "s", $email);
    mysqli_stmt_execute($stmtCheckUser);
    mysqli_stmt_store_result($stmtCheckUser);

    if (mysqli_stmt_num_rows($stmtCheckUser) > 0) {
        // User with the provided email exists, fetch user data
        mysqli_stmt_bind_result($stmtCheckUser, $id, $username, $email, $storedPassword);
        mysqli_stmt_fetch($stmtCheckUser);

        // Verify the provided password against the stored password
        if ($password === $storedPassword) {
            $response['user'] = [
                'id' => $id,
                'username' => $username,
                'email' => $email,
            ];
            $response['status'] = "200";
            $response['message'] = "Login success";
        } else {
            // Incorrect password
            $response['user'] = (object)[];
            $response['status'] = "400";
            $response['message'] = "Wrong credentials";
        }
    } else {
        // User with the provided email does not exist
        $response['user'] = (object)[];
        $response['status'] = "400";
        $response['message'] = "User does not exist";
    }

    mysqli_stmt_close($stmtCheckUser);
    mysqli_close($con);

    echo json_encode($response);

    ?>
