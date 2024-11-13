<?php
session_start();
include 'db.php'; // Include the database connection

// Check if the user is logged in and has the role of "TaxAuthority"
//echo $_SESSION['role']; // Temporarily output the role for debugging

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taxauthority') {
    die("Access denied.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TaxAuthority Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        .section {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .section h2 {
            color: #333;
        }
        table {
            width: 100%;
            margin: 10px 0;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #35F374;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, TaxAuthority</h1>

        <!-- Section: View Tax Professionals -->
        <div class="section">
            <h2>View Tax Professionals</h2>
            <?php
            $professionals_query = "SELECT id, name, email, phone FROM TaxProfessional";
            $professionals_result = $conn->query($professionals_query);
            if ($professionals_result->num_rows > 0) {
                echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr>";
                while ($row = $professionals_result->fetch_assoc()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['email']}</td><td>{$row['phone']}</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No tax professionals found.";
            }
            ?>
        </div>

        <!-- Section: View Clients -->
        <div class="section">
            <h2>View Clients</h2>
            <?php
            $clients_query = "SELECT id, name, email, phone FROM Taxpayer";
            $clients_result = $conn->query($clients_query);
            if ($clients_result->num_rows > 0) {
                echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr>";
                while ($row = $clients_result->fetch_assoc()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['email']}</td><td>{$row['phone']}</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No clients found.";
            }
            ?>
        </div>

        <!-- Section: Monitor Payment Status -->
        <div class="section">
            <h2>Monitor Payment Status</h2>
            <?php
            $payments_query = "SELECT payment_id, taxpayer_id, amount, status, payment_date FROM Payments";
            $payments_result = $conn->query($payments_query);
            if ($payments_result->num_rows > 0) {
                echo "<table><tr><th>Payment ID</th><th>Taxpayer ID</th><th>Amount</th><th>Status</th><th>Date</th></tr>";
                while ($row = $payments_result->fetch_assoc()) {
                    echo "<tr><td>{$row['payment_id']}</td><td>{$row['taxpayer_id']}</td><td>{$row['amount']}</td><td>{$row['status']}</td><td>{$row['payment_date']}</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No payment records found.";
            }
            ?>
        </div>

        <!-- Section: Generate Tax Reports -->
        <div class="section">
            <h2>Generate Tax Reports</h2>
            <form action="generate_report.php" method="POST">
                <label for="year">Select Year:</label>
                <select name="year" required>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
                <button type="submit">Generate Report</button>
            </form>
        </div>

        <!-- Section: Handle Tax Refunds and Audits -->
        <div class="section">
            <h2>Handle Tax Refunds and Audits</h2>
            <?php
            $refunds_query = "SELECT refund_id, taxpayer_id, amount, status, request_date FROM Refunds";
            $refunds_result = $conn->query($refunds_query);
            if ($refunds_result->num_rows > 0) {
                echo "<table><tr><th>Refund ID</th><th>Taxpayer ID</th><th>Amount</th><th>Status</th><th>Request Date</th></tr>";
                while ($row = $refunds_result->fetch_assoc()) {
                    echo "<tr><td>{$row['refund_id']}</td><td>{$row['taxpayer_id']}</td><td>{$row['amount']}</td><td>{$row['status']}</td><td>{$row['request_date']}</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No refunds or audits found.";
            }
            ?>
        </div>
    </div>
</body>
</html>
