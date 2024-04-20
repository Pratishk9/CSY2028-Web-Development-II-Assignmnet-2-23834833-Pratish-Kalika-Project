<?php
session_start();
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');

// Fetch clients from the database
$stmtClients = $pdo->query('SELECT * FROM clients');
$clients = $stmtClients->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Client Management</title>
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
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
            <!-- Client Management Section -->
            <h2>Client Management</h2>
            <a href="addclient.php" class="new">Add Client</a>
            <table>
                <thead>
                <tr>
                    <th>Company</th>
                    <th>Contact Person</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?php echo $client['company']; ?></td>
                        <td><?php echo $client['contact_person']; ?></td>
                        <td><?php echo $client['username']; ?></td>
                        <td><?php echo $client['email']; ?></td>
                        <td>
                            <!-- Password field initially hidden -->
                            <span id="password-<?php echo $client['client_id']; ?>" data-type="password" data-plain="<?php echo htmlspecialchars($client['password']); ?>">
                                **********
                            </span>
                            <!-- Toggle button to show/hide password -->
                            <span class="toggle-password" onclick="togglePassword('<?php echo $client['client_id']; ?>')">👁️</span>
                        </td>
                        <td><a href="editclient.php?id=<?php echo $client['client_id']; ?>">Edit</a></td>
                        <td>
                            <form method="post" action="deleteclient.php">
                                <input type="hidden" name="id" value="<?php echo $client['client_id']; ?>" />
                                <input type="submit" name="submit" value="Delete" />
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <!-- Display login form if not logged in -->
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
    function togglePassword(clientId) {
        const passwordSpan = document.getElementById('password-' + clientId);
        const currentType = passwordSpan.getAttribute('data-type');

        if (currentType === 'password') {
            // Show plain text password
            passwordSpan.textContent = passwordSpan.getAttribute('data-plain');
            passwordSpan.setAttribute('data-type', 'text');
        } else {
            // Show masked password
            passwordSpan.textContent = '**********';
            passwordSpan.setAttribute('data-type', 'password');
        }
    }
</script>

</body>
</html>
