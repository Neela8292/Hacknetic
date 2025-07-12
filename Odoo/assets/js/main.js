// Main JavaScript functionality for SkillSwap Platform

document.addEventListener("DOMContentLoaded", () => {
    // Initialize components
    initializeNavigation()
    initializeForms()
    initializeModals()
  })
  
  function initializeNavigation() {
    // Mobile navigation toggle
    const navToggle = document.querySelector(".nav-toggle")
    const navMenu = document.querySelector(".nav-menu")
  
    if (navToggle && navMenu) {
      navToggle.addEventListener("click", () => {
        navMenu.classList.toggle("active")
      })
    }
  
    // Active navigation highlighting
    const currentPage = window.location.pathname.split("/").pop()
    const navLinks = document.querySelectorAll(".nav-link")
  
    navLinks.forEach((link) => {
      const href = link.getAttribute("href")
      if (href === currentPage || (currentPage === "" && href === "index.php")) {
        link.classList.add("active")
      }
    })
  }
  
  function initializeForms() {
    // Form validation
    const forms = document.querySelectorAll("form")
  
    forms.forEach((form) => {
      form.addEventListener("submit", (e) => {
        const requiredFields = form.querySelectorAll("[required]")
        let isValid = true
  
        requiredFields.forEach((field) => {
          if (!field.value.trim()) {
            isValid = false
            field.classList.add("error")
          } else {
            field.classList.remove("error")
          }
        })
  
        if (!isValid) {
          e.preventDefault()
          showAlert("Please fill in all required fields.", "error")
        }
      })
    })
  
    // Real-time validation
    const inputs = document.querySelectorAll("input, textarea, select")
    inputs.forEach((input) => {
      input.addEventListener("blur", function () {
        if (this.hasAttribute("required") && !this.value.trim()) {
          this.classList.add("error")
        } else {
          this.classList.remove("error")
        }
      })
  
      input.addEventListener("input", function () {
        if (this.classList.contains("error") && this.value.trim()) {
          this.classList.remove("error")
        }
      })
    })
  }
  
  function initializeModals() {
    // Close modals when clicking outside
    window.addEventListener("click", (event) => {
      const modals = document.querySelectorAll(".modal")
      modals.forEach((modal) => {
        if (event.target === modal) {
          modal.style.display = "none"
        }
      })
    })
  
    // Close modals with Escape key
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        const modals = document.querySelectorAll(".modal")
        modals.forEach((modal) => {
          if (modal.style.display === "block") {
            modal.style.display = "none"
          }
        })
      }
    })
  }
  
  // Utility functions
  function showAlert(message, type = "info") {
    const alertDiv = document.createElement("div")
    alertDiv.className = `alert alert-${type}`
    alertDiv.textContent = message
  
    // Insert at the top of the main content
    const mainContent = document.querySelector(".main-content")
    if (mainContent) {
      mainContent.insertBefore(alertDiv, mainContent.firstChild)
  
      // Auto-remove after 5 seconds
      setTimeout(() => {
        if (alertDiv.parentNode) {
          alertDiv.parentNode.removeChild(alertDiv)
        }
      }, 5000)
    }
  }
  
  function confirmAction(message) {
    return confirm(message)
  }
  
  // Search functionality
  function initializeSearch() {
    const searchForm = document.querySelector(".search-form")
    if (searchForm) {
      const skillInput = searchForm.querySelector('input[name="skill"]')
      const locationInput = searchForm.querySelector('input[name="location"]')
  
      // Auto-complete functionality could be added here
      if (skillInput) {
        skillInput.addEventListener("input", () => {
          // Implement skill suggestions
        })
      }
    }
  }
  
  // Skill management
  function toggleForm(formId) {
    const form = document.getElementById(formId)
    if (form) {
      const isVisible = form.style.display !== "none"
      form.style.display = isVisible ? "none" : "block"
  
      if (!isVisible) {
        // Focus on first input when showing form
        const firstInput = form.querySelector("input, select, textarea")
        if (firstInput) {
          firstInput.focus()
        }
      }
    }
  }
  
  // Swap request modal
  function openSwapModal(userId, userName) {
    const modal = document.getElementById("swapModal")
    if (modal) {
      document.getElementById("requested_user_id").value = userId
      document.getElementById("requested_user_name").textContent = userName
      modal.style.display = "block"
  
      // Focus on first form element
      const firstInput = modal.querySelector("select, input, textarea")
      if (firstInput) {
        firstInput.focus()
      }
    }
  }
  
  function closeSwapModal() {
    const modal = document.getElementById("swapModal")
    if (modal) {
      modal.style.display = "none"
    }
  }
  
  // Animation utilities
  function fadeIn(element, duration = 300) {
    element.style.opacity = 0
    element.style.display = "block"
  
    const start = performance.now()
  
    function animate(currentTime) {
      const elapsed = currentTime - start
      const progress = elapsed / duration
  
      if (progress < 1) {
        element.style.opacity = progress
        requestAnimationFrame(animate)
      } else {
        element.style.opacity = 1
      }
    }
  
    requestAnimationFrame(animate)
  }
  
  function fadeOut(element, duration = 300) {
    const start = performance.now()
    const startOpacity = Number.parseFloat(getComputedStyle(element).opacity)
  
    function animate(currentTime) {
      const elapsed = currentTime - start
      const progress = elapsed / duration
  
      if (progress < 1) {
        element.style.opacity = startOpacity * (1 - progress)
        requestAnimationFrame(animate)
      } else {
        element.style.opacity = 0
        element.style.display = "none"
      }
    }
  
    requestAnimationFrame(animate)
  }
  
  // Responsive utilities
  function isMobile() {
    return window.innerWidth <= 768
  }
  
  function isTablet() {
    return window.innerWidth > 768 && window.innerWidth <= 1024
  }
  
  function isDesktop() {
    return window.innerWidth > 1024
  }
  
  // Handle responsive behavior
  window.addEventListener("resize", () => {
    // Adjust layout based on screen size
    const navMenu = document.querySelector(".nav-menu")
    if (navMenu && isDesktop()) {
      navMenu.classList.remove("active")
    }
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
  
  // Loading states
  function showLoading(element) {
    if (element) {
      element.classList.add("loading")
      element.disabled = true
    }
  }
  
  function hideLoading(element) {
    if (element) {
      element.classList.remove("loading")
      element.disabled = false
    }
  }
  
  // Local storage utilities
  function saveToLocalStorage(key, data) {
    try {
      localStorage.setItem(key, JSON.stringify(data))
    } catch (e) {
      console.warn("Could not save to localStorage:", e)
    }
  }
  
  function getFromLocalStorage(key) {
    try {
      const data = localStorage.getItem(key)
      return data ? JSON.parse(data) : null
    } catch (e) {
      console.warn("Could not read from localStorage:", e)
      return null
    }
  }
  
  // Initialize search on page load
  document.addEventListener("DOMContentLoaded", () => {
    initializeSearch()
  })
  