<?php
/**
 * Database Migration - Add Driver Location Tracking
 * Run this script once to add the location fields to the drivers table
 */

require_once __DIR__ . '/core/config.php';

try {
    // Add columns to drivers table
    $queries = [
        "ALTER TABLE drivers ADD COLUMN latitude DECIMAL(10, 8) DEFAULT 0 AFTER status",
        "ALTER TABLE drivers ADD COLUMN longitude DECIMAL(11, 8) DEFAULT 0 AFTER latitude", 
        "ALTER TABLE drivers ADD COLUMN last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER longitude",
    ];

    foreach ($queries as $query) {
        try {
            $pdo->exec($query);
            echo "✓ " . substr($query, 0, 50) . "... <br>";
        } catch (PDOException $e) {
            // Column might already exist, that's okay
            if (strpos($e->getMessage(), '1060') === false) {
                throw $e;
            }
            echo "ℹ Column already exists (skipped) <br>";
        }
    }

    echo "<h3 style='color: green; margin-top: 20px;'>✓ Migration completed successfully!</h3>";
    echo "<p>Driver location tracking fields have been added to the database.</p>";
    echo "<p><a href='/?p=login' style='color: blue;'>Return to login</a></p>";

} catch (PDOException $e) {
    echo "<h3 style='color: red;'>✗ Migration failed</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>