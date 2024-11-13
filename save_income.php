<?php
session_start();
require 'db_connection.php'; // Include database connection file

// Check if the request is a POST and data is sent as JSON
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    $taxpayerId = $_SESSION['user_id']; // Assume the taxpayer's user ID is stored in session
    $professionalId = $data['professionalId'];
    $annualIncome = $data['annualIncome'];

    // Validate data
    if (empty($taxpayerId) || empty($professionalId) || empty($annualIncome)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit();
    }

    // Insert income record into the database
    $sql = "INSERT INTO income_records (taxpayer_id, professional_id, annual_income) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iid", $taxpayerId, $professionalId, $annualIncome);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>