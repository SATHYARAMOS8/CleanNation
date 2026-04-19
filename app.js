// CleanNation - Interactive JavaScript Features

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add loading states to forms
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<span class="loading me-2"></span>Processing...');

        // Re-enable after 3 seconds if no redirect (fallback)
        setTimeout(function() {
            submitBtn.prop('disabled', false).html(originalText);
        }, 3000);
    });

    // Real-time form validation
    $('.form-control[required], .form-select[required]').on('blur', function() {
        validateField($(this));
    });

    $('.form-control[required], .form-select[required]').on('input', function() {
        if ($(this).hasClass('is-invalid')) {
            validateField($(this));
        }
    });

    // Password strength indicator
    $('input[type="password"]').on('input', function() {
        const password = $(this).val();
        const strength = calculatePasswordStrength(password);

        // Remove existing indicators
        $(this).removeClass('is-valid is-invalid');
        $(this).next('.password-strength').remove();

        if (password.length > 0) {
            let strengthClass = 'text-danger';
            let strengthText = 'Weak';

            if (strength >= 3) {
                strengthClass = 'text-success';
                strengthText = 'Strong';
                $(this).addClass('is-valid');
            } else if (strength >= 2) {
                strengthClass = 'text-warning';
                strengthText = 'Medium';
            } else {
                $(this).addClass('is-invalid');
            }

            $(this).after(`<div class="password-strength small ${strengthClass} mt-1">${strengthText}</div>`);
        }
    });

    // Date picker enhancement
    $('input[type="date"]').on('change', function() {
        const selectedDate = new Date($(this).val());
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate < today) {
            showAlert('Please select a future date for pickup.', 'warning');
            $(this).val('');
        }
    });

    // Phone number formatting
    $('input[type="tel"]').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 10) {
            value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
        }
        $(this).val(value);
    });

    // Email validation enhancement
    $('input[type="email"]').on('blur', function() {
        const email = $(this).val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email && !emailRegex.test(email)) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Please enter a valid email address.</div>');
            }
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    // Table row animations
    $('.table tbody tr').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's').addClass('fade-in');
    });

    // Card animations
    $('.card').addClass('slide-up');

    // Alert auto-dismiss with animation
    $('.alert').each(function() {
        const alert = $(this);
        setTimeout(function() {
            alert.fadeOut(500, function() {
                alert.remove();
            });
        }, 5000);
    });

    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(event) {
        const target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 70
            }, 500);
        }
    });

    // Dynamic navbar background on scroll
    $(window).scroll(function() {
        const scroll = $(window).scrollTop();
        if (scroll > 50) {
            $('.navbar').addClass('navbar-scrolled');
        } else {
            $('.navbar').removeClass('navbar-scrolled');
        }
    });

    // Add icons to buttons dynamically
    $('.btn:contains("Login")').prepend('<i class="fas fa-sign-in-alt me-2"></i>');
    $('.btn:contains("Register")').prepend('<i class="fas fa-user-plus me-2"></i>');
    $('.btn:contains("Request Pickup")').prepend('<i class="fas fa-truck me-2"></i>');
    $('.btn:contains("Logout")').prepend('<i class="fas fa-sign-out-alt me-2"></i>');

    // Status badge enhancements
    $('.table td:contains("pending")').addClass('text-warning').prepend('<i class="fas fa-clock me-1"></i>');
    $('.table td:contains("completed")').addClass('text-success').prepend('<i class="fas fa-check-circle me-1"></i>');
    $('.table td:contains("in_progress")').addClass('text-info').prepend('<i class="fas fa-spinner fa-spin me-1"></i>');
});

// Utility functions
function validateField(field) {
    const value = field.val().trim();
    const isRequired = field.prop('required');

    field.removeClass('is-valid is-invalid');
    field.next('.invalid-feedback').remove();

    if (isRequired && !value) {
        field.addClass('is-invalid');
        field.after('<div class="invalid-feedback">This field is required.</div>');
        return false;
    }

    if (value) {
        field.addClass('is-valid');
    }

    return true;
}

function calculatePasswordStrength(password) {
    let strength = 0;

    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    return strength;
}

function showAlert(message, type = 'info') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    $('.container.page-content').prepend(alertHtml);

    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').first().fadeOut(500, function() {
            $(this).remove();
        });
    }, 5000);
}

// Add loading overlay for long operations
function showLoadingOverlay() {
    if (!$('#loading-overlay').length) {
        $('body').append(`
            <div id="loading-overlay" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(5px);
            ">
                <div class="text-center text-white">
                    <div class="spinner-border text-light mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>Please wait...</div>
                </div>
            </div>
        `);
    }
}

function hideLoadingOverlay() {
    $('#loading-overlay').fadeOut(300, function() {
        $(this).remove();
    });
}