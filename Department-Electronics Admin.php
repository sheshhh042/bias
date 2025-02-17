<?php
// Start the session to access session data
session_start();

// Include the database connection
include('dbconn.php');

// Optionally, fetch existing electronics research data (if needed)
$sql_research = "SELECT * FROM electronics_research";
$result_research = $conn->query($sql_research);

// ---------- Add New Research ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_research'])) {
  // Get the form values
  $researchDate  = $_POST['research_date'];
  $researchTitle = $_POST['research_title'];
  $author        = $_POST['author'];
  $status        = $_POST['status'];
  $location      = $_POST['location'];

  // Handle Image Upload
  $imagePath = '';
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/'; // Ensure this directory exists
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0777, true);
    }

    $fileInfo = pathinfo($_FILES['image']['name']);
    $fileExt  = strtolower($fileInfo['extension']);
    $allowed  = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExt, $allowed)) {
      $newFilename = uniqid() . '.' . $fileExt;
      $targetFile  = $uploadDir . $newFilename;
      if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $imagePath = $targetFile;
      } else {
        echo "<script>alert('Error uploading image.');</script>";
      }
    } else {
      echo "<script>alert('Invalid file type. Allowed types: jpg, jpeg, png, gif');</script>";
    }
  }

  // Prepare the SQL query to insert the data including the image path
  $sql = "INSERT INTO electronics_research (research_date, research_title, author, location, status, image_path) 
          VALUES (?, ?, ?, ?, ?, ?)";

  // Prepare and bind the parameters
  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ssssss", $researchDate, $researchTitle, $author, $location, $status, $imagePath);

    // Execute the statement
    if ($stmt->execute()) {
      echo "<script>alert('Research added successfully!');</script>";
    } else {
      echo "<script>alert('Error: Unable to add research.');</script>";
    }

    // Close the statement
    $stmt->close();
  } else {
    echo "<script>alert('Error preparing the query: " . $conn->error . "');</script>";
  }
}

// ---------- Edit Research ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_research'])) {
  $id            = $_POST['id'];
  $researchTitle = $_POST['research_title'];
  $author        = $_POST['author'];
  $researchDate  = $_POST['research_date'];
  $status        = $_POST['status'];
  $location      = $_POST['location'];

  // SQL query to update the research record (image not updated here)
  $sql = "UPDATE electronics_research 
          SET research_title = ?, author = ?, research_date = ?, location = ?, status = ? 
          WHERE id = ?";

  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssssi", $researchTitle, $author, $researchDate, $location, $status, $id);
    if ($stmt->execute()) {
      echo "<script>alert('Research updated successfully!');</script>";
      header("Location: Department-Electronics Admin.php");
      exit;
    } else {
      echo "Error updating research: " . $stmt->error;
    }
    $stmt->close();
  } else {
    echo "Error preparing update statement: " . $conn->error;
  }
}

// ---------- Delete Research ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_research'])) {
  $id = $_POST['id'];

  // SQL query to delete the research record
  $sql = "DELETE FROM electronics_research WHERE id = ?";

  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
      echo "<script>alert('Research deleted successfully!');</script>";
    } else {
      echo "Error deleting research: " . $stmt->error;
    }
    $stmt->close();
  } else {
    echo "Error preparing deletion statement: " . $conn->error;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Components / Electronics</title>
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .message-container {
      display: none;
      margin-top: 5px;
      background-color: #f8f9fa;
      padding: 5px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .toggle-message {
      color: #000;
      /* Sets the text color to black (or any color you choose) */
      text-decoration: none;
      /* Removes the underline */
    }

    .toggle-message:hover {
      color: #000;
      /* Keeps the same color on hover (optional) */
      text-decoration: none;
      /* Ensures no underline on hover */
    }
  </style>
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="admin.php" class="logo d-flex align-items-center">
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
            <img src="assets/img/human.png" alt="Profile" class="rounded-circle">
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
          <a class="dropdown-item d-flex align-items-center" href="useradmin.php">
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
      <h1>Electronics</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin.php">Home</a></li>
          <li class="breadcrumb-item">Components</li>
          <li class="breadcrumb-item active">Electronics</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Electronics Research Data</h5>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResearchModal">
                Add Research Title
              </button>
              <!-- Table with electronics research data -->
              <table class="table datatable">
            <thead>
                <tr>
                    <th>Research Date</th>
                    <th>Research Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop through the result set -->
                <?php while ($row = $result_research->fetch_assoc()) { ?>
                  <tr>
                  <td><?php echo htmlspecialchars($row['research_date']); ?></td>
                  <td>
                    <!-- Research Title clickable to toggle message -->
                    <a href="#" class="toggle-message"><?php echo htmlspecialchars($row['research_title']); ?></a>
                    <!-- Hidden picture container -->
                    <div class="message-container">
                      <?php if (!empty($row['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>"
                          alt="<?php echo htmlspecialchars($row['research_title']); ?>" style="max-width: 100%;">
                      <?php else: ?>
                        <p>No picture available.</p>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td><?php echo htmlspecialchars($row['author']); ?></td>
                  <td><?php echo htmlspecialchars($row['status']); ?></td>
                  <td><?php echo htmlspecialchars($row['location']); ?></td>
                  <td>
                    <!-- Edit and Delete Buttons -->
                    <button class="btn btn-success btn-sm"
                      onclick='editResearch(<?php echo json_encode($row); ?>)'>Edit</button>
                    <form method="POST" action="delete.php" style="display:inline;">
                      <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                      <button type="submit" name="delete_research" class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to delete this research?')">Delete</button>
                    </form>
                  </td>
                </tr>
                <?php } ?>
              <!-- End Table with stripped rows -->
              <div class="modal fade" id="addResearchModal" tabindex="-1" aria-labelledby="addResearchModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl"> <!-- Wider modal -->
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="addResearchModalLabel">Add New Research Title</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <!-- Form to add research title -->
                  <form class="row g-3" id="addResearchForm" method="POST" enctype="multipart/form-data">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label for="research_date" class="form-label">Date</label>
                          <input type="date" class="form-control" id="research_date" name="research_date" required>
                        </div>
                        <div class="mb-3">
                          <label for="research_title" class="form-label">Research Title</label>
                          <input type="text" class="form-control" id="research_title" name="research_title" required>
                        </div>
                        <div class="mb-3">
                          <label for="author" class="form-label">Author</label>
                          <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label for="location" class="form-label">Location</label>
                          <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="mb-3">
                          <label for="status" class="form-label">Status</label>
                          <select class="form-control" id="status" name="status" required>
                            <option value="Published">Published</option>
                            <option value="Draft">Draft</option>
                            <option value="Pending">Pending</option>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label for="image" class="form-label">Upload Image</label>
                          <input type="file" class="form-control" id="image" name="image" accept=".jpg,.jpeg,.png,.gif">
                        </div>
                      </div>
                    </div>
                    <!-- Align Add Research button to the right -->
                    <div class="text-end">
                      <button type="submit" name="add_research" class="btn btn-primary">Add Research Title</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade" id="editResearchModal" tabindex="-1" aria-labelledby="editResearchModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl"> <!-- Wider modal -->
              <div class="modal-content">
                <form method="POST" action="" enctype="multipart/form-data">
                  <div class="modal-header">
                    <h5 class="modal-title" id="editResearchModalLabel">Edit Research</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <!-- Hidden field for record ID -->
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label for="edit_research_date" class="form-label">Research Date</label>
                          <input type="date" class="form-control" name="research_date" id="edit_research_date" required>
                        </div>
                        <div class="mb-3">
                          <label for="edit_research_title" class="form-label">Research Title</label>
                          <input type="text" class="form-control" name="research_title" id="edit_research_title"
                            required>
                        </div>
                        <div class="mb-3">
                          <label for="edit_author" class="form-label">Author</label>
                          <input type="text" class="form-control" name="author" id="edit_author" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label for="edit_location" class="form-label">Location</label>
                          <input type="text" class="form-control" id="edit_location" name="location" required>
                        </div>
                        <div class="mb-3">
                          <label for="edit_status" class="form-label">Status</label>
                          <select class="form-control" id="edit_status" name="status" required>
                            <option value="Published">Published</option>
                            <option value="Draft">Draft</option>
                            <option value="Pending">Pending</option>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label for="edit_image" class="form-label">Upload New Image (optional)</label>
                          <input type="file" class="form-control" id="edit_image" name="image"
                            accept=".jpg,.jpeg,.png,.gif">
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="modal-footer">
                    <button type="submit" name="edit_research" class="btn btn-primary btn-sm">Update</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

    </section>

  </main><!-- End #main -->

  <script>
    // Function to open the edit modal and pre-populate the fields
    function editResearch(rowData) {
      // Populate the modal fields with data from the row
      document.getElementById('edit_id').value = rowData.id;
      document.getElementById('edit_research_date').value = rowData.research_date;
      document.getElementById('edit_research_title').value = rowData.research_title;
      document.getElementById('edit_author').value = rowData.author;
      // Show the modal using Bootstrap's modal API
      var editModal = new bootstrap.Modal(document.getElementById('editResearchModal'));
      editModal.show();
    }
  </script>

<script>
    $(document).ready(function () {
      $(document).on('click', '.toggle-message', function (e) {
        e.preventDefault();
        var $messageContainer = $(this).siblings('.message-container');
        $messageContainer.slideToggle(function () {
          if ($messageContainer.is(':visible')) {

          }
        });
      });
    });
  </script>
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