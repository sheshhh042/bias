<?php
// Start the session to access session data
session_start();

// Include the database connection
include('dbconn.php');

// Fetch all research records from `comptech_research`
$sql_research = "SELECT * FROM comptech_research";
$result_research = $conn->query($sql_research);

// ---------- Add New Research ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_research'])) {
  // Get the form values
  $researchDate = $_POST['research_date'];
  $researchTitle = $_POST['research_title'];
  $author = $_POST['author'];
  $status = $_POST['status'];
  $location = $_POST['location'];
  $description = $_POST['description'];

  // Handle Image Upload
  $imagePath = '';
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/'; // Ensure this directory exists
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0777, true);
    }

    $fileInfo = pathinfo($_FILES['image']['name']);
    $fileExt = strtolower($fileInfo['extension']);
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExt, $allowed)) {
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

  // Insert data into `comptech_research`
  $sql = "INSERT INTO comptech_research (research_date, research_title, author, location, status, description, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)";
  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssssss", $researchDate, $researchTitle, $author, $location, $status, $description, $imagePath);
    if ($stmt->execute()) {
      echo "<script>alert('Research added successfully!'); window.location.href='comptech_admin.php';</script>";
    } else {
      echo "<script>alert('Error: Unable to add research.');</script>";
    }
    $stmt->close();
  } else {
    echo "<script>alert('Error preparing query: " . $conn->error . "');</script>";
  }
}

// ---------- Edit Research ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_research'])) {
  $id = $_POST['id'];
  $researchTitle = $_POST['research_title'];
  $author = $_POST['author'];
  $researchDate = $_POST['research_date'];
  $status = $_POST['status'];
  $location = $_POST['location'];
  $description = $_POST['description'];

  // Handle Image Upload (Optional)
  $imagePath = $_POST['existing_image']; // Keep the old image if no new one is uploaded
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    $fileInfo = pathinfo($_FILES['image']['name']);
    $fileExt = strtolower($fileInfo['extension']);
    if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
      $newFilename = uniqid() . '.' . $fileExt;
      $targetFile = $uploadDir . $newFilename;
      if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $imagePath = $targetFile;
      }
    }
  }

  // Update `comptech_research`
  $sql = "UPDATE comptech_research SET research_title = ?, author = ?, research_date = ?, location = ?, status = ?, description = ?, image_path = ? WHERE id = ?";
  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssssssi", $researchTitle, $author, $researchDate, $location, $status, $description, $imagePath, $id);
    if ($stmt->execute()) {
      echo "<script>alert('Research updated successfully!'); window.location.href='comptech_admin.php';</script>";
    } else {
      echo "<script>alert('Error updating research.');</script>";
    }
    $stmt->close();
  } else {
    echo "<script>alert('Error preparing update query: " . $conn->error . "');</script>";
  }
}

// ---------- Delete Research ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_research'])) {
  $id = $_POST['id'];

  // Delete from `comptech_research`
  $sql = "DELETE FROM comptech_research WHERE id = ?";
  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
      echo "<script>alert('Research deleted successfully!'); window.location.href='comptech_admin.php';</script>";
    } else {
      echo "<script>alert('Error deleting research.');</script>";
    }
    $stmt->close();
  } else {
    echo "<script>alert('Error preparing delete query: " . $conn->error . "');</script>";
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Components / Comptech</title>
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
              <h6><?php echo htmlspecialchars($user['email'] ?? ''); ?></h6> <!-- Dynamic User Name -->
              <span><?php echo htmlspecialchars($user['job_title'] ?? 'Admin'); ?></span>
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

      </ul>
      </li>

      </ul>
    </nav>

  </header>

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link " data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Department</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="Department-comptech Admin.php" class="active">
              <i class="bi bi-circle"></i><span>Comptech</span>
            </a>
          </li>
          <li>
            <a href="Department-Electronics Admin.php">
              <i class="bi bi-circle"></i><span>Electronics</span>
            </a>
          </li>
          <li>
            <a href="Department-Education Admin.php">
              <i class="bi bi-circle"></i><span>Education</span>
            </a>
          </li>
          <li>
            <a href="Department-HospitalityManagment Admin.php">
              <i class="bi bi-circle"></i><span>Hospitality Managment</span>
            </a>
          </li>
          <li>
            <a href="Department-Tourism Admin.php">
              <i class="bi bi-circle"></i><span>Tourism</span>
            </a>
          </li>

        </ul>

  </aside>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Comptech</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin.php">Home</a></li>
          <li class="breadcrumb-item">Components</li>
          <li class="breadcrumb-item active">Comptech</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <div class="col-lg-12">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title">Comptech Research Data</h5>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResearchModal">
            Add Research Title
          </button>
          <!-- Table with comptech research data -->
          <table class="table datatable">
            <thead>
              <tr>
                <th><b>Date</b></th>
                <th><b>Research Title</b></th>
                <th><b>Author</b></th>
                <th><b>Status</b></th>
                <th><b>Location</b></th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
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
            </tbody>
          </table>
          <!-- End Table with stripped rows -->
          <!-- Add Research Modal -->
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
          <!-- Edit Research Modal -->
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

  </main>
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
</body>

</html>