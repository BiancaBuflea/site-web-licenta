// Mobile menu functionality
document.addEventListener("DOMContentLoaded", () => {
  const mobileToggle = document.querySelector(".mobile-menu-toggle")
  const nav = document.querySelector("nav ul")
  

  if (mobileToggle && nav) {
    mobileToggle.addEventListener("click", () => {
      nav.classList.toggle("active")

      // Change icon
      const icon = mobileToggle.querySelector("i")
      if (nav.classList.contains("active")) {
        icon.classList.remove("fa-bars")
        icon.classList.add("fa-times")
      } else {
        icon.classList.remove("fa-times")
        icon.classList.add("fa-bars")
      }
    })

    // Close menu when clicking on a link
    const navLinks = nav.querySelectorAll("a")
    navLinks.forEach((link) => {
      link.addEventListener("click", () => {
        nav.classList.remove("active")
        const icon = mobileToggle.querySelector("i")
        icon.classList.remove("fa-times")
        icon.classList.add("fa-bars")
      })
    })

    // Close menu when clicking outside
    document.addEventListener("click", (event) => {
      if (!nav.contains(event.target) && !mobileToggle.contains(event.target)) {
        nav.classList.remove("active")
        const icon = mobileToggle.querySelector("i")
        icon.classList.remove("fa-times")
        icon.classList.add("fa-bars")
      }
    })
  }

  // Handle dropdown menus on mobile
  const dropdowns = document.querySelectorAll(".dropdown")
  dropdowns.forEach((dropdown) => {
    const dropdownToggle = dropdown.querySelector("a")
    const dropdownMenu = dropdown.querySelector(".dropdown-menu")

    if (window.innerWidth <= 768) {
      dropdownToggle.addEventListener("click", (e) => {
        e.preventDefault()
        dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block"
      })
    }
  })

  // Handle window resize
  window.addEventListener("resize", () => {
    if (window.innerWidth > 768) {
      nav.classList.remove("active")
      const icon = mobileToggle.querySelector("i")
      if (icon) {
        icon.classList.remove("fa-times")
        icon.classList.add("fa-bars")
      }
    }
  })
})

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault()
    const target = document.querySelector(this.getAttribute("href"))
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      })
    }
  })
})

// Add loading animation for images
document.addEventListener("DOMContentLoaded", () => {
  const images = document.querySelectorAll("img")
  images.forEach((img) => {
    img.addEventListener("load", function () {
      this.style.opacity = "1"
    })

    // Set initial opacity
    img.style.opacity = "0"
    img.style.transition = "opacity 0.3s ease"

    // If image is already loaded
    if (img.complete) {
      img.style.opacity = "1"
    }
  })
})
