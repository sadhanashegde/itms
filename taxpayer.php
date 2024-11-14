<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Session user_id not set!";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch taxpayer details using the user_id (assuming user_id corresponds to TaxpayerID in the Taxpayer table)
$query = "SELECT * FROM `Taxpayer` WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$taxpayer = $stmt->get_result()->fetch_assoc();

if (!$taxpayer) {
    echo "No taxpayer data found for this user.";
    exit;
}

// Now that we have the taxpayer, get the TaxpayerID (which corresponds to UserID in Notification table)
$taxpayer_id = $taxpayer['TaxpayerID'];  // TaxpayerID is the actual ID we use for notifications

// Fetch assigned professional's name based on taxpayer's tax_professional_id
$professional_query = "SELECT name FROM TaxProfessional WHERE user_id = ?";
$professional_stmt = $conn->prepare($professional_query);
$professional_stmt->bind_param("i", $taxpayer['tax_professional_id']);
$professional_stmt->execute();
$professional = $professional_stmt->get_result()->fetch_assoc();

// Fetch most recent notification for taxpayer using the TaxpayerID
$recent_notification_query = "SELECT * FROM Notification WHERE UserID = ? ORDER BY Timestamp DESC LIMIT 1";
$recent_notification_stmt = $conn->prepare($recent_notification_query);
$recent_notification_stmt->bind_param("i", $taxpayer_id);  // Use taxpayer_id here
$recent_notification_stmt->execute();
$recent_notification = $recent_notification_stmt->get_result()->fetch_assoc();

// Fetch all notifications for taxpayer (excluding the most recent one)
$notifications_query = "SELECT * FROM Notification WHERE UserID = ? AND NotificationID != ? ORDER BY Timestamp DESC";
$notifications_stmt = $conn->prepare($notifications_query);
$notifications_stmt->bind_param("ii", $taxpayer_id, $recent_notification['NotificationID']);
$notifications_stmt->execute();
$notifications = $notifications_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Taxpayer Dashboard</title>
</head>
<body>

<div>
    <h2>Welcome, <?php echo htmlspecialchars($taxpayer['Name']); ?></h2>
    <p><strong>Your Professional:</strong> <?php echo htmlspecialchars($professional['name'] ?? 'N/A'); ?></p>

    <form action="upload_file.php" method="POST" enctype="multipart/form-data">
        <h3>Upload Document</h3>
        <input type="file" name="document" required>
        <button type="submit">Upload Document</button>
    </form>

    <form action="add_revenue.php" method="POST">
        <h3>Add Revenue</h3>
        <input type="number" name="revenue" placeholder="Enter Revenue" required>
        <button type="submit">Add Revenue</button>
    </form>
</div>

<div>
    <h3>Recent Notification</h3>
    <?php if ($recent_notification && isset($recent_notification['Message'])): ?>
        <p><?php echo htmlspecialchars($recent_notification['Message']); ?>
        <?php if (isset($recent_notification['Type']) && $recent_notification['Type'] === 'comment') { ?>
            <span>(Comment from Professional)</span>
        <?php } ?>
        </p>
    <?php else: ?>
        <p>No recent notifications.</p>
    <?php endif; ?>

    <h3>All Notifications</h3>
    <ul>
        <?php while ($notification = $notifications->fetch_assoc()) { ?>
            <li>
                <?php if (isset($notification['Message'])) {
                    echo htmlspecialchars($notification['Message']);
                } ?>
                <?php if (isset($notification['Type']) && $notification['Type'] === 'comment') { ?>
                    <span>(Comment from Professional)</span>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</div>

</body>
</html>
