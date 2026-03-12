/* ============================================
   AlagApp Clinic - Admin Dashboard Scripts
   Handles section nav, modals, notifications
   ============================================ */

// ---- Section Navigation ----
function showSection(sectionName) {
    document.querySelectorAll('.section-content').forEach(function(section) {
        section.classList.add('hidden');
    });

    var targetSection = document.getElementById(sectionName + '-section');
    if (targetSection) {
        targetSection.classList.remove('hidden');
    }

    document.querySelectorAll('nav a').forEach(function(link) {
        link.classList.remove('bg-white/20');
    });

    // Use event.target if available (called from onclick), otherwise find by data attribute
    if (typeof event !== 'undefined' && event && event.target) {
        var activeLink = event.target.closest('a');
        if (activeLink) {
            activeLink.classList.add('bg-white/20');
        }
    }
}

// ---- Notification System ----
function showNotification(message, type) {
    type = type || 'success';
    var notification = document.getElementById('notification');
    if (!notification) return;

    notification.textContent = message;
    notification.className = 'notification ' + type + ' show';

    setTimeout(function() {
        notification.classList.remove('show');
    }, 3000);
}

// ---- Modal Functions ----
function openAddUserModal() {
    var el = document.getElementById('addUserModal');
    if (el) el.classList.remove('hidden');
}

function closeAddUserModal() {
    var el = document.getElementById('addUserModal');
    if (el) el.classList.add('hidden');
    var form = document.getElementById('addUserForm');
    if (form) form.reset();
}

function openAddScheduleModal() {
    var el = document.getElementById('addScheduleModal');
    if (el) el.classList.remove('hidden');
}

function closeAddScheduleModal() {
    var el = document.getElementById('addScheduleModal');
    if (el) el.classList.add('hidden');
    var form = document.getElementById('addScheduleForm');
    if (form) form.reset();
}

function openAddServiceModal() {
    var el = document.getElementById('addServiceModal');
    if (el) el.classList.remove('hidden');
}

function closeAddServiceModal() {
    var el = document.getElementById('addServiceModal');
    if (el) el.classList.add('hidden');
    var form = document.getElementById('addServiceForm');
    if (form) form.reset();
}

// ---- User Management ----
function toggleUserStatus(userId) {
    if (!confirm('Are you sure you want to change this user\'s status?')) {
        return;
    }

    var formData = new FormData();
    formData.append('user_id', userId);
    formData.append('action', 'toggle_user_status');
    if (window.CSRF_TOKEN) formData.append('csrf_token', window.CSRF_TOKEN);

    fetch('admin-actions-secure.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            showNotification('User status updated successfully!');
            setTimeout(function() { location.reload(); }, 1000);
        } else {
            showNotification(data.message || 'Error updating user status', 'error');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        showNotification('Error updating user status', 'error');
    });
}

function editUser(userId) {
    showNotification('Edit user feature coming soon!', 'info');
}

function filterUsers() {
    showNotification('Filter functionality coming soon!', 'info');
}

// ---- Schedule Management ----
function editSchedule(scheduleId) {
    showNotification('Edit schedule feature coming soon!', 'info');
}

function deleteSchedule(scheduleId) {
    if (!confirm('Are you sure you want to delete this schedule?')) {
        return;
    }

    var formData = new FormData();
    formData.append('schedule_id', scheduleId);
    formData.append('action', 'delete_schedule');
    if (window.CSRF_TOKEN) formData.append('csrf_token', window.CSRF_TOKEN);

    fetch('admin-actions-secure.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            showNotification('Schedule deleted successfully!');
            setTimeout(function() { location.reload(); }, 1000);
        } else {
            showNotification(data.message || 'Error deleting schedule', 'error');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        showNotification('Error deleting schedule', 'error');
    });
}

// ---- Service Management ----
function editService(serviceId) {
    showNotification('Edit service feature coming soon!', 'info');
}

function toggleServiceStatus(serviceId) {
    if (!confirm('Are you sure you want to change this service\'s status?')) {
        return;
    }

    var formData = new FormData();
    formData.append('service_id', serviceId);
    formData.append('action', 'toggle_service_status');
    if (window.CSRF_TOKEN) formData.append('csrf_token', window.CSRF_TOKEN);

    fetch('admin-actions-secure.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            showNotification('Service status updated successfully!');
            setTimeout(function() { location.reload(); }, 1000);
        } else {
            showNotification(data.message || 'Error updating service status', 'error');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        showNotification('Error updating service status', 'error');
    });
}

// ---- Appointment Management ----
function editAppointment(appointmentId) {
    showNotification('Edit appointment feature coming soon!', 'info');
}

function updateAppointmentStatus(appointmentId) {
    showNotification('Update appointment status feature coming soon!', 'info');
}

function filterAppointments() {
    showNotification('Filter functionality coming soon!', 'info');
}

function filterLogs() {
    showNotification('Filter functionality coming soon!', 'info');
}
