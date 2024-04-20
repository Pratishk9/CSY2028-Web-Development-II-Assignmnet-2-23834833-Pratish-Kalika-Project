<?php
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    // Retrieve category information
    $stmt = $pdo->prepare('SELECT * FROM category WHERE id = :id');
    $stmt->execute(['id' => $_POST['id']]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    // Delete the category from the database
    $stmt = $pdo->prepare('DELETE FROM category WHERE id = :id');
    $stmt->execute(['id' => $_POST['id']]);

    // Delete the associated PHP file
    $filename = strtolower(str_replace(' ', '', $category['name'])) . '.php';
    $filepath = __DIR__ . "/../$filename";
    if (file_exists($filepath)) {
        unlink($filepath);
    }

    header('location: categories.php');
}
?>
