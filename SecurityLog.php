<?php
/**
 * Security Log Model
 */
class SecurityLog {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function logSuspicious($ip, $reason, $category) {
        $stmt = $this->pdo->prepare('INSERT INTO suspicious_ips (ip_address, reason, category) VALUES (?, ?, ?)');
        $stmt->execute([$ip, $reason, $category]);
    }
    
    public function getRecent($limit = 50) {
        $stmt = $this->pdo->query("SELECT * FROM suspicious_ips ORDER BY created_at DESC LIMIT $limit");
        return $stmt->fetchAll();
    }
    
    public function getStats() {
        $stmt = $this->pdo->query('SELECT category, COUNT(*) as count FROM suspicious_ips GROUP BY category');
        return $stmt->fetchAll();
    }
    
    public function getBlockedIPs() {
        $stmt = $this->pdo->query('SELECT * FROM blocked_ips ORDER BY blocked_at DESC');
        return $stmt->fetchAll();
    }
    
    public function blockIP($ip, $reason = 'manual') {
        $stmt = $this->pdo->prepare('INSERT IGNORE INTO blocked_ips (ip_address, blocked_by) VALUES (?, ?)');
        $stmt->execute([$ip, $reason]);
    }
    
    public function unblockIP($ip) {
        $stmt = $this->pdo->prepare('DELETE FROM blocked_ips WHERE ip_address = ?');
        $stmt->execute([$ip]);
    }
}
?>

