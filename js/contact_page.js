(function () {
  'use strict';

  // Cache key elements once so handlers reuse the same nodes and avoid repeated DOM queries.
  var myForm = document.getElementById('myForm');
  var resetFormButton = document.getElementById('resetFormButton');
  var formErrorSummary = document.getElementById('formErrorSummary');

  if (!myForm) {
    return;
  }

  // Move focus to the first invalid control so keyboard users land where correction is needed first.
  function focusFirstInvalidField() {
    var firstInvalidField = myForm.querySelector('[aria-invalid="true"], :invalid');
    if (firstInvalidField && typeof firstInvalidField.focus === 'function') {
      firstInvalidField.focus();
    }
  }

  // Mirror Bootstrap validation behavior on submit so invalid forms never post to the server.
  myForm.addEventListener('submit', function (event) {
    if (!myForm.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
      myForm.classList.add('was-validated');
      focusFirstInvalidField();
      return;
    }

    myForm.classList.add('was-validated');
  });

  // Confirm reset to prevent accidental data loss, then clear validation state for a clean retry.
  if (resetFormButton) {
    resetFormButton.addEventListener('click', function (event) {
      var shouldReset = window.confirm('Clear all form fields?');
      if (!shouldReset) {
        event.preventDefault();
        return;
      }

      window.setTimeout(function () {
        myForm.classList.remove('was-validated');

        var invalidMarkedFields = myForm.querySelectorAll('[aria-invalid="true"]');
        invalidMarkedFields.forEach(function (field) {
          field.removeAttribute('aria-invalid');
        });
      }, 0);
    });
  }

  // Focus summary after server-side errors so assistive tech announces issues immediately.
  if (formErrorSummary) {
    formErrorSummary.focus();
    focusFirstInvalidField();
  }
})();
