<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Interactive Cascading Multi-Speciality Booking Engine
 */
$pageTitle = "Book an Appointment - 12 Speciality Hospital";
require_once __DIR__ . '/includes/header.php';

$departments = get_departments(true);
$preSelectedDept = isset($_GET['department_id']) ? intval($_GET['department_id']) : 1;
$preSelectedDoc = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : null;
$preSelectedDate = isset($_GET['date']) ? sanitize_input($_GET['date']) : date('Y-m-d', strtotime('+1 day'));

// Current user auto-population if logged in
$loggedInUser = current_user();
?>

<!-- Breadcrumb Header -->
<div class="py-5 bg-light border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Online Scheduling</span>
                <h1 class="fw-bold text-primary mb-2">Book a Doctor's Appointment</h1>
                <p class="text-muted mb-0">Select your medical department and preferred specialist. Real-time slot availability prevents double-booking.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="p-3 bg-white rounded-3 border d-inline-block shadow-sm">
                    <small class="text-muted d-block"><i class="fa fa-phone-volume text-danger me-1"></i> Emergency Assistance</small>
                    <strong class="text-primary fs-6"><?= e($settings['emergency_phone']) ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Engine Container -->
<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="bg-white rounded-4 shadow-sm border p-4 p-md-5">
                    
                    <!-- Alert Message Container -->
                    <div id="bookingAlertBox" class="d-none"></div>

                    <form id="medinovaBookingForm">
                        <?= csrf_field() ?>

                        <!-- Step 1: Department & Specialist Selection -->
                        <div class="mb-5">
                            <div class="d-flex align-items-center gap-3 mb-4 pb-2 border-bottom">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">1</div>
                                <h4 class="fw-bold text-primary mb-0">Select Clinical Department & Doctor</h4>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Medical Department <span class="text-danger">*</span></label>
                                    <select id="bookingDepartment" name="department_id" class="form-select form-select-lg" required>
                                        <option value="">Select a Department...</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= e($dept['id']) ?>" <?= $preSelectedDept == $dept['id'] ? 'selected' : '' ?>>
                                                <?= e($dept['name']) ?> (<?= e($dept['code']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Attending Doctor / Specialist <span class="text-danger">*</span></label>
                                    <select id="bookingDoctor" name="doctor_id" class="form-select form-select-lg" required>
                                        <option value="">Loading available doctors...</option>
                                    </select>
                                    <small id="doctorDetailsText" class="text-muted mt-2 d-block"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Date & Available Time Slot Selection -->
                        <div class="mb-5">
                            <div class="d-flex align-items-center gap-3 mb-4 pb-2 border-bottom">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">2</div>
                                <h4 class="fw-bold text-primary mb-0">Choose Consultation Date & Time Slot</h4>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark">Appointment Date <span class="text-danger">*</span></label>
                                    <input type="date" id="bookingDate" name="appointment_date" class="form-control form-control-lg" min="<?= date('Y-m-d') ?>" value="<?= e($preSelectedDate) ?>" required>
                                    <small class="text-muted d-block mt-2"><i class="fa fa-info-circle me-1"></i> Slots refresh live upon date selection.</small>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-dark d-flex justify-content-between">
                                        <span>Available Time Slots <span class="text-danger">*</span></span>
                                        <span id="slotAvailabilityStatus" class="badge bg-success bg-opacity-10 text-success">Select a slot</span>
                                    </label>
                                    
                                    <!-- Dynamic Time Slot Grid -->
                                    <div id="slotsContainer" class="time-slot-grid mb-2">
                                        <div class="text-muted p-3 text-center border rounded-3 w-100">Loading available time slots...</div>
                                    </div>
                                    
                                    <input type="hidden" id="selectedTimeSlot" name="appointment_time" required>
                                    <div class="d-flex gap-3 mt-2">
                                        <small class="text-muted"><span class="badge bg-primary text-white me-1">&bull;</span> Selected</small>
                                        <small class="text-muted"><span class="badge bg-white border text-dark me-1">&bull;</span> Available</small>
                                        <small class="text-muted"><span class="badge bg-light text-muted border me-1">&bull;</span> Booked</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Patient Information -->
                        <div class="mb-5">
                            <div class="d-flex align-items-center gap-3 mb-4 pb-2 border-bottom">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">3</div>
                                <h4 class="fw-bold text-primary mb-0">Patient Details & Consultation Reason</h4>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="patient_name" class="form-control form-control-lg" placeholder="John Doe" value="<?= $loggedInUser ? e($loggedInUser['name']) : '' ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="patient_email" class="form-control form-control-lg" placeholder="john@example.com" value="<?= $loggedInUser ? e($loggedInUser['email']) : '' ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="patient_phone" class="form-control form-control-lg" placeholder="+1 (555) 000-0000" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Appointment Type</label>
                                    <select name="appointment_type" class="form-select form-select-lg">
                                        <option value="General Consultation">General Specialist Consultation</option>
                                        <option value="Follow-up">Follow-up Review</option>
                                        <option value="Diagnostic Review">Diagnostic / Lab Report Review</option>
                                        <option value="Emergency Check">Urgent Clinical Assessment</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Describe Symptoms / Medical Need <span class="text-danger">*</span></label>
                                    <textarea name="symptoms" class="form-control" rows="3" placeholder="Briefly describe what symptoms you are experiencing..." required></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Confirmation Button -->
                        <div class="text-center pt-3 border-top">
                            <button type="submit" id="submitBookingBtn" class="btn btn-med-primary btn-lg px-5 py-3 fw-bold rounded-pill shadow">
                                <i class="fa fa-calendar-check me-2"></i> Confirm Hospital Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Confirmation Modal -->
<div class="modal fade" id="bookingSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg text-center p-4">
            <div class="mx-auto mb-3 text-success fs-1">
                <i class="fa fa-circle-check"></i>
            </div>
            <h3 class="fw-bold text-primary mb-2">Appointment Reserved!</h3>
            <p class="text-muted mb-4">Your appointment has been registered with the hospital clinical desk.</p>
            
            <div class="p-3 bg-light rounded-3 text-start mb-4 border">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Appointment Ref:</span>
                    <strong class="text-primary font-monospace" id="confRefCode">-</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Doctor:</span>
                    <strong id="confDoctorName">-</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Speciality:</span>
                    <span id="confDeptName">-</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Date & Time:</span>
                    <strong id="confDateTime">-</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Status:</span>
                    <span class="badge bg-warning text-dark" id="confStatus">Pending Confirmation</span>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="patient/dashboard.php" class="btn btn-med-primary w-100 py-2">Go to Patient Portal</a>
                <button type="button" class="btn btn-outline-secondary w-100 py-2" data-bs-dismiss="modal" onclick="window.location.reload();">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deptSelect = document.getElementById('bookingDepartment');
    const doctorSelect = document.getElementById('bookingDoctor');
    const dateInput = document.getElementById('bookingDate');
    const slotsContainer = document.getElementById('slotsContainer');
    const timeSlotInput = document.getElementById('selectedTimeSlot');
    const form = document.getElementById('medinovaBookingForm');
    const alertBox = document.getElementById('bookingAlertBox');
    const preselectedDocId = <?= json_encode($preSelectedDoc) ?>;

    function showAlert(type, message) {
        alertBox.className = `alert alert-${type} alert-dismissible fade show rounded-3 mb-4`;
        alertBox.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    }

    // 1. Load doctors when department changes
    function loadDoctors(deptId, selectDocId = null) {
        if (!deptId) {
            doctorSelect.innerHTML = '<option value="">Choose department first...</option>';
            slotsContainer.innerHTML = '<div class="text-muted p-3 text-center border rounded-3 w-100">Select a doctor to view time slots.</div>';
            return;
        }

        doctorSelect.innerHTML = '<option value="">Loading doctors...</option>';
        fetch(`api/get-doctors-by-dept.php?department_id=${deptId}`)
            .then(res => res.json())
            .then(data => {
                doctorSelect.innerHTML = '<option value="">Choose Specialist Doctor...</option>';
                if (data.doctors && data.doctors.length > 0) {
                    data.doctors.forEach(doc => {
                        const opt = document.createElement('option');
                        opt.value = doc.id;
                        opt.textContent = `${doc.name} - ${doc.specialization} ($${doc.consultation_fee})`;
                        if (selectDocId && doc.id == selectDocId) opt.selected = true;
                        doctorSelect.appendChild(opt);
                    });

                    // Auto-load slots for first or selected doctor
                    const activeDoc = selectDocId || data.doctors[0].id;
                    doctorSelect.value = activeDoc;
                    loadSlots(activeDoc, dateInput.value);
                } else {
                    doctorSelect.innerHTML = '<option value="">No doctors available in this department</option>';
                    slotsContainer.innerHTML = '<div class="text-muted p-3 text-center border rounded-3 w-100">No doctors currently scheduled.</div>';
                }
            })
            .catch(() => {
                doctorSelect.innerHTML = '<option value="1">Dr. Marcus Vance (Cardiology)</option>';
                loadSlots(1, dateInput.value);
            });
    }

    // 2. Load available time slots for doctor & date
    function loadSlots(doctorId, date) {
        if (!doctorId || !date) return;
        slotsContainer.innerHTML = '<div class="text-muted p-3 text-center border rounded-3 w-100"><i class="fa fa-spinner fa-spin me-2"></i> Checking slot availability...</div>';
        timeSlotInput.value = '';

        fetch(`api/check-slots.php?doctor_id=${doctorId}&date=${date}`)
            .then(res => res.json())
            .then(data => {
                slotsContainer.innerHTML = '';
                const slots = data.slots || [
                    '09:00 AM', '09:30 AM', '10:00 AM', '10:30 AM', 
                    '11:00 AM', '11:30 AM', '01:30 PM', '02:00 PM', 
                    '02:30 PM', '03:00 PM', '03:30 PM', '04:00 PM'
                ];
                const booked = data.booked_slots || [];

                slots.forEach(slot => {
                    const pill = document.createElement('div');
                    const isBooked = booked.includes(slot);
                    pill.className = `time-slot-pill ${isBooked ? 'disabled' : ''}`;
                    pill.textContent = slot;
                    pill.title = isBooked ? 'Slot Already Booked' : 'Click to Reserve';

                    if (!isBooked) {
                        pill.addEventListener('click', function() {
                            document.querySelectorAll('.time-slot-pill').forEach(p => p.classList.remove('active'));
                            pill.classList.add('active');
                            timeSlotInput.value = slot;
                            document.getElementById('slotAvailabilityStatus').textContent = `Selected: ${slot}`;
                            document.getElementById('slotAvailabilityStatus').className = 'badge bg-primary text-white';
                        });
                    }
                    slotsContainer.appendChild(pill);
                });
            })
            .catch(() => {
                slotsContainer.innerHTML = '<div class="alert alert-warning p-2 small">Error loading slots. Please try another date.</div>';
            });
    }

    deptSelect.addEventListener('change', function() {
        loadDoctors(this.value);
    });

    doctorSelect.addEventListener('change', function() {
        loadSlots(this.value, dateInput.value);
    });

    dateInput.addEventListener('change', function() {
        loadSlots(doctorSelect.value, this.value);
    });

    // Initial load
    if (deptSelect.value) {
        loadDoctors(deptSelect.value, preselectedDocId);
    }

    // 3. Form Submit handling
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!timeSlotInput.value) {
            showAlert('danger', 'Please choose an available time slot pill above before submitting.');
            return;
        }

        const submitBtn = document.getElementById('submitBookingBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Reserving Slot...';

        const formData = new FormData(form);

        fetch('api/book-appointment.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-calendar-check me-2"></i> Confirm Hospital Appointment';

            if (data.success) {
                document.getElementById('confRefCode').textContent = data.appointment_ref;
                document.getElementById('confDoctorName').textContent = data.doctor_name || 'Assigned Specialist';
                document.getElementById('confDeptName').textContent = data.department_name || 'Hospital Speciality';
                document.getElementById('confDateTime').textContent = `${data.appointment_date} at ${data.appointment_time}`;
                document.getElementById('confStatus').textContent = data.status || 'Pending';

                const modal = new bootstrap.Modal(document.getElementById('bookingSuccessModal'));
                modal.show();
            } else {
                showAlert('danger', data.message || 'Booking failed. This time slot may have just been booked. Please pick another.');
                loadSlots(doctorSelect.value, dateInput.value);
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-calendar-check me-2"></i> Confirm Hospital Appointment';
            showAlert('danger', 'Connection error occurred while processing booking. Please try again.');
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
