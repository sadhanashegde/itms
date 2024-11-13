

<?php
// Starting session
session_start();
include 'db.php';
// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Example user data (in a real application, you'd fetch this from the database)
$userData = [
    'firstname' => 'John',
    'phone' => ['1234567890', '0987654321'],
    'email' => 'john@example.com',
    'address' => '123 Street Name, City',
    'area' => 'Local Area',
    'pincode' => '123456'
];

// Handle Profile Update or Delete Actions (incomplete, for demonstration purposes)
// This is where you would write PHP code to process form submissions for updating/deleting profile

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Payer - ITMS</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to an external CSS file -->
</head>
<body>

<!-- Header -->
<header>
    <div class="header-left">
        <h1>Welcome, Taxpayer</h1>
    </div>
    <div class="header-right">
        <button onclick="showProfile()">Profile</button>
    </div>
</header>

<!-- Profile Popup -->
<div id="profilePopup" class="popup">
    <h2>Profile Information</h2>
    <p><strong>First Name:</strong> <?php echo $userData['firstname']; ?></p>
    <p><strong>Phone:</strong> <?php echo implode(", ", $userData['phone']); ?></p>
    <p><strong>Email:</strong> <?php echo $userData['email']; ?></p>
    <p><strong>Address:</strong> <?php echo $userData['address']; ?></p>
    <p><strong>Area:</strong> <?php echo $userData['area']; ?></p>
    <p><strong>Pincode:</strong> <?php echo $userData['pincode']; ?></p>
    <button onclick="editPhone()">Edit Phone Number</button>
    <button onclick="deleteProfile()">Delete Profile</button>
    <button onclick="closeProfile()">Close</button>
</div>

<!-- Main Content -->
<main>
    <!-- Dropdown for Tax Professionals -->
    <section>
        <label for="taxProfessional">Choose a Tax Professional:</label>
        <select id="taxProfessional" name="taxProfessional">
            <option value="">Select Professional</option>
            <option value="professional1">Professional 1</option>
            <option value="professional2">Professional 2</option>
            <option value="professional3">Professional 3</option>
        </select>
        <button onclick="saveProfessional()">Save Selection</button>
    </section>

    <!-- Document Upload and Income Input -->
    <section>
        <h3>Document Upload</h3>
        <input type="file" id="documentUpload" name="documentUpload">
        <label for="annualIncome">Annual Income:</label>
        <input type="number" id="annualIncome" name="annualIncome" placeholder="Enter your annual income">
        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="student">Student</option>
            <option value="professional">Professional</option>
            <option value="retired">Retired</option>
            <option value="ngo">NGO</option>
        </select>
    </section>

    <!-- Notifications -->
    <section>
        <h3>Notifications</h3>
        <div id="notificationBox">
            <p>No new notifications.</p>
        </div>
    </section>

    <!-- Payment Buttons -->
    <section>
        <button onclick="makePayment()">Make Payment</button>
        <button onclick="payProfessional()">Pay Professional</button>
    </section>
</main>

<script>
    // Show Profile Popup
    function showProfile() {
        document.getElementById('profilePopup').style.display = 'block';
    }

    // Close Profile Popup
    function closeProfile() {
        document.getElementById('profilePopup').style.display = 'none';
    }

    // Edit Phone Number
    function editPhone() {
        const newPhone = prompt("Enter new phone number:");
        if (newPhone) {
            // Update phone number via AJAX or a form submission to PHP backend (not implemented here)
            alert("Phone number updated to " + newPhone);
        }
    }

    // Delete Profile
    function deleteProfile() {
        if (confirm("Are you sure you want to delete your profile?")) {
            // Redirect to homepage after deletion
            window.location.href = 'index.php';
            // In a real application, this would involve a backend script to delete the user's profile from the database
        }
    }

    // Save Professional Selection
    function saveProfessional() {
        const selectedProfessional = document.getElementById('taxProfessional').value;
        if (selectedProfessional) {
            alert("Professional " + selectedProfessional + " selected.");
            // You can add AJAX here to save the selection to the backend
        } else {
            alert("Please select a professional.");
        }
    }

    // Mock functions for payment actions
    function makePayment() {
        alert("Proceeding to payment...");
    }

    function payProfessional() {
        alert("Proceeding to pay the professional...");
    }
</script>

</body>
</html>