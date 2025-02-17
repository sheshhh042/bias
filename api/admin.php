<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *"); // Consider limiting this to specific domains
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

session_start();
require_once('../dbconn.php'); // Ensure this file exists and contains the database connection

// Function to respond with JSON
function respond($status, $message) {
    http_response_code($status);
    echo json_encode(["message" => $message]);
    exit;
}

// Handle request method
$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case 'GET':
        // Fetch all research records
        $sql = "SELECT * FROM admin_dashboard";
        $result = $conn->query($sql);
        $researchData = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $researchData[] = $row;
            }
        }

        echo json_encode($researchData);
        break;

    case 'POST':
        // Handle adding new research
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['research_title'], $data['author'], $data['research_date'], $data['status'], $data['location'])) {
            $stmt = $conn->prepare("INSERT INTO admin_dashboard (research_title, author, research_date, status, location) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $data['research_title'], $data['author'], $data['research_date'], $data['status'], $data['location']);

            if ($stmt->execute()) {
                respond(201, "Research added successfully!");
            } else {
                respond(500, "Failed to add research.");
            }
            $stmt->close();
        } else {
            respond(400, "Incomplete data provided.");
        }
        break;

    case 'PUT':
        // Handle updating research
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['id'], $data['research_title'], $data['author'], $data['research_date'], $data['status'], $data['location'])) {
            $stmt = $conn->prepare("UPDATE admin_dashboard SET research_title=?, author=?, research_date=?, status=?, location=? WHERE id=?");
            $stmt->bind_param("sssssi", $data['research_title'], $data['author'], $data['research_date'], $data['status'], $data['location'], $data['id']);

            if ($stmt->execute()) {
                respond(200, "Research updated successfully!");
            } else {
                respond(500, "Failed to update research.");
            }
            $stmt->close();
        } else {
            respond(400, "Incomplete data provided.");
        }
        break;

    case 'DELETE':
        // Handle deleting research
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['id'])) {
            $stmt = $conn->prepare("DELETE FROM admin_dashboard WHERE id = ?");
            $stmt->bind_param("i", $data['id']);

            if ($stmt->execute()) {
                respond(200, "Research deleted successfully!");
            } else {
                respond(500, "Failed to delete research.");
            }
            $stmt->close();
        } else {
            respond(400, "ID not provided.");
        }
        break;

    default:
        respond(405, "Invalid request method.");
}

$conn->close();
?>
