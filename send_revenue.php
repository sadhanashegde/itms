<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $tax_professional_id = $_POST['tax_professional'];
    $revenue = $_POST['revenue'];

    // Create a notification for the tax professional
    $message = "Taxpayer $user_id has submitted a revenue of $revenue.";

    // Insert the revenue submission as a notification
    $query = "INSERT INTO Notification (UserID, Message, Timestamp, Type, Status) VALUES (?, ?, NOW(), 'Revenue Submitted', 'Unseen')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $tax_professional_id, $message);

    if ($stmt->execute()) {
        echo "Revenue submitted successfully!";
    } else {
        echo "Error submitting revenue.";
    }
}
?>
