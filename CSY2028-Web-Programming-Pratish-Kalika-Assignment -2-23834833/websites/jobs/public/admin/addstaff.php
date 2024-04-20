<?php
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Add Staff</title>
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
            <li><a href="jobs.php">Jobs</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="staff.php">Staff</a></li>

        </ul>
    </section>
    <section class="right">
        <?php
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            if (isset($_POST['submit'])) {
                $stmt = $pdo->prepare('INSERT INTO staff (username, password, full_name, email) VALUES (:username, :password, :full_name, :email)');
                $criteria = [
                    'username' => $_POST['username'],
                    'password' => $_POST['password'], // NOTE: You should hash this password for security
                    'full_name' => $_POST['full_name'],
                    'email' => $_POST['email']
                ];
                $stmt->execute($criteria);
                echo 'Staff Added';
            } else {
                ?>
                <h2>Add Staff</h2>
                <form action="addstaff.php" method="POST">
                    <label>Username</label>
                    <input type="text" name="username" />

                    <label>Password</label>
                    <input type="password" name="password" />

                    <label>Full Name</label>
                    <input type="text" name="full_name" />

                    <label>Email</label>
                    <input type="email" name="email" />

                    <input type="submit" name="submit" value="Add" />
                </form>
                <?php
            }
        } else {
            ?>
            <h2>Log in</h2>
            <form action="index.php" method="post">
                <label>Password</label>
                <input type="password" name="password" />
                <input type="submit" name="submit" value="Log In" />
            </form>
            <?php
        }
        ?>
    </section>
</main>
<footer>
    &copy; Jo's Jobs 2024
</footer>
</body>
</html>
