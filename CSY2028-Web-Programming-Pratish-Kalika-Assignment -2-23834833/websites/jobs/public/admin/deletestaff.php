<?php
// Check if user is logged in
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

// Check if the form is submitted with valid data
if (isset($_POST['submit'], $_POST['id'])) {
    $staffId = $_POST['id'];

    // Establish database connection
    $pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');

    // Prepare and execute DELETE query to delete staff member by ID
    $stmt = $pdo->prepare('DELETE FROM staff WHERE id = :id');
    $stmt->execute(['id' => $staffId]);

    // Redirect back to staff management page after deletion
    header('Location: staff.php');
    exit;
} else {
    // If form data is not valid or not submitted properly, redirect to staff management page
    header('Location: staff.php');
    exit;
}
