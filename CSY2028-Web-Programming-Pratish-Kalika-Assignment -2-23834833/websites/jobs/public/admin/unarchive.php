<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['job_id'])) {
    $pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');

    // Update the database to unarchive the job
    $stmt = $pdo->prepare('UPDATE job SET archived = 0 WHERE id = :id');
    $stmt->execute(['id' => $_POST['job_id']]);

    // Redirect back to jobs.php
    header("Location: jobs.php");
    exit();
} else {
    // Handle invalid request
    http_response_code(400);
    exit("Invalid request");
}
?>
