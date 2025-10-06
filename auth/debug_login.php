<?php
require_once '../config/db_config.php';

// This file helps you debug login issues
// Access it directly to check user credentials

$conn = getDBConnection();

// Get all non-student users
$stmt = $conn->prepare("SELECT user_id, email, name, position, user_type, is_active, created_at FROM users WHERE user_type != 'student'");
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Non-Student Users in Database:</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>User ID</th><th>Email</th><th>Name</th><th>Position</th><th>User Type</th><th>Active</th><th>Created</th></tr>";

while ($user = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$user['user_id']}</td>";
    echo "<td>{$user['email']}</td>";
    echo "<td>{$user['name']}</td>";
    echo "<td>{$user['position']}</td>";
    echo "<td>{$user['user_type']}</td>";
    echo "<td>" . ($user['is_active'] ? 'Yes' : 'No') . "</td>";
    echo "<td>{$user['created_at']}</td>";
    echo "</tr>";
}

echo "</table>";

$stmt->close();
$conn->close();
?>

<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; margin: 20px 0; }
    th { background: #3b82f6; color: white; }
    td, th { padding: 10px; text-align: left; }
</style>
