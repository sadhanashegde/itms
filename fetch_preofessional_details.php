<?php
include 'db.php';

// Get the selected tax professional ID from the URL
$tax_professional_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($tax_professional_id > 0) {
    // Query to fetch the details of the selected tax professional
    $query = "SELECT * FROM TaxProfessional WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tax_professional_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $professional = $result->fetch_assoc();

    if ($professional) {
        // Display additional information about the selected professional
        echo "<h4>Professional Details</h4>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($professional['name']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($professional['email']) . "</p>";
        echo "<p><strong>Phone:</strong> " . htmlspecialchars($professional['phone']) . "</p>";
    } else {
        echo "<p>No details found for the selected professional.</p>";
    }
} else {
    echo "<p>Invalid professional selection.</p>";
}
?>
