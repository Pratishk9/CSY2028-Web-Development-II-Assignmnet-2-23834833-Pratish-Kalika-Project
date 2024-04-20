<?php
session_start();
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');

// Function to create a PHP file for a category
function createCategoryPage($categoryName, $categoryId) {
    $filename = strtolower(str_replace(' ', '', $categoryName)) . '.php';
    $fileContent = <<<PHP
<?php
\$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');

\$stmt = \$pdo->prepare('SELECT * FROM job WHERE categoryId = :categoryId AND archived = 0');
\$stmt->execute(['categoryId' => $categoryId]);
\$jobs = \$stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - $categoryName</title>
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
    <?php if(isset(\$_SESSION['loggedin']) && \$_SESSION['loggedin'] == true): ?>
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
                \$stmt = \$pdo->query('SELECT * FROM category ORDER BY name ASC');
                \$categories = \$stmt->fetchAll(PDO::FETCH_ASSOC);

                // Display each category as a list item with a link to its respective page
                foreach (\$categories as \$category) {
                    \$categoryId = \$category['id']; // Assuming 'id' is the primary key column
                    \$categoryName = \$category['name'];
                    \$categorySlug = strtolower(str_replace(' ', '', \$categoryName)) . '.php';
                    echo "<li><a href=\"\$categorySlug\">\$categoryName</a></li>";
                }
            ?>
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
            // Fetch all categories from the database ordered alphabetically
            \$stmt = \$pdo->query('SELECT * FROM category ORDER BY name ASC');
            \$categories = \$stmt->fetchAll(PDO::FETCH_ASSOC);

            // Display each category as a list item with a link to its respective page
            foreach (\$categories as \$category) {
                \$categoryName = \$category['name'];
                \$categorySlug = strtolower(str_replace(' ', '', \$categoryName)) . '.php';
                echo "<li><a href=\"\$categorySlug\">\$categoryName</a></li>";
            }
            ?>
        </ul>
    </section>

    <section class="right">
        <h1>Jobs</h1>
        <ul class="listing">
            <?php
            // Fetch all jobs for the specific category
            \$stmt = \$pdo->prepare('SELECT * FROM job WHERE categoryId = :categoryId AND archived = 0');
            \$stmt->execute(['categoryId' => $categoryId]); // Use the $categoryId passed to the function

            
            // This is addcategory.php
            foreach (\$jobs as \$job) {
                echo "<li class=\"job\">";
                echo "<h2>{\$job['title']}</h2>";
                echo "<p><strong>Job overview:</strong> {\$job['description']}</p>";
                echo "<p><strong>Salary:</strong> {\$job['salary']}</p>";
                echo "<p><strong>Closing Date:</strong> {\$job['closingDate']}</p>";
                echo "<p><strong>Location:</strong> {\$job['location']}</p>";
                echo "<button class=\"apply-button\"><a href=\"apply.php?job_id={\$job['id']}\">Apply</a></button>";
                echo "</li>";
            }
            
            
            
            ?>
            
        </ul>
    </section>
</main>

<footer>
    &copy; Jo's Jobs 2024
</footer>
</body>
</html>
PHP;

    // Save the content to the new PHP file inside the public directory
    file_put_contents(__DIR__ . "/../$filename", $fileContent);
}


// Check if user is logged in
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: /admin/index.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $categoryName = $_POST['name'];

    // Insert the new category into the database
    $stmt = $pdo->prepare('INSERT INTO category (name) VALUES (:name)');
    $stmt->execute(['name' => $categoryName]);

    // Get the ID of the newly inserted category
    $categoryId = $pdo->lastInsertId();

    // Create a new PHP file for the category
    createCategoryPage($categoryName, $categoryId);

    // Redirect back to admin home page
    header("Location: /admin/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Add Category</title>
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
        </ul>
    </section>
    <section class="right">
        <?php
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            ?>
            <h2>Add Category</h2>
            <form action="" method="POST">
                <label>Name</label>
                <input type="text" name="name" />
                <input type="submit" value="Add Category" />
            </form>
            <?php
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
