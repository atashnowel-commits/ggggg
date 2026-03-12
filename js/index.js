/* ============================================
   AlagApp Clinic - Landing Page Scripts
   Handles carousels, modals, form validation
   ============================================ */

// ---- Carousel Initialization ----
document.addEventListener('DOMContentLoaded', function() {
    // Services Carousel
    if (document.getElementById('services-carousel')) {
        var servicesCarousel = new Splide('#services-carousel', {
            type: 'loop',
            perPage: 3,
            perMove: 1,
            gap: '2rem',
            pagination: false,
            arrows: true,
            breakpoints: {
                1024: { perPage: 2 },
                768: { perPage: 1 }
            }
        });
        servicesCarousel.mount();
    }

    // Announcements Carousel
    if (document.getElementById('announcements-carousel')) {
        var announcementsCarousel = new Splide('#announcements-carousel', {
            type: 'loop',
            perPage: 3,
            perMove: 1,
            gap: '2rem',
            pagination: false,
            arrows: true,
            autoplay: true,
            interval: 5000,
            pauseOnHover: true,
            breakpoints: {
                1024: { perPage: 2 },
                768: { perPage: 1 }
            }
        });
        announcementsCarousel.mount();
    }

    // Initialize form event listeners
    var passwordField = document.getElementById('registerPassword');
    var confirmField = document.getElementById('confirmPassword');
    var emailField = document.getElementById('registerEmail');

    if (passwordField) {
        passwordField.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
        passwordField.addEventListener('focus', function() {
            showPasswordRequirements();
        });
    }

    if (confirmField) {
        confirmField.addEventListener('input', function() {
            checkPasswordMatch();
        });
    }

    if (emailField) {
        emailField.addEventListener('blur', function() {
            validateEmail();
        });
    }
});

// ---- Modal Functions ----
function openLoginModal() {
    document.getElementById('loginModal').classList.remove('hidden');
}

function closeLoginModal() {
    document.getElementById('loginModal').classList.add('hidden');
}

function openRegisterModal() {
    document.getElementById('registerModal').classList.remove('hidden');
}

function closeRegisterModal() {
    document.getElementById('registerModal').classList.add('hidden');
}

function switchToRegister() {
    closeLoginModal();
    setTimeout(function() { openRegisterModal(); }, 300);
}

function switchToLogin() {
    closeRegisterModal();
    setTimeout(function() { openLoginModal(); }, 300);
}

function showForgotPassword() {
    closeLoginModal();
    document.getElementById('forgotPasswordModal').classList.remove('hidden');
}

function closeForgotPassword() {
    document.getElementById('forgotPasswordModal').classList.add('hidden');
    openLoginModal();
}

function toggleMobileMenu() {
    var menu = document.getElementById('mobileMenu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

function scrollToServices() {
    var el = document.getElementById('services');
    if (el) {
        el.scrollIntoView({ behavior: 'smooth' });
    }
}

// ---- Password Visibility Toggle ----
function togglePasswordVisibility(fieldId) {
    var field = document.getElementById(fieldId);
    if (!field) return;

    var button = field.parentNode.querySelector('button[type="button"]');
    if (!button) return;

    if (field.type === 'password') {
        field.type = 'text';
        button.innerHTML =
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" ' +
            'd="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>' +
            '</svg>';
    } else {
        field.type = 'password';
        button.innerHTML =
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>' +
            '</svg>';
    }
}

// ---- Password Validation ----
function showPasswordRequirements() {
    var el = document.getElementById('passwordRequirements');
    if (el) {
        el.classList.remove('hidden');
    }
    // Update immediately when shown
    var password = document.getElementById('registerPassword');
    if (password && password.value.length > 0) {
        checkPasswordStrength(password.value);
    }
}

function hidePasswordRequirements() {
    if (document.activeElement && document.activeElement.id === 'registerPassword') {
        return;
    }
    var el = document.getElementById('passwordRequirements');
    if (el) {
        el.classList.add('hidden');
    }
}

function checkPasswordStrength(password) {
    var strength = document.getElementById('passwordStrength');
    var passwordField = document.getElementById('registerPassword');
    if (!strength || !passwordField) return false;

    var requirements = {
        length: password.length >= 6,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*]/.test(password)
    };

    // Update requirement indicators
    updateRequirementIndicator('reqLength', requirements.length);
    updateRequirementIndicator('reqUppercase', requirements.uppercase);
    updateRequirementIndicator('reqLowercase', requirements.lowercase);
    updateRequirementIndicator('reqNumber', requirements.number);
    updateRequirementIndicator('reqSpecial', requirements.special);

    // Calculate strength score
    var score = Object.values(requirements).filter(Boolean).length;

    var strengthText = '';
    var color = 'text-red-500';
    var bgColor = 'bg-red-50';
    var borderColor = 'border-red-400';
    var strengthBarColor = 'bg-red-500';

    // Remove all previous state classes from password field
    passwordField.classList.remove(
        'border-red-500', 'border-yellow-500', 'border-green-500',
        'ring-2', 'ring-green-200', 'ring-yellow-200', 'ring-red-200'
    );

    if (score === 5) {
        strengthText = 'Strong Password';
        color = 'text-green-600';
        bgColor = 'bg-green-50';
        borderColor = 'border-green-400';
        strengthBarColor = 'bg-green-500';
        passwordField.classList.add('border-green-500', 'ring-2', 'ring-green-200');
    } else if (score >= 3) {
        strengthText = 'Medium Password';
        color = 'text-yellow-600';
        bgColor = 'bg-yellow-50';
        borderColor = 'border-yellow-400';
        strengthBarColor = 'bg-yellow-500';
        passwordField.classList.add('border-yellow-500', 'ring-2', 'ring-yellow-200');
    } else if (password.length > 0) {
        strengthText = 'Weak Password';
        color = 'text-red-600';
        bgColor = 'bg-red-50';
        borderColor = 'border-red-400';
        strengthBarColor = 'bg-red-500';
        passwordField.classList.add('border-red-500', 'ring-2', 'ring-red-200');
    }

    // Update strength display
    if (strengthText) {
        var emoji = score === 5 ? ' &#10004;' : score >= 3 ? ' &#9888;' : ' &#10060;';
        strength.innerHTML =
            '<div class="inline-flex items-center px-3 py-1 rounded-full ' + bgColor + ' ' + color + ' border ' + borderColor + '">' +
            '<span class="mr-2 text-sm font-medium">' + strengthText + '</span>' + emoji +
            '</div>';
    } else {
        strength.innerHTML = '';
    }

    updatePasswordStrengthBar(score, strengthBarColor);

    return Object.values(requirements).every(Boolean);
}

function updateRequirementIndicator(elementId, isMet) {
    var element = document.getElementById(elementId);
    if (!element) return;

    var icon = element.querySelector('span');
    var text = element.querySelector('span:last-child');
    if (!icon || !text) return;

    if (isMet) {
        icon.innerHTML = '&#10003;';
        icon.className = 'mr-2 text-green-500 font-bold text-sm';
        text.className = 'text-xs text-green-600 font-medium';
        element.classList.add('requirement-met');
        element.classList.remove('requirement-not-met');
    } else {
        icon.innerHTML = '&#9675;';
        icon.className = 'mr-2 text-gray-400';
        text.className = 'text-xs text-gray-600';
        element.classList.add('requirement-not-met');
        element.classList.remove('requirement-met');
    }
}

function updatePasswordStrengthBar(score, color) {
    var strengthBar = document.getElementById('passwordStrengthBar');
    if (!strengthBar) return;

    var percentage = (score / 5) * 100;
    strengthBar.className = 'h-full transition-all duration-300 ' + color;
    strengthBar.style.width = percentage + '%';
}

function checkPasswordMatch() {
    var password = document.getElementById('registerPassword');
    var confirmPassword = document.getElementById('confirmPassword');
    var matchDiv = document.getElementById('passwordMatch');

    if (!password || !confirmPassword || !matchDiv) return;

    var passVal = password.value;
    var confirmVal = confirmPassword.value;

    if (confirmVal.length === 0) {
        matchDiv.classList.add('hidden');
        confirmPassword.classList.remove('border-green-500', 'ring-2', 'ring-green-200', 'border-red-500', 'ring-red-200');
        return;
    }

    if (passVal === confirmVal) {
        matchDiv.innerHTML =
            '<div class="inline-flex items-center px-3 py-1 rounded-full bg-green-50 text-green-600 border border-green-400">' +
            '<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">' +
            '<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>' +
            '</svg>Passwords match</div>';
        matchDiv.classList.remove('hidden');
        confirmPassword.classList.add('border-green-500', 'ring-2', 'ring-green-200');
        confirmPassword.classList.remove('border-red-500', 'ring-red-200');
    } else {
        matchDiv.innerHTML =
            '<div class="inline-flex items-center px-3 py-1 rounded-full bg-red-50 text-red-600 border border-red-400">' +
            '<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">' +
            '<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>' +
            '</svg>Passwords do not match</div>';
        matchDiv.classList.remove('hidden');
        confirmPassword.classList.add('border-red-500', 'ring-2', 'ring-red-200');
        confirmPassword.classList.remove('border-green-500', 'ring-green-200');
    }
}

// ---- Email Validation ----
function validateEmail() {
    var emailInput = document.getElementById('registerEmail');
    var emailError = document.getElementById('emailError');
    if (!emailInput || !emailError) return true;

    var email = emailInput.value;

    if (!email) {
        emailError.classList.add('hidden');
        emailInput.classList.remove('border-green-500', 'ring-2', 'ring-green-200', 'border-red-500', 'ring-red-200');
        return true;
    }

    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
        emailError.textContent = 'Please enter a valid email address';
        emailError.classList.remove('hidden');
        emailInput.classList.add('border-red-500', 'ring-2', 'ring-red-200');
        emailInput.classList.remove('border-green-500', 'ring-green-200');
        return false;
    }

    emailError.classList.add('hidden');
    emailInput.classList.add('border-green-500', 'ring-2', 'ring-green-200');
    emailInput.classList.remove('border-red-500', 'ring-red-200');
    return true;
}

// ---- Form Validation ----
function validateLoginForm() {
    var email = document.getElementById('loginEmail');
    var password = document.getElementById('loginPassword');

    if (!email || !password || !email.value || !password.value) {
        alert('Please fill in all fields');
        return false;
    }
    return true;
}

function validateRegisterForm() {
    var firstName = document.getElementById('firstName').value.trim();
    var lastName = document.getElementById('lastName').value.trim();
    var email = document.getElementById('registerEmail').value.trim();
    var phone = document.getElementById('phoneNumber').value.trim();
    var dateOfBirth = document.getElementById('dateOfBirth').value;
    var gender = document.getElementById('gender').value;
    var password = document.getElementById('registerPassword').value;
    var confirmPassword = document.getElementById('confirmPassword').value;
    var terms = document.getElementById('terms').checked;

    var requiredFields = [
        { id: 'firstName', value: firstName, name: 'First Name' },
        { id: 'lastName', value: lastName, name: 'Last Name' },
        { id: 'registerEmail', value: email, name: 'Email' },
        { id: 'phoneNumber', value: phone, name: 'Phone Number' },
        { id: 'dateOfBirth', value: dateOfBirth, name: 'Date of Birth' },
        { id: 'gender', value: gender, name: 'Gender' },
        { id: 'registerPassword', value: password, name: 'Password' },
        { id: 'confirmPassword', value: confirmPassword, name: 'Confirm Password' }
    ];

    var hasErrors = false;

    for (var i = 0; i < requiredFields.length; i++) {
        var field = requiredFields[i];
        if (!field.value) {
            showFieldError(field.id, field.name + ' is required');
            hasErrors = true;
        } else {
            clearFieldError(field.id);
        }
    }

    if (email && !validateEmail()) {
        hasErrors = true;
    }

    // Validate phone number (Philippine format)
    if (phone) {
        var cleanPhone = phone.replace(/\s/g, '');
        var phoneRegex = /^9\d{9}$/;
        if (!phoneRegex.test(cleanPhone)) {
            showFieldError('phoneNumber', 'Please enter a valid Philippine mobile number (10 digits starting with 9)');
            hasErrors = true;
        }
    }

    // Validate password strength
    if (password && !checkPasswordStrength(password)) {
        showFieldError('registerPassword', 'Please ensure your password meets all requirements');
        hasErrors = true;
    }

    // Check passwords match
    if (password && confirmPassword && password !== confirmPassword) {
        showFieldError('confirmPassword', 'Passwords do not match');
        hasErrors = true;
    }

    if (!terms) {
        var termsError = document.getElementById('termsError');
        if (termsError) termsError.classList.remove('hidden');
        hasErrors = true;
    } else {
        var termsErrorEl = document.getElementById('termsError');
        if (termsErrorEl) termsErrorEl.classList.add('hidden');
    }

    if (hasErrors) {
        return false;
    }

    // Show loading state
    var submitBtn = document.querySelector('#registerForm button[type="submit"]');
    var buttonText = document.getElementById('registerButtonText');
    var spinner = document.getElementById('registerSpinner');

    if (submitBtn && buttonText && spinner) {
        buttonText.textContent = 'Creating Account...';
        spinner.classList.remove('hidden');
        submitBtn.disabled = true;
    }

    return true;
}

// ---- Field Error Helpers ----
function showFieldError(fieldId, message) {
    var field = document.getElementById(fieldId);
    if (!field) return;

    var errorDiv = document.getElementById(fieldId + 'Error');

    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = fieldId + 'Error';
        errorDiv.className = 'mt-1 text-sm text-red-600';
        field.parentNode.appendChild(errorDiv);
    }

    errorDiv.textContent = message;
    errorDiv.classList.remove('hidden');

    field.classList.add('border-red-500', 'ring-2', 'ring-red-200');

    setTimeout(function() {
        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
        field.focus();
    }, 100);
}

function clearFieldError(fieldId) {
    var field = document.getElementById(fieldId);
    if (!field) return;

    var errorDiv = document.getElementById(fieldId + 'Error');
    if (errorDiv) {
        errorDiv.classList.add('hidden');
    }

    field.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
}

// ---- Phone Number Formatting ----
function formatPhoneNumber(input) {
    var phone = input.value.replace(/\D/g, '');

    // Ensure it starts with 9 (Philippine mobile)
    if (phone.length > 0 && phone.charAt(0) !== '9') {
        phone = '9' + phone;
    }

    // Limit to 10 digits
    phone = phone.substring(0, 10);

    // Format: 9XX XXX XXXX
    if (phone.length > 3) {
        phone = phone.substring(0, 3) + ' ' + phone.substring(3);
    }
    if (phone.length > 7) {
        phone = phone.substring(0, 7) + ' ' + phone.substring(7);
    }

    input.value = phone;
}

// ---- Announcement Modal ----
function openAnnouncementModal(announcementId) {
    fetchAnnouncementDetails(announcementId);
    var modal = document.getElementById('announcementModal');
    if (modal) modal.classList.remove('hidden');
}

function closeAnnouncementModal() {
    var modal = document.getElementById('announcementModal');
    if (modal) modal.classList.add('hidden');
}

function fetchAnnouncementDetails(announcementId) {
    var modalContent = document.getElementById('announcementContent');
    if (!modalContent) return;

    modalContent.innerHTML =
        '<div class="text-center py-8">' +
        '<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>' +
        '<p class="text-gray-500 mt-4">Loading announcement...</p>' +
        '</div>';

    // Simulate API call - replace with actual AJAX call using announcementId
    setTimeout(function() {
        var sampleData = {
            title: "System Maintenance Scheduled",
            category: "MAINTENANCE",
            date: "Nov 24, 2025",
            author: "By: IT Department",
            content:
                '<p>Our system will undergo maintenance on Saturday from 2:00 AM to 6:00 AM. Services may be temporarily unavailable during this period.</p>' +
                '<p class="mt-4">We apologize for any inconvenience this may cause and appreciate your understanding as we work to improve our services.</p>' +
                '<div class="bg-pink-50 border-l-4 border-primary p-4 mt-6 rounded">' +
                '<p class="text-pink-800 font-semibold">Important Notes:</p>' +
                '<ul class="list-disc list-inside mt-2 text-pink-700">' +
                '<li>Emergency services will remain available</li>' +
                '<li>All data will be securely backed up</li>' +
                '<li>Normal operations will resume after maintenance</li>' +
                '</ul></div>'
        };

        var title = document.getElementById('modalTitle');
        var category = document.getElementById('modalCategory');
        var date = document.getElementById('modalDate');
        var author = document.getElementById('modalAuthor');

        if (title) title.textContent = sampleData.title;
        if (category) category.textContent = sampleData.category;
        if (date) date.textContent = sampleData.date;
        if (author) author.textContent = sampleData.author;
        modalContent.innerHTML = sampleData.content;
    }, 800);
}
