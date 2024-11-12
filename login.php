<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $password = $_POST["password"];

    // Fetch user from the database
    $query = "SELECT * FROM User WHERE name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if user exists and password matches
    if ($user && password_verify($password, $user['password'])) {
        // Start session and store user info
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] == 'taxpayer') {
            header("Location: taxpayer.php");
        } elseif ($user['role'] == 'taxprofessional') {
            header("Location: professional.php");
        } else {
            header("Location: taxauthority.php");
        }
        exit();
    } else {
        echo "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f9; margin: 0; }
        .container { max-width: 400px; padding: 2em; background: #fff; box-shadow: 0px 4px 10px rgba(0,0,0,0.1); border-radius: 8px; }
        h2 { text-align: center; color: #333; }
        form { display: flex; flex-direction: column; }
        input, button { padding: 10px; margin: 8px 0; border-radius: 4px; border: 1px solid #ddd; }
        button { background-color: #007bff; color: #fff; border: none; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .redirect-link { text-align: center; margin-top: 10px; }
        .redirect-link a { color: #007bff; text-decoration: none; }
        .redirect-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="login.php">
            <input type="text" name="name" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="redirect-link">
            Don't have an account? <a href="signup.php">Sign up here</a>
        </div>
    </div>
</body>
</html>
