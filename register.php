<?php
$title = $title ?? 'Register';
include BASE_PATH . '/views/layout/header.php';
?>

<div class="row justify-content-center align-items-center py-5">
    <div class="col-md-8 col-lg-6">
        <div class="card card-shadow border-0">
            <div class="card-body p-5">
                <div class="text-center mb-5">
                    <div class="mb-4">
                        <i class="fas fa-user-plus text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h1 class="h2 text-success mb-2 fw-bold">Join CleanNation</h1>
                    <p class="text-muted">Create your professional waste collection account</p>
                </div>

                <form method="POST" id="register-form" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrfToken()); ?>" />

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="fas fa-user me-2 text-success"></i>Username
                            </label>
                            <input type="text" name="username" class="form-control form-control-lg"
                                   placeholder="Choose a username" required autofocus
                                   data-bs-toggle="tooltip" data-bs-placement="right" title="Choose a unique username">
                            <div class="invalid-feedback">
                                Username must be at least 3 characters long.
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="fas fa-users-cog me-2 text-success"></i>Account Type
                            </label>
                            <select name="role" class="form-select form-select-lg" required>
                                <option value="">Select account type</option>
                                <option value="customer">🏠 Customer</option>
                                <option value="driver">🚛 Driver</option>
                                <option value="admin">⚙️ Admin</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select an account type.
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-lock me-2 text-success"></i>Password
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control form-control-lg"
                                   placeholder="Create a strong password" required id="reg-password">
                            <button class="btn btn-outline-success" type="button" id="toggle-reg-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">
                            Password must be at least 8 characters with uppercase, lowercase, and number.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-lock me-2 text-success"></i>Confirm Password
                        </label>
                        <input type="password" name="confirm_password" class="form-control form-control-lg"
                               placeholder="Confirm your password" required id="confirm-password">
                        <div class="invalid-feedback">
                            Passwords do not match.
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms-agree" required>
                            <label class="form-check-label text-muted" for="terms-agree">
                                I agree to the <a href="#" class="text-success">Terms of Service</a> and <a href="#" class="text-success">Privacy Policy</a>
                            </label>
                            <div class="invalid-feedback">
                                You must agree to the terms and conditions.
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-success btn-lg w-100 mb-4" type="submit">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                </form>

                <div class="text-center">
                    <p class="text-muted mb-2">Already have an account?</p>
                    <a href="<?php echo BASE_URL; ?>login" class="btn btn-outline-success">
                        <i class="fas fa-sign-in-alt me-2"></i>Login Instead
                    </a>
                </div>

                <hr class="my-4">

                <div class="text-center text-muted small">
                    <i class="fas fa-shield-alt me-1"></i>
                    Secure registration with encrypted data protection
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Registration form enhancements
$(document).ready(function() {
    // Password toggle
    $('#toggle-reg-password').on('click', function() {
        const passwordField = $('#reg-password');
        const icon = $(this).find('i');

        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Password confirmation validation
    $('#confirm-password').on('input', function() {
        const password = $('#reg-password').val();
        const confirmPassword = $(this).val();

        if (confirmPassword && password !== confirmPassword) {
            $(this).addClass('is-invalid');
        } else if (confirmPassword) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    });

    // Username validation
    $('input[name="username"]').on('input', function() {
        const username = $(this).val();
        if (username.length < 3) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    });

    // Password strength validation
    $('#reg-password').on('input', function() {
        const password = $(this).val();
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasNumber = /\d/.test(password);
        const isLongEnough = password.length >= 8;

        if (isLongEnough && hasUpper && hasLower && hasNumber) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).addClass('is-invalid');
        }
    });

    // Form submission validation
    $('#register-form').on('submit', function(e) {
        const password = $('#reg-password').val();
        const confirmPassword = $('#confirm-password').val();
        const termsAgreed = $('#terms-agree').is(':checked');

        if (password !== confirmPassword) {
            e.preventDefault();
            $('#confirm-password').addClass('is-invalid');
            showAlert('Passwords do not match!', 'danger');
            return false;
        }

        if (!termsAgreed) {
            e.preventDefault();
            $('#terms-agree').addClass('is-invalid');
            showAlert('Please agree to the terms and conditions!', 'danger');
            return false;
        }
    });
});
</script>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
</div>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
