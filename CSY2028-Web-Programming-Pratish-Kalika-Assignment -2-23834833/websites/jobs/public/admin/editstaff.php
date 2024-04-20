<?php
session_start();
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');

// Fetch categories from the database sorted by name
$stmt = $pdo->query('SELECT * FROM category ORDER BY name');
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Fetch the user details from the database based on the provided ID
    $stmt = $pdo->prepare('SELECT staff_id, username, full_name, email FROM staff WHERE staff_id = :id'); // Use 'staff_id' instead of 'id'
    $stmt->execute(['id' => $userId]); // Use 'id' as the key for the parameter binding
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Process form submission
        $updatedUsername = $_POST['username'];
        $updatedFullName = $_POST['full_name'];
        $updatedEmail = $_POST['email'];
        $updatedPassword = $_POST['password']; // Updated password

        // Update user details in the database (including password)
        $stmt = $pdo->prepare('UPDATE staff SET username = :username, full_name = :full_name, email = :email, password = :password WHERE staff_id = :id'); // Use 'staff_id' for WHERE condition
        $stmt->execute([
            'username' => $updatedUsername,
            'full_name' => $updatedFullName,
            'email' => $updatedEmail,
            'password' => $updatedPassword, // Store password securely (e.g., hashed)
            'id' => $userId // Use 'id' to bind the parameter for WHERE condition
        ]);

        // Redirect to staff.php after successful update
        header('Location: staff.php');
        exit;
    }
} else {
    echo "User ID not provided";
    exit;
}

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
                <?php foreach ($categories as $category): ?>
                    <li><a href="<?= strtolower($category['name']) ?>.php"><?= $category['name'] ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li><a href="/about.html">About Us</a></li>
        <li><a href="/careeradvice.php">Career Advice</a></li>
    </ul>
</nav>
<img src="/images/randombanner.php"/>

<main class="content">
    <h2>Edit User</h2>
    <form method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br><br>

        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

        <!-- Display and edit password field -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" value="" placeholder="Enter new password"><br><br>

        <input type="submit" name="submit" value="Update">
    </form>
</main>

<footer>
    &copy; Jo's Jobs 2024
</footer>

</body>
</html>
