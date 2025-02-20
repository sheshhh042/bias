<?php
include('dbconn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Retrieve form data and trim extra spaces
  $name     = trim($_POST['name']);
  $email    = trim($_POST['email']);
  $username = trim($_POST['username']);
  $password = $_POST['password']; // Remember to hash the password

  // Validate email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format.";
    exit;
  }

  // Check if the email is a Gmail address (case-insensitive)
  if (!preg_match('/@gmail\.com$/i', $email)) {
    echo "Please register with a Gmail account.";
    exit;
  }

  // Optional: Check password strength
  function checkPasswordStrength($password) {
    $strength = 0;
    if (strlen($password) > 5) $strength++;
    if (strlen($password) > 7) $strength++;
    if (preg_match('/[A-Z]/', $password)) $strength++;
    if (preg_match('/[0-9]/', $password)) $strength++;
    if (preg_match('/[\W]/', $password)) $strength++;
    return $strength;
  }
  if (checkPasswordStrength($password) < 3) {
    echo "Password is not strong enough.";
    exit;
  }

  // Check if the email is already registered
  $sql_check = "SELECT * FROM users WHERE email=?";
  $stmt_check = $conn->prepare($sql_check);
  if ($stmt_check) {
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
      echo "This Email is already registered.";
      $stmt_check->close();
      exit;
    }
    $stmt_check->close();
  } else {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
  }

  // Generate a verification token
  $verification_token = md5($email . time());

  // Prepare the INSERT query (assumes your table has a column named verification_code)
  $sql_insert = "INSERT INTO users (name, email, username, password, verification_code) VALUES (?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql_insert);
  if ($stmt === false) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
  }

  // Hash the password before saving it to the database
  $password_hashed = password_hash($password, PASSWORD_DEFAULT);
  $stmt->bind_param("sssss", $name, $email, $username, $password_hashed, $verification_token);

  // Execute the query
  if ($stmt->execute()) {
    // Build the verification link
    $verification_link = "http://localhost/research/verify.php?email=" . urlencode($email) . "&token=" . $verification_token;

    // Prepare data for sendmail.php
    $post_data = [
      'email'             => $email,
      'name'              => $name,
      'verification_link' => $verification_link
    ];

    // Use cURL to send a POST request to sendmail.php
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/research/sendmail.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Show modal based on response
    if ($response === "Email sent successfully") {
      showModal("Success", "Registration successful! A verification email has been sent to your email address.");
    } else {
      showModal("Success", "Registration successful, but the verification email could not be sent. <br>Response: " . $response);
    }
    
    // Redirect the user to verify.php so that the verification code is processed and the account is activated.
    header("Location: " . $verification_link);
    exit;
  } else {
    echo "Error executing query: " . $stmt->error;
  }

  // Close the statement
  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Register</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="assets/img/llcc.png" rel="icon">
  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
                    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                    <p class="text-center small">Enter your personal details to create an account</p>
                  </div>

                  <form class="row g-3 needs-validation" method="POST" action="register.php" novalidate>
                    <div class="col-12">
                      <label for="yourName" class="form-label">Your Name</label>
                      <input type="text" name="name" class="form-control" id="yourName" required>
                      <div class="invalid-feedback">Please, enter your name!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourEmail" class="form-label">Your Email</label>
                      <input type="email" name="email" class="form-control" id="yourEmail" required>
                      <div class="invalid-feedback">Please enter a valid Email address!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="text" name="username" class="form-control" id="yourUsername" required>
                        <div class="invalid-feedback">Please choose a username.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required>
                      <!-- Password strength indicator -->
                      <div id="password-strength" class="mt-1"></div>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <!-- <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms" required>
                        <label class="form-check-label" for="acceptTerms">
                          I agree and accept the <a href="#">terms and conditions</a>
                        </label>
                        <div class="invalid-feedback">You must agree before submitting.</div>
                      </div>
                    </div> -->

                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Create Account</button>
                    </div>

                    <div class="col-12">
                      <p class="small mb-0">Already have an account? <a href="login.php">Log in</a></p>
                    </div>
                  </form>

                </div>
              </div><!-- End Card -->

            </div>
          </div>
        </div>
      </section>
    </div>
  </main><!-- End #main -->

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <script>
    // Function to check password strength (client-side)
    function checkPasswordStrength(password) {
      let strength = 0;
      if (password.length > 5) strength++;
      if (password.length > 7) strength++;
      if (/[A-Z]/.test(password)) strength++;
      if (/[0-9]/.test(password)) strength++;
      if (/[\W]/.test(password)) strength++;
      return strength;
    }

    // Real-time password strength indicator
    document.getElementById('yourPassword').addEventListener('input', function () {
      let password = this.value;
      let strength = checkPasswordStrength(password);
      let strengthText = "";
      let strengthColor = "";

      if (password.length === 0) {
        strengthText = "";
      } else if (strength <= 2) {
        strengthText = "Weak";
        strengthColor = "red";
      } else if (strength <= 4) {
        strengthText = "Moderate";
        strengthColor = "orange";
      } else {
        strengthText = "Strong";
        strengthColor = "green";
      }

      let strengthDiv = document.getElementById('password-strength');
      strengthDiv.innerText = "Password Strength: " + strengthText;
      strengthDiv.style.color = strengthColor;
    });
  </script>
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
