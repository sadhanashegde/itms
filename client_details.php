<?php
session_start();
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Session user_id not set!";
    exit;
}

$user_id = $_SESSION['user_id'];
$client_id = isset($_GET['client_id']) ? $_GET['client_id'] : 0;

if ($client_id == 0) {
    echo "Invalid client!";
    exit;
}

// Fetch Tax Professional details
$query = "SELECT * FROM TaxProfessional WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$tax_professional = $result->fetch_assoc();
$name = $tax_professional['name'] ?? 'Tax Professional';

// Fetch Client Details
$client_query = "SELECT * FROM Taxpayer WHERE id = ?";
$client_stmt = $conn->prepare($client_query);
$client_stmt->bind_param("i", $client_id);
$client_stmt->execute();
$client_result = $client_stmt->get_result();
$client = $client_result->fetch_assoc();

// Fetch Client Documents
$documents_query = "SELECT * FROM Documents WHERE taxpayer_id = ?";
$documents_stmt = $conn->prepare($documents_query);
$documents_stmt->bind_param("i", $client_id);
$documents_stmt->execute();
$documents_result = $documents_stmt->get_result();

// Fetch Client Error Logs
$error_logs_query = "SELECT * FROM ErrorLogs WHERE taxpayer_id = ?";
$error_logs_stmt = $conn->prepare($error_logs_query);
$error_logs_stmt->bind_param("i", $client_id);
$error_logs_stmt->execute();
$error_logs_result = $error_logs_stmt->get_result();

// Fetch Notifications for the Client
$notifications_query = "SELECT * FROM Notification WHERE UserID = ? ORDER BY Timestamp DESC";
$notifications_stmt = $conn->prepare($notifications_query);
$notifications_stmt->bind_param("i", $client_id);
$notifications_stmt->execute();
$notifications_result = $notifications_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
        }

        .client-info, .documents, .error-logs, .notifications {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .client-info h2 {
            color: #2c3e50;
        }

        .documents ul, .error-logs ul, .notifications ul {
            list-style: none;
            padding: 0;
        }

        .documents li, .error-logs li, .notifications li {
            background-color: #f9f9f9;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="client-info">
        <h2>Client: <?php echo htmlspecialchars($client['Name']); ?></h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($client['Email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($client['Phone']); ?></p>
    </div>

    <!-- Documents -->
    <div class="documents">
        <h3>Client Documents</h3>
        <ul>
            <?php while ($document = $documents_result->fetch_assoc()) { ?>
                <li><strong><?php echo htmlspecialchars($document['DocumentTitle']); ?>:</strong> 
                    <a href="documents/<?php echo htmlspecialchars($document['DocumentFile']); ?>" target="_blank">View Document</a>
                </li>
            <?php } ?>
        </ul>
    </div>

    <!-- Error Logs -->
    <div class="error-logs">
        <h3>Error Logs</h3>
        <ul>
            <?php while ($error = $error_logs_result->fetch_assoc()) { ?>
                <li><strong><?php echo htmlspecialchars($error['ErrorType']); ?>:</strong> 
                    <?php echo htmlspecialchars($error['ErrorMessage']); ?>
                </li>
            <?php } ?>
        </ul>
    </div>

    <!-- Notifications -->
    <div class="notifications">
        <h3>Notifications</h3>
        <ul>
            <?php while ($notification = $notifications_result->fetch_assoc()) { ?>
                <li><strong><?php echo htmlspecialchars($notification['NotificationTitle']); ?>:</strong>
                    <?php echo htmlspecialchars($notification['NotificationMessage']); ?>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>

</body>
</html>
