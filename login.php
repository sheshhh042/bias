<?php
session_start();  // Start the session
require_once 'dbconn.php';  // Include your database connection

// Initialize error message
$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and trim the submitted email and password
    $user = trim($_POST['username']);  // This should be email for login
    $pass = trim($_POST['password']);

    // Basic validation: Ensure both fields are provided
    if (empty($user) || empty($pass)) {
        $error_message = "Please enter both username and password.";
    } else {
        // SQL query to fetch the user by email
        $sql = "SELECT * FROM users WHERE email=?";
        if (!$stmt = $conn->prepare($sql)) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        
        // Bind the email parameter to prevent SQL injection
        $stmt->bind_param("s", $user);  // "s" indicates the variable is a string
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a user was found
        if ($result->num_rows > 0) {
            // User found, check the password
            $row = $result->fetch_assoc();

            // Verify the password (ensure that the password was hashed on registration)
            if (password_verify($pass, $row['password'])) {
                // Correct password: store user data in session
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];  // assuming you still want to store username

                // Redirect to the dashboard or a welcome page
                header("Location: index.php");
                exit();  // Ensure no further code is executed after redirect
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "No user found with that email.";
        }
        $stmt->close();  // Close the prepared statement
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Login</title>
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
  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="d-flex justify-content-center py-4">
                <a href="login.php" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/llcc.png" alt="">
                  <span class="d-none d-lg-block" style="color: #012970;">Bias System</span>
                </a>
              </div>

              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your username & password to login</p>
                  </div>

                  <!-- Show error message if login fails -->
                  <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger">
                      <?php echo $error_message; ?>
                    </div>
                  <?php endif; ?>

                  <!-- Login Form -->
                  <form method="POST" action="login.php" class="row g-3 needs-validation" novalidate>
                    <div class="col-12">
                      <label for="username" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="email" name="username" class="form-control" id="yourUsername" required>
                        <div class="invalid-feedback">Please enter your email.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="password" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Login</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0"><a href="loginadmin.php">Login as Administration</a></p>
                      <p class="small mb-0">Don't have an account? <a href="register.php">Create an account</a></p>
                    </div>
                  </form>
                </div>
              </div><!-- End Card -->
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <!-- Template Main JS File -->
  <script src="http://localhost/research/assets/js/main.js"></script>
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
</body>

</html>