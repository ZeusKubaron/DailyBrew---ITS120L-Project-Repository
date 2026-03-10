/**
 * DailyBrew - Main JavaScript
 * AI-Assisted Student Scheduler
 */

// Check if user is logged in on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DailyBrew loaded successfully!');
});

// Global functions
function showAlert(message, type = 'info') {
    alert(message);
}

function formatDate(date) {
    const d = new Date(date);
    return d.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatTime(time) {
    const [hours, minutes] = time.split(':');
    const h = parseInt(hours);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const hour = h % 12 || 12;
    return `${hour}:${minutes} ${ampm}`;
}

// Export functions for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { showAlert, formatDate, formatTime };
}

