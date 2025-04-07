const viewCategories = {
    "Equipment Management": ["total_equipment_return", "total_ewaste"],
    "Room and Printer Checks": ["total_room_check", "Total_printer_check"],
    "Deliveries": ["bulk_delivery_view", "toner_delivery_view", "other_delivery_view", "individual_delivery_view"]
};

const viewMapping = {
    "total_equipment_return": "Equipment Return",
    "total_ewaste": "E-waste",
    "total_room_check": "Room Check",
    "Total_printer_check": "Printer Check",
    "bulk_delivery_view": "Bulk Delivery",
    "toner_delivery_view": "Toner Delivery",
    "other_delivery_view": "Other Delivery",
    "individual_delivery_view": "Individual Delivery"
};

function initializeDashboard() {
    const dashboard = document.getElementById('dashboard');
    
    Object.entries(viewCategories).forEach(([category, views]) => {
        const categoryCard = document.createElement('div');
        categoryCard.className = 'category-card';
        
        categoryCard.innerHTML = `
            <div class="category-header">
                <h2>${category}</h2>
            </div>
            <div class="category-content">
                ${views.map(viewId => `
                    <div class="view-item">
                        <button class="view-button" data-view="${viewId}">
                            <span>${viewMapping[viewId]}</span>
                            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="view-content" id="content-${viewId}">
                            <div class="table-wrapper">
                                <div class="table-container">
                                    <div class="loading">
                                        <div class="spinner"></div>
                                        <p>Loading data...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
        
        dashboard.appendChild(categoryCard);
    });

    document.querySelectorAll('.view-button').forEach(button => {
        button.addEventListener('click', () => {
            const viewId = button.dataset.view;
            const content = document.getElementById(`content-${viewId}`);
            const isExpanded = button.classList.contains('active');
            
            button.classList.toggle('active');
            content.classList.toggle('active');

            if (!isExpanded && !content.dataset.loaded) {
                fetchViewData(viewId);
            }
        });
    });

}

const PAGE_SIZE = 50;
const viewDataCache = {};

function filterTable(searchText, tbody, cachedData) {
    const searchLower = searchText.toLowerCase();
    
    // If search is empty, show initial page of data
    if (!searchText) {
        tbody.innerHTML = renderRows(cachedData.data.slice(0, PAGE_SIZE), cachedData.columns);
        return cachedData.data.length;
    }

    // Filter data
    const filteredData = cachedData.data.filter(row => 
        Object.values(row).some(value => 
            String(value).toLowerCase().includes(searchLower)
        )
    );

    // Render filtered data
    tbody.innerHTML = renderRows(filteredData, cachedData.columns);
    return filteredData.length;
}

function renderRows(data, columns) {
    return data.map(row => `
        <tr>
            ${columns.map(column => `<td>${row[column] || ''}</td>`).join('')}
        </tr>
    `).join('');
}


async function fetchViewData(viewId) {
    const content = document.getElementById(`content-${viewId}`);
    const tableContainer = content.querySelector('.table-container');

    try {
        if (!viewDataCache[viewId]) {
            const response = await fetch(`../INDEX-PHP/expand.php?view=${viewId}`);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error);
            }

            viewDataCache[viewId] = {
                columns: data.columns,
                data: data.data,
                currentPage: 0
            };
        }

        const cachedData = viewDataCache[viewId];

        if (cachedData.data.length === 0) {
            tableContainer.innerHTML = '<p class="loading">No data available</p>';
            return;
        }

        // Create sticky header container
        const stickyControls = document.createElement('div');
        stickyControls.className = 'sticky-controls';

        // Create search bar
        const searchContainer = document.createElement('div');
        searchContainer.className = 'search-container';
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'search-input';
        searchInput.placeholder = 'Search all columns...';
        searchContainer.appendChild(searchInput);

        // Create table header with export button
        const tableHeader = document.createElement('div');
        tableHeader.className = 'table-header';

        const exportButton = document.createElement('button');
        exportButton.className = 'export-button';
        exportButton.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"></path>
            </svg>
            Export CSV
        `;
        
        tableHeader.appendChild(searchContainer);
        tableHeader.appendChild(exportButton);
        stickyControls.appendChild(tableHeader);

        // Set up container structure
        tableContainer.innerHTML = '';
        tableContainer.appendChild(stickyControls);

        // Create table structure
        const tableWrapper = document.createElement('div');
        tableWrapper.className = 'table-wrapper';

        const table = document.createElement('table');
        
        const thead = document.createElement('thead');
        thead.innerHTML = `
            <tr>
                ${cachedData.columns.map(column => `
                    <th>${column}</th>
                `).join('')}
            </tr>
        `;
        table.appendChild(thead);

        const tbody = document.createElement('tbody');
        table.appendChild(tbody);

        // Initial load of rows
        tbody.innerHTML = renderRows(cachedData.data.slice(0, PAGE_SIZE), cachedData.columns);

        tableWrapper.appendChild(table);
        tableContainer.appendChild(tableWrapper);

        // Create load more button
        const loadMoreButton = document.createElement('button');
        loadMoreButton.className = 'load-more-button';
        loadMoreButton.textContent = `Load More (showing ${Math.min(PAGE_SIZE, cachedData.data.length)} of ${cachedData.data.length})`;
        tableContainer.appendChild(loadMoreButton);

        // Add search functionality
        let debounceTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                const totalResults = filterTable(e.target.value, tbody, cachedData);
                if (e.target.value) {
                    loadMoreButton.textContent = `Showing ${totalResults} matching results`;
                    loadMoreButton.style.display = 'none';
                } else {
                    loadMoreButton.textContent = `Load More (showing ${Math.min(PAGE_SIZE, cachedData.data.length)} of ${cachedData.data.length})`;
                    loadMoreButton.style.display = totalResults > PAGE_SIZE ? 'block' : 'none';
                }
            }, 300);
        });

        // Load More functionality
        loadMoreButton.addEventListener('click', () => {
            const currentRows = tbody.querySelectorAll('tr').length;
            const newRowsHTML = renderRows(
                cachedData.data.slice(currentRows, currentRows + PAGE_SIZE),
                cachedData.columns
            );
            tbody.insertAdjacentHTML('beforeend', newRowsHTML);
            
            const totalShown = tbody.querySelectorAll('tr').length;
            loadMoreButton.textContent = `Load More (showing ${totalShown} of ${cachedData.data.length})`;
            
            if (totalShown >= cachedData.data.length) {
                loadMoreButton.disabled = true;
                loadMoreButton.textContent = `Showing all ${cachedData.data.length} rows`;
            }
        });

        // Export functionality
        exportButton.addEventListener('click', () => {
            const csv = convertToCSV(cachedData.columns, cachedData.data);
            const filename = `${viewMapping[viewId]}_${new Date().toISOString().split('T')[0]}.csv`;
            downloadCSV(csv, filename);
        });

        content.dataset.loaded = 'true';

    } catch (error) {
        tableContainer.innerHTML = `
            <p class="error">Error loading data: ${error.message}</p>
        `;
    }
}

function convertToCSV(columns, data) {
    const header = columns.join(',');
    const rows = data.map(row => 
        columns.map(column => {
            const value = row[column] || '';
            return `"${String(value).replace(/"/g, '""')}"`;
        }).join(',')
    );
    return header + '\n' + rows.join('\n');
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.display = 'none';
    document.body.appendChild(link);
    
    link.click();
    
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// Initialize when the page loads
document.addEventListener('DOMContentLoaded', initializeDashboard);