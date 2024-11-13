<?php
include 'db.php';  // Ensure your db.php file is connecting to the database properly

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $address = $_POST["address"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);  // Encrypt password
    $role = $_POST["role"];
    $tin = "TIN" . rand(1000, 9999);  // Generate random TIN

    // Insert into User table first
    $userQuery = "INSERT INTO User (name, password, role, email, phone) VALUES (?, ?, ?, ?, ?)";
    $userStmt = $conn->prepare($userQuery);
    
    if ($userStmt === false) {
        die('MySQL prepare error for User table: ' . $conn->error);  // Debugging line to check User query
    }

    $userStmt->bind_param("sssss", $name, $password, $role, $email, $phone);
    
    if ($userStmt->execute()) {
        // Retrieve the last inserted user_id
        $user_id = $conn->insert_id;

        // Now insert into the appropriate role table based on the user_id
        if ($role == "taxpayer") {
            // SQL query for taxpayer
            $query = "INSERT INTO Taxpayer (user_id, name, tin, address, email, phone, registrationdate, password) 
                      VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
            $stmt = $conn->prepare($query);
            
            if ($stmt === false) {
                die('MySQL prepare error for Taxpayer: ' . $conn->error);  // Debugging line to check SQL query
            }

            $stmt->bind_param("issssss", $user_id, $name, $tin, $address, $email, $phone, $password);

        } else {
            // SQL query for taxprofessional
            $certification_id = "CERT" . rand(1000, 9999);  // Generate random certification ID
            $query = "INSERT INTO TaxProfessional (user_id, name, tin, certification_id, email, phone, registrationdate, password) 
                      VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                die('MySQL prepare error for TaxProfessional: ' . $conn->error);  // Debugging line to check SQL query
            }

            $stmt->bind_param("issssss", $user_id, $name, $tin, $certification_id, $email, $phone, $password);
        }

        // Execute the main role-specific insertion
        if ($stmt->execute()) {
            echo "User and role-specific data inserted successfully!";
            header("Location: login.php");  // Redirect to login page
            exit();
        } else {
            echo "Error inserting into main role table: " . $stmt->error;  // Print error message if query fails
        }

    } else {
        echo "Error inserting into User table: " . $userStmt->error;  // Print error message if query fails
    }

    // Close the prepared statements
    $userStmt->close();
    if (isset($stmt)) $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f9; margin: 0; }
        .container { max-width: 400px; padding: 2em; background: #fff; box-shadow: 0px 4px 10px rgba(0,0,0,0.1); border-radius: 8px; }
        h2 { text-align: center; color: #333; }
        form { display: flex; flex-direction: column; }
        input, select, button { padding: 10px; margin: 8px 0; border-radius: 4px; border: 1px solid #ddd; }
        button { background-color: #28a745; color: #fff; border: none; cursor: pointer; }
        button:hover { background-color: #218838; }
        .redirect-link { text-align: center; margin-top: 10px; }
        .redirect-link a { color: #007bff; text-decoration: none; }
        .redirect-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <form method="POST" action="signup.php">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="address" placeholder="Address">
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="taxpayer">Taxpayer</option>
                <option value="taxprofessional">TaxProfessional</option>
            </select>
            <button type="submit">Sign Up</button>
        </form>
        <div class="redirect-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>
