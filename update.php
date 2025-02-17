<?php
include('dbconn.php');

// Check if the necessary data is available
if (isset($_POST['id']) && isset($_POST['research_date']) && isset($_POST['research_title']) && isset($_POST['author']) && isset($_POST['status'])) {
    $researchId   = $_POST['id'];
    $researchDate = $_POST['research_date'];
    $researchTitle= $_POST['research_title'];
    $author       = $_POST['author'];
    $status       = $_POST['status'];
    
    // Initialize variable for new image path
    $newImagePath = '';
    
    // Check if a new image file was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        // Ensure the uploads directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileInfo = pathinfo($_FILES['image']['name']);
        $fileExt  = strtolower($fileInfo['extension']);
        $allowed  = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($fileExt, $allowed)) {
            // Generate a unique file name to avoid collisions
            $newFilename = uniqid() . '.' . $fileExt;
            $targetFile  = $uploadDir . $newFilename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $newImagePath = $targetFile;
            } else {
                error_log("Error uploading image: " . $_FILES['image']['error'], 3, 'error_log.txt');
                header("Location: admin.php?error=image_upload");
                exit();
            }
        } else {
            error_log("Invalid image file type.", 3, 'error_log.txt');
            header("Location: admin.php?error=invalid_file_type");
            exit();
        }
    }
    
    // Prepare the SQL query based on whether a new image was provided
    if ($newImagePath !== '') {
        // If a new image is provided, update the image_path field as well
        $sql = "UPDATE admin_dashboard SET research_date = ?, research_title = ?, author = ?, status = ?, image_path = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("Error preparing SQL: " . $conn->error, 3, 'error_log.txt');
            echo json_encode(['success' => false, 'error' => 'Error preparing the SQL query.']);
            exit();
        }
        $stmt->bind_param("sssssi", $researchDate, $researchTitle, $author, $status, $newImagePath, $researchId);
    } else {
        // If no new image is provided, update only the text fields
        $sql = "UPDATE admin_dashboard SET research_date = ?, research_title = ?, author = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("Error preparing SQL: " . $conn->error, 3, 'error_log.txt');
            echo json_encode(['success' => false, 'error' => 'Error preparing the SQL query.']);
            exit();
        }
        $stmt->bind_param("ssssi", $researchDate, $researchTitle, $author, $status, $researchId);
    }
    
    // Execute the update query
    if ($stmt->execute()) {
        header("Location: admin.php?success=true");
    } else {
        error_log("Error executing SQL: " . $stmt->error, 3, 'error_log.txt');
        header("Location: admin.php?error=4");
    }
    
    $stmt->close();
} else {
    header("Location: admin.php?error=5");
}

$conn->close();
?>
