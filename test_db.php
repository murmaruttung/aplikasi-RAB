<?php
// test_db.php - Test database connection
require_once 'config/database.php';

try {
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengguna");
    $data = mysqli_fetch_assoc($result);
    echo "✅ Database connection successful!\n";
    echo "Total users: " . $data['total'] . "\n";

    $result = mysqli_query($conn, "SHOW TABLES");
    echo "Tables in database:\n";
    while ($row = mysqli_fetch_array($result)) {
        echo "- " . $row[0] . "\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>