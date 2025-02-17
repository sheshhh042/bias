<?php
// Start the session to access session data
session_start();

// Include the database connection
include('dbconn.php');

// Initialize variables to prevent undefined variable warnings
$name = '';
$email = '';
$username = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get form data using null coalescing operator to handle undefined values
  $name = $_POST['name'] ?? '';
  $email = $_POST['email'] ?? '';
  $username = $_POST['username'] ?? '';

  // Ensure the user is logged in (check session)
  if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit();
  }

  // Get the user ID from session (it should be set after login)
  $user_id = $_SESSION['user_id'];

  // Prepare the update query
  $sql = "UPDATE users SET name = ?, email = ?, username = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);

  // Check if the statement was prepared successfully
  if ($stmt === false) {
    echo "Error preparing the statement: " . $conn->error;
  } else {
    // Bind parameters (string, string, string, integer) for the update query
    $stmt->bind_param("sssi", $name, $email, $username, $user_id);

    // Execute the query
    if ($stmt->execute()) {
      echo "Profile updated successfully!";
    } else {
      echo "Error updating profile: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
  }
}

// Fetch the user information to pre-populate the form
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];  // Use the session ID to fetch user data

  // Fetch name and email from the database
  $sql = "SELECT name, email, username FROM users WHERE id = ?";
  $stmt = $conn->prepare($sql);

  // Check if the statement was prepared successfully
  if ($stmt === false) {
    echo "Error preparing the statement: " . $conn->error;
  } else {
    // Bind user ID parameter for the SELECT query
    $stmt->bind_param("i", $user_id);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists in the database
    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();  // Fetch user data into an associative array
      // If user exists, pre-populate form values
      $name = $user['name'];  // Populate name
      $email = $user['email'];
      $username = $user['username'];
    } else {
      echo "No user found.";
    }

    // Close the statement
    $stmt->close();
  }
} else {
  echo "User is not logged in.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Bias System</title>
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


</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="admin.php" class="logo d-flex align-items-center">
        <img src="#" alt="">
        <span class="d-none d-lg-block">Bias System</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->



    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">

          </a>
        </li><!-- End Search Icon-->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="assets/img/human.png" alt="Profile" class="rounded-circle">
            <span
              class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($user['name'] ?? 'Guest'); ?></span>
            <!-- Dynamic User Name -->
          </a><!-- End Profile Image Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo htmlspecialchars($user['name'] ?? 'Guest'); ?></h6> <!-- Dynamic User Name -->
              <span><?php echo htmlspecialchars($user['job_title'] ?? 'Admin'); ?></span>
              <!-- Dynamic User Job Title -->
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="useradmin.html">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="login.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Departments</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="Department-comptech.php">
              <i class="bi bi-circle"></i><span>Comptech</span>
            </a>
          </li>
          <li>
            <a href="Department-Electronics.php">
              <i class="bi bi-circle"></i><span>Electronics</span>
            </a>
          </li>
          <li>
            <a href="Department-Education.php">
              <i class="bi bi-circle"></i><span>Education</span>
            </a>
          </li>
          <li>
            <a href="Department-HospitalityManagment.php">
              <i class="bi bi-circle"></i><span>Hospitality Managment</span>
            </a>
          </li>
          <li>
            <a href="Department-Tourism.php">
              <i class="bi bi-circle"></i><span>Tourism</span>
            </a>
          </li>

        </ul>
      </li><!-- End Components Nav -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin.php">Home</a></li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Profile</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row">
        <div class="col-xl-4">
          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
              <img src="assets/img/human.png" alt="Profile" class="rounded-circle">
              <h2><?php echo htmlspecialchars($user['name']); ?></h2> <!-- Display full name -->
              <h3><?php echo htmlspecialchars($user['username']); ?></h3> <!-- Display username -->
              <div class="social-links mt-2">
                <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-8">
          <div class="card">
            <div class="card-body pt-3">
              <ul class="nav nav-tabs nav-tabs-bordered">
                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab"
                    data-bs-target="#profile-overview">Overview</button>
                </li>
                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                </li>
                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change
                    Password</button>
                </li>
              </ul>

              <div class="tab-content pt-2">
                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                  <h5 class="card-title">Profile Details</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Full Name</div>
                    <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($user['name']); ?></div>
                    <!-- Display full name -->
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Username</div>
                    <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($user['username']); ?></div>
                    <!-- Display username -->
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($user['email']); ?></div>
                    <!-- Display email -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
        <!-- Profile Edit Form -->
        <form method="POST" action="">
          <?php
          if (isset($success_message)) {
            echo "<div class='alert alert-success'>$success_message</div>";
          }
          if (isset($error_message)) {
            echo "<div class='alert alert-danger'>$error_message</div>";
          }
          ?>

          <!-- Full Name -->
          <div class="row mb-3">
            <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
            <div class="col-md-8 col-lg-9">
              <input type="text" name="name" class="form-control"
                value="<?php echo isset($user['name']) ? htmlspecialchars($user['name']) : ''; ?>" required>
            </div>
          </div>

          <!-- Username -->
          <div class="row mb-3">
            <label for="username" class="col-md-4 col-lg-3 col-form-label">Username</label>
            <div class="col-md-8 col-lg-9">
              <input type="text" name="username" class="form-control"
                value="<?php echo isset($user['username']) ? htmlspecialchars($user['username']) : ''; ?>" required>
            </div>
          </div>

          <!-- Email -->
          <div class="row mb-3">
            <label for="email" class="col-md-4 col-lg-3 col-form-label">Email</label>
            <div class="col-md-8 col-lg-9">
              <input type="email" name="email" class="form-control"
                value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="row mb-3">
            <div class="col-md-8 col-lg-9 offset-md-4 offset-lg-3">
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
          </div>
        </form>
      </div>
      </div>

      <div class="tab-pane fade pt-3" id="profile-change-password">
        <!-- Change Password Form -->
        <form>

          <div class="row mb-3">
            <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
            <div class="col-md-8 col-lg-9">
              <input name="password" type="password" class="form-control" id="currentPassword">
            </div>
          </div>

          <div class="row mb-3">
            <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
            <div class="col-md-8 col-lg-9">
              <input name="newpassword" type="password" class="form-control" id="newPassword">
            </div>
          </div>

          <div class="row mb-3">
            <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
            <div class="col-md-8 col-lg-9">
              <input name="renewpassword" type="password" class="form-control" id="renewPassword">
            </div>
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary">Change Password</button>
          </div>
        </form><!-- End Change Password Form -->

      </div>

      </div><!-- End Tab Content -->
      </div>
      </div>
      </div>
      </div>
    </section>

  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>