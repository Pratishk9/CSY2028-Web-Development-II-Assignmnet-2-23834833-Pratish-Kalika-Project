<?php
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Categories</title>
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
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <!-- Display sidebar for admin -->
            <section class="left">
                <ul>
                    <li><a href="jobs.php">Jobs</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="staff.php">Staff</a></li>
                    <li><a href="client.php">Client</a></li>
                </ul>
            </section>
        <?php elseif ($_SESSION['role'] == 'staff'): ?>
            <!-- Display sidebar for staff -->
            <section class="left">
                <ul>
                    <li><a href="jobs.php">Jobs</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="client.php">Client</a></li>

                </ul>
            </section>
        <?php endif; ?>
        <section class="right">
            <?php
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
                echo '<h2>Categories</h2>';
                echo '<a class="new" href="addcategory.php">Add new category</a>';

                echo '<table>';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Name</th>';
                echo '<th style="width: 5%">&nbsp;</th>';
                echo '<th style="width: 5%">&nbsp;</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                $categories = $pdo->query('SELECT * FROM category');

                foreach ($categories as $category) {
                    echo '<tr>';
                    echo '<td>' . $category['name'] . '</td>';
                    echo '<td><a style="float: right" href="editcategory.php?id=' . $category['id'] . '">Edit</a></td>';
                    echo '<td><form method="post" action="deletecategory.php">';
                    echo '<input type="hidden" name="id" value="' . $category['id'] . '" />';
                    echo '<input type="submit" name="submit" value="Delete" />';
                    echo '</form></td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<h2>Log in</h2>';
                echo '<form action="index.php" method="post">';
                echo '<label>Password</label>';
                echo '<input type="password" name="password" />';
                echo '<input type="submit" name="submit" value="Log In" />';
                echo '</form>';
            }
            ?>
        </section>
    <?php endif; ?>
</main>

<footer>
    &copy; Jo's Jobs 2024
</footer>
</body>
</html>