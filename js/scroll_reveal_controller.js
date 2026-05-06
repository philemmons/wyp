/**
 * Contains ONLY logic previously inline in footer.php
 * (CSP-compliant replacement)
 *
 * Does NOT include:
 * - back_to_top_control.js (kept separate)
 */

(function () {
  'use strict';


  // DOM Ready Helper
  function runAfterDomReady(onDomReady) {
    if (document.readyState !== 'loading') {
      onDomReady();
    } else {
      document.addEventListener('DOMContentLoaded', onDomReady);
    }
  }

  runAfterDomReady(function () {

    // Scroll Animations (IntersectionObserver)
    const animationTargetElements = document.querySelectorAll('[data-animate]');

    // Respect user motion preferences
    const userPrefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!animationTargetElements.length) return;

    // If user prefers reduced motion → show immediately
    if (userPrefersReducedMotion) {
      animationTargetElements.forEach(animationTargetElement => animationTargetElement.classList.add('animate-in'));
      return;
    }

    // Modern browser support
    if ('IntersectionObserver' in window) {

      const revealObserver = new IntersectionObserver((visibilityEntries) => {
        visibilityEntries.forEach(visibilityEntry => {
          if (visibilityEntry.isIntersecting) {
            visibilityEntry.target.classList.add('animate-in');
            revealObserver.unobserve(visibilityEntry.target);
          }
        });
      }, {
        threshold: 0.15
      });

      animationTargetElements.forEach(animationTargetElement => {
        animationTargetElement.classList.add('animate-init');
        revealObserver.observe(animationTargetElement);
      });

    } else {
      // Fallback for older browsers
      animationTargetElements.forEach(animationTargetElement => animationTargetElement.classList.add('animate-in'));
    }

  });

})();
