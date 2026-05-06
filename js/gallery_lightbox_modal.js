/**
 * gallery_lightbox_modal.js
 * Gallery modal state management for gallery.php.
 */
document.addEventListener("DOMContentLoaded", function () {
  // Populate the Bootstrap modal with the image selected from the carousel.
  var modalElement = document.getElementById("galleryLightboxModal");
  var modalImage = document.getElementById("galleryLightboxImage");
  var modalCaption = document.getElementById("galleryLightboxCaption");
  var galleryTriggers = document.querySelectorAll(".gallery-lightbox-trigger");
  var lastFocusedTrigger = null;

  if (!modalImage || !modalCaption || galleryTriggers.length === 0) {
    return;
  }

  galleryTriggers.forEach(function (trigger) {
    trigger.addEventListener("click", function () {
      lastFocusedTrigger = trigger;
      var fullSrc =
        trigger.getAttribute("data-fullsrc") ||
        trigger.getAttribute("data-full-src") ||
        trigger.getAttribute("href") ||
        "";
      var altText = trigger.getAttribute("data-alt") || "";
      var captionText = trigger.getAttribute("data-caption") || "";

      modalImage.setAttribute("src", fullSrc);
      modalImage.setAttribute("alt", altText);
      modalCaption.textContent = captionText;
    });
  });

  if (modalElement) {
    modalElement.addEventListener("hidden.bs.modal", function () {
      modalImage.setAttribute("src", "");
      modalImage.setAttribute("alt", "");
      modalCaption.textContent = "";

      if (lastFocusedTrigger) {
        lastFocusedTrigger.focus();
      }
    });
  }
});
