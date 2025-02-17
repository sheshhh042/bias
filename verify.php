<?php
include('dbconn.php');

if (isset($_GET['code'])) {
    $verification_code = $_GET['code'];

    // Prepare update query to mark the email as verified
    $sql = "UPDATE users SET is_verified=1 WHERE verification_code=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $verification_code);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo "Your email has been verified successfully. You can now <a href='login.php'>log in</a>.";
        } else {
            echo "Invalid or expired verification code.";
        }
        $stmt->close();
    } else {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    }
} else {
    echo "No verification code provided.";
}
?>
