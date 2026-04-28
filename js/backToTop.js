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
  const SCROLL_THRESHOLD = 200; // px before button appears


  // Element reference
  const backToTopBtn = document.getElementById('back-to-top-link');

  // Exit early if element is not present (prevents errors)
  if (!backToTopBtn) return;


  // Accessibility: Reduced motion preference
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;


  // Scroll handler (throttled)
  let ticking = false;

  function handleScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > SCROLL_THRESHOLD) {
      backToTopBtn.classList.add('visible');
      backToTopBtn.setAttribute('tabindex', '0');
      backToTopBtn.setAttribute('aria-hidden', 'false');
    } else {
      backToTopBtn.classList.remove('visible');
      backToTopBtn.setAttribute('tabindex', '-1');
      backToTopBtn.setAttribute('aria-hidden', 'true');
    }

    ticking = false;
  }

  function onScroll() {
    if (!ticking) {
      window.requestAnimationFrame(handleScroll);
      ticking = true;
    }
  }

  // Attach optimized scroll listener
  window.addEventListener('scroll', onScroll, { passive: true });


  // Click handler (scroll to top)
  backToTopBtn.addEventListener('click', function (e) {
    e.preventDefault();

    window.scrollTo({
      top: 0,
      behavior: prefersReducedMotion ? 'auto' : 'smooth'
    });
  });


  // Initial state (prevents flash of button)
  handleScroll();

})();
