<?php
// Start session and include database connection
session_start();
include('dbconn.php');

// Set the content type to JSON
header('Content-Type: application/json');

$data = [
    "admin_dashboard"      => [],
    "comptech_research"    => [],
    "electronics_research" => [],
    "hospitality_research" => [] // Added hospitality_research array
];

// ---------- Fetch Data from `admin_dashboard` ----------
$sql_admin = "SELECT * FROM admin_dashboard";
$result_admin = $conn->query($sql_admin);

if ($result_admin->num_rows > 0) {
    while ($row = $result_admin->fetch_assoc()) {
        if (!empty($row['image_path'])) {
            $row['image_path'] = 'http://localhost/research/' . $row['image_path'];
        }
        $data["admin_dashboard"][] = $row;
    }
}

// ---------- Fetch Data from `comptech_research` ----------
$sql_comptech = "SELECT * FROM comptech_research";
$result_comptech = $conn->query($sql_comptech);

if ($result_comptech->num_rows > 0) {
    while ($row = $result_comptech->fetch_assoc()) {
        if (!empty($row['image_path'])) {
            $row['image_path'] = 'http://localhost/research/' . $row['image_path'];
        }
        $data["comptech_research"][] = $row;
    }
}

// ---------- Fetch Data from `electronics_research` ----------
$sql_electronics = "SELECT * FROM electronics_research";
$result_electronics = $conn->query($sql_electronics);

if ($result_electronics->num_rows > 0) {
    while ($row = $result_electronics->fetch_assoc()) {
        if (!empty($row['image_path'])) {
            $row['image_path'] = 'http://localhost/research/' . $row['image_path'];
        }
        $data["electronics_research"][] = $row;
    }
}

// ---------- Fetch Data from `hospitality_research` ----------
$sql_hospitality = "SELECT * FROM hospitality_research";
$result_hospitality = $conn->query($sql_hospitality);

if ($result_hospitality->num_rows > 0) {
    while ($row = $result_hospitality->fetch_assoc()) {
        if (!empty($row['image_path'])) {
            $row['image_path'] = 'http://localhost/research/' . $row['image_path'];
        }
        $data["hospitality_research"][] = $row;
    }
}

// Return the JSON response
echo json_encode($data);
$conn->close();
?>
