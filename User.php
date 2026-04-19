<?php
/**
 * User Model
 */
class User {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function findByUsername($username) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    public function create($username, $password, $role) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO users (username, password_hash, role, login_attempts, locked_until) VALUES (?, ?, ?, 0, NULL)');
        return $stmt->execute([$username, $hash, $role]);
    }
    
    public function isLocked($user) {
        if (!$user) {
            return false;
        }
        if (!empty($user['locked_until']) && $user['locked_until'] > date('Y-m-d H:i:s')) {
            return true;
        }
        return false;
    }
    
    public function countByRole($role) {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE role = ?');
        $stmt->execute([$role]);
        return (int) $stmt->fetchColumn();
    }

    public function hasAdmin() {
        return $this->countByRole('admin') > 0;
    }

    public function incrementLoginAttempts($userId) {
        $stmt = $this->pdo->prepare('UPDATE users SET login_attempts = login_attempts + 1 WHERE id = ?');
        $stmt->execute([$userId]);

        $stmt = $this->pdo->prepare('SELECT login_attempts FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $attempts = (int) $stmt->fetchColumn();

        if ($attempts >= 5) {
            $stmt = $this->pdo->prepare('UPDATE users SET locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE), login_attempts = 0 WHERE id = ?');
            $stmt->execute([$userId]);
        }
    }

    public function resetLoginAttempts($userId) {
        $stmt = $this->pdo->prepare('UPDATE users SET login_attempts = 0, locked_until = NULL WHERE id = ?');
        $stmt->execute([$userId]);
    }
}
?>

