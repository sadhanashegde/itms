<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $file = $_FILES["file"];
    $file_name = $file["name"];
    $file_tmp_name = $file["tmp_name"];
    $file_error = $file["error"];

    if ($file_error === 0) {
        $file_destination = "uploads/" . $file_name;
        if (move_uploaded_file($file_tmp_name, $file_destination)) {
            echo "File uploaded successfully!";
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "There was an error uploading your file.";
    }
}
?>
