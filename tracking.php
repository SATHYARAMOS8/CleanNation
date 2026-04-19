<?php
/**
 * Driver Tracking API - AJAX Endpoints
 */
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/security.php';

header('Content-Type: application/json');

// Check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_driver_status':
        getDriverStatus();
        break;
    case 'get_assigned_pickups':
        getAssignedPickups();
        break;
    case 'update_driver_location':
        updateDriverLocation();
        break;
    case 'get_pickup_driver':
        getPickupDriver();
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}

function getDriverStatus() {
    global $pdo;
    
    $pickupId = $_GET['pickup_id'] ?? null;
    if (!$pickupId) {
        http_response_code(400);
        echo json_encode(['error' => 'Pickup ID required']);
        return;
    }
    
    $stmt = $pdo->prepare('
        SELECT d.id, u.username as driver_name, d.vehicle_number, d.status, 
               d.latitude, d.longitude, d.last_updated, p.id as pickup_id, p.status as pickup_status
        FROM pickups p
        LEFT JOIN drivers d ON p.driver_id = d.id
        LEFT JOIN users u ON d.user_id = u.id
        WHERE p.id = ?
    ');
    $stmt->execute([$pickupId]);
    $result = $stmt->fetch();
    
    if (!$result) {
        http_response_code(404);
        echo json_encode(['error' => 'Pickup not found']);
        return;
    }
    
    echo json_encode([
        'driver_id' => $result['id'],
        'driver_name' => $result['driver_name'],
        'vehicle_number' => $result['vehicle_number'],
        'status' => $result['status'],
        'latitude' => floatval($result['latitude'] ?? 0),
        'longitude' => floatval($result['longitude'] ?? 0),
        'last_updated' => $result['last_updated'],
        'pickup_status' => $result['pickup_status']
    ]);
}

function getPickupDriver() {
    global $pdo;
    
    $pickupId = $_GET['pickup_id'] ?? null;
    if (!$pickupId) {
        http_response_code(400);
        echo json_encode(['error' => 'Pickup ID required']);
        return;
    }
    
    $stmt = $pdo->prepare('
        SELECT d.id, u.username as driver_name, d.vehicle_number, d.status,
               d.latitude, d.longitude, d.last_updated, p.address
        FROM pickups p
        LEFT JOIN drivers d ON p.driver_id = d.id
        LEFT JOIN users u ON d.user_id = u.id
        WHERE p.id = ? AND p.user_id = ?
    ');
    $stmt->execute([$pickupId, $_SESSION['user_id']]);
    $result = $stmt->fetch();
    
    if (!$result) {
        echo json_encode(['error' => 'No driver assigned']);
        return;
    }
    
    echo json_encode($result);
}

function getAssignedPickups() {
    global $pdo;
    
    $stmt = $pdo->prepare('
        SELECT id, name, status, pickup_date, address, driver_id
        FROM pickups
        WHERE user_id = ? AND driver_id IS NOT NULL
        ORDER BY pickup_date DESC
    ');
    $stmt->execute([$_SESSION['user_id']]);
    $results = $stmt->fetchAll();
    
    echo json_encode($results);
}

function updateDriverLocation() {
    global $pdo;
    
    // Only drivers can update their location
    if (($_SESSION['role'] ?? null) !== 'driver') {
        http_response_code(403);
        echo json_encode(['error' => 'Only drivers can update location']);
        return;
    }
    
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    
    if (!$latitude || !$longitude) {
        http_response_code(400);
        echo json_encode(['error' => 'Latitude and longitude required']);
        return;
    }
    
    // Get driver ID from user
    $stmt = $pdo->prepare('SELECT id FROM drivers WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $driver = $stmt->fetch();
    
    if (!$driver) {
        http_response_code(404);
        echo json_encode(['error' => 'Driver not found']);
        return;
    }
    
    // Update driver location
    $stmt = $pdo->prepare('UPDATE drivers SET latitude = ?, longitude = ?, last_updated = NOW() WHERE id = ?');
    $success = $stmt->execute([floatval($latitude), floatval($longitude), $driver['id']]);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Location updated']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update location']);
    }
}
?>