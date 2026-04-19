<?php
/**
 * Pickup Model
 */
class Pickup {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO pickups (user_id, name, phone, email, address, pickup_date, waste_type) VALUES (?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([
            $data['user_id'],
            $data['name'],
            $data['phone'],
            $data['email'],
            $data['address'],
            $data['pickup_date'],
            $data['waste_type'] ?? 'general'
        ]);
    }
    
    public function getPending() {
        $stmt = $this->pdo->query("SELECT * FROM pickups WHERE status = 'pending' ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getByDriverId($driverId) {
        $stmt = $this->pdo->prepare('SELECT p.* FROM pickups p WHERE p.driver_id = ? ORDER BY p.created_at DESC');
        $stmt->execute([$driverId]);
        return $stmt->fetchAll();
    }

    public function getDrivers() {
        $stmt = $this->pdo->query('SELECT d.id, u.username AS driver_name, d.vehicle_number, d.status FROM drivers d JOIN users u ON d.user_id = u.id ORDER BY u.username ASC');
        return $stmt->fetchAll();
    }

    public function getByUser($userId) {
        $stmt = $this->pdo->prepare('SELECT p.* FROM pickups p WHERE p.user_id = ? ORDER BY p.created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function assignDriver($pickupId, $driverId) {
        $stmt = $this->pdo->prepare('UPDATE pickups SET driver_id = ?, status = "assigned" WHERE id = ? AND status = "pending"');
        return $stmt->execute([$driverId, $pickupId]);
    }
    
    public function complete($pickupId, $driverId) {
        $stmt = $this->pdo->prepare('UPDATE pickups SET status = "completed" WHERE id = ? AND driver_id = ?');
        return $stmt->execute([$pickupId, $driverId]);
    }
    
    public function getStats() {
        $stmt = $this->pdo->query('SELECT status, COUNT(*) as count FROM pickups GROUP BY status');
        return $stmt->fetchAll();
    }

    public function getWasteTypeStats() {
        $stmt = $this->pdo->query('SELECT waste_type, COUNT(*) as count FROM pickups GROUP BY waste_type');
        return $stmt->fetchAll();
    }
}
?>

