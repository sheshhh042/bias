<?php
// delete_research.php
include('dbconn.php');  // Include the database connection

// Check if the research ID is passed in the POST data
if (isset($_POST['id'])) {
    $research_id = $_POST['id'];

    // Prepare the DELETE query to remove the research entry from the database
    $sql = "DELETE FROM admin_dashboard WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $research_id);

    // Execute the query
    if ($stmt->execute()) {
        header("Location: admin.php");  // Redirect back to the admin page after deletion
        exit();
    } else {
        echo "Error deleting research: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid research ID.";
}
?>