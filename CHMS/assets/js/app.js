/**
 * Form Validation and Utilities
 * Child Health Monitoring System
 */

// Email validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Phone validation
function validatePhone(phone) {
    const re = /^[+]?[0-9\s\-()]+$/;
    return re.test(phone);
}

// Date validation
function validateDate(dateString) {
    const date = new Date(dateString);
    return date instanceof Date && !isNaN(date);
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColor = type === 'error' ? 'bg-red-50 text-red-700' : 
                    type === 'success' ? 'bg-green-50 text-green-700' : 
                    'bg-blue-50 text-blue-700';
    
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg ${bgColor} max-w-md z-50 animate-fade-in`;
    notification.innerHTML = `
        <div class="flex items-center">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-xl">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Confirm dialog
function confirmAction(message) {
    return confirm(message);
}

// Format date for display
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Calculate age from date of birth
function calculateAge(dob) {
    const birthDate = new Date(dob);
    const today = new Date();
    const diffTime = Math.abs(today - birthDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    const years = Math.floor(diffDays / 365);
    const months = Math.floor((diffDays % 365) / 30);
    
    if (years === 0) {
        return `${months} month${months !== 1 ? 's' : ''}`;
    } else if (months === 0) {
        return `${years} year${years !== 1 ? 's' : ''}`;
    } else {
        return `${years} year${years !== 1 ? 's' : ''} ${months} month${months !== 1 ? 's' : ''}`;
    }
}

// Form submission with validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('border-red-500');
        } else {
            input.classList.remove('border-red-500');
        }
    });
    
    return isValid;
}

// Add event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide flash messages after 5 seconds
    const flashMessages = document.querySelectorAll('[class*="flash-message"]');
    flashMessages.forEach(msg => {
        setTimeout(() => {
            msg.style.transition = 'opacity 0.5s';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500);
        }, 5000);
    });
    
    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirmAction('Are you sure you want to delete this? This action cannot be undone.')) {
                e.preventDefault();
                return false;
            }
        });
    });
});
