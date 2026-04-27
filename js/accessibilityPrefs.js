/**
 * accessibilityPrefs.js
 * WCAG 2.2 AAA reading/display preference toggles.
 */
(function () {
  'use strict';

  var STORAGE_KEY = 'wyp_accessibility_prefs_v1';
  var mapping = {
    contrast: 'pref-contrast',
    spacing: 'pref-spacing',
    width: 'pref-width'
  };

  function readPrefs() {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      return raw ? JSON.parse(raw) : {};
    } catch (e) {
      return {};
    }
  }

  function writePrefs(prefs) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(prefs));
    } catch (e) {
      // No-op when storage is unavailable.
    }
  }

  function applyPrefs(prefs) {
    Object.keys(mapping).forEach(function (key) {
      document.body.classList.toggle(mapping[key], !!prefs[key]);
    });
  }

  function syncButtons(prefs) {
    var buttons = document.querySelectorAll('[data-pref-toggle]');
    buttons.forEach(function (button) {
      var key = button.getAttribute('data-pref-toggle');
      button.setAttribute('aria-pressed', prefs[key] ? 'true' : 'false');
    });
  }

  function init() {
    var buttons = document.querySelectorAll('[data-pref-toggle]');
    if (!buttons.length) return;

    var prefs = readPrefs();
    applyPrefs(prefs);
    syncButtons(prefs);

    buttons.forEach(function (button) {
      button.addEventListener('click', function () {
        var key = button.getAttribute('data-pref-toggle');
        prefs[key] = !prefs[key];
        applyPrefs(prefs);
        syncButtons(prefs);
        writePrefs(prefs);
      });
    });
  }

  if (document.readyState !== 'loading') {
    init();
  } else {
    document.addEventListener('DOMContentLoaded', init);
  }
})();
