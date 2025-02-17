<?php
// Start the session to access session data
session_start();

// Include the database connection
include('dbconn.php');

$sql_research = "SELECT * FROM tourism_research";
$result_research = $conn->query($sql_research);

// ---------- Add New Research ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_research'])) {

  // Sanitize input
  $researchDate = trim($_POST['research_date']);
  $researchTitle = trim($_POST['research_title']);
  $author = trim($_POST['author']);
  $location = trim($_POST['location']);
  $status = trim($_POST['status']);

  // Correct SQL statement (without `description`)
  $sql = "INSERT INTO tourism_research (research_date, research_title, author, location, status) 
          VALUES (?, ?, ?, ?, ?)";

  if ($stmt = $conn->prepare($sql)) {
      $stmt->bind_param("sssss", $researchDate, $researchTitle, $author, $location, $status);
      if ($stmt->execute()) {
          echo "<script>alert('Research added successfully!'); window.location.href='Department-Tourism Admin.php';</script>";
      } else {
          echo "<script>alert('Error adding research.');</script>";
      }
      $stmt->close();
  } else {
      echo "<script>alert('Error preparing query.');</script>";
  }
}
// ---------- Edit Research ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_research'])) {

  // Retrieve form data
  $id = $_POST['id'];
  $researchTitle = $_POST['research_title'];
  $author = $_POST['author'];
  $researchDate = $_POST['research_date'];
  $location = $_POST['location'];
  $status = $_POST['status'];

  // Correct SQL query (No `description`)
  $sql = "UPDATE tourism_research 
          SET research_title = ?, author = ?, research_date = ?, location = ?, status = ? 
          WHERE id = ?";

  $stmt = $conn->prepare($sql);
  if ($stmt !== false) {
      $stmt->bind_param("sssssi", $researchTitle, $author, $researchDate, $location, $status, $id);
      if ($stmt->execute()) {
          echo "<script>alert('Research updated successfully!'); window.location.href='Department-Tourism Admin.php';</script>";
      } else {
          echo "<script>alert('Error updating research.');</script>";
      }
      $stmt->close();
  } else {
      echo "<script>alert('Error preparing update statement.');</script>";
  }
}

// ---------- Delete Research ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_research'])) {
  $id = $_POST['id'];

  // SQL query to delete the research record
  $sql = "DELETE FROM tourism_research WHERE id = ?";

  $stmt = $conn->prepare($sql);
  if ($stmt !== false) {
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

  <title>Components / Tourism</title>
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
        <a class="nav-link " data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Department</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="Department-comptech Admin.php">
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
            <a href="Department-Tourism Admin.php" class="active">
              <i class="bi bi-circle"></i><span>Tourism</span>
            </a>
          </li>
          <!--  <li>
            <a href="components-cards.html">
              <i class="bi bi-circle"></i><span>Cards</span>
            </a>
          </li>-->

        </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Toursim</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin.php">Home</a></li>
          <li class="breadcrumb-item">Components</li>
          <li class="breadcrumb-item active">Toursim</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Tourism Research Data</h5>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResearchModal">
                Add Research Title
              </button>
              <!-- Table with tourism research data -->
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
                        <td><?php echo htmlspecialchars($row['research_title']); ?></td> 
                        <td><?php echo htmlspecialchars($row['author']); ?></td>                      
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td>
                            <!-- Edit button with a JavaScript function to prefill the edit form -->
                            <button class='btn btn-success btn-sm'
                                onclick='editResearch(<?php echo json_encode($row); ?>)'>Edit</button>

                            <!-- Delete button with a form -->
                            <form method="POST" action="delete.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class='btn btn-danger btn-sm'
                                    onclick="return confirm('Are you sure you want to delete this research?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                     <!-- End Table with stripped rows -->
              <div class="modal fade" id="addResearchModal" tabindex="-1" aria-labelledby="addResearchModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="addResearchModalLabel">Add New Research Title</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <!-- Form to add research title -->
                      <form id="addResearchForm" method="POST">
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
                        <button type="submit" name="add_research" class="btn btn-primary">Add Research Title</button>
                      </form>


                    </div>
                  </div>

                </div>
              </div>
              <!-- Edit Modal -->
              <div class="modal fade" id="editResearchModal" tabindex="-1" aria-labelledby="editResearchModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form method="POST" action="">
                      <div class="modal-header">
                        <h5 class="modal-title" id="editResearchModalLabel">Edit Research</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <!-- Hidden field for record ID -->
                        <input type="hidden" name="id" id="edit_id">
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
                      </div>
                      <div class="modal-footer">
                        <button type="submit" name="edit_research" class="btn btn-primary btn-sm">Update</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
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