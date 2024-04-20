<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Career Advice</title>
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
    <!-- Add the button to go to admin login page -->
    <a href="/admin/" class="admin-login-button">Admin Login</a>
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
<img src="images/randombanner.php"/>
<main class="career-advice">
    <section>
        <h2>Welcome to Jo's Jobs Career Advice!</h2>
        <p>Embarking on a successful career journey requires more than just qualifications; it requires guidance, strategy, and ongoing support. At Jo's Jobs, we're committed to providing you with the tools and resources you need to navigate the ever-evolving job market and achieve your professional goals.</p>
        <p>Our Career Advice section is your go-to destination for insightful articles, practical tips, and expert guidance across all stages of your career development. Whether you're a recent graduate exploring your first job opportunities, a mid-career professional seeking advancement, or a seasoned executive contemplating a career pivot, we have tailored content to address your needs.</p>
        <h3>What You'll Discover:</h3>
        <ul>
            <li><strong>Job Search Strategies:</strong> Master the art of job hunting with expert advice on crafting winning resumes, writing compelling cover letters, acing job interviews, and leveraging networking opportunities.</li>
            <li><strong>Career Development:</strong> Take proactive steps towards your career growth with insights on identifying your strengths, setting achievable career goals, pursuing further education or certifications, and staying relevant in a rapidly changing workforce.</li>
            <li><strong>Workplace Success:</strong> Learn essential skills for thriving in any workplace environment, from effective communication and conflict resolution to time management and stress management techniques.</li>
            <li><strong>Industry Insights:</strong> Stay informed about the latest trends, innovations, and emerging opportunities in your industry or field of interest, and position yourself as a thought leader by staying ahead of the curve.</li>
            <li><strong>Personal Branding:</strong> Cultivate a strong personal brand that reflects your unique skills, values, and aspirations, and learn how to effectively market yourself to potential employers or clients both online and offline.</li>
            <li><strong>Entrepreneurship:</strong> Explore the world of entrepreneurship with practical advice on starting and growing your own business, navigating the challenges of entrepreneurship, and achieving sustainable success as an entrepreneur.</li>
        </ul>
        <p>Our team of career experts, industry professionals, and seasoned mentors is dedicated to empowering you with the knowledge, skills, and confidence needed to make informed career decisions and build a fulfilling and rewarding career path.</p>
        <p>Whether you're just starting out on your career journey or looking to take the next big leap in your professional life, Jo's Jobs is here to support you every step of the way. Let's embark on this journey together and unlock the doors to your future success!</p>
    </section>
</main>
<footer>
    &copy; Jo's Jobs 2024
</footer>
</body>
</html>
