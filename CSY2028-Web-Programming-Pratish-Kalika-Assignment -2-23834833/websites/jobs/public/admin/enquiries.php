<?php
ob_start(); // Start output buffering
session_start();

// Database connection
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable PDO exceptions

// Handle assigning enquiry to a staff member (only for admins)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_enquiry']) && $_SESSION['role'] == 'admin') {
    if (isset($_POST['enquiry_id'], $_POST['staff_id'])) {
        $enquiryId = $_POST['enquiry_id'];
        $staffId = $_POST['staff_id'];

        // Update enquiry with the selected staff member
        try {
            $stmt = $pdo->prepare('UPDATE enquiries SET staff_id = ?, status = "Assigned" WHERE id = ?');
            $stmt->execute([$staffId, $enquiryId]);

            // Insert record into assigned_enquiries table
            $assignedAt = date('Y-m-d H:i:s'); // Current timestamp
            $insertStmt = $pdo->prepare('INSERT INTO assigned_enquiries (enquiry_id, staff_id, assigned_at) VALUES (?, ?, ?)');
            $insertStmt->execute([$enquiryId, $staffId, $assignedAt]);

            // Redirect after successful assignment
            header('Location: enquiries.php');
            exit();
        } catch (PDOException $e) {
            die("Error assigning enquiry: " . $e->getMessage());
        }
    } else {
        die("Error: Missing required parameters for enquiry assignment.");
    }
}

// Fetch enquiries based on user role
$enquiries = [];

if ($_SESSION['role'] == 'admin') {
    // For admin, fetch all enquiries with staff details
    $stmt = $pdo->query('SELECT e.*, s.username AS staff_username FROM enquiries e LEFT JOIN staff s ON e.staff_id = s.staff_id ORDER BY e.created_at DESC');
    if ($stmt) {
        $enquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        die("Error fetching enquiries: " . $pdo->errorInfo()[2]);
    }

    // Fetch staff members for dropdown menu
    $staffStmt = $pdo->query('SELECT * FROM staff');
    $staffMembers = $staffStmt->fetchAll(PDO::FETCH_ASSOC);
}
ob_end_flush();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Enquiries</title>
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
                $stmt = $pdo->query('SELECT * FROM category ORDER BY name ASC');
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($categories as $category) {
                    $categoryId = $category['id'];
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
                <li><a href="assignedenquiries.php">Assigned Enquiries</a></li>

            <?php elseif ($_SESSION['role'] == 'client'): ?>
                <!-- Display sidebar for client -->
                <li><a href="jobs.php">Jobs</a></li>
            <?php endif; ?>
        </ul>
</section>
    <section class="right">
        <h1>Enquiries</h1>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Telephone</th>
                <th>Enquiry</th>
                <th>Status</th>
                <th>Assign Staff</th>
            </tr>
            <!-- Inside the table loop -->
            <?php foreach ($enquiries as $enquiry): ?>
                <tr>
                    <td><?= htmlspecialchars($enquiry['first_name'] . ' ' . $enquiry['surname']) ?></td>
                    <td><?= htmlspecialchars($enquiry['email']) ?></td>
                    <td><?= htmlspecialchars($enquiry['telephone']) ?></td>
                    <td><?= htmlspecialchars($enquiry['enquiry']) ?></td>
                    <td><?= isset($enquiry['status']) ? htmlspecialchars($enquiry['status']) : 'Pending' ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="enquiry_id" value="<?= $enquiry['id'] ?>">
                            <select name="staff_id">
                                <option value="">Select Staff</option>
                                <?php foreach ($staffMembers as $staff): ?>
                                    <option value="<?= $staff['staff_id'] ?>"><?= htmlspecialchars($staff['username']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="submit" name="assign_enquiry" value="Assign">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>
</main>
<footer>
    &copy; Jo's Jobs 2024
</footer>
</body>
</html>
