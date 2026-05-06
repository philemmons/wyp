/**
 * gallery_lightbox_modal.js
 * Gallery modal state management for gallery.php.
 */
document.addEventListener("DOMContentLoaded", function () {
  // Populate the Bootstrap modal with the image selected from the carousel.
  var modalElement = document.getElementById("galleryLightboxModal");
  var modalCloseButton = modalElement
    ? modalElement.querySelector(".btn-close")
    : null;
  var modalImage = document.getElementById("galleryLightboxImage");
  var modalCaption = document.getElementById("galleryLightboxCaption");
  var galleryTriggers = document.querySelectorAll(".gallery-lightbox-trigger");
  var lastFocusedTrigger = null;
  var carouselElement = document.getElementById("galleryPhotoCarousel");
  var carouselStatus = document.getElementById("galleryCarouselStatus");

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
    modalElement.addEventListener("shown.bs.modal", function () {
      if (modalCloseButton && typeof modalCloseButton.focus === "function") {
        modalCloseButton.focus();
      }
    });

    modalElement.addEventListener("hidden.bs.modal", function () {
      modalImage.setAttribute("src", "");
      modalImage.setAttribute("alt", "");
      modalCaption.textContent = "";

      if (lastFocusedTrigger) {
        lastFocusedTrigger.focus();
      }
    });
  }

  if (carouselElement && carouselStatus) {
    var carouselItems = carouselElement.querySelectorAll(".carousel-item");
    var totalSlides = carouselItems.length;

    function updateCarouselAnnouncement(activeIndex) {
      carouselStatus.textContent =
        "Gallery image " +
        (activeIndex + 1) +
        " of " +
        totalSlides;
    }

    if (totalSlides > 0) {
      updateCarouselAnnouncement(0);
    }

    carouselElement.addEventListener("slid.bs.carousel", function (event) {
      if (typeof event.to === "number") {
        updateCarouselAnnouncement(event.to);
      }
    });
  }
});
