<?php
session_start();
include 'db.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    echo "Session user_id not set!";
    exit;
}

$user_id = $_SESSION['user_id'];

// Query to fetch taxpayer details using user_id as a reference
$query = "SELECT * FROM `Taxpayer` WHERE user_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$taxpayer = $result->fetch_assoc();

if (!$taxpayer) {
    echo "No taxpayer data found for this user.";
    exit;
}

// Set default values if taxpayer data is not returned
$name = isset($taxpayer['Name']) && !empty($taxpayer['Name']) ? $taxpayer['Name'] : 'Unknown';
$email = isset($taxpayer['Email']) && !empty($taxpayer['Email']) ? $taxpayer['Email'] : 'Not available';
$phone = isset($taxpayer['Phone']) && !empty($taxpayer['Phone']) ? $taxpayer['Phone'] : 'Not available';

// Fetch tax professionals
$professionals_query = "SELECT * FROM TaxProfessional";
$professionals_result = $conn->query($professionals_query);

if (!$professionals_result) {
    die("Error fetching tax professionals: " . $conn->error);
}

// Fetch notifications for the taxpayer
$notifications_query = "SELECT * FROM Notification WHERE UserID = ? ORDER BY Timestamp DESC";
$notifications_stmt = $conn->prepare($notifications_query);

if (!$notifications_stmt) {
    die("Notifications query preparation failed: " . $conn->error);
}

$notifications_stmt->bind_param("i", $user_id);
$notifications_stmt->execute();
$notifications_result = $notifications_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taxpayer Dashboard</title>
    <style>
        /* Reset and basic styling */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            display: flex;
            justify-content: space-between;
            margin: 20px;
        }

        .left-panel, .right-panel {
            width: 48%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .left-panel h2, .right-panel h3 {
            color: #333;
        }

        .left-panel p, .right-panel p {
            font-size: 14px;
            color: #666;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type="file"], input[type="number"], select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #35F374;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #28a745;
        }

        select {
            padding: 12px;
            font-size: 14px;
        }

        h2, h3 {
            text-align: center;
        }

        .notifications {
            max-height: 300px;
            overflow-y: auto;
        }

        .notifications ul {
            list-style-type: none;
            padding: 0;
        }

        .notifications li {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .notifications li strong {
            color: #333;
        }

        .right-panel {
            background-color: #f9f9f9;
        }

        .profile-info {
            margin-bottom: 20px;
        }

        .profile-info p {
            font-size: 16px;
            color: #333;
            margin: 5px 0;
        }

        .profile-info strong {
            color: #2c3e50;
        }

        .form-container {
            margin-top: 20px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: center;
            }

            .left-panel, .right-panel {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Left Panel (Profile, File Upload, and Revenue Form) -->
    <div class="left-panel">
        <h2>Welcome, <?php echo htmlspecialchars($name); ?></h2>
        <div class="profile-info">
            <h3>Your Profile</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
        </div>

        <div class="form-container">
            <h3>Select Tax Professional</h3>
            <form action="send_revenue.php" method="POST">
                <select name="tax_professional" id="tax_professional" required>
                    <option value="">Select a Professional</option>
                    <?php while ($professional = $professionals_result->fetch_assoc()) { ?>
                        <option value="<?php echo htmlspecialchars($professional['id']); ?>">
                            <?php echo htmlspecialchars(!empty($professional['name']) ? $professional['name'] : 'Unknown'); ?>
                        </option>
                    <?php } ?>
                </select>

                <!-- Placeholder for the dynamic tax professional details -->
                <div id="professional-details"></div>

                <h3>Add Revenue</h3>
                <input type="number" name="revenue" placeholder="Enter Revenue" required>
                <button type="submit">Send to Professional</button>
            </form>
        </div>

        <div class="form-container">
            <h3>Upload Document</h3>
            <form action="upload_document.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="document" required>
                <button type="submit">Upload Document</button>
            </form>
        </div>
    </div>

    <!-- Right Panel (Notifications) -->
    <div class="right-panel">
        <h3>Notifications</h3>
        <div class="notifications">
            <ul>
                <?php while ($notification = $notifications_result->fetch_assoc()) { ?>
                    <li><strong><?php echo htmlspecialchars($notification['Type'] ?? 'Notification'); ?>:</strong>
                        <?php echo htmlspecialchars($notification['Message'] ?? 'No message'); ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>

<script>
// JavaScript to handle the change event of the tax professional dropdown
document.getElementById('tax_professional').addEventListener('change', function() {
    var taxProfessionalId = this.value;

    // Check if a valid selection is made
    if (taxProfessionalId) {
        // Create an AJAX request to fetch professional details
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_professional_details.php?id=' + taxProfessionalId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Update the professional details section with the returned data
                document.getElementById('professional-details').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    } else {
        // Clear the professional details if no professional is selected
        document.getElementById('professional-details').innerHTML = '';
    }
});
</script>

</body>
</html>
