/* ============================================
   AlagApp Clinic - Doctor Dashboard Scripts
   Handles patient management, vaccinations, modals
   ============================================ */

// Global Variables
var currentPatient = null;
var currentPatientForVaccine = null;

// ---- Navigation ----
function showSection(sectionName) {
    document.querySelectorAll('.section-content').forEach(function(section) {
        section.classList.add('hidden');
    });

    var targetSection = document.getElementById(sectionName + '-section');
    if (targetSection) {
        targetSection.classList.remove('hidden');
        targetSection.classList.add('fade-in');
        setActiveNav(sectionName);
        if (sectionName === 'dashboard') {
            setTimeout(initializeCharts, 100);
        }
    }
}

function setActiveNav(sectionName) {
    document.querySelectorAll('.nav-item').forEach(function(item) {
        item.classList.remove('active', 'bg-white/20');
    });
    var activeNav = document.querySelector('.nav-item[data-section="' + sectionName + '"]');
    if (activeNav) {
        activeNav.classList.add('active', 'bg-white/20');
    }
}

// ---- Modal Management ----
function closeModal(modalId) {
    var el = document.getElementById(modalId);
    if (el) el.classList.add('hidden');
}

function closeAllModals() {
    document.querySelectorAll('.modal-container').forEach(function(modal) {
        modal.classList.add('hidden');
    });
}

// ---- Notification System ----
function showNotification(message, type, duration) {
    type = type || 'success';
    duration = duration || 4000;
    var notification = document.getElementById('notification');
    if (!notification) return;

    notification.textContent = message;
    notification.className = 'notification ' + type;
    notification.classList.add('show');

    setTimeout(function() {
        notification.classList.remove('show');
    }, duration);
}

// ---- Initialization ----
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.nav-item[data-section]').forEach(function(navItem) {
        navItem.addEventListener('click', function(e) {
            e.preventDefault();
            var sectionName = this.getAttribute('data-section');
            showSection(sectionName);
        });
    });

    setActiveNav('dashboard');
    var adminDateEl = document.getElementById('administration_date');
    if (adminDateEl) {
        adminDateEl.value = new Date().toISOString().split('T')[0];
    }
    initializeCharts();
});

// ---- Chart Initialization ----
function initializeCharts() {
    var chartData = window.APPOINTMENT_CHART_DATA || { dates: [], counts: [] };
    var vaccinationChartData = window.VACCINATION_CHART_DATA || { months: [], vaccination_counts: [] };

    var appointmentsCtx = document.getElementById('appointmentsChart');
    if (appointmentsCtx && chartData.dates && chartData.dates.length > 0 && typeof Chart !== 'undefined') {
        new Chart(appointmentsCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: chartData.dates,
                datasets: [{
                    label: 'Daily Appointments',
                    data: chartData.counts,
                    borderColor: '#FF6B9A',
                    backgroundColor: 'rgba(255, 107, 154, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'top' } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

    var vaccinationsCtx = document.getElementById('vaccinationsChart');
    if (vaccinationsCtx && vaccinationChartData.months && vaccinationChartData.months.length > 0 && typeof Chart !== 'undefined') {
        new Chart(vaccinationsCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: vaccinationChartData.months,
                datasets: [{
                    label: 'Vaccinations Administered',
                    data: vaccinationChartData.vaccination_counts,
                    backgroundColor: '#4F46E5',
                    borderColor: '#3730A3',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'top' } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }
}

// ---- Patient Management ----
function selectPatient(patientId, element) {
    document.querySelectorAll('.patient-item').forEach(function(item) {
        item.classList.remove('bg-primary/10', 'border-primary', 'selected');
    });
    if (element) {
        element.classList.add('bg-primary/10', 'border-primary', 'selected');
    }
    currentPatient = patientId;
    loadPatientDetails(patientId);
    loadPatientVaccineNeeds(patientId);
    loadPatientVaccinationHistory(patientId);
}

function loadPatientDetails(patientId) {
    var patientSummary = document.getElementById('patientSummary');
    if (!patientSummary) return;

    patientSummary.innerHTML =
        '<div class="text-center">' +
        '<div class="loading-spinner mx-auto mb-2"></div>' +
        '<div class="text-sm text-gray-500">Loading patient details...</div>' +
        '</div>';

    var formData = new FormData();
    formData.append('action', 'get_patient_details');
    formData.append('patient_id', patientId);
    formData.append('ajax', 'true');
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                displayPatientDetails(data.patient);
            } else {
                patientSummary.innerHTML = '<p class="text-red-500">' + (data.message || 'Error') + '</p>';
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            patientSummary.innerHTML = '<p class="text-red-500">Failed to load patient details</p>';
        });
}

function displayPatientDetails(patient) {
    var patientSummary = document.getElementById('patientSummary');
    if (!patientSummary) return;

    var age = patient.age_years > 0
        ? patient.age_years + ' years'
        : patient.age_months + ' months';

    patientSummary.innerHTML =
        '<div class="text-left space-y-4">' +
        '<div><h4 class="font-semibold text-gray-800 mb-2">Patient Information</h4>' +
        '<div class="grid grid-cols-2 gap-2 text-sm">' +
        '<div class="text-gray-600">Name:</div><div class="font-medium">' + escapeHtml(patient.first_name) + ' ' + escapeHtml(patient.last_name) + '</div>' +
        '<div class="text-gray-600">DOB:</div><div class="font-medium">' + new Date(patient.date_of_birth).toLocaleDateString() + '</div>' +
        '<div class="text-gray-600">Age:</div><div class="font-medium">' + age + '</div>' +
        '<div class="text-gray-600">Gender:</div><div class="font-medium">' + escapeHtml(patient.gender || 'N/A') + '</div>' +
        '</div></div></div>';
}

function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) return '';
    return String(unsafe)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// ---- Vaccination History ----
function loadPatientVaccinationHistory(patientId) {
    var formData = new FormData();
    formData.append('action', 'get_patient_vaccination_records');
    formData.append('patient_id', patientId);
    formData.append('ajax', 'true');
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                renderPatientVaccinationHistory(data.data);
            }
        })
        .catch(function(error) { console.error('Error:', error); });
}

function renderPatientVaccinationHistory(records) {
    var container = document.getElementById('patientVaccinationHistory');
    if (!container) return;

    if (!records || records.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-4">No vaccination history</p>';
        return;
    }

    var html = '<div class="space-y-3">';
    records.forEach(function(record) {
        html +=
            '<div class="p-3 border border-gray-200 rounded-lg bg-white hover:shadow-md transition-shadow">' +
            '<div class="flex justify-between items-start mb-2"><div>' +
            '<div class="font-semibold text-gray-800 text-sm">' + escapeHtml(record.vaccine_name) + '</div>' +
            '<div class="text-xs text-gray-600">' + new Date(record.administration_date).toLocaleDateString() + '</div>' +
            '</div>' +
            '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">Dose #' + escapeHtml(record.dose_number) + '</span>' +
            '</div>' +
            (record.lot_number ? '<div class="text-xs text-gray-600 mb-2">Lot: ' + escapeHtml(record.lot_number) + '</div>' : '') +
            (record.notes ? '<div class="text-xs text-gray-600 mb-2"><strong>Notes:</strong> ' + escapeHtml(record.notes) + '</div>' : '') +
            '<div class="flex gap-2 mt-3">' +
            '<button onclick="editVaccinationModal(' + record.id + ')" class="flex-1 px-2 py-1 text-xs bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition-colors">Edit</button>' +
            '<button onclick="deleteVaccinationFromRecord(' + record.id + ')" class="flex-1 px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors">Delete</button>' +
            '</div></div>';
    });
    html += '</div>';
    container.innerHTML = html;
}

// ---- Vaccine Needs ----
function openVaccineNeedModal() {
    if (!currentPatient) {
        showNotification('Please select a patient first', 'error');
        return;
    }
    currentPatientForVaccine = currentPatient;

    var form = document.getElementById('vaccineNeedForm');
    if (form) form.reset();
    var needId = document.getElementById('vaccine_need_id');
    if (needId) needId.value = '';
    var patId = document.getElementById('vaccine_need_patient_id');
    if (patId) patId.value = currentPatient;

    var selectedPatient = document.querySelector('.patient-item.selected');
    if (selectedPatient) {
        var patientName = selectedPatient.querySelector('.font-semibold');
        var display = document.getElementById('vaccine_patient_display');
        if (patientName && display) display.textContent = patientName.textContent;
    }

    loadRecommendedVaccines(currentPatient);
    loadVaccineNeedsForModal(currentPatient);

    var modal = document.getElementById('vaccineNeedModal');
    if (modal) modal.classList.remove('hidden');
}

function loadRecommendedVaccines(patientId) {
    var formData = new FormData();
    formData.append('action', 'get_recommended_vaccines');
    formData.append('patient_id', patientId);
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                var ageMonths = data.age_months;
                var ageDisplay = '';
                if (ageMonths < 12) {
                    ageDisplay = ageMonths + ' months';
                } else {
                    var years = Math.floor(ageMonths / 12);
                    var months = ageMonths % 12;
                    ageDisplay = years + ' year' + (years > 1 ? 's' : '') + ' ' + months + ' month' + (months !== 1 ? 's' : '');
                }
                var ageEl = document.getElementById('vaccine_age_display');
                if (ageEl) ageEl.textContent = ageDisplay;

                var vaccinesList = document.getElementById('recommendedVaccinesList');
                if (vaccinesList) {
                    if (data.data && data.data.length > 0) {
                        var html = '<ul class="list-disc list-inside space-y-1">';
                        data.data.forEach(function(vaccine) {
                            html += '<li>' + escapeHtml(vaccine.vaccine_name) + ' (' + escapeHtml(vaccine.disease_protected) + ')</li>';
                        });
                        html += '</ul>';
                        vaccinesList.innerHTML = html;
                    } else {
                        vaccinesList.innerHTML = '<p class="text-gray-700">No vaccines recommended for this age</p>';
                    }
                }
            }
        })
        .catch(function(error) { console.error('Error:', error); });
}

function loadVaccineNeedsForModal(patientId) {
    var formData = new FormData();
    formData.append('action', 'get_vaccine_needs');
    formData.append('patient_id', patientId);
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success && data.data) {
                renderVaccineNeedsForModal(data.data);
            }
        })
        .catch(function(error) { console.error('Error:', error); });
}

function renderVaccineNeedsForModal(vaccines) {
    var container = document.getElementById('vaccineNeedsModalList');
    if (!container) return;

    if (!vaccines || vaccines.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">No vaccine needs recorded</p>';
        return;
    }

    var statusColors = {
        'RECOMMENDED': 'bg-yellow-100 text-yellow-800',
        'SCHEDULED': 'bg-blue-100 text-blue-800',
        'GIVEN': 'bg-green-100 text-green-800',
        'NOT_NEEDED': 'bg-gray-100 text-gray-800'
    };

    var html = '';
    vaccines.forEach(function(vaccine) {
        var statusColor = statusColors[vaccine.status] || 'bg-gray-100 text-gray-800';
        var dateDisplay = vaccine.recommended_date
            ? new Date(vaccine.recommended_date).toLocaleDateString()
            : 'Not set';

        html +=
            '<div class="p-4 bg-white border border-gray-200 rounded-lg">' +
            '<div class="flex justify-between items-start mb-2"><div>' +
            '<div class="font-semibold text-gray-800">' + escapeHtml(vaccine.vaccine_name) + '</div>' +
            '<div class="text-sm text-gray-600">Due: ' + dateDisplay + '</div>' +
            '</div><span class="px-2 py-1 text-xs font-semibold rounded ' + statusColor + '">' + escapeHtml(vaccine.status) + '</span></div>' +
            (vaccine.notes ? '<div class="text-sm text-gray-600 mb-2"><strong>Notes:</strong> ' + escapeHtml(vaccine.notes) + '</div>' : '') +
            '<div class="flex gap-2 mt-3">' +
            '<button onclick=\'editVaccineNeed(' + JSON.stringify(vaccine) + ')\' class="flex-1 px-3 py-1 text-sm bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition-colors">Edit</button>' +
            '<button onclick="deleteVaccineNeed(' + vaccine.id + ')" class="flex-1 px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors">Delete</button>' +
            '</div></div>';
    });
    container.innerHTML = html;
}

function editVaccineNeed(vaccine) {
    var fields = {
        'vaccine_need_id': vaccine.id,
        'vaccine_name_input': vaccine.vaccine_name,
        'recommended_date_input': vaccine.recommended_date || '',
        'vaccine_status_input': vaccine.status,
        'vaccine_notes_input': vaccine.notes || ''
    };
    for (var id in fields) {
        var el = document.getElementById(id);
        if (el) el.value = fields[id];
    }
}

function handleVaccineNeedForm(event) {
    event.preventDefault();
    var form = event.target;
    var formData = new FormData(form);
    formData.append('ajax', 'true');

    showNotification('Saving vaccine need...', 'info');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification(data.message, 'success');
                form.reset();
                var needId = document.getElementById('vaccine_need_id');
                if (needId) needId.value = '';
                loadVaccineNeedsForModal(currentPatientForVaccine);
                loadPatientVaccineNeeds(currentPatientForVaccine);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            showNotification('Failed to save vaccine need', 'error');
        });
}

function deleteVaccineNeed(vaccineNeedId) {
    if (!confirm('Are you sure you want to delete this vaccine need?')) return;

    var formData = new FormData();
    formData.append('action', 'delete_vaccine_need');
    formData.append('vaccine_need_id', vaccineNeedId);
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    showNotification('Deleting vaccine need...', 'info');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification(data.message, 'success');
                loadVaccineNeedsForModal(currentPatientForVaccine);
                loadPatientVaccineNeeds(currentPatientForVaccine);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            showNotification('Failed to delete vaccine need', 'error');
        });
}

function loadPatientVaccineNeeds(patientId) {
    var formData = new FormData();
    formData.append('action', 'get_vaccine_needs');
    formData.append('patient_id', patientId);
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success && data.data) {
                renderPatientVaccineNeeds(data.data);
            }
        })
        .catch(function(error) { console.error('Error:', error); });
}

function renderPatientVaccineNeeds(vaccines) {
    var container = document.getElementById('patientVaccineNeedsList');
    if (!container) return;

    if (!vaccines || vaccines.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-4">No vaccine needs recorded</p>';
        return;
    }

    var statusColors = {
        'RECOMMENDED': 'bg-yellow-100 text-yellow-800',
        'SCHEDULED': 'bg-blue-100 text-blue-800',
        'GIVEN': 'bg-green-100 text-green-800',
        'NOT_NEEDED': 'bg-gray-100 text-gray-800'
    };

    var html = '<div class="space-y-3">';
    vaccines.forEach(function(vaccine) {
        var statusColor = statusColors[vaccine.status] || 'bg-gray-100 text-gray-800';
        var dateDisplay = vaccine.recommended_date
            ? new Date(vaccine.recommended_date).toLocaleDateString()
            : 'Not set';

        html +=
            '<div class="p-3 border border-gray-200 rounded-lg bg-white hover:shadow-md transition-shadow">' +
            '<div class="flex justify-between items-start mb-2"><div>' +
            '<div class="font-semibold text-gray-800 text-sm">' + escapeHtml(vaccine.vaccine_name) + '</div>' +
            '<div class="text-xs text-gray-600">Due: ' + dateDisplay + '</div></div>' +
            '<span class="px-2 py-1 text-xs font-semibold rounded ' + statusColor + '">' + escapeHtml(vaccine.status) + '</span></div>' +
            (vaccine.notes ? '<div class="text-xs text-gray-600 mb-2">' + escapeHtml(vaccine.notes) + '</div>' : '') +
            '</div>';
    });
    html += '</div>';
    container.innerHTML = html;
}

// ---- Vaccination Records ----
function openVaccinationModal() {
    if (!currentPatient) {
        showNotification('Please select a patient first', 'error');
        return;
    }
    var el = document.getElementById('vaccine_patient_id');
    if (el) el.value = currentPatient;
    var modal = document.getElementById('vaccinationModal');
    if (modal) modal.classList.remove('hidden');
}

function handleVaccinationForm(event) {
    event.preventDefault();
    var form = event.target;
    var formData = new FormData(form);
    formData.append('ajax', 'true');

    showNotification('Recording vaccination...', 'info');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification(data.message, 'success');
                closeModal('vaccinationModal');
                form.reset();
                var dateEl = document.getElementById('administration_date');
                if (dateEl) dateEl.value = new Date().toISOString().split('T')[0];
                if (currentPatient) loadPatientVaccinationHistory(currentPatient);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(function(error) {
            showNotification('Failed to record vaccination', 'error');
            console.error('Error:', error);
        });
}

function editVaccinationModal(vaccinationId) {
    var formData = new FormData();
    formData.append('action', 'get_vaccination_record');
    formData.append('vaccination_id', vaccinationId);
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success && data.record) {
                showVaccinationEditModal(data.record);
            } else {
                showNotification('Failed to load vaccination record', 'error');
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            showNotification('Failed to load vaccination record', 'error');
        });
}

function showVaccinationEditModal(record) {
    var fields = {
        'edit_vaccination_id': record.id,
        'edit_vaccine_patient_id': record.patient_id,
        'edit_vaccine_name': record.vaccine_name,
        'edit_dose_number': record.dose_number,
        'edit_administration_date': record.administration_date,
        'edit_lot_number': record.lot_number || '',
        'edit_vaccination_notes': record.notes || ''
    };
    for (var id in fields) {
        var el = document.getElementById(id);
        if (el) el.value = fields[id];
    }
    var modal = document.getElementById('editVaccinationModal');
    if (modal) modal.classList.remove('hidden');
}

function handleEditVaccinationForm(event) {
    event.preventDefault();
    var form = event.target;
    var formData = new FormData(form);
    formData.append('ajax', 'true');

    showNotification('Updating vaccination record...', 'info');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification(data.message, 'success');
                closeModal('editVaccinationModal');
                if (currentPatient) loadPatientVaccinationHistory(currentPatient);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(function(error) {
            showNotification('Failed to update vaccination record', 'error');
            console.error('Error:', error);
        });
}

function deleteVaccinationFromRecord(vaccinationId) {
    if (!confirm('Are you sure you want to delete this vaccination record?')) return;

    var formData = new FormData();
    formData.append('action', 'delete_vaccination_record');
    formData.append('vaccination_id', vaccinationId);
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    showNotification('Deleting vaccination record...', 'info');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification(data.message, 'success');
                if (currentPatient) loadPatientVaccinationHistory(currentPatient);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            showNotification('Failed to delete vaccination record', 'error');
        });
}

// ---- Modal Openers ----
function openConsultationModal() {
    if (!currentPatient) {
        showNotification('Please select a patient first', 'error');
        return;
    }
    var selectedPatient = document.querySelector('.patient-item.selected');
    if (selectedPatient) {
        var name = selectedPatient.querySelector('.font-semibold');
        var display = document.getElementById('consultationPatientName');
        if (name && display) display.textContent = name.textContent;
    }
    var patId = document.getElementById('consultation_patient_id');
    if (patId) patId.value = currentPatient;
    var modal = document.getElementById('consultationModal');
    if (modal) modal.classList.remove('hidden');
}

function openPrescriptionModal() {
    if (!currentPatient) {
        showNotification('Please select a patient first', 'error');
        return;
    }
    var selectedPatient = document.querySelector('.patient-item.selected');
    if (selectedPatient) {
        var name = selectedPatient.querySelector('.font-semibold');
        var display = document.getElementById('prescriptionPatientName');
        if (name && display) display.textContent = name.textContent;
    }
    var patId = document.getElementById('prescription_patient_id');
    if (patId) patId.value = currentPatient;
    var modal = document.getElementById('prescriptionModal');
    if (modal) modal.classList.remove('hidden');
}

function openMedicalRecordModal() {
    if (!currentPatient) {
        showNotification('Please select a patient first', 'error');
        return;
    }
    var selectedPatient = document.querySelector('.patient-item.selected');
    if (selectedPatient) {
        var name = selectedPatient.querySelector('.font-semibold');
        var display = document.getElementById('medicalRecordPatientName');
        if (name && display) display.textContent = name.textContent;
    }
    var patId = document.getElementById('medical_record_patient_id');
    if (patId) patId.value = currentPatient;
    var modal = document.getElementById('medicalRecordModal');
    if (modal) modal.classList.remove('hidden');
}

// ---- Form Handlers ----
function handleConsultationForm(event) {
    event.preventDefault();
    var form = event.target;
    var formData = new FormData(form);
    formData.append('ajax', 'true');

    showNotification('Saving consultation notes...', 'info');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification(data.message, 'success');
                closeModal('consultationModal');
                form.reset();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(function(error) {
            showNotification('Failed to save consultation notes', 'error');
            console.error('Error:', error);
        });
}

function handlePrescriptionForm(event) {
    event.preventDefault();
    var form = event.target;
    var formData = new FormData(form);
    formData.append('ajax', 'true');

    showNotification('Saving prescription...', 'info');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification(data.message, 'success');
                closeModal('prescriptionModal');
                form.reset();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(function(error) {
            showNotification('Failed to save prescription', 'error');
            console.error('Error:', error);
        });
}

function handleMedicalRecordForm(event) {
    event.preventDefault();
    var form = event.target;
    var formData = new FormData(form);
    formData.append('ajax', 'true');

    showNotification('Saving medical record...', 'info');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification(data.message, 'success');
                closeModal('medicalRecordModal');
                form.reset();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(function(error) {
            showNotification('Failed to save medical record', 'error');
            console.error('Error:', error);
        });
}

// ---- Appointment Actions ----
function approveAppointment(appointmentId) {
    var formData = new FormData();
    formData.append('csrf_token', window.CSRF_TOKEN || '');
    formData.append('action', 'approve_appointment');
    formData.append('appointment_id', appointmentId);
    formData.append('ajax', 'true');

    showNotification('Approving appointment...', 'info');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(function() { location.reload(); }, 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(function(error) {
            showNotification('Failed to approve appointment', 'error');
            console.error('Error:', error);
        });
}

function rejectAppointment(appointmentId) {
    var reason = prompt('Enter reason for rejection:');
    if (!reason) return;

    var formData = new FormData();
    formData.append('csrf_token', window.CSRF_TOKEN || '');
    formData.append('action', 'reject_appointment');
    formData.append('appointment_id', appointmentId);
    formData.append('reason', reason);
    formData.append('ajax', 'true');

    showNotification('Rejecting appointment...', 'info');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(function() { location.reload(); }, 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(function(error) {
            showNotification('Failed to reject appointment', 'error');
            console.error('Error:', error);
        });
}

function completeAppointment(appointmentId) {
    var formData = new FormData();
    formData.append('csrf_token', window.CSRF_TOKEN || '');
    formData.append('action', 'complete_appointment');
    formData.append('appointment_id', appointmentId);
    formData.append('ajax', 'true');

    showNotification('Completing appointment...', 'info');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(function() { location.reload(); }, 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(function(error) {
            showNotification('Failed to complete appointment', 'error');
            console.error('Error:', error);
        });
}

function filterAppointments() {
    var filter = document.getElementById('appointmentFilter');
    if (filter) {
        showNotification('Filtering appointments: ' + filter.value, 'info');
    }
}

function viewAppointmentDetails(appointmentId) {
    var formData = new FormData();
    formData.append('action', 'get_appointment_details');
    formData.append('appointment_id', appointmentId);
    formData.append('ajax', 'true');
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch('', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification('Appointment loaded', 'success');
                console.log('Appointment Details:', data.appointment);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(function(error) {
            showNotification('Failed to load appointment details', 'error');
            console.error('Error:', error);
        });
}

// ---- Close Modals ----
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-backdrop')) {
        closeAllModals();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAllModals();
    }
});
