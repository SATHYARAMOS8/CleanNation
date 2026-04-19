<?php
$title = 'Page Not Found';
include BASE_PATH . '/views/layout/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-8 col-lg-6">
        <div class="card card-shadow">
            <div class="card-body text-center">
                <h1 class="display-4">404</h1>
                <p class="lead">Oops! Page not found.</p>
                <p>The page you requested does not exist. Use the navigation to continue.</p>
                <a href="<?php echo BASE_URL; ?>login" class="btn btn-success">Back to Login</a>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>