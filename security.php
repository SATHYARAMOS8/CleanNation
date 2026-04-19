<?php
$title = $title ?? 'Security Management';
include BASE_PATH . '/views/layout/header.php';
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card card-shadow border-0" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: white;">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-4 fw-bold mb-2">
                                <i class="fas fa-lock me-3"></i>Security & IP Management
                            </h1>
                            <p class="lead mb-0">Monitor suspicious activities and manage blocked IP addresses</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <i class="fas fa-shield-alt" style="font-size: 5rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-shadow">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-ban text-danger" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="mb-1"><?php echo count($blockedIPs); ?></h3>
                    <p class="text-muted mb-0">Blocked IPs</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-shadow">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="mb-1"><?php echo count($securityLogs); ?></h3>
                    <p class="text-muted mb-0">Suspicious Events</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-shadow">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-check-circle text-success" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="mb-1">Automated</h3>
                    <p class="text-muted mb-0">IP Blocking Active</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Block New IP Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-shadow">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Block New IP Address
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo BASE_URL; ?>block_ip" class="row g-3">
                        <input type="hidden" name="csrf_token" value="<?php echo e(csrfToken()); ?>" />
                        <div class="col-md-8">
                            <label class="form-label"><i class="fas fa-globe me-2"></i>IP Address</label>
                            <input type="text" name="ip_address" class="form-control form-control-lg" 
                                   placeholder="e.g., 192.168.1.1 or 2001:0db8:85a3:0000:0000:8a2e:0370:7334"
                                   title="Enter a valid IPv4 or IPv6 address" required>
                            <small class="text-muted">Enter the IP address you want to block (IPv4 or IPv6)</small>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-danger btn-lg w-100">
                                <i class="fas fa-ban me-2"></i>Block IP Address
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Currently Blocked IPs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-shadow">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Blocked IP Addresses (<?php echo count($blockedIPs); ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($blockedIPs)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                            <h5 class="text-muted">No blocked IPs</h5>
                            <p class="text-muted">Your system is secure. No IP addresses are currently blocked.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-danger">
                                    <tr>
                                        <th width="30%"><i class="fas fa-globe me-2"></i>IP Address</th>
                                        <th width="20%"><i class="fas fa-user me-2"></i>Block Type</th>
                                        <th width="30%"><i class="fas fa-calendar me-2"></i>Date Blocked</th>
                                        <th width="20%"><i class="fas fa-cogs me-2"></i>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($blockedIPs as $blocked): ?>
                                        <tr>
                                            <td>
                                                <code class="bg-light px-3 py-2 rounded d-inline-block"><?php echo e($blocked['ip_address']); ?></code>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo ($blocked['blocked_by'] === 'manual') ? 'warning' : 'info'; ?> p-2">
                                                    <i class="fas fa-<?php echo ($blocked['blocked_by'] === 'manual') ? 'hand-paper' : 'robot'; ?> me-1"></i>
                                                    <?php echo ($blocked['blocked_by'] === 'manual') ? 'Manual Block' : 'Automatic Block'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    <?php echo e(date('F j, Y \a\t h:i A', strtotime($blocked['blocked_at']))); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <form method="POST" action="<?php echo BASE_URL; ?>unblock_ip" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo e(csrfToken()); ?>" />
                                                    <input type="hidden" name="ip_address" value="<?php echo e($blocked['ip_address']); ?>" />
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            onclick="return confirm('Are you sure you want to unblock <?php echo e($blocked['ip_address']); ?>?');">
                                                        <i class="fas fa-check-circle me-1"></i>Unblock
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Suspicious Activity Log -->
    <div class="row">
        <div class="col-12">
            <div class="card card-shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Suspicious Activity Log (Last 50 Events)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($securityLogs)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-muted">No suspicious activity detected</h5>
                            <p class="text-muted">Your system is operating normally with no security alerts.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-warning">
                                    <tr>
                                        <th width="15%"><i class="fas fa-globe me-2"></i>IP Address</th>
                                        <th width="15%"><i class="fas fa-signal me-2"></i>Threat Level</th>
                                        <th width="40%"><i class="fas fa-file-alt me-2"></i>Details</th>
                                        <th width="20%"><i class="fas fa-clock me-2"></i>Timestamp</th>
                                        <th width="10%"><i class="fas fa-cogs me-2"></i>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($securityLogs as $log): ?>
                                        <tr class="<?php echo ($log['category'] === 'high') ? 'table-danger' : (($log['category'] === 'medium') ? 'table-warning' : ''); ?>">
                                            <td>
                                                <code class="bg-light px-2 py-1 rounded"><?php echo e($log['ip_address']); ?></code>
                                            </td>
                                            <td>
                                                <?php 
                                                $badgeClass = $log['category'] === 'high' ? 'danger' : ($log['category'] === 'medium' ? 'warning' : 'info');
                                                $icon = $log['category'] === 'high' ? '🔴' : ($log['category'] === 'medium' ? '🟡' : '🔵');
                                                ?>
                                                <span class="badge bg-<?php echo $badgeClass; ?> p-2">
                                                    <?php echo $icon; ?> <?php echo ucfirst($log['category']); ?> Risk
                                                </span>
                                            </td>
                                            <td>
                                                <small><?php echo e($log['reason']); ?></small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    <?php echo e(date('M j, Y h:i:s A', strtotime($log['created_at']))); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <form method="POST" action="<?php echo BASE_URL; ?>block_ip" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo e(csrfToken()); ?>" />
                                                    <input type="hidden" name="ip_address" value="<?php echo e($log['ip_address']); ?>" />
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Block <?php echo e($log['ip_address']); ?>?');"
                                                            title="Block this IP address">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Information -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card card-shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Security System Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Input Verification</h6>
                            <ul class="list-unstyled text-muted">
                                <li><i class="fas fa-check-circle text-success me-2"></i>SQL Injection Detection</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>XSS Attack Prevention</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>CSRF Protection</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Input Sanitization</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">IP Blocking Features</h6>
                            <ul class="list-unstyled text-muted">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Auto-block after 5+ attacks/hour</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Manual IP blocking capability</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Threat level categorization</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Detailed activity logging</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>