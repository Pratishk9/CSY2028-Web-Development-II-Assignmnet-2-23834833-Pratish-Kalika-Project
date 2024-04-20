<?php
// Ensure that the session is started
session_start();

// Check if the user is logged in and has the necessary permissions
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect unauthorized users to the login page
    header("Location: login.php");
    exit(); // Stop further execution
}

// Check if the job ID is provided in the request
if (!isset($_POST['id'])) {
    // If not provided, redirect back to the jobs page or display an error message
    header("Location: jobs.php");
    exit(); // Stop further execution
}

// Establish database connection
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');

// Retrieve the job ID from the request
$jobId = $_POST['id'];

// Perform the archive action in the database
try {
    // Prepare SQL statement to update the job's archived status
    $stmt = $pdo->prepare('UPDATE job SET archived = 1 WHERE id = :id');
    $stmt->execute(['id' => $jobId]);

    // Redirect back to the jobs page after archiving
    header("Location: jobs.php");
    exit(); // Stop further execution
} catch (PDOException $e) {
    // Handle database errors
    // You might want to display an error message or log the error
    echo "Error: " . $e->getMessage();
}
?>
