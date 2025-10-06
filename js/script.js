// YUNG THEME DARK AND LIGHT TRIP LANG NAMIN LAGYAN PERO IF AYAW PWEDE NAMAN TANGGALIN
let currentTheme = "dark"

function initializeTheme() {
  const savedTheme = localStorage.getItem("theme") || "dark"
  setTheme(savedTheme)
}

function toggleTheme() {
  const newTheme = currentTheme === "light" ? "dark" : "light"
  setTheme(newTheme)
}

function setTheme(theme) {
  currentTheme = theme
  document.body.className = `theme-${theme}`
  localStorage.setItem("theme", theme)

  const themeBtn = document.querySelector(".theme-btn")
  if (themeBtn) {
    const lightIcon = themeBtn.querySelector(".theme-icon-light")
    const darkIcon = themeBtn.querySelector(".theme-icon-dark")
    if (theme === "dark") {
      if (lightIcon) lightIcon.style.display = "none"
      if (darkIcon) darkIcon.style.display = "block"
    } else {
      if (lightIcon) lightIcon.style.display = "block"
      if (darkIcon) darkIcon.style.display = "none"
    }
  }
}

let sidebarState = "open" 

function initializeSidebar() {
  const savedState = localStorage.getItem("sidebarState") || "open"
  setSidebarState(savedState)

  addTooltipsToNavLinks()
}

function setSidebarState(state) {
  sidebarState = state
  const sidebar = document.getElementById("sidebar")
  const mainContent = document.querySelector(".main-content")
  const overlay = document.getElementById("sidebarOverlay")
  const toggleButton = document.querySelector(".universal-menu-toggle")
  const menuIcon = toggleButton?.querySelector(".toggle-icon-menu")
  const closeIcon = toggleButton?.querySelector(".toggle-icon-close")

  if (!sidebar || !mainContent) return

  sidebar.classList.remove("show", "closed")
  mainContent.classList.remove("sidebar-closed")
  if (overlay) overlay.classList.remove("show")

  if (menuIcon) menuIcon.style.display = "block"
  if (closeIcon) closeIcon.style.display = "none"

  if (window.innerWidth > 768) {
    if (state === "closed") {
      sidebar.classList.add("closed")
      mainContent.classList.add("sidebar-closed")
      if (menuIcon) menuIcon.style.display = "block"
      if (closeIcon) closeIcon.style.display = "none"
    }
  } else {
    if (state === "open") {
      sidebar.classList.add("show")
      if (overlay) overlay.classList.add("show")
      document.body.style.overflow = "hidden"
      if (menuIcon) menuIcon.style.display = "none"
      if (closeIcon) closeIcon.style.display = "block"
    } else {
      document.body.style.overflow = ""
    }
  }

  localStorage.setItem("sidebarState", state)
}

function toggleSidebar() {
  const newState = sidebarState === "open" ? "closed" : "open"
  setSidebarState(newState)
}

function closeSidebar() {
  const isMobile = window.innerWidth <= 768

  if (isMobile && sidebarState === "open") {
    setSidebarState("closed")
    document.body.style.overflow = ""
  }
}

function addTooltipsToNavLinks() {
  const navLinks = document.querySelectorAll(".nav-link")
  navLinks.forEach((link) => {
    const textSpan = link.querySelector(".nav-text")
    if (textSpan) {
      link.setAttribute("title", textSpan.textContent)
    }
  })
}


function getSectionIcon(sectionId) {
  switch (sectionId) {
    case "tuition-balance":
      return `
        <span class="search-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2">
            <line x1="12" y1="1" x2="12" y2="23"></line>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
          </svg>
        </span>`
    case "scholarship-application":
      return `
        <span class="search-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
          </svg>
        </span>`
    case "payment-portal":
      return `
        <span class="search-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2">
            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
            <line x1="1" y1="10" x2="23" y2="10"></line>
          </svg>
        </span>`
    case "history":
      return `
        <span class="search-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12,6 12,12 16,14"></polyline>
          </svg>
        </span>`
    case "account-settings":
      return `
        <span class="search-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-icon lucide-circle-user">
          <circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3"/>
          <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"/></svg>
        </span>`
    case "Security":
       return `
        <span class="search-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-fingerprint-icon lucide-fingerprint"><path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"/><path d="M14 13.12c0 2.38 0 6.38-1 8.88"/><path d="M17.29 21.02c.12-.6.43-2.3.5-3.02"/><path d="M2 12a10 10 0 0 1 18-6"/><path d="M2 16h.01"/><path d="M21.8 16c.2-2 .131-5.354 0-6"/>
          <path d="M5 19.5C5.5 18 6 15 6 12a6 6 0 0 1 .34-2"/><path d="M8.65 22c.21-.66.45-1.32.57-2"/><path d="M9 6.8a6 6 0 0 1 9 5.2v2"/></svg>
        </span>`
    case "Student Additional Information":
      return `
        <span class="search-icon">
         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-user-icon lucide-file-user"><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M15 18a3 3 0 1 0-6 0"/>
          <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><circle cx="12" cy="13" r="2"/></svg>
        </span>`
    case "Account Settings":
      return `
        <span class="search-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83
                     2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33
                     1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2
                     2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4
                     a1.65 1.65 0 0 0-1.82.33l-.06-.06a2 2 0 0 1-2.83 0
                     2 2 0 0 1 0-2.83l.06.06a1.65 1.65 0 0 0 .33-1.82
                     1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2
                     2 2 0 0 1-2-2h.09A1.65 1.65 0 0 0 4.6 9
                     a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83
                     2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9
                     a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2
                     2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51
                     1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0
                     2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9
                     a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2
                     2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
          </svg>
        </span>`
    default:
      return `
        <span class="search-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="M21 21l-4.35-4.35"></path>
          </svg>
        </span>`
  }
}

function initializeSearch() {
  const searchTrigger = document.querySelector(".search-trigger")
  const searchOverlay = document.getElementById("searchOverlay")
  const globalSearchInput = document.getElementById("globalSearch")
  const searchClose = document.querySelector(".search-close")
  const searchResults = document.getElementById("searchResults")

  if (searchTrigger) searchTrigger.addEventListener("click", openGlobalSearch)
  if (searchClose) searchClose.addEventListener("click", closeGlobalSearch)
  if (globalSearchInput) globalSearchInput.addEventListener("input", handleGlobalSearch)

  // Close on ESC and click outside
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      if (searchOverlay && searchOverlay.classList.contains("show")) {
        closeGlobalSearch()
      }
      const sidebar = document.getElementById("sidebar")
      if (sidebar && sidebar.classList.contains("show") && window.innerWidth <= 768) {
        closeSidebar()
      }
    }
  })

  if (searchOverlay) {
    searchOverlay.addEventListener("click", (e) => {
      if (e.target === searchOverlay) closeGlobalSearch()
    })
  }

  if (searchResults) {
    searchResults.addEventListener("click", (e) => {
      const item = e.target.closest(".search-suggestion")
      if (!item) return
      const section = item.dataset.section
      if (section) showSection(section)
    })
  }
}

function openGlobalSearch() {
  const searchOverlay = document.getElementById("searchOverlay")
  const globalSearchInput = document.getElementById("globalSearch")
  const searchResults = document.getElementById("searchResults")

  if (searchOverlay) searchOverlay.classList.add("show")
  if (globalSearchInput) {
    globalSearchInput.value = ""
    if (window.innerWidth > 768) {
      globalSearchInput.focus()
    } else {
      setTimeout(() => globalSearchInput.focus(), 100)
    }
  }

  if (searchResults) {
    searchResults.innerHTML = `
      <div class="search-suggestion" data-section="tuition-balance">
        ${getSectionIcon("tuition-balance")}
        <span>Check tuition balance</span>
      </div>
      <div class="search-suggestion" data-section="scholarship-application">
        ${getSectionIcon("scholarship-application")}
        <span>Apply for scholarship</span>
      </div>
      <div class="search-suggestion" data-section="payment-portal">
        ${getSectionIcon("payment-portal")}
        <span>Payment Portal</span>
      </div>
    `
  }
}

function closeGlobalSearch() {
  const searchOverlay = document.getElementById("searchOverlay")
  const globalSearchInput = document.getElementById("globalSearch")
  if (searchOverlay) searchOverlay.classList.remove("show")
  if (globalSearchInput) {
    globalSearchInput.value = ""
    globalSearchInput.blur()
  }
}

function handleGlobalSearch(e) {
  const query = e.target.value.toLowerCase()
  const searchResults = document.getElementById("searchResults")
  if (!searchResults) return

  if (query.length === 0) {
    searchResults.innerHTML = `
      <div class="search-suggestion" data-section="tuition-balance">
        ${getSectionIcon("tuition-balance")}
        <span>Check tuition balance</span>
      </div>
      <div class="search-suggestion" data-section="scholarship-application">
        ${getSectionIcon("scholarship-application")}
        <span>Apply for scholarship</span>
      </div>
      <div class="search-suggestion" data-section="payment-portal">
        ${getSectionIcon("payment-portal")}
        <span>Payment Portal</span>
      </div>
    `
    return
  }

  const results = performSearch(query)
  displaySearchResults(results)
}

function performSearch(query) {
  const pool = [
    { title: "Tuition Balance", section: "tuition-balance" },
    { title: "Payment Portal", section: "payment-portal" },
    { title: "Scholarship Application", section: "scholarship-application" },
    { title: "Account Settings", section: "account-settings" },
    { title: "Payment History", section: "history" },
    { title: "Security", section: "Security" },
    { title: "Student Information Addition", section: "Student Information Addition" },
  ]
  return pool.filter((r) => r.title.toLowerCase().includes(query))
}

function displaySearchResults(results) {
  const searchResults = document.getElementById("searchResults")
  if (!searchResults) return

  if (results.length === 0) {
    searchResults.innerHTML = '<div class="search-suggestion">No results found</div>'
    return
  }

  searchResults.innerHTML = results
    .map(
      (r) => `
        <div class="search-suggestion" data-section="${r.section}">
          ${getSectionIcon(r.section)}
          <span>${r.title}</span>
        </div>
      `,
    )
    .join("")
}

function showSection(sectionId) {
  closeGlobalSearch()
  const link = document.querySelector(`.sidebar-nav [data-section="${sectionId}"]`)
  if (link && link.getAttribute("href")) {
    window.location.href = link.getAttribute("href")
    return
  }

  const routes = {
    dashboard: "index.php",
    "tuition-balance": "tutionbalance.php",
    "scholarship-application": "scholarship.php",
    "payment-portal": "paymentportal.php",
    history: "history.php",
    "account-settings": "account.php",
    "Security": "security.php",
    "Student Information Addition": "studentinfo.php",
  }
  window.location.href = routes[sectionId] || "index.php"
}
// IF MY LOGIN NA KAYO LINK NIYO NALANG
function logout() {
  alert("Logging out…")
   window.location.href = "../auth/logout.php";
}

function initializeMobile() {
  const overlay = document.getElementById("sidebarOverlay")
  const navLinks = document.querySelectorAll(".nav-link")

  if (overlay) {
    overlay.addEventListener("click", closeSidebar)
  }

  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      if (window.innerWidth <= 768 && sidebarState === "open") {
        closeSidebar()
      }
    })
  })

  let resizeTimeout
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimeout)
    resizeTimeout = setTimeout(() => {
      setSidebarState(sidebarState) 
    }, 250)
  })
}

document.addEventListener("DOMContentLoaded", () => {
  initializeTheme()
  initializeSidebar()
  initializeSearch()
  initializeMobile()
})
// SECURITY
function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const wrapper = input.closest('.password-input-wrapper');
            const eyeIcon = wrapper.querySelector('.eye-icon');
            const eyeOffIcon = wrapper.querySelector('.eye-off-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                input.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        }

        function resetForm() {
            document.getElementById('passwordChangeForm').reset();
        }

        document.getElementById('passwordChangeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword !== confirmPassword) {
                alert('New password and confirm password do not match.');
                return;
            }
            
            // Add your password change logic here
            console.log('Password change submitted');
        });
