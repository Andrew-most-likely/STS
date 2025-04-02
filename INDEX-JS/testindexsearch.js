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
                        window.location.href = `../INDEX-HTML/expand.html?view=${result.table}&highlight=${result.id}`;
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
