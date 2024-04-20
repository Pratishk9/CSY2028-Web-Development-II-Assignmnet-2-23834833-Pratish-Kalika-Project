<?php
session_start();

// Database connection
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');

// Fetch categories from the database sorted by name
$stmt = $pdo->query('SELECT * FROM category ORDER BY name');
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Flag to track invalid login attempt
$invalidLogin = false;

// Check login
if (isset($_POST['login_submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL query to check credentials for staff
    $sql_staff = "SELECT * FROM staff WHERE username = ? AND password = ?";
    $stmt_staff = $pdo->prepare($sql_staff);
    $stmt_staff->execute([$username, $password]);
    $staff = $stmt_staff->fetch(PDO::FETCH_ASSOC);

    if ($staff) {
        // Staff login successful, set session and redirect to staff dashboard
        $_SESSION['loggedin'] = true;
        $_SESSION['role'] = 'staff';
        $_SESSION['username'] = $staff['username'];
        $_SESSION['staff_id'] = $staff['staff_id']; // Store staff_id in session

        header('Location: /admin/index.php'); // Redirect to staff dashboard
        exit;
    } else {
        // Handle invalid login
        $invalidLogin = true;
    }
}

// Check admin login
if (isset($_POST['admin_submit']) && $_POST['admin_password'] == 'letmein') {
    $_SESSION['loggedin'] = true;
    $_SESSION['role'] = 'admin';
    header('Location: /admin/index.php'); // Redirect to admin dashboard
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Login</title>
    <script>
        // JavaScript function to display alert for invalid login
        <?php if ($invalidLogin): ?>
        window.onload = function() {
            alert("Invalid credentials. Please try again.");
        };
        <?php endif; ?>
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
    <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
        <li><a href="logout.php" class="admin-login-button">Log Out</a></li>
    <?php endif; ?>
</header>
<nav>
    <ul>
        <li><a href="/">Home</a></li>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
            <li>Jobs
                <ul>
                    <?php foreach ($categories as $category): ?>
                        <li><a href="<?= strtolower($category['name']) ?>.php"><?= $category['name'] ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li><a href="/about.html">About Us</a></li>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <li><a href="/careeradvice.php">Career Advice</a></li>
            <?php endif; ?>
        <?php else: ?>
            <!-- Display default links for non-logged-in users -->
            <li><a href="/about.html">About Us</a></li>
            <li><a href="/careeradvice.php">Career Advice</a></li>
        <?php endif; ?>
    </ul>
</nav>
<img src="/images/randombanner.php"/>
<main class="sidebar">
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <!-- Display sidebar for admin -->
            <section class="left">
                <ul>
                    <li><a href="jobs.php">Jobs</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="staff.php">Staff</a></li>
                    <li><a href="client.php">Client</a></li>
                    <li><a href="enquiries.php">Enquiries</a></li>
                </ul>
            </section>
            <section class="right">
                <h2>You are now logged in as Admin</h2>
            </section>
        <?php elseif ($_SESSION['role'] == 'staff'): ?>
            <!-- Display sidebar for staff -->
            <section class="left">
                <ul>
                    <li><a href="jobs.php">Jobs</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="client.php">Client</a></li>
                    <li><a href="assignedenquiries.php">Assigned Enquiries</a></li>
                </ul>
            </section>
            <section class="right">
                <h2>You are now logged in as Staff (<?php echo $_SESSION['username']; ?>)</h2>
                <?php if (isset($_SESSION['staff_id'])): ?>
                    <p>Staff ID: <?php echo $_SESSION['staff_id']; ?></p> <!-- Display Staff ID -->
                <?php endif; ?>
            </section>
        <?php elseif ($_SESSION['role'] == 'client'): ?>
            <!-- Display sidebar for client -->
            <section class="left">
                <ul>
                    <li><a href="jobs.php">Jobs</a></li>
                </ul>
            </section>
            <section class="right">
                <h2>You are now logged in as Client (<?php echo $_SESSION['username']; ?>)</h2>
                <?php if (isset($_SESSION['client_id'])): ?>
                    <p>Client ID: <?php echo $_SESSION['client_id']; ?></p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    <?php else: ?>
        <h2>Log in</h2>
        <!-- Admin Login Form -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="padding: 40px">
            <label>Admin Password</label>
            <input type="password" name="admin_password" />
            <input type="submit" name="admin_submit" value="Log In as Admin" />
        </form>
        <!-- Login Form -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="padding: 40px">
            <label>Username</label>
            <input type="text" name="username" />
            <label>Password</label>
            <input type="password" name="password" />
            <input type="submit" name="login_submit" value="Log In" />
        </form>
    <?php endif; ?>
</main>
<footer>
    &copy; Jo's Jobs 2024
</footer>
</body>
</html>
