/**
 * app.js
 * ----------------------------------------
 * Contains ONLY logic previously inline in footer.php
 * (CSP-compliant replacement)
 *
 * Does NOT include:
 * - backToTop.js (kept separate)
 */

(function () {
  'use strict';

  // ─────────────────────────────────────────────
  // DOM Ready Helper
  // ─────────────────────────────────────────────
  function ready(fn) {
    if (document.readyState !== 'loading') {
      fn();
    } else {
      document.addEventListener('DOMContentLoaded', fn);
    }
  }

  ready(function () {

    // =========================================================
    // Scroll Animations (IntersectionObserver)
    // =========================================================
    const elements = document.querySelectorAll('[data-animate]');

    // Respect user motion preferences
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!elements.length) return;

    // If user prefers reduced motion → show immediately
    if (prefersReducedMotion) {
      elements.forEach(el => el.classList.add('animate-in'));
      return;
    }

    // Modern browser support
    if ('IntersectionObserver' in window) {

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate-in');
            observer.unobserve(entry.target);
          }
        });
      }, {
        threshold: 0.15
      });

      elements.forEach(el => {
        el.classList.add('animate-init');
        observer.observe(el);
      });

    } else {
      // Fallback for older browsers
      elements.forEach(el => el.classList.add('animate-in'));
    }

  });

})();