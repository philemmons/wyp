/**
 * Contains ONLY logic previously inline in footer.php
 * (CSP-compliant replacement)
 *
 * Does NOT include:
 * - back_to_top_button.js (kept separate)
 */

(function () {
  'use strict';


  // DOM Ready Helper
  function runWhenDomReady(callback) {
    if (document.readyState !== 'loading') {
      callback();
    } else {
      document.addEventListener('DOMContentLoaded', callback);
    }
  }

  runWhenDomReady(function () {

    // Scroll Animations (IntersectionObserver)
    const animatedElements = document.querySelectorAll('[data-animate]');

    // Respect user motion preferences
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!animatedElements.length) return;

    // If user prefers reduced motion → show immediately
    if (prefersReducedMotion) {
      animatedElements.forEach(animatedElement => animatedElement.classList.add('animate-in'));
      return;
    }

    // Modern browser support
    if ('IntersectionObserver' in window) {

      const animationObserver = new IntersectionObserver((intersectionEntries) => {
        intersectionEntries.forEach(intersectionEntry => {
          if (intersectionEntry.isIntersecting) {
            intersectionEntry.target.classList.add('animate-in');
            animationObserver.unobserve(intersectionEntry.target);
          }
        });
      }, {
        threshold: 0.15
      });

      animatedElements.forEach(animatedElement => {
        animatedElement.classList.add('animate-init');
        animationObserver.observe(animatedElement);
      });

    } else {
      // Fallback for older browsers
      animatedElements.forEach(animatedElement => animatedElement.classList.add('animate-in'));
    }

  });

})();
