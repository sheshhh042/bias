<?php
// Start the session to access session data
session_start();

// Include the database connection
include('dbconn.php');

// Initialize variables to prevent undefined variable warnings
$name = '';
$email = '';
$username = '';

// Fetch recent research data from the database (query to fetch research titles)
$sql_research = "SELECT * FROM admin_dashboard";
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
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
      $uploadDir = 'uploads/';
      // Ensure the uploads directory exists
      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
      }

      $fileInfo = pathinfo($_FILES['image']['name']);
      $fileExt = strtolower($fileInfo['extension']);
      $allowed = array('jpg', 'jpeg', 'png', 'gif');

      if (in_array($fileExt, $allowed)) {
        // Generate a unique file name to avoid collisions
        $newFilename = uniqid() . '.' . $fileExt;
        $targetFile = $uploadDir . $newFilename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
          $imagePath = $targetFile;
        } else {
          echo "<script>alert('Error uploading image.');</script>";
        }
      } else {
        echo "<script>alert('Invalid file type. Allowed types: jpg, jpeg, png, gif');</script>";
      }
    }
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


  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/Img/llcc.png" alt="">
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
            <img src="assets/img/user.png" alt="Profile" class="rounded-circle">
            <span
              class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($user['name'] ?? 'Guest'); ?></span>
            <!-- Dynamic User Name -->
          </a><!-- End Profile Image Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo htmlspecialchars($user['email'] ?? 'Guest'); ?></h6> <!-- Dynamic User Name -->
              <span><?php echo htmlspecialchars($user['job_title'] ?? 'Student'); ?></span>
              <!-- Dynamic User Job Title -->
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <hr class="dropdown-divider">
        </li>

        <li>
          <a class="dropdown-item d-flex align-items-center" href="user.php">
            <i class="bi bi-gear"></i>
            <span>Account Settings</span>
          </a>
        </li>
        <li>
          <hr class="dropdown-divider">
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
        <a class="nav-link " href="index.php">
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
              <i class="bi bi-motherboard fs-6"></i><span>BINTECH</span><i class="bi bi-chevron-down ms-auto"></i>
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
              <i class="bi bi-backpack3 fs-6"></i><span>Education</span><i class="bi bi-chevron-down ms-auto"></i>
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

              </li>
            </ul>
          </li>
          <!-- End Education Nav -->

          <!-- Hospitality Management Nav -->
          <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#hospitality-nav" data-bs-toggle="collapse" href="#">
              <i class="bi bi-airplane-engines fs-6"></i><span>BSHTM</span><i class="bi bi-chevron-down ms-auto"></i>
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


          <!-- <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="register.html">
          <i class="bi bi-card-list"></i>
          <span>Register</span>
        </a>
      </li> End Register Page Nav 

      <li class="nav-item">
        <a class="nav-link collapsed" href="login.html">
          <i class="bi bi-box-arrow-in-right"></i>
          <span>Login</span>
        </a>
      </li> End Login Page Nav

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-error-404.html">
          <i class="bi bi-dash-circle"></i>
          <span>Error 404</span>
        </a>
      </li> End Error 404 Page Nav -->

        </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <!-- Recent Research -->
    <div class="col-12">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title">Recent Research Titles <span>| Today</span></h5>

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
              <?php
              $api_url = 'http://localhost/research/api.php'; // Updated API URL
              $json_data = file_get_contents($api_url);
              $data = json_decode($json_data, true);

              // Check if data was fetched successfully
              if (!empty($data["admin_dashboard"])) {
                foreach ($data["admin_dashboard"] as $research) {
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($research['research_date']) . "</td>";
                  echo "<td>
                <a href='#' class='research-title' data-image='" . htmlspecialchars($research['image_path']) . "'>
                  " . htmlspecialchars($research['research_title']) . "
                </a>
                <div class='image-popup' style='display: none;'>
                  <img src='" . htmlspecialchars($research['image_path']) . "' 
                       alt='" . htmlspecialchars($research['research_title']) . "' 
                       style='max-width: 100%;'>
                </div>
              </td>";
                  echo "<td>" . htmlspecialchars($research['author']) . "</td>";
                  echo "<td>" . htmlspecialchars($research['status']) . "</td>";
                  echo "<td>" . htmlspecialchars($research['location']) . "</td>";
                  echo "<td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='7'>No research data found.</td></tr>";
              }
              ?>

            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Add jQuery for slide-down effect -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function () {
        // Add click event to research titles
        $('.research-title').click(function (e) {
          e.preventDefault(); // Prevent default link behavior

          // Find the associated image popup
          var imagePopup = $(this).next('.image-popup');

          // Toggle the visibility of the image popup with slide effect
          imagePopup.slideToggle('fast');
        });
      });
    </script>

    <!-- Optional: Add some CSS for the image popup -->
    <style>
      .image-popup {
        margin-top: 10px;
        padding: 10px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
      }

      .research-title {
        cursor: pointer;
        color: black;
        text-decoration: none;
      }

      .research-title:hover {
        text-decoration: none;
        color: black;
      }
    </style>


    </table>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div><!-- End Recent Sales -->
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