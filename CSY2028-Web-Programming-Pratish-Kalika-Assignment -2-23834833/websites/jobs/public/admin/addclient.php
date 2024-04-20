<?php
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    if (isset($_POST['submit'])) {
        // Retrieve form data
        $company = $_POST['company'];
        $contactPerson = $_POST['contact_person'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $username = $_POST['username'];
        $password = $_POST['password']; // Retrieve plaintext password

        // Prepare SQL query to insert client data into database
        $stmt = $pdo->prepare('INSERT INTO clients (company, contact_person, email, phone, address, username, password) VALUES (:company, :contact_person, :email, :phone, :address, :username, :password)');
        
        // Bind parameters and execute the query
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':contact_person', $contactPerson);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password); // Store plaintext password
        
        // Execute the prepared statement
        if ($stmt->execute()) {
            // Redirect to client.php after successful client addition
            header('Location: client.php');
            exit(); // Ensure script stops here to perform the redirect
        } else {
            // Handle database insertion error
            echo "Failed to add client.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Add Client</title>
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
        <h2>Add Client</h2>
        <form action="addclient.php" method="POST">
            <label>Company Name</label>
            <input type="text" name="company" required />

            <label>Contact Person</label>
            <input type="text" name="contact_person" required />

            <label>Username</label>
            <input type="text" name="username" required />

            <label>Password</label>
            <input type="password" name="password" required />

            <label>Email</label>
            <input type="email" name="email" required />

            <label>Phone</label>
            <input type="text" name="phone" />

            <label>Address</label>
            <textarea name="address" rows="4"></textarea>

            <input type="submit" name="submit" value="Add" />
        </form>
    </section>
</main>
<footer>
    &copy; Jo's Jobs 2024
</footer>
</body>
</html>
