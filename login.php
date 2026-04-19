<?php
$title = $title ?? 'Login';
include BASE_PATH . '/views/layout/header.php';
?>

<div class="row justify-content-center align-items-center min-vh-80 py-5">
    <div class="col-md-6 col-lg-5">
        <div class="card card-shadow border-0">
            <div class="card-body p-5">
                <div class="text-center mb-5">
                    <div class="mb-4">
                        <i class="fas fa-leaf text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h1 class="h2 text-success mb-2 fw-bold">Welcome to CleanNation</h1>
                    <p class="text-muted">Secure Login to Your Account</p>
                </div>

                <form method="POST" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrfToken()); ?>" />

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-user me-2 text-success"></i>Username
                        </label>
                        <input type="text" name="username" class="form-control form-control-lg"
                               placeholder="Enter your username" required autofocus
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Enter your registered username">
                        <div class="invalid-feedback">
                            Please enter a valid username.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-lock me-2 text-success"></i>Password
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control form-control-lg"
                                   placeholder="Enter your password" required
                                   id="password-field">
                            <button class="btn btn-outline-success" type="button" id="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">
                            Please enter your password.
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember-me">
                            <label class="form-check-label text-muted" for="remember-me">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <button class="btn btn-success btn-lg w-100 mb-4" type="submit">
                        <i class="fas fa-sign-in-alt me-2"></i>Login to Dashboard
                    </button>
                </form>

                <div class="text-center">
                    <p class="text-muted mb-2">Don't have an account?</p>
                    <a href="<?php echo BASE_URL; ?>register" class="btn btn-outline-success">
                        <i class="fas fa-user-plus me-2"></i>Create New Account
                    </a>
                </div>

                <hr class="my-4">

                <div class="text-center text-muted small">
                    <i class="fas fa-shield-alt me-1"></i>
                    Your data is secure and encrypted
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Password toggle functionality
$(document).ready(function() {
    $('#toggle-password').on('click', function() {
        const passwordField = $('#password-field');
        const icon = $(this).find('i');

        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>

