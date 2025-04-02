// Toggle sidebar functionality
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.tab');
    const toggleBtn = document.getElementById('toggleBtn');
    const overlay = document.getElementById('overlay');
    const profileIcon = document.getElementById('profileIcon');
    const dropdown = document.getElementById('dropdown');
    const usernameDisplay = document.getElementById('usernameDisplay');
    const themeToggle = document.getElementById('themeToggle');
    const sidebarLogo = document.getElementById('sidebarLogo');

// Apply sidebar state ASAP before anything else renders
const sidebar = document.getElementById('sidebar');
if (sidebar) {
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }
}

    
// Add click event listeners to each tab
tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        // Remove active class from all tabs
        tabs.forEach(t => t.classList.remove('active'));

        // Add active class to the clicked tab
        tab.classList.add('active');

        // Store the active tab's ID in localStorage
        localStorage.setItem('activeTab', tab.id);

        // Get the href from the button and navigate
        const href = tab.getAttribute('onclick').split('location.href="')[1].split('"')[0];
        window.location.href = href;
    });
});

// On page load, retrieve and set the active tab
window.addEventListener('DOMContentLoaded', () => {
    const activeTabId = localStorage.getItem('activeTab');
    if (activeTabId) {
        const activeTab = document.getElementById(activeTabId);
        if (activeTab) {
            activeTab.classList.add('active'); // Set active class on the tab
        }
    }
});
// username from session
function getUsernameFromSession() {
    const username = sessionStorage.getItem("userName"); // Correct method name: getItem
    if (username) {
        document.getElementById("usernameDisplay").textContent = username;
    }
    return username || "Username"; // If no username found, return a default value
}

window.onload = function () {
    getUsernameFromSession();
};

// Set username display and first letter for profile icon
const username = getUsernameFromSession();
usernameDisplay.textContent = username;
profileIcon.textContent = username.charAt(0).toUpperCase();

toggleBtn.addEventListener('click', function () {
    sidebar.classList.toggle('collapsed');

    // Store sidebar state in localStorage
    if (sidebar.classList.contains('collapsed')) {
        localStorage.setItem('sidebarCollapsed', 'true');
    } else {
        localStorage.setItem('sidebarCollapsed', 'false');
    }
});


// Profile dropdown toggle
profileIcon.addEventListener('click', function (e) {
    e.stopPropagation();
    dropdown.classList.toggle('show');
});

// Close dropdown when clicking elsewhere
document.addEventListener('click', function (e) {
    if (!profileIcon.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});

// Close sidebar when clicking overlay (mobile)
overlay.addEventListener('click', function () {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
});
/*
// Handle window resize for responsive behavior
window.addEventListener('resize', function () {
    if (window.innerWidth > 768) {
        overlay.classList.remove('active');
        if (sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
        }
    }
});

// Mobile sidebar toggle
if (window.innerWidth <= 768) {
    toggleBtn.addEventListener('click', function () {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    });
}
    */
function updateLogo(theme) {
    if (theme === 'dark') {
        sidebarLogo.src = "../ASSETS/logo-Black&White.png"; // Dark mode logo
    } else {
        sidebarLogo.src = "../ASSETS/logo64.png"; // Light mode logo
    }
}

// Theme toggle functionality
themeToggle.addEventListener('change', function () {
    if (this.checked) {
        document.body.classList.add('dark-theme');
        localStorage.setItem('theme', 'dark');
        updateLogo('dark');
    } else {
        document.body.classList.remove('dark-theme');
        localStorage.setItem('theme', 'light');
        updateLogo('light');
    }
});

// Load theme from localStorage
function loadTheme() {
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme === 'dark') {
        document.body.classList.add('dark-theme');
        themeToggle.checked = true;
    } else {
        document.body.classList.remove('dark-theme');
        themeToggle.checked = false;
    }
    updateLogo(currentTheme);
}
loadTheme(); // Call on page load to apply correct logo and theme
});

// Placeholder logout function
const logout = () => {
    sessionStorage.removeItem('isLoggedIn');
    sessionStorage.removeItem('userName');
    window.location.href = '../index.html';
};

