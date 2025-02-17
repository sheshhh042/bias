<?php
session_start();
require_once 'dbconn.php';  // Include your database connection

$error_message = "";

// Check if the user is already logged in as admin
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
  header("Location: admin.php");  // Redirect to admin dashboard if already logged in
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get the submitted username and password from the POST request
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  // Basic validation: Ensure both fields are provided
  if (empty($username) || empty($password)) {
    $error_message = "Please enter both username and password.";
  } else {
    // SQL query to fetch the user from the database
    $sql = "SELECT * FROM users WHERE LOWER(username) = LOWER(?) LIMIT 1";
    if ($stmt = $conn->prepare($sql)) {
      $stmt->bind_param("s", $username);  // "s" indicates the variable is a string
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        // User found, check the password
        $row = $result->fetch_assoc();

        // Debugging: Output the stored and entered passwords
        // echo "Stored hashed password: " . $row['password'];  // Check the stored password
        // echo "Input password: " . $password;  // Check the input password

        // Verify the password using PHP's password_verify function
        if (password_verify($password, $row['password'])) {
          // Correct password, store user data in session
          $_SESSION['user_id'] = $row['id'];
          $_SESSION['username'] = $row['username'];

          // Check if the user is an admin
          if ($row['is_admin'] === 1) {
            $_SESSION['is_admin'] = true;  // Mark the session as admin
            header("Location: admin.php");  // Redirect to admin page
            exit();
          } else {
            $error_message = "You do not have admin privileges.";
          }
        } else {
          $error_message = "Invalid password.";
        }
      } else {
        $error_message = "No user found with that username.";
      }

      $stmt->close();  // Close the prepared statement
    } else {
      $error_message = "Database query failed. Please try again later.";
    }
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
</head>

<body>

  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/llcc.png" alt="">
                  <span class="d-none d-lg-block" style="color: #012970;">Bias System</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your username & password to login</p>
                  </div>

                  <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger">
                      <?php echo $error_message; ?>
                    </div>
                  <?php endif; ?>

                  <!-- Login Form -->
                  <form method="POST" action="loginadmin.php" class="row g-3 needs-validation" novalidate>
                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="text" name="username" class="form-control" id="yourUsername" required>
                        <div class="invalid-feedback">Please enter your username.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
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
                    <p class="small mb-0"><a href="login.php">Login as a Student</a></p>
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
</body>

</html>
