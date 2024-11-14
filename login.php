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
    // Redirect based on role
    if ($user['role'] == 'taxpayer') {
        header("Location: taxpayer.php");
    } elseif ($user['role'] == 'taxprofessional') {
        header("Location: taxprofessional.php");
    } elseif ($user['role'] == 'taxauthority') {
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
        /* General Reset */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        /* Body and Background */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #0a0b0b; /* Dark background color */
            color: #fff; /* Light text color for contrast */
            margin: 0;
        }

        /* Container Styling */
        .container {
            width: 500px; /* Increased width */
            padding: 3em; /* Increased padding */
            background: #1a1a1a; /* Dark background for container */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }

        /* Heading Styling */
        h2 {
            text-align: center;
            color: #ff4757; /* Accent color for heading */
        }

        /* Form Styling */
        form { display: flex; flex-direction: column; }

        /* Input and Button Styling */
        input, button {
            padding: 12px; /* Slightly larger padding for inputs */
            margin: 10px 0; /* Increased margin */
            border-radius: 4px;
            border: 1px solid #333;
            background-color: #333; /* Dark input background */
            color: #fff; /* Light text color */
        }

        /* Button Styling */
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Link Styling */
        .redirect-link {
            text-align: center;
            margin-top: 15px;
        }

        .redirect-link a {
            color: #ff4757; /* Accent color for links */
            text-decoration: none;
        }

        .redirect-link a:hover {
            text-decoration: underline;
        }
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
