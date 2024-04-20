<?php
ob_start(); // Start output buffering
session_start();

// Database connection
$pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable PDO exceptions

// Handle form submission to assign enquiry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_enquiry'])) {
    if (isset($_POST['enquiry_id'], $_POST['staff_id'])) {
        $enquiryId = $_POST['enquiry_id'];
        $staffId = $_POST['staff_id'];

        // Update the enquiry with the selected staff member
        $stmt = $pdo->prepare('UPDATE enquiries SET staff_id = ?, status = "Assigned" WHERE id = ?');
        $stmt->execute([$staffId, $enquiryId]);

        // Insert record into assigned_enquiries table
        $insertStmt = $pdo->prepare('INSERT INTO assigned_enquiries (enquiry_id, staff_id, assigned_at) VALUES (?, ?, NOW())');
        $insertStmt->execute([$enquiryId, $staffId]);

        // Redirect after successful assignment
        header('Location: assignedenquiries.php');
        exit();
    } else {
        die("Error: Missing required parameters for enquiry assignment.");
    }
}

// Handle form submission to mark enquiry as completed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['handle_enquiry'])) {
    if (isset($_POST['enquiry_id'])) {
        $enquiryId = $_POST['enquiry_id'];

        // Update the enquiry status to "Completed"
        $stmt = $pdo->prepare('UPDATE enquiries SET status = "Completed" WHERE id = ?');
        $stmt->execute([$enquiryId]);

        // Redirect after successful completion
        header('Location: assignedenquiries.php');
        exit();
    } else {
        die("Error: Missing required parameter for completing the enquiry.");
    }
}

// Fetch assigned enquiries for the current logged-in staff member
$assignedEnquiries = [];
$staffId = $_SESSION['staff_id']; // Assuming staff_id is stored in session upon login
$stmt = $pdo->prepare('SELECT ae.*, e.first_name, e.surname, e.email, e.telephone, e.enquiry, e.status
                       FROM assigned_enquiries ae
                       JOIN enquiries e ON ae.enquiry_id = e.id
                       WHERE ae.staff_id = ?
                       ORDER BY ae.assigned_at DESC');
$stmt->execute([$staffId]);
$assignedEnquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_end_flush();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Assigned Enquiries</title>
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
        <h1><a href="/admin/index.php" class="logo-link">Jo's Jobs</a></h1>
    </section>
    <li><a href="logout.php" class="admin-login-button">Log Out</a></li>
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
        <h2>Assigned Enquiries</h2>
        <?php if (empty($assignedEnquiries)): ?>
            <p>No enquiries have been assigned to you.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Telephone</th>
                    <th>Enquiry</th>
                    <th>Assigned At</th>
                    <th>Handle</th>
                </tr>
                <?php foreach ($assignedEnquiries as $assigned): ?>
                    <tr>
                        <td><?= htmlspecialchars($assigned['first_name'] . ' ' . $assigned['surname']) ?></td>
                        <td><?= htmlspecialchars($assigned['email']) ?></td>
                        <td><?= htmlspecialchars($assigned['telephone']) ?></td>
                        <td><?= htmlspecialchars($assigned['enquiry']) ?></td>
                        <td><?= htmlspecialchars($assigned['assigned_at']) ?></td>
                        <td>
                            <?php if ($assigned['status'] == 'Assigned'): ?>
                                <form method="post">
                                    <input type="hidden" name="enquiry_id" value="<?= $assigned['enquiry_id'] ?>">
                                    <input type="submit" name="handle_enquiry" value="Handle">
                                </form>
                            <?php else: ?>
                                Completed
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </section>
</main>
<footer>
    &copy; Jo's Jobs 2024
</footer>
</body>
</html>
