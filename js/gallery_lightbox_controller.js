/**
 * gallery_lightbox_controller.js
 * Gallery modal state management for gallery.php.
 */
document.addEventListener("DOMContentLoaded", function () {
  // Populate the Bootstrap modal with the image selected from the carousel.
  var lightboxModalElement = document.getElementById("galleryLightboxModal");
  var lightboxCloseButton = lightboxModalElement
    ? lightboxModalElement.querySelector(".btn-close")
    : null;
  var lightboxImageElement = document.getElementById("galleryLightboxImage");
  var lightboxCaptionElement = document.getElementById("galleryLightboxCaption");
  var lightboxTriggerButtons = document.querySelectorAll(".gallery-lightbox-trigger");
  var lastFocusedLightboxTrigger = null;
  var galleryCarouselElement = document.getElementById("galleryPhotoCarousel");
  var carouselStatusElement = document.getElementById("galleryCarouselStatus");
  var galleryCarouselController =
    window.bootstrap && galleryCarouselElement
      ? window.bootstrap.Carousel.getOrCreateInstance(galleryCarouselElement, {
          interval: false,
          ride: false,
          touch: true,
          keyboard: true
        })
      : null;

  if (!lightboxImageElement || !lightboxCaptionElement || lightboxTriggerButtons.length === 0) {
    return;
  }

  lightboxTriggerButtons.forEach(function (lightboxTriggerButton) {
    lightboxTriggerButton.addEventListener("click", function () {
      lastFocusedLightboxTrigger = lightboxTriggerButton;
      var fullSizeImageSource =
        lightboxTriggerButton.getAttribute("data-fullsrc") ||
        lightboxTriggerButton.getAttribute("data-full-src") ||
        lightboxTriggerButton.getAttribute("href") ||
        "";
      var triggerPreviewImage = lightboxTriggerButton.querySelector("img");
      var altText =
        lightboxTriggerButton.getAttribute("data-alt") ||
        (triggerPreviewImage ? triggerPreviewImage.getAttribute("alt") : "") ||
        "";
      var captionText = lightboxTriggerButton.getAttribute("data-caption") || "";

      lightboxImageElement.setAttribute("src", fullSizeImageSource);
      lightboxImageElement.setAttribute("alt", altText);
      lightboxCaptionElement.textContent = captionText;
    });
  });

  if (lightboxModalElement) {
    lightboxModalElement.addEventListener("show.bs.modal", function (event) {
      if (event && event.relatedTarget && event.relatedTarget.focus) {
        lastFocusedLightboxTrigger = event.relatedTarget;
      }
    });

    lightboxModalElement.addEventListener("shown.bs.modal", function () {
      if (lightboxCloseButton && typeof lightboxCloseButton.focus === "function") {
        lightboxCloseButton.focus();
      }
    });

    lightboxModalElement.addEventListener("hidden.bs.modal", function () {
      lightboxImageElement.setAttribute("src", "");
      lightboxImageElement.setAttribute("alt", "");
      lightboxCaptionElement.textContent = "";

      if (lastFocusedLightboxTrigger) {
        lastFocusedLightboxTrigger.focus();
      }
    });

    lightboxModalElement.addEventListener("keydown", function (event) {
      if (
        event.key === "Escape" &&
        window.bootstrap &&
        typeof window.bootstrap.Modal.getOrCreateInstance === "function"
      ) {
        window.bootstrap.Modal.getOrCreateInstance(lightboxModalElement).hide();
      }
    });
  }

  if (galleryCarouselElement && carouselStatusElement) {
    var carouselSlideItems = galleryCarouselElement.querySelectorAll(".carousel-item");
    var totalSlides = carouselSlideItems.length;

    function announceActiveCarouselSlide(activeSlideIndex) {
      carouselStatusElement.textContent =
        "Gallery image " +
        (activeSlideIndex + 1) +
        " of " +
        totalSlides;
    }

    if (totalSlides > 0) {
      announceActiveCarouselSlide(0);
    }

    galleryCarouselElement.addEventListener("slid.bs.carousel", function (event) {
      if (typeof event.to === "number") {
        announceActiveCarouselSlide(event.to);
      }
    });

    galleryCarouselElement.addEventListener("keydown", function (event) {
      if (!galleryCarouselController) {
        return;
      }

      if (event.key === "ArrowLeft") {
        event.preventDefault();
        galleryCarouselController.prev();
      }

      if (event.key === "ArrowRight") {
        event.preventDefault();
        galleryCarouselController.next();
      }

      if (event.key === "Home") {
        event.preventDefault();
        galleryCarouselController.to(0);
      }

      if (event.key === "End" && totalSlides > 0) {
        event.preventDefault();
        galleryCarouselController.to(totalSlides - 1);
      }
    });
  }
});
