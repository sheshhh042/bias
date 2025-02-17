<?php
// Start the session to access session data
session_start();

// Include the database connection
include('dbconn.php');

// Initialize variables to prevent undefined variable warnings
$name = '';
$email = '';
$username = '';

// Query to fetch tourism research data
$sql_research = "SELECT * FROM  hospitality_research";
$result_research = $conn->query($sql_research);

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

  <title>Components / Hospitality Managment</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
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
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">Bias System</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->



        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="assets/img/user.png" alt="Profile" class="rounded-circle">
            <span
              class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($user['name'] ?? 'Guest'); ?></span>
            <!-- Dynamic User Name -->
          </a><!-- End Profile Image Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo htmlspecialchars($user['name'] ?? 'Guest'); ?></h6> <!-- Dynamic User Name -->
              <span><?php echo htmlspecialchars($user['job_title'] ?? 'Student'); ?></span>
              <!-- Dynamic User Job Title -->
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <hr class="dropdown-divider">
        </li>

        <li>
          <a class="dropdown-item d-flex align-items-center" href="user.html">
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
        <a class="nav-link collapsed" href="index.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#departments-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Departments</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="departments-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <!-- BINTECH Nav -->
          <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#bintech-nav" data-bs-toggle="collapse" href="#">
              <i class="bi bi-journal-text"></i><span>BINTECH</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="bintech-nav" class="nav-content collapse" data-bs-parent="#departments-nav">
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
            </ul>
          </li>
          <!-- End BINTECH Nav -->

          <!-- Education Nav -->
          <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#education-nav" data-bs-toggle="collapse" href="#">
              <i class="bi bi-journal-text"></i><span>Education</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="education-nav" class="nav-content collapse" data-bs-parent="#departments-nav">
              <li>
                <a href="Department-Education.php">
                  <i class="bi bi-circle"></i><span>Education</span>
                </a>
                <a href="Department-Education.php">
                  <i class="bi bi-circle"></i><span>Education</span>
                </a>
                <a href="Department-Education.php">
                  <i class="bi bi-circle"></i><span>Education</span>
                </a>
                <a href="Department-Education.php">
                  <i class="bi bi-circle"></i><span>Education</span>
                </a>
              </li>
            </ul>
          </li>
          <!-- End Education Nav -->

          <!-- Hospitality Management Nav -->
          <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#hospitality-nav" data-bs-toggle="collapse" href="#">
              <i class="bi bi-journal-text"></i><span>BSHTM</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="hospitality-nav" class="nav-content collapse" data-bs-parent="#departments-nav">
              <li>
                <a href="Department-HospitalityManagment.php">
                  <i class="bi bi-circle"></i><span>Hospitality Management</span>
                </a>
                <a href="Department-Tourism.php">
                  <i class="bi bi-circle"></i><span>Tourism</span>
                </a>
              </li>
            </ul>
          </li>


  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Hospitality Managment</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Components</li>
          <li class="breadcrumb-item active">Hospitality Managment</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Hospitality Management Research Data</h5>

              <!-- Table with hospitality research data -->
              <table class="table datatable">
                <thead>
                  <tr>
                  <th><b>Date</b></th>
                    <th><b>Research Title</b></th>
                    <th><b>Author</b></th>
                    <th><b>Status</b></th>
                    <th><b>Location</b></th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                <?php while ($row = $result_research->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['research_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['research_title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td>
                                    
                                    </td>
                                </tr>
                            <?php } ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

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