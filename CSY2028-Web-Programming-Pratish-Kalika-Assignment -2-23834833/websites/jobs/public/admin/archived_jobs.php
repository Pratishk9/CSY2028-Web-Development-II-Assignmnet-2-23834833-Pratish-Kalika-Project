<?php
session_start();

// Check if user is logged in and is either a client, staff, or admin
if (!isset($_SESSION['loggedin']) || ($_SESSION['role'] !== 'client' && $_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin')) {
    header('Location: /login.php');
    exit();
}

try {
    // Database connection
    $pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve archived jobs for the logged-in client, staff, or all clients (for admins)
    if ($_SESSION['role'] === 'client') {
        $stmt = $pdo->prepare('SELECT * FROM job WHERE archived = 1 AND client_id = :client_id');
        $stmt->execute(['client_id' => $_SESSION['client_id']]);
    } elseif ($_SESSION['role'] === 'staff' || $_SESSION['role'] === 'admin') {
        $stmt = $pdo->prepare('SELECT * FROM job WHERE archived = 1');
        $stmt->execute();
    }

    $archivedJobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database error
    die("Error fetching archived jobs: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Archived Jobs</title>
    <script>
        function unarchiveJob(jobId) {
            if (confirm('Are you sure you want to unarchive this job?')) {
                document.getElementById('unarchive_form_' + jobId).submit();
            }
        }
    </script>
</head>

<body>
<header>
    <section>
        <aside>
            <h3>Office Hours:</h3>
            <p>Mon-Fri: 09:00-17:30</p>
            <p>Sat: 09:00-17:00</p>
            <p>Sun: Closed</p>
        </aside>
        <h1><a href="/admin/index.php" class="logo-link">Jo's Jobs</a></h1>
    </section>
</header>
<nav>
    <ul>
        <li><a href="/">Home</a></li>
        <li>Jobs</li>
        <li><a href="/about.html">About Us</a></li>
    </ul>
</nav>
<img src="/images/randombanner.php"/>
<main class="sidebar">
<section class="left">
        <ul>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <!-- Display sidebar for admin -->
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="staff.php">Staff</a></li>
                <li><a href="client.php">Client</a></li>
            <?php elseif ($_SESSION['role'] == 'staff'): ?>
                <!-- Display sidebar for staff -->
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="client.php">Client</a></li>
            <?php elseif ($_SESSION['role'] == 'client'): ?>
                <!-- Display sidebar for client -->
                <li><a href="jobs.php">Jobs</a></li>
            <?php endif; ?>
        </ul>
    </section>
    <section class="right">
        <h2>Archived Jobs</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Salary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($archivedJobs as $job): ?>
                    <tr>
                        <td><?php echo $job['title']; ?></td>
                        <td><?php echo $job['description']; ?></td>
                        <td><?php echo $job['salary']; ?></td>
                        <td>
                            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff' || ($_SESSION['role'] === 'client' && $job['client_id'] === $_SESSION['client_id'])): ?>
                                <button onclick="unarchiveJob(<?php echo $job['id']; ?>)">Unarchive</button>
                                <form id="unarchive_form_<?php echo $job['id']; ?>" method="post" action="unarchive.php">
                                    <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                </form>
                            <?php else: ?>
                                <!-- Display a message indicating no action can be taken by clients -->
                                <span>No action available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>
<footer>
    &copy; Jo's Jobs 2024
</footer>
</body>
</html>
