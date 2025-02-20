<?php
session_start();

// If the user is already verified, redirect them (optional)
if (isset($_SESSION['verified']) && $_SESSION['verified'] === true) {
    header("Location: index.php");
    exit();
}

$error = '';

// Process verification when the user submits the form
if (isset($_POST['verify_code'])) {
    $enteredCode = trim($_POST['code']);

    if (isset($_SESSION['verification_code']) && $enteredCode == $_SESSION['verification_code']) {
        // Correct code: mark the user as verified
        $_SESSION['verified'] = true;
        unset($_SESSION['verification_code']);
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid verification code. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Email Verification</title>
    <link href="http://localhost/research/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/img/llcc.png" rel="icon">
    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
</head>

<body>
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="card p-4" style="width: 100%; max-width: 400px;">
            <!-- Header with logo and Verification Code text -->
            <h2 class="card-title text-center mb-4 d-flex align-items-center justify-content-center"
                style="background-color: #012970; color: white; padding: 10px; border-radius: 5px;">
                <img src="assets/img/llcc.png" alt="Logo" style="height: 40px; margin-right: 10px;">
                Verification Code
            </h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="code">Enter Verification Code</label>
                    <input type="text" name="code" id="code" class="form-control" placeholder="Enter code" required>
                </div>
                <div class="form-group mt-3">
                    <button type="submit" name="verify_code" class="btn btn-primary d-block mx-auto w-100">Submit
                        Code</button>
                </div>

            </form>
        </div>
    </div>
</body>

<script src="http://localhost/research/assets/js/main.js"></script>
<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/chart.js/chart.umd.js"></script>
<script src="assets/vendor/echarts/echarts.min.js"></script>
<script src="assets/vendor/quill/quill.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/vendor/tinymce/tinymce.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>

</html>