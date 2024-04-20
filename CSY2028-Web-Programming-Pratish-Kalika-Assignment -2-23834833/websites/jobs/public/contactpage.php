<?php
session_start();

// Database connection
try {
    $pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable PDO exceptions
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['first_name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $enquiry = $_POST['enquiry'];

    try {
        // Insert enquiry into database
        $stmt = $pdo->prepare('INSERT INTO enquiries (first_name, surname, email, telephone, enquiry) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$firstName, $surname, $email, $telephone, $enquiry]);
        
        // Redirect after successful submission (optional)
        header("Location: thank_you_page.php");
        exit();
    } catch (PDOException $e) {
        die("Error inserting enquiry: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Contact Us</title>
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
        <h1>Jo's Jobs</h1>
    </section>
    <!-- Add a logout button -->
    <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
        <li><a href="logout.php" class="admin-login-button">Log Out</a></li>
    <?php endif; ?>
</header>
<nav>
    <ul>
        <li><a href="/">Home</a></li>
        <li>Jobs
        <ul>
            <?php
                // Fetch all categories from the database and sort alphabetically
                $stmt = $pdo->query('SELECT * FROM category ORDER BY name ASC');
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Display each category as a list item with a link to its respective page
                foreach ($categories as $category) {
                    $categoryId = $category['id']; // Assuming 'id' is the primary key column
                    $categoryName = $category['name'];
                    $categorySlug = strtolower(str_replace(' ', '', $categoryName)) . '.php';
                    echo "<li><a href=\"$categorySlug\">$categoryName</a></li>";
                }
            ?>
        </ul>
        </li>
        <li><a href="/about.html">About Us</a></li>
    </ul>
</nav>
<main class="sidebar">
    <h1>Contact Us</h1>
    <form method="post">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br><br>
        <label for="surname">Surname:</label>
        <input type="text" id="surname" name="surname" required><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="telephone">Telephone:</label>
        <input type="tel" id="telephone" name="telephone" required><br><br>
        <label for="enquiry">Enquiry:</label><br>
        <textarea id="enquiry" name="enquiry" rows="4" required></textarea><br><br>
        <input type="submit" value="Submit">
    </form>
</main>

<footer>
    &copy; Jo's Jobs 2024
</footer>

</body>
</html>
