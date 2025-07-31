<?php
if (!isset($_SESSION['emailaddress'])) {
    header('Location: login.php');
    exit();
}

// Get user's first name for greeting
require_once 'db_connect.php';
$email = $_SESSION['emailaddress'];
$query = $dbconnect->prepare("SELECT firstname FROM user WHERE emailaddress = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$name = $result->fetch_assoc()['firstname'] ?? 'User';
?>
<body>
<div class="header-bar">
    <h2><span>👋 Welcome, <?= htmlspecialchars($name) ?></span></h2>
    <nav>
        <a href="profile.php">🧑 View Profile</a>
        <a href="logout.php">🚪 Logout</a>
    </nav>
</div>

<style>
.header-bar {
    background-color: #333;
    color: white;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.header-bar a {
    color: white;
    margin-left: 20px;
    text-decoration: none;
}
.header-bar a:hover {
    text-decoration: underline;
}
</style>
</body>