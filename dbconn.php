<?php
// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database connection details
$servername = "localhost";  // Change if needed
$username = "root";         // Change if needed
$password = "";             // Change if needed
$dbname = "research";       // Change to match your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ---------- Add Research ----------
function addResearch($researchDate, $researchTitle, $author, $status, $location, $imagePath) {
    global $conn;
    
    $sql = "INSERT INTO admin_dashboard (research_date, research_title, author, location, action, image_path) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false; // Error preparing statement
    }

    $stmt->bind_param("ssssss", $researchDate, $researchTitle, $author, $location, $status, $imagePath);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result; // True if successful, false otherwise
}

// ---------- Edit Research ----------
function editResearch($id, $researchTitle, $author, $researchDate, $status, $location, $action) {
    global $conn;

    $sql = "UPDATE admin_dashboard 
            SET research_title = ?, author = ?, research_date = ?, location = ?, action = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false; // Error preparing statement
    }

    $stmt->bind_param("sssssi", $researchTitle, $author, $researchDate, $location, $action, $id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// ---------- Delete Research ----------
function deleteResearch($id) {
    global $conn;

    $sql = "DELETE FROM admin_dashboard WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false; // Error preparing statement
    }

    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Test connection (optional)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['test'])) {
    echo "Database connected successfully!";
}
?>
