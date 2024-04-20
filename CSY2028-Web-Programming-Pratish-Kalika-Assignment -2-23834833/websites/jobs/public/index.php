<?php
// Database connection
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');

// Fetch categories from the database sorted by name
$stmt = $pdo->query('SELECT * FROM category ORDER BY name');
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$isSearch = isset($_GET['query']) && !empty($_GET['query']);

if ($isSearch) {
    $searchQuery = '%' . $_GET['query'] . '%';
    $stmt = $pdo->prepare('SELECT * FROM job WHERE title LIKE :query OR location LIKE :query');
    $stmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
    $stmt->execute();
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch unique jobs that are going to close soonest and are not archived
$stmt = $pdo->prepare('SELECT DISTINCT j.* FROM job j WHERE j.closingDate >= CURDATE() AND j.archived = 0 ORDER BY j.closingDate ASC LIMIT 5');
$stmt->execute();
$closingSoonJobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Home</title>
    <style>
 /* Style for search form */
 form {
            float: right;
            width: 50%;
            position:absolute;
            left:15rem;
            top:5rem;
            text-align: right;
            margin-top: 20px; /* Add margin-top to move the form down */
        }

        /* Style for search input */
        input[type="text"] {
            padding: 10px;
            width: 70%;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-sizing: border-box;
        }

        /* Style for search button */
        .button1 {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            position: absolute;
            top:1.25rem;
            left:35rem;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        /* Clear float after header section */
        .clear {
            clear: both;
        }
    </style>   
    <script>
        // JavaScript function to scroll to search results section
        function scrollToSearchResults() {
            var searchResultsHeading = document.getElementById('search-results-heading');
            if (searchResultsHeading) {
                searchResultsHeading.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
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
        <h1>Jo's Jobs</h1>

        <!-- Search bar -->
        <form id="search-form" method="GET" action="">
            <input type="text" name="query" placeholder="Search jobs..." value="<?= isset($_GET['query']) ? $_GET['query'] : '' ?>">
            <button class="button1" type="submit">Search</button>
        </form>

    </section>
    <!-- Add the button to go to admin login page -->
    <a href="/admin/" class="admin-login-button">Login</a>
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
        <li><a href="/contactpage.php">Contact Page</a></li>
    </ul>
</nav>

<main class="home">
    <p>Welcome to Jo's Jobs, we're a recruitment agency based in Northampton. We offer a range of different office jobs. Get in touch if you'd like to list a job with us.</p>
    <h2>Select the type of job you are looking for:</h2>
    <ul>
        <?php foreach ($categories as $category): ?>
            <li><a href="<?= strtolower($category['name']) ?>.php"><?= $category['name'] ?></a></li>
        <?php endforeach; ?>
    </ul>

    <!-- Display search results if available -->
    <?php if ($isSearch): ?>
        <script>
            // Scroll to search results on page load when a search is performed
            window.onload = function() {
                scrollToSearchResults();
            };
        </script>
        <h2 id="search-results-heading">Search Results</h2>
        <ul>
            <?php if (empty($searchResults)): ?>
                <li>No jobs found matching your search.</li>
            <?php else: ?>
                <?php foreach ($searchResults as $job): ?>
                    <li class="job">
                        <h3><?= htmlspecialchars($job['title']) ?></h3>
                        <p><strong>Job overview:</strong> <?= htmlspecialchars($job['description']) ?></p>
                        <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary']) ?></p>
                        <p><strong>Closing Date:</strong> <?= htmlspecialchars($job['closingDate']) ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
                        <button class="apply-button"><a href="apply.php?job_id=<?= $job['id'] ?>">Apply</a></button>
                    </li>
                    <hr> <!-- Horizontal line after each job -->
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    <?php endif; ?>

    <h2>Jobs Closing Soon</h2>
    <ul>
        <?php foreach ($closingSoonJobs as $job): ?>
            <li class="job">
                <h3><?= $job['title'] ?></h3>
                <p><strong>Job overview:</strong> <?= $job['description'] ?></p>
                <p><strong>Salary:</strong> <?= $job['salary'] ?></p>
                <p><strong>Closing Date:</strong> <?= $job['closingDate'] ?></p>
                <p><strong>Location:</strong> <?= $job['location'] ?></p>
                <button class="apply-button"><a href="apply.php?job_id=<?= $job['id'] ?>">Apply</a></button>
            </li>
            <hr> <!-- Horizontal line after each job -->
        <?php endforeach; ?>
    </ul>
</main>
<footer>
    &copy; Jo's Jobs 2024
</footer>
</body>
</html>
