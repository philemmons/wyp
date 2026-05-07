(function () {
  'use strict';

  // Cache key elements once so handlers reuse the same nodes and avoid repeated DOM queries.
  var contactForm = document.getElementById('contactForm');
  var contactFormStatusSummary = document.getElementById('contactFormStatusSummary');
  var recaptchaGroup = document.getElementById('recaptcha-group');
  var recaptchaWidgetContainer = document.querySelector('.g-recaptcha[data-sitekey]');
  var recaptchaLoadError = document.getElementById('recaptchaLoadError');
  var recaptchaValidationError = document.getElementById('recaptchaValidationError');
  var userPrefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var hasRecaptchaRendered = false;

  if (!contactForm) {
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

    if (recaptchaGroup) {
      recaptchaGroup.setAttribute('aria-invalid', 'true');
    }

    recaptchaValidationError.textContent = message;
    recaptchaValidationError.classList.remove('d-none');
    if (typeof recaptchaValidationError.focus === 'function') {
      recaptchaValidationError.focus();
    }
  }

  function hideRecaptchaValidationError() {
    if (!recaptchaValidationError) {
      return;
    }

    if (recaptchaGroup) {
      recaptchaGroup.removeAttribute('aria-invalid');
    }

    recaptchaValidationError.textContent = '';
    recaptchaValidationError.classList.add('d-none');
  }

  function initializeRecaptchaWidget() {
    if (!recaptchaWidgetContainer) {
      return;
    }

    var recaptchaSiteKey = (recaptchaWidgetContainer.getAttribute('data-sitekey') || '').trim();
    if (!recaptchaSiteKey) {
      showRecaptchaLoadError();
      return;
    }

    window.wypRecaptchaOnload = function () {
      if (!window.grecaptcha || typeof window.grecaptcha.render !== 'function') {
        showRecaptchaLoadError();
        return;
      }

      try {
        window.grecaptcha.render(recaptchaWidgetContainer, { sitekey: recaptchaSiteKey });
        hasRecaptchaRendered = true;
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
      if (!hasRecaptchaRendered) {
        showRecaptchaLoadError();
      }
    }, 7000);
  }

  initializeRecaptchaWidget();

  // Move focus to the first invalid control so keyboard users land where correction is needed first.
  function focusFirstInvalidField() {
    var firstInvalidField = contactForm.querySelector('[aria-invalid="true"], :invalid');
    if (firstInvalidField && typeof firstInvalidField.focus === 'function') {
      firstInvalidField.focus();
      return;
    }

    if (
      recaptchaValidationError &&
      !recaptchaValidationError.classList.contains('d-none') &&
      typeof recaptchaValidationError.focus === 'function'
    ) {
      recaptchaValidationError.focus();
    }
  }

  // Mirror Bootstrap validation behavior on submit so invalid forms never post to the server.
  contactForm.addEventListener('submit', function (event) {
    hideRecaptchaValidationError();

    if (!contactForm.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
      contactForm.classList.add('was-validated');
      focusFirstInvalidField();
      return;
    }

    if (recaptchaWidgetContainer) {
      var recaptchaResponseField = contactForm.querySelector('textarea[name="g-recaptcha-response"]');
      var hasRecaptchaResponse = recaptchaResponseField && recaptchaResponseField.value.trim() !== '';

      if (!hasRecaptchaResponse) {
        event.preventDefault();
        event.stopPropagation();
        showRecaptchaValidationError('Please complete reCAPTCHA before submitting.');

        if (recaptchaWidgetContainer && typeof recaptchaWidgetContainer.scrollIntoView === 'function') {
          recaptchaWidgetContainer.scrollIntoView({
            behavior: userPrefersReducedMotion ? 'auto' : 'smooth',
            block: 'center'
          });
        }
        return;
      }
    }

    contactForm.classList.add('was-validated');
  });

  // Focus summary after server-side errors so assistive tech announces issues immediately.
  if (contactFormStatusSummary && contactFormStatusSummary.getAttribute('data-form-status') === 'error') {
    contactFormStatusSummary.focus();
  }
})();
