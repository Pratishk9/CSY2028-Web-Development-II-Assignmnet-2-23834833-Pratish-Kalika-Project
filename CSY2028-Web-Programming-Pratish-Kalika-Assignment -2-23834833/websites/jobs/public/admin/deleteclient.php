<?php
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    // Check if client ID is provided via POST parameter
    if (isset($_POST['id'])) {
        $clientId = $_POST['id'];

        // Delete the client from the database
        $stmt = $pdo->prepare('DELETE FROM clients WHERE id = :id');
        $stmt->execute(['id' => $clientId]);

        // Redirect back to client.php after deletion
        header('Location: client.php');
        exit();
    } else {
        echo 'Client ID not specified.';
    }
} else {
    // Redirect to login page if not logged in
    header('Location: index.php');
    exit();
}
?>
