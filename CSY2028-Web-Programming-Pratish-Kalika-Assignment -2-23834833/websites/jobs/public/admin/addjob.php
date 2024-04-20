<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: /login.php'); // Redirect to login page if not logged in
    exit();
}

// Set the role based on the user's session
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Database connection
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');

// Handle form submission to add a job
if (isset($_POST['submit'])) {
    $stmt = $pdo->prepare('INSERT INTO job (title, description, salary, location, closingDate, categoryId, client_id)
                           VALUES (:title, :description, :salary, :location, :closingDate, :categoryId, :client_id)');

    $criteria = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'salary' => $_POST['salary'],
        'location' => $_POST['location'],
        'categoryId' => $_POST['categoryId'],
        'closingDate' => $_POST['closingDate'],
        'client_id' => $_SESSION['client_id'] // Use client_id of the currently logged-in client
    ];

    $stmt->execute($criteria);

    echo 'Job Added';
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Add Job</title>
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
            <?php
            // Display sidebar links based on user role
            if ($role == 'admin') {
                echo '<li><a href="jobs.php">Jobs</a></li>';
                echo '<li><a href="categories.php">Categories</a></li>';
                echo '<li><a href="staff.php">Staff</a></li>';
                echo '<li><a href="client.php">Client</a></li>';
            } elseif ($role == 'staff') {
                echo '<li><a href="jobs.php">Jobs</a></li>';
                echo '<li><a href="categories.php">Categories</a></li>';
                echo '<li><a href="client.php">Client</a></li>';
            } elseif ($role == 'client') {
                echo '<li><a href="jobs.php">Jobs</a></li>';
            }
            ?>
        </ul>
    </section>

    <section class="right">
        <h2>Add Job</h2>
        <form action="addjob.php" method="POST">
            <label>Title</label>
            <input type="text" name="title" />

            <label>Description</label>
            <textarea name="description"></textarea>

            <label>Salary</label>
            <input type="text" name="salary" />

            <label>Location</label>
            <input type="text" name="location" />

            <label>Category</label>
            <select name="categoryId">
                <?php
                // Fetch categories from database and populate dropdown
                $stmt = $pdo->prepare('SELECT * FROM category');
                $stmt->execute();

                foreach ($stmt as $row) {
                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                }
                ?>
            </select>

            <label>Closing Date</label>
            <input type="date" name="closingDate" />

            <input type="submit" name="submit" value="Add" />
        </form>
    </section>
</main>

<footer>
    &copy; Jo's Jobs 2024
</footer>
</body>
</html>
