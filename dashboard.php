<?php
$title = $title ?? 'Driver Dashboard';
include BASE_PATH . '/views/layout/header.php';
?>

    <div class="container mt-5">
        <div class="card card-shadow">
            <div class="card-body">
                <h3>Driver Dashboard</h3>
                <p class="text-muted">Welcome back, <?php echo e($_SESSION['username'] ?? 'Driver'); ?>.</p>
                <p>You are currently assigned to handle pickups from the admin panel. Active work orders appear here.</p>

                <?php if (empty($pickups)): ?>
                    <div class="alert alert-info">No assigned pickups currently.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr><th>#</th><th>Name</th><th>Address</th><th>Date</th><th>Status</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pickups as $p): ?>
                                    <tr>
                                        <td><?php echo e($p['id']); ?></td>
                                        <td><?php echo e($p['name']); ?></td>
                                        <td><?php echo e($p['address']); ?></td>
                                        <td><?php echo e($p['pickup_date']); ?></td>
                                        <td><?php echo e(ucfirst($p['status'])); ?></td>
                                        <td>
                                            <?php if ($p['status'] === 'assigned'): ?>
                                                <form method="POST" action="<?php echo BASE_URL; ?>complete_pickup" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?php echo e(csrfToken()); ?>" />
                                                    <input type="hidden" name="pickup_id" value="<?php echo e($p['id']); ?>" />
                                                    <button class="btn btn-sm btn-primary">Mark Completed</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge bg-success"><?php echo e(ucfirst($p['status'])); ?></span>
                                            <?php endif; ?>
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

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
