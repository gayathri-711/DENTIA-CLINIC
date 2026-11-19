/**
 * Doctor Dental Clinic Dentia - Interactive Client-Side Logic
 * Slot Availability Checker, Duplicate Booking Prevention, and Dynamic UI
 */

(function($) {
  'use strict';

  $(document).ready(function() {

    // --- Dynamic Time Slot Availability Checker ---
    function updateAvailableSlots() {
      var dentistId = $('#dentist_select').val();
      var selectedDate = $('#appointment_date').val();
      var slotContainer = $('#time_slots_container');
      var hiddenTimeInput = $('#selected_time');

      if (!dentistId || !selectedDate) {
        slotContainer.html('<div class="text-muted small p-2"><i class="fa fa-info-circle me-1"></i> Please select a dentist and appointment date to view available time slots.</div>');
        return;
      }

      slotContainer.html('<div class="text-muted small p-2"><i class="fa fa-spinner fa-spin me-1"></i> Checking real-time slot availability...</div>');

      $.ajax({
        url: 'api/check-slots.php',
        type: 'GET',
        dataType: 'json',
        data: {
          dentist_id: dentistId,
          date: selectedDate
        },
        success: function(response) {
          if (response.success) {
            var slots = response.slots;
            var booked = response.booked || [];
            var html = '<div class="slot-container">';

            if (slots.length === 0) {
              html += '<p class="text-danger small">No clinic hours available on this date.</p>';
            } else {
              slots.forEach(function(slot) {
                var isBooked = booked.indexOf(slot) !== -1;
                if (isBooked) {
                  html += '<div class="slot-pill booked" title="Already booked - unavailable">' + slot + ' <span class="d-block small" style="font-size:10px;">Booked</span></div>';
                } else {
                  var isSelected = (hiddenTimeInput.val() === slot) ? ' selected' : '';
                  html += '<div class="slot-pill' + isSelected + '" data-slot="' + slot + '">' + slot + '</div>';
                }
              });
            }
            html += '</div>';
            slotContainer.html(html);

            // Bind click event on available slots
            $('.slot-pill:not(.booked)').on('click', function() {
              $('.slot-pill').removeClass('selected');
              $(this).addClass('selected');
              var chosenSlot = $(this).data('slot');
              hiddenTimeInput.val(chosenSlot);
              $('#slot_error_msg').addClass('d-none');
            });

          } else {
            slotContainer.html('<div class="text-danger small">' + (response.message || 'Error checking slots') + '</div>');
          }
        },
        error: function() {
          // Fallback slots if API is unreachable
          var defaultSlots = ['09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM'];
          var html = '<div class="slot-container">';
          defaultSlots.forEach(function(slot) {
            html += '<div class="slot-pill" data-slot="' + slot + '">' + slot + '</div>';
          });
          html += '</div>';
          slotContainer.html(html);

          $('.slot-pill').on('click', function() {
            $('.slot-pill').removeClass('selected');
            $(this).addClass('selected');
            hiddenTimeInput.val($(this).data('slot'));
          });
        }
      });
    }

    // Trigger slot updates when dentist or date changes
    $('#dentist_select, #appointment_date').on('change', function() {
      updateAvailableSlots();
    });

    // Run on initial load if elements exist and have values
    if ($('#dentist_select').length && $('#appointment_date').length) {
      if ($('#dentist_select').val() && $('#appointment_date').val()) {
        updateAvailableSlots();
      }
    }

    // --- Interactive Appointment Booking Submission ---
    $('#dentia_booking_form').on('submit', function(e) {
      e.preventDefault();

      var form = $(this);
      var submitBtn = form.find('button[type="submit"]');
      var alertBox = $('#booking_alert_box');
      var hiddenTimeInput = $('#selected_time');

      if (!hiddenTimeInput.val()) {
        alertBox.removeClass('d-none alert-success').addClass('alert-danger')
                .html('<i class="fa fa-exclamation-triangle me-2"></i> Please click and select an available time slot above.');
        $('html, body').animate({ scrollTop: $('#time_slots_section').offset().top - 100 }, 400);
        return false;
      }

      submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Confirming Reservation...');
      alertBox.addClass('d-none').removeClass('alert-danger alert-success');

      $.ajax({
        url: 'api/book-appointment.php',
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(res) {
          submitBtn.prop('disabled', false).html('<span>Book Appointment Now</span>');

          if (res.success) {
            // Render beautiful Confirmation Card
            var confHtml = `
              <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center p-4 p-md-5 my-4">
                <div class="mb-4">
                  <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle shadow-sm" style="width:72px; height:72px; font-size:32px;">
                    <i class="fa fa-check"></i>
                  </div>
                </div>
                <h3 class="fw-bold text-dark mb-2">Appointment Confirmed!</h3>
                <p class="text-muted mb-4">Your visit has been scheduled. A confirmation summary has been saved to your account and emailed to <strong>${res.details.email}</strong>.</p>
                
                <div class="card bg-light border-0 rounded-3 p-4 text-start mb-4 mx-auto" style="max-width: 500px;">
                  <div class="row g-2">
                    <div class="col-6 text-muted">Booking Reference:</div>
                    <div class="col-6 fw-bold text-primary">${res.ref}</div>
                    
                    <div class="col-6 text-muted">Dental Service:</div>
                    <div class="col-6 fw-bold">${res.details.service_name}</div>
                    
                    <div class="col-6 text-muted">Doctor:</div>
                    <div class="col-6 fw-bold">${res.details.dentist_name}</div>
                    
                    <div class="col-6 text-muted">Date & Time:</div>
                    <div class="col-6 fw-bold text-success">${res.details.date} at ${res.details.time}</div>
                    
                    <div class="col-6 text-muted">Clinic Location:</div>
                    <div class="col-6 fw-bold">45 Cherry Road, Near New Bus Stand, Salem, Tamil Nadu</div>
                  </div>
                </div>

                <div class="d-flex justify-content-center gap-3">
                  <a href="booking.php" class="btn btn-outline-primary rounded-pill px-4">Book Another</a>
                  <a href="patient-dashboard.php" class="btn btn-primary rounded-pill px-4">View in Dashboard</a>
                </div>
              </div>
            `;
            $('#booking_form_wrapper').html(confHtml);
            $('html, body').animate({ scrollTop: $('#booking_form_wrapper').offset().top - 80 }, 400);

          } else {
            alertBox.removeClass('d-none alert-success').addClass('alert-danger')
                    .html('<i class="fa fa-exclamation-circle me-2"></i> ' + res.message);
            // Refresh slots to show newly occupied slots if duplicate detected
            updateAvailableSlots();
            $('html, body').animate({ scrollTop: alertBox.offset().top - 120 }, 400);
          }
        },
        error: function(xhr) {
          submitBtn.prop('disabled', false).html('<span>Book Appointment Now</span>');
          alertBox.removeClass('d-none alert-success').addClass('alert-danger')
                  .html('<i class="fa fa-exclamation-triangle me-2"></i> An unexpected server error occurred. Please try again.');
        }
      });
    });

    // --- Services Real-time Search Filter ---
    $('#service_search_input').on('keyup', function() {
      var query = $(this).val().toLowerCase().trim();
      $('.service-filter-item').each(function() {
        var text = $(this).text().toLowerCase();
        if (text.indexOf(query) !== -1) {
          $(this).fadeIn(200);
        } else {
          $(this).fadeOut(200);
        }
      });
    });

  });

})(jQuery);

