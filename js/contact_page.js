(function () {
  'use strict';

  // Cache key elements once so handlers reuse the same nodes and avoid repeated DOM queries.
  var myForm = document.getElementById('myForm');
  var resetFormButton = document.getElementById('resetFormButton');
  var formErrorSummary = document.getElementById('formErrorSummary');
  var recaptchaContainer = document.querySelector('.g-recaptcha[data-sitekey]');
  var recaptchaLoadError = document.getElementById('recaptchaLoadError');
  var recaptchaValidationError = document.getElementById('recaptchaValidationError');

  var recaptchaDidRender = false;

  if (!myForm) {
    return;
  }

  function showRecaptchaLoadError() {
    if (recaptchaLoadError) {
      recaptchaLoadError.classList.remove('d-none');
    }
  }

  function hideRecaptchaLoadError() {
    if (recaptchaLoadError) {
      recaptchaLoadError.classList.add('d-none');
    }
  }

  function showRecaptchaValidationError(message) {
    if (!recaptchaValidationError) {
      return;
    }

    recaptchaValidationError.textContent = message;
    recaptchaValidationError.classList.remove('d-none');
  }

  function hideRecaptchaValidationError() {
    if (!recaptchaValidationError) {
      return;
    }

    recaptchaValidationError.textContent = '';
    recaptchaValidationError.classList.add('d-none');
  }

  function initializeRecaptcha() {
    if (!recaptchaContainer) {
      return;
    }

    var siteKey = (recaptchaContainer.getAttribute('data-sitekey') || '').trim();
    if (!siteKey) {
      showRecaptchaLoadError();
      return;
    }

    window.wypRecaptchaOnload = function () {
      if (!window.grecaptcha || typeof window.grecaptcha.render !== 'function') {
        showRecaptchaLoadError();
        return;
      }

      try {
        window.grecaptcha.render(recaptchaContainer, { sitekey: siteKey });
        recaptchaDidRender = true;
        hideRecaptchaLoadError();
      } catch (error) {
        showRecaptchaLoadError();
      }
    };

    var existingRecaptchaScript = document.querySelector('script[src*="google.com/recaptcha/api.js"]');
    if (!existingRecaptchaScript) {
      var recaptchaApiScript = document.createElement('script');
      recaptchaApiScript.src = 'https://www.google.com/recaptcha/api.js?onload=wypRecaptchaOnload&render=explicit';
      recaptchaApiScript.async = true;
      recaptchaApiScript.defer = true;
      recaptchaApiScript.onerror = showRecaptchaLoadError;
      document.head.appendChild(recaptchaApiScript);
    }

    window.setTimeout(function () {
      if (!recaptchaDidRender) {
        showRecaptchaLoadError();
      }
    }, 7000);
  }

  initializeRecaptcha();

  // Move focus to the first invalid control so keyboard users land where correction is needed first.
  function focusFirstInvalidField() {
    var firstInvalidField = myForm.querySelector('[aria-invalid="true"], :invalid');
    if (firstInvalidField && typeof firstInvalidField.focus === 'function') {
      firstInvalidField.focus();
    }
  }

  // Mirror Bootstrap validation behavior on submit so invalid forms never post to the server.
  myForm.addEventListener('submit', function (event) {
    hideRecaptchaValidationError();

    if (!myForm.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
      myForm.classList.add('was-validated');
      focusFirstInvalidField();
      return;
    }

    if (recaptchaContainer) {
      var recaptchaResponseField = myForm.querySelector('textarea[name="g-recaptcha-response"]');
      var hasRecaptchaResponse = recaptchaResponseField && recaptchaResponseField.value.trim() !== '';

      if (!hasRecaptchaResponse) {
        event.preventDefault();
        event.stopPropagation();
        showRecaptchaValidationError('Please complete reCAPTCHA before submitting.');

        if (recaptchaContainer && typeof recaptchaContainer.scrollIntoView === 'function') {
          recaptchaContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
      }
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
        hideRecaptchaValidationError();

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
    if (formErrorSummary.getAttribute('data-form-status') === 'error') {
      focusFirstInvalidField();
    }
  }
})();
