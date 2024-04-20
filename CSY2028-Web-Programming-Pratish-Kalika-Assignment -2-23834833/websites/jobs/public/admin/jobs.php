<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: /login.php');
    exit();
}

// Database connection
try {
    $pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error reporting
} catch (PDOException $e) {
    // Handle database connection error
    die("Database connection failed: " . $e->getMessage());
}

// Fetch jobs based on user role (admin or staff)
if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff') {
    // Admin and staff can view all jobs
    try {
        $stmt = $pdo->prepare('SELECT * FROM job WHERE archived = 0');
        $stmt->execute();
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle database error
        die("Error fetching jobs: " . $e->getMessage());
    }
} elseif ($_SESSION['role'] == 'client') {
    // Client can only view their own jobs
    try {
        $stmt = $pdo->prepare('SELECT * FROM job WHERE client_id = :client_id AND archived = 0');
        $stmt->execute(['client_id' => $_SESSION['client_id']]);
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle database error
        die("Error fetching jobs: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Job list</title>
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
        <!-- Make the "Jobs" logo clickable -->
        <h1><a href="/admin/index.php" class="logo-link">Jo's Jobs</a></h1>
    </section>

    <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
        <li><a href="logout.php" class="admin-login-button">Log Out</a></li>
    <?php endif; ?>
</header>
<nav>
    <ul>
        <li><a href="/">Home</a></li>
        <li>Jobs
            <ul>
                <li><a href="/it.php">IT</a></li>
                <li><a href="/hr.php">Human Resources</a></li>
                <li><a href="/sales.php">Sales</a></li>
            </ul>
        </li>
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
        <h2>Jobs</h2>
        <?php if ($_SESSION['role'] == 'client'): ?>
            <a class="new" href="addjob.php">Add new job</a>
        <?php endif; ?>
        <a class="view-archived" href="archived_jobs.php" style="float: right;">View Archived Jobs</a>

        <!-- Job Listings -->
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Salary</th>
                    <th>Client_id</th>
                    <th>Edit</th>
                    <th>Delete</th>
                    <th>Archive</th>
                    <th>View Applicants</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($jobs)): ?>
                    <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><?php echo $job['title']; ?></td>
                            <td><?php echo $job['description']; ?></td>
                            <td><?php echo $job['location']; ?></td>
                            <td><?php echo $job['salary']; ?></td>
                            <td><?php echo $job['client_id']; ?></td> <!-- Display client_id -->

                            <td><a href="editjob.php?id=<?php echo $job['id']; ?>">Edit</a></td>
                            <td>
                                <form method="post" action="deletejob.php">
                                    <input type="hidden" name="id" value="<?php echo $job['id']; ?>" />
                                    <input type="submit" name="submit" value="Delete" />
                                </form>
                            </td>
                            <td>
                                <form method="post" action="archive.php"> <!-- Assuming this form handles archiving -->
                                    <input type="hidden" name="id" value="<?php echo $job['id']; ?>" />
                                    <input type="submit" name="submit" value="Archive" />
                                </form>
                            </td>
                            <td><a href="applicants.php?id=<?php echo $job['id']; ?>">View applicants</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9">No jobs found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<footer>
    &copy; Jo's Jobs 2024
</footer>
</body>
</html>
