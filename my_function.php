
<?php
require 'db_connect.php';
function sanitize($value): array|string {
    global $dbconnect;

    if (is_array($value)) {
        return array_map(fn($v) => mysqli_real_escape_string($dbconnect, trim($v)), $value);
    }

    return mysqli_real_escape_string($dbconnect, trim($value));
}
