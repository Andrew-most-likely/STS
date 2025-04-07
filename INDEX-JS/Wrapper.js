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


        // Modify existing sidebar toggle functionality for mobile
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleBtn');

            const overlay = document.getElementById('overlay');

            // Function to remove the overlay when it's clicked/tapped
            overlay.addEventListener('click', function () {
                overlay.style.display = 'none';
            });


            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            });

            overlay.addEventListener('click', function () {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                overlay.style.display = 'none';
            });

            // Ensure responsive behavior on resize
            window.addEventListener('resize', function () {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');
                }
            });
        });


        // Initialize jsConfetti
        const jsConfetti = new JSConfetti();

        // Add click event listeners to all buttons
        document.querySelectorAll('.submit').forEach(button => {
            button.addEventListener('click', () => {
                // Retrieve the emoji from the data-emoji attribute of the clicked button
                const emoji = button.getAttribute('data-emoji');
                // Trigger the confetti with the specific emoji
                jsConfetti.addConfetti({
                    emojis: [emoji], // Use the emoji as confetti
                    emojiSize: 70,
                    confettiNumber: 40, // Number of confetti particles
                });
            });
        });




        // Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchContainer = document.querySelector('.search-container');
    
    // Create the search results container if it doesn't exist
    let searchResults = document.querySelector('.search-results');
    if (!searchResults) {
        searchResults = document.createElement('div');
        searchResults.className = 'search-results';
        searchContainer.appendChild(searchResults);
    }
    
    // Function to show search results
    function showSearchResults(results) {
        // Clear previous results
        searchResults.innerHTML = '';
        
        if (results.length === 0) {
            searchResults.innerHTML = '<div class="search-no-results">No results found</div>';
        } else {
            // Create result items
            results.forEach(result => {
                const resultItem = document.createElement('div');
                resultItem.className = 'search-result-item';
                
                // Create content based on result structure
                resultItem.innerHTML = `
                    <div class="search-result-title">${result.asset || ''}</div>
                    <div class="search-result-details">${result.description || ''}</div>
                    <div class="search-result-footer">
                        <span class="search-result-tag">${result.tag}</span>
                        ${result.date ? `<span class="search-result-date">${formatDate(result.date)}</span>` : ''}
                    </div>
                `;
                
                // Add click handler to select this result
                resultItem.addEventListener('click', function() {
                    searchInput.value = result.asset || '';
                    searchResults.classList.remove('active');
                    
                    // Navigate to detail page
                    if (result.id && result.table) {
                        // This will navigate to the raw data page with the specific view and highlight
                        window.location.href = `../INDEX-HTML/Raw-Data.html?view=${result.table}&highlight=${result.id}`;
                    }
                });
                
                searchResults.appendChild(resultItem);
            });
        }
        
        // Show the results dropdown
        searchResults.classList.add('active');
    }
    
    // Helper function to format dates
    function formatDate(dateString) {
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) {
                return dateString; // Return as is if it's not a valid date
            }
            
            // Calculate how long ago
            const now = new Date();
            const diffMs = now - date;
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            
            if (diffDays === 0) {
                return 'Today';
            } else if (diffDays === 1) {
                return 'Yesterday';
            } else if (diffDays < 7) {
                return `${diffDays} days ago`;
            } else {
                return date.toLocaleDateString();
            }
        } catch (e) {
            return dateString;
        }
    }
    
    // Function to search assets via AJAX
    function searchAssets(query) {
        if (!query) {
            searchResults.classList.remove('active');
            return;
        }
        
        // Create AJAX request
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `../INDEX-PHP/search-assets.php?q=${encodeURIComponent(query)}`, true);
        
        xhr.onload = function() {
            if (this.status === 200) {
                try {
                    const response = JSON.parse(this.responseText);
                    showSearchResults(response);
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    searchResults.classList.remove('active');
                }
            } else {
                console.error('Request failed with status:', this.status);
                searchResults.classList.remove('active');
            }
        };
        
        xhr.onerror = function() {
            console.error('Request failed');
            searchResults.classList.remove('active');
        };
        
        xhr.send();
    }
    
    // Debounce function to prevent too many requests
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }
    
    // Debounced search function
    const debouncedSearch = debounce(function(query) {
        searchAssets(query);
    }, 300);
    
    // Event listener for input changes (to show suggestions as you type)
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        debouncedSearch(query);
    });
    
    
    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchContainer.contains(e.target)) {
            searchResults.classList.remove('active');
        }
    });
    
    // Handle keyboard navigation in search results
    searchInput.addEventListener('keydown', function(e) {
        if (!searchResults.classList.contains('active')) return;
        
        const items = searchResults.querySelectorAll('.search-result-item');
        if (items.length === 0) return;
        
        // Find the currently focused item
        const focused = searchResults.querySelector('.search-result-item.focused');
        let index = -1;
        
        if (focused) {
            // Remove focus from current item
            focused.classList.remove('focused');
            
            // Get the index of the focused item
            for (let i = 0; i < items.length; i++) {
                if (items[i] === focused) {
                    index = i;
                    break;
                }
            }
        }
        
        // Navigate using arrow keys
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            index = (index + 1) % items.length;
            items[index].classList.add('focused');
            items[index].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            index = (index - 1 + items.length) % items.length;
            items[index].classList.add('focused');
            items[index].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter' && index !== -1) {
            e.preventDefault();
            items[index].click();
        } else if (e.key === 'Escape') {
            searchResults.classList.remove('active');
        }
    });
});
