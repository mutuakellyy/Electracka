<?php
require 'db_connect.php';
session_start();

if (!isset($_SESSION['institution_id'])) {
    exit('Unauthorized');
}

$institution_id = $_SESSION['institution_id'];
$sql = "SELECT * FROM supervisor_report WHERE institution_id = '$institution_id' ORDER BY date_created DESC LIMIT 5";
$res = mysqli_query($dbconnect, $sql);

echo "<table><tr><th>Title</th><th>Details</th><th>Date</th></tr>";
while ($r = mysqli_fetch_assoc($res)) {
    echo "<tr><td>{$r['title']}</td><td>{$r['details']}</td><td>{$r['date_created']}</td></tr>";
}
echo "</table>";