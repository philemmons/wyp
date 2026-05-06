/**
 * Handles visibility and behavior of the "Back to Top" button.
 *
 * Improvements:
 * - Uses addEventListener (no global overrides)
 * - Throttled scroll handling via requestAnimationFrame
 * - Passive event listener for better performance
 * - Smooth scroll with reduced-motion accessibility support
 * - Safe element checks to prevent JS errors
 */

(function () {
  'use strict';


  // Config
  const SCROLL_OFFSET_TO_REVEAL_BUTTON = 200; // px before button appears


  // Element reference
  const backToTopButton = document.getElementById('back-to-top-link');

  // Exit early if element is not present (prevents errors)
  if (!backToTopButton) return;


  // Accessibility: Reduced motion preference
  const userPrefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;


  // Scroll handler (throttled)
  let isAnimationFramePending = false;

  function updateBackToTopVisibility() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > SCROLL_OFFSET_TO_REVEAL_BUTTON) {
      backToTopButton.classList.add('visible');
      backToTopButton.setAttribute('tabindex', '0');
      backToTopButton.setAttribute('aria-hidden', 'false');
    } else {
      backToTopButton.classList.remove('visible');
      backToTopButton.setAttribute('tabindex', '-1');
      backToTopButton.setAttribute('aria-hidden', 'true');
    }

    isAnimationFramePending = false;
  }

  function queueBackToTopVisibilityUpdate() {
    if (!isAnimationFramePending) {
      window.requestAnimationFrame(updateBackToTopVisibility);
      isAnimationFramePending = true;
    }
  }

  // Attach optimized scroll listener
  window.addEventListener('scroll', queueBackToTopVisibilityUpdate, { passive: true });


  // Click handler (scroll to top)
  backToTopButton.addEventListener('click', function (clickEvent) {
    clickEvent.preventDefault();

    window.scrollTo({
      top: 0,
      behavior: userPrefersReducedMotion ? 'auto' : 'smooth'
    });
  });


  // Initial state (prevents flash of button)
  updateBackToTopVisibility();

})();
