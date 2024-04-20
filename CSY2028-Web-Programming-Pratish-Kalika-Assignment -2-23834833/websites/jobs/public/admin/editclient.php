<?php
session_start();
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');

// Check if the form was submitted
if (isset($_POST['submit'])) {
    // Retrieve form data
    $clientId = $_POST['id'];
    $company = $_POST['company'];
    $contactPerson = $_POST['contact_person'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password']; // Plain text password

    // Prepare SQL statement to update client data
    $stmt = $pdo->prepare('UPDATE clients SET company = :company, contact_person = :contact_person, username = :username, email = :email, phone = :phone, address = :address WHERE client_id = :clientId');
    $stmt->execute([
        'company' => $company,
        'contact_person' => $contactPerson,
        'username' => $username,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'clientId' => $clientId // Use 'clientId' to match the parameter in the SQL query
    ]);

    // Check if new password is provided and not empty
    if (!empty($password)) {
        // Update password in the database (without hashing)
        $stmtUpdatePassword = $pdo->prepare('UPDATE clients SET password = :password WHERE client_id = :clientId');
        $stmtUpdatePassword->execute(['password' => $password, 'clientId' => $clientId]);
    }

    // Redirect to client.php after successful update
    header('Location: client.php');
    exit(); // Stop script execution after redirection
}

// Check if client ID is provided in URL
if (isset($_GET['id'])) {
    $clientId = $_GET['id'];

    // Fetch client details from the database
    $stmt = $pdo->prepare('SELECT * FROM clients WHERE client_id = :clientId');
    $stmt->execute(['clientId' => $clientId]); // Use 'clientId' to match the parameter in the SQL query
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        // Client not found
        echo 'Client not found.';
        exit(); // Stop script execution
    }
} else {
    // Client ID not provided
    echo 'Client ID not specified.';
    exit(); // Stop script execution
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Edit Client</title>
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
            <li><a href="jobs.php">Jobs</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="staff.php">Staff</a></li>
            <li><a href="client.php">Client</a></li>
        </ul>
    </section>
    <section class="right">
        <h2>Edit Client</h2>
        <form action="editclient.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $client['client_id']; ?>" />
            
            <label>Company Name</label>
            <input type="text" name="company" value="<?php echo $client['company']; ?>" required />

            <label>Contact Person</label>
            <input type="text" name="contact_person" value="<?php echo $client['contact_person']; ?>" required />

            <label>Username</label>
            <input type="text" name="username" value="<?php echo $client['username']; ?>" required />

            <label>Email</label>
            <input type="email" name="email" value="<?php echo $client['email']; ?>" required />

            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo $client['phone']; ?>" />

            <label>Address</label>
            <textarea name="address" rows="4" required><?php echo $client['address']; ?></textarea>

            <label>Password</label>
            <input type="text" name="password" value="" /> <!-- Display plain text password field -->

            <input type="submit" name="submit" value="Update" />
        </form>
    </section>
</main>
<footer>
    &copy; Jo's Jobs 2024
</footer>
</body>
</html>
