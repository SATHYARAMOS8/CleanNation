<?php
require_once __DIR__ . '/../core/config.php';

// Get current user IP
$ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
$user_ip = '0.0.0.0';
foreach ($ip_keys as $key) {
    if (!empty($_SERVER[$key])) {
        $user_ip = $_SERVER[$key];
        break;
    }
}

echo "<h2>IP Block Removal Tool</h2>";
echo "<p><strong>Your Current IP:</strong> <code>$user_ip</code></p>";

// Show all blocked IPs
echo "<h3>Currently Blocked IPs:</h3>";
$stmt = $pdo->query("SELECT ip_address, blocked_at, blocked_by FROM blocked_ips ORDER BY blocked_at DESC");
$blockedIPs = $stmt->fetchAll();

if (empty($blockedIPs)) {
    echo "<p style='color: green;'>No IPs are currently blocked.</p>";
} else {
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
    echo "<tr><th style='padding: 10px; text-align: left;'>IP Address</th><th style='padding: 10px;'>Blocked At</th><th style='padding: 10px;'>Type</th><th style='padding: 10px;'>Action</th></tr>";
    
    foreach ($blockedIPs as $blocked) {
        echo "<tr>";
        echo "<td style='padding: 10px;'><code>" . htmlspecialchars($blocked['ip_address']) . "</code></td>";
        echo "<td style='padding: 10px;'>" . $blocked['blocked_at'] . "</td>";
        echo "<td style='padding: 10px;'>" . ($blocked['blocked_by'] === 'manual' ? 'Manual' : 'Automatic') . "</td>";
        echo "<td style='padding: 10px;'>";
        echo "<form method='POST' style='display: inline;'>";
        echo "<input type='hidden' name='action' value='remove'>";
        echo "<input type='hidden' name='ip' value='" . htmlspecialchars($blocked['ip_address']) . "'>";
        echo "<button type='submit'>Unblock</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Handle unblock request
if ($_POST['action'] === 'remove') {
    $ip_to_remove = $_POST['ip'];
    
    // Validate IP format
    if (filter_var($ip_to_remove, FILTER_VALIDATE_IP)) {
        $stmt = $pdo->prepare("DELETE FROM blocked_ips WHERE ip_address = ?");
        $stmt->execute([$ip_to_remove]);
        echo "<p style='color: green; font-weight: bold;'>✓ IP <code>$ip_to_remove</code> has been unblocked!</p>";
        echo "<p><a href='" . BASE_URL . "' style='color: blue;'>Return to login</a></p>";
    } else {
        echo "<p style='color: red;'>Invalid IP format.</p>";
    }
}
?>
<hr>
<p style='color: #666; font-size: 12px;'>
    <strong>Note:</strong> If your IP still appears blocked after unblocking, please clear your browser cache and try again.<br>
    This tool is for emergency unblocking. Use the admin panel for regular IP management.
</p>
