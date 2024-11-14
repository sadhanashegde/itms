<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taxprofessional') {
    die("Access denied.");
}

$professional_id = $_SESSION['user_id']; // Assuming the professional logs in

// Fetch clients assigned to the professional
$clients_query = "SELECT * FROM Taxpayer WHERE tax_professional_id = ?";
$clients_stmt = $conn->prepare($clients_query);
$clients_stmt->bind_param("i", $professional_id);
$clients_stmt->execute();
$clients = $clients_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tax Professional Dashboard</title>
</head>
<body>

<h1>Your Clients</h1>
<ul>
    <?php while ($client = $clients->fetch_assoc()) { ?>
        <li>
            <a href="view_client.php?client_id=<?php echo $client['user_id']; ?>">
                <?php echo htmlspecialchars($client['Name']); ?>
            </a>
        </li>
    <?php } ?>
</ul>

</body>
</html>
