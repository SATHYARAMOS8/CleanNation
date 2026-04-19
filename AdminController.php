<?php
/**
 * Admin Controller
 */
require_once __DIR__ . '/../core/middleware.php';
require_once __DIR__ . '/../models/Pickup.php';
require_once __DIR__ . '/../models/SecurityLog.php';

class AdminController {
    private $pdo;
    private $pickup;
    private $security;
    
    public function __construct($pdo) {
        runMiddleware();
        requireRole('admin');
        $this->pdo = $pdo;
        $this->pickup = new Pickup($pdo);
        $this->security = new SecurityLog($pdo);
    }
    
    public function dashboard() {
        $pickups = $this->pickup->getPending();
        $securityLogs = $this->security->getRecent(10);
        $blockedIPs = $this->security->getBlockedIPs();
        $stats = $this->pickup->getStats();
        $statusLabels = array_map(function($item){ return ucfirst($item['status']); }, $stats);
        $statusCounts = array_map(function($item){ return (int)$item['count']; }, $stats);

        $wasteStats = $this->pickup->getWasteTypeStats();
        $wasteLabels = array_map(function($item){ return ucfirst($item['waste_type']); }, $wasteStats);
        $wasteCounts = array_map(function($item){ return (int)$item['count']; }, $wasteStats);

        $drivers = $this->pickup->getDrivers();

        view('admin/dashboard', compact('pickups', 'securityLogs', 'blockedIPs', 'stats', 'statusLabels', 'statusCounts', 'wasteStats', 'wasteLabels', 'wasteCounts', 'drivers'));
    }
    
    public function assignDriver() {
        if (isPost()) {
            if (!verifyCsrfToken(input('csrf_token'))) {
                setFlash('danger', 'Invalid form submission.');
                redirect('admin');
            }

            $pickupId = input('pickup_id');
            $driverId = input('driver_id');

            // Ensure referenced driver exists before assigning
            $driverExists = false;
            foreach ($this->pickup->getDrivers() as $driver) {
                if ($driver['id'] == $driverId) {
                    $driverExists = true;
                    break;
                }
            }

            if (!$driverExists) {
                setFlash('danger', 'Selected driver does not exist');
                redirect('admin');
            }

            if ($this->pickup->assignDriver($pickupId, $driverId)) {
                setFlash('success', 'Driver assigned successfully');
            } else {
                setFlash('danger', 'Assignment failed');
            }
        }
        redirect('admin');
    }

    public function blockIP() {
        if (isPost()) {
            if (!verifyCsrfToken(input('csrf_token'))) {
                setFlash('danger', 'Invalid form submission.');
                redirect('admin');
            }

            $ip = input('ip_address');
            $this->security->blockIP($ip, 'manual');
            setFlash('success', 'IP blocked');
        }
        redirect('admin');
    }

    public function unblockIP() {
        if (isPost()) {
            if (!verifyCsrfToken(input('csrf_token'))) {
                setFlash('danger', 'Invalid form submission.');
                redirect('admin');
            }

            $ip = input('ip_address');
            $this->security->unblockIP($ip);
            setFlash('success', 'IP unblocked');
        }
        redirect('admin');
    }

    public function securityDashboard() {
        $securityLogs = $this->security->getRecent(50);
        $blockedIPs = $this->security->getBlockedIPs();
        view('admin/security', compact('securityLogs', 'blockedIPs'));
    }
}
?>

