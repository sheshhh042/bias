<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Check if required POST parameters are set
if (isset($_POST['email'], $_POST['name'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $name  = filter_var($_POST['name'], FILTER_SANITIZE_STRING);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address";
        exit();
    }

    // Generate a random 6-digit verification code
    $verificationCode = mt_rand(100000, 999999);
    // Store the code in session (or save to your database as needed)
    $_SESSION['verification_code'] = $verificationCode;

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->SMTPAuth   = true;
        $mail->Host       = 'smtp.gmail.com';
        $mail->Username   = 'joshuamangubat62@gmail.com'; // Your Gmail address
        $mail->Password   = 'hnrtkeyefdnqddud';            // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('joshuamangubat62@gmail.com', 'Bias System');
        $mail->addAddress($email, $name);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code';
        $mail->Body    = "<h3>Hello " . htmlspecialchars($name) . ",</h3>
                          <p>Your verification code is: <strong>" . $verificationCode . "</strong></p>
                          <p>Please use this code to verify your email address.</p>
                          <p>If you did not register, please ignore this email.</p>";

        // Send email
        $mail->send();
        echo "Email sent successfully";
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Invalid request.";
    exit();
}
?>
