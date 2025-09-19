// ITO IF GUSTO NIYO LANG NAMAN MAY LIGHT AND DARK MODE HWAHHHAHAHAHHAHA
let currentTheme = "dark";

function initializeTheme() {
  const savedTheme = localStorage.getItem("theme") || "dark";
  setTheme(savedTheme);
}

function toggleTheme() {
  const newTheme = currentTheme === "light" ? "dark" : "light";
  setTheme(newTheme);
}

function setTheme(theme) {
  currentTheme = theme;
  document.body.className = `theme-${theme}`;
  localStorage.setItem("theme", theme);

  const themeBtn = document.querySelector(".theme-btn");
  if (themeBtn) {
    const lightIcon = themeBtn.querySelector(".theme-icon-light");
    const darkIcon = themeBtn.querySelector(".theme-icon-dark");
    if (theme === "dark") {
      if (lightIcon) lightIcon.style.display = "none";
      if (darkIcon) darkIcon.style.display = "block";
    } else {
      if (lightIcon) lightIcon.style.display = "block";
      if (darkIcon) darkIcon.style.display = "none";
    }
  }
}

// IG GUSTO NIYO MALINIS KAHIT PAPANO PWEDE KAYO MAY FUNCTION AND CALL OUT NIYO NALANG PAG DIRECT KASI NAKAKAGULO KAYA BAHALA NA KAYO 
// THEN HANAP NALANG LUCIDE MAGANDA PERO KAYO BAHALA
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
        </span>`;
    case "scholarship-application":
      return `
        <span class="search-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
          </svg>
        </span>`;
    case "payment-portal":
      return `
        <span class="search-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2">
            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
            <line x1="1" y1="10" x2="23" y2="10"></line>
          </svg>
        </span>`;
    case "history":
      return `
        <span class="search-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12,6 12,12 16,14"></polyline>
          </svg>
        </span>`;
    case "account-settings":
      return `
        <span class="search-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83
                     2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33
                     1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2
                     2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4
                     a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0
                     2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82
                     1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2
                     2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9
                     a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83
                     2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9
                     a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2
                     2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51
                     1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0
                     2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9
                     a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2
                     2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
          </svg>
        </span>`;
    default:
      return `
        <span class="search-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="M21 21l-4.35-4.35"></path>
          </svg>
        </span>`;
  }
}

function initializeSearch() {
  const searchTrigger = document.querySelector(".search-trigger");
  const searchOverlay = document.getElementById("searchOverlay");
  const globalSearchInput = document.getElementById("globalSearch");
  const searchClose = document.querySelector(".search-close");
  const searchResults = document.getElementById("searchResults");

  if (searchTrigger) searchTrigger.addEventListener("click", openGlobalSearch);
  if (searchClose) searchClose.addEventListener("click", closeGlobalSearch);
  if (globalSearchInput) globalSearchInput.addEventListener("input", handleGlobalSearch);

  // Close on ESC and click outside
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && searchOverlay && searchOverlay.classList.contains("show")) {
      closeGlobalSearch();
    }
  });
  if (searchOverlay) {
    searchOverlay.addEventListener("click", (e) => {
      if (e.target === searchOverlay) closeGlobalSearch();
    });
  }

  
  if (searchResults) {
    searchResults.addEventListener("click", (e) => {
      const item = e.target.closest(".search-suggestion");
      if (!item) return;
      const section = item.dataset.section;
      if (section) showSection(section);
    });
  }
}

function openGlobalSearch() {
  const searchOverlay = document.getElementById("searchOverlay");
  const globalSearchInput = document.getElementById("globalSearch");
  const searchResults = document.getElementById("searchResults");

  if (searchOverlay) searchOverlay.classList.add("show");
  if (globalSearchInput) {
    globalSearchInput.value = "";
    globalSearchInput.focus();
  }

  // pwede kayo mag direct nasa inyo na yan magulo kasi sa paningin ko
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
    `;
  }
}

function closeGlobalSearch() {
  const searchOverlay = document.getElementById("searchOverlay");
  const globalSearchInput = document.getElementById("globalSearch");
  if (searchOverlay) searchOverlay.classList.remove("show");
  if (globalSearchInput) globalSearchInput.value = "";
}

function handleGlobalSearch(e) {
  const query = e.target.value.toLowerCase();
  const searchResults = document.getElementById("searchResults");
  if (!searchResults) return;
// pwede niyo gawin direct SVG NA REMOVE NIYO NALANG FUNCTION
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
    `;
    return;
  }

  const results = performSearch(query);
  displaySearchResults(results);
}
// change niyo rin to
function performSearch(query) {
  const pool = [
    { title: "Tuition Balance", section: "tuition-balance" },
    { title: "Payment Portal", section: "payment-portal" },
    { title: "Scholarship Application", section: "scholarship-application" },
    { title: "Account Settings", section: "account-settings" },
    { title: "Payment History", section: "history" },
  ];
  return pool.filter((r) => r.title.toLowerCase().includes(query));
}

function displaySearchResults(results) {
  const searchResults = document.getElementById("searchResults");
  if (!searchResults) return;

  if (results.length === 0) {
    searchResults.innerHTML = '<div class="search-suggestion">No results found</div>';
    return;
  }

  searchResults.innerHTML = results
    .map(
      (r) => `
        <div class="search-suggestion" data-section="${r.section}">
          ${getSectionIcon(r.section)}
          <span>${r.title}</span>
        </div>
      `
    )
    .join("");
}

function showSection(sectionId) {
  closeGlobalSearch();
  const link = document.querySelector(`.sidebar-nav [data-section="${sectionId}"]`);
  if (link && link.getAttribute("href")) {
    window.location.href = link.getAttribute("href");
    return;
  }
  // change niyo nalang according to your modules
  const routes = {
    dashboard: "index.html",
    "tuition-balance": "tutionbalance.html",
    "scholarship-application": "scholarship.html",
    "payment-portal": "paymentportal.html",
    history: "history.html",
    "account-settings": "account.html",
  };
  window.location.href = routes[sectionId] || "index.html";
}

/* IF MAY LOG OUT NA KAYO PWEDE NA */
function logout() {
  alert("Logging out…");
  // window.location.href = "login.html"; 
}

/* Boot */
document.addEventListener("DOMContentLoaded", () => {
  initializeTheme();
  initializeSearch();
});
