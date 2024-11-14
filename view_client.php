<?php
session_start();
include 'db.php';

$client_id = $_GET['client_id'];

// Fetch client details
$client_query = "SELECT * FROM Taxpayer WHERE user_id = ?";
$client_stmt = $conn->prepare($client_query);

if (!$client_stmt) {
    die("Error preparing client query: " . $conn->error);
}

$client_stmt->bind_param("i", $client_id);
$client_stmt->execute();
$client = $client_stmt->get_result()->fetch_assoc();

if (!$client) {
    die("No client data found for this ID.");
}

// Fetch client revenue (updated query to remove 'date' field)
$revenue_query = "SELECT amount FROM tax_revenues WHERE user_id = ?";
$revenue_stmt = $conn->prepare($revenue_query);

if (!$revenue_stmt) {
    die("Error preparing revenue query: " . $conn->error);
}

$revenue_stmt->bind_param("i", $client_id);
$revenue_stmt->execute();
$revenues = $revenue_stmt->get_result();

// Fetch client files
$file_query = "SELECT file_path, upload_date FROM documents WHERE user_id = ?";
$file_stmt = $conn->prepare($file_query);

if (!$file_stmt) {
    die("Error preparing file query: " . $conn->error);
}

$file_stmt->bind_param("i", $client_id);
$file_stmt->execute();
$files = $file_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Client Details</title>
</head>
<body>

<h1>Client: <?php echo htmlspecialchars($client['Name']); ?></h1>

<h2>Revenues</h2>
<ul>
    <?php while ($revenue = $revenues->fetch_assoc()) { ?>
        <li>Amount: <?php echo htmlspecialchars($revenue['amount']); ?></li>
    <?php } ?>
</ul>

<h2>Files</h2>
<ul>
    <?php while ($file = $files->fetch_assoc()) { ?>
        <li><a href="<?php echo htmlspecialchars($file['file_path']); ?>">View File</a></li>
    <?php } ?>
</ul>

<form action="add_comment.php" method="POST">
    <h3>Add Comment</h3>
    <textarea name="comment" required></textarea>
    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
    <button type="submit">Add Comment</button>
</form>

</body>
</html>
