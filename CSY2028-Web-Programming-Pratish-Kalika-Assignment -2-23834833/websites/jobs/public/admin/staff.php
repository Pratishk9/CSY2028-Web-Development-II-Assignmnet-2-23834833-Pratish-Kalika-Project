<?php
session_start();
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');

// Fetch users from the database
$stmtUsers = $pdo->query('SELECT * FROM staff');
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Staff Management</title>
    <style>
        /* Style for the toggle password button */
        .toggle-password {
            cursor: pointer;
            margin-left: 5px;
        }
    </style>
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
                <li><a href="it.php">IT</a></li>
                <li><a href="hr.php">Human Resources</a></li>
                <li><a href="sales.php">Sales</a></li>
            </ul>
        </li>
        <li><a href="/about.html">About Us</a></li>
        <li><a href="/careeradvice.php">Career Advice</a></li>
    </ul>
</nav>
<img src="/images/randombanner.php"/>
<main class="sidebar">
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
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
            <!-- User Management Section -->
            <h2>Staff Users</h2>
            <a href="addstaff.php" class="new">Add Staff</a>
            <table>
                <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['full_name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <!-- Password field initially hidden -->
                            <span id="password-<?php echo $user['staff_id']; ?>">
                                **********
                            </span>
                            <!-- Toggle button to show/hide password -->
                            <span class="toggle-password" onclick="togglePassword('<?php echo $user['staff_id']; ?>')">👁️</span>
                        </td>
                        <td><a href="editstaff.php?id=<?php echo $user['staff_id']; ?>">Edit</a></td>
                        <td>
                            <form method="post" action="deletestaff.php">
                                <input type="hidden" name="id" value="<?php echo $user['staff_id']; ?>" />
                                <input type="submit" name="submit" value="Delete" />
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <h2>Log in</h2>
            <form action="index.php" method="post">
                <label>Password</label>
                <input type="password" name="password"/>
                <input type="submit" name="submit" value="Log In"/>
            </form>
        <?php endif; ?>
    </section>
</main>
<footer>
    &copy; Jo's Jobs 2024
</footer>

<!-- JavaScript to toggle password visibility -->
<script>
    function togglePassword(userId) {
        const passwordSpan = document.getElementById('password-' + userId);
        const currentType = passwordSpan.getAttribute('data-type');

        if (currentType === 'password') {
            passwordSpan.textContent = '**********'; // Show asterisks
            passwordSpan.setAttribute('data-type', 'hidden');
        } else {
            passwordSpan.textContent = '<?php echo htmlspecialchars($user['password']); ?>'; // Show actual password
            passwordSpan.setAttribute('data-type', 'password');
        }
    }
</script>

</body>
</html>
