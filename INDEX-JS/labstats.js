/*

-------------------------------------------------------------
 WARNING: This JS was written under extreme conditions.
 Possible side effects include:
  - Unexplained div soup
  - CSS that works... until it doesn’t
  - JavaScript sorcery holding everything together
  - Debugging that involves lots of staring and crying

 If you’re reading this, good luck. You’ll need it.
-------------------------------------------------------------

recent changes in the code have made the above statment no longer true
the following code is godlike and is perfect
I could not improve it if I wanted to 
it stands as a testiment to what 8 shots of esspresso can do to code quality
:P

*/

let stationsByGroup = new Map();
let groupData = new Map(); // Stores { groupId: groupName }

// Authentication
const checkAuth = () => {
    if (sessionStorage.getItem('isLoggedIn') !== 'true') {
        window.location.href = '../index.html';
    } else {
        const username = sessionStorage.getItem('userName');
        document.getElementById('content').textContent =
            `Welcome to STS ${username.charAt(0).toUpperCase() + username.slice(1)}`;
    }
};

const logout = () => {
    sessionStorage.removeItem('isLoggedIn');
    sessionStorage.removeItem('userName');
    window.location.href = '../index.html';
};

async function fetchChildGroups() {
    const refreshBtn = document.getElementById('btn');
    
    // Show loading spinner and disable button
    refreshBtn.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i> Refreshing...';
    refreshBtn.disabled = true;

    try {
        const urls = [
            'http://10.9.5.21:3000/groups/1101/groups',
            'http://10.9.5.21:3000/groups/1116/groups'
        ];

        const responses = await Promise.all(urls.map(url => fetch(url)));
        const data = await Promise.all(responses.map(res => res.json()));

        // Store group names with their IDs
        groupData.clear();
        data.flat().forEach(group => {
            groupData.set(group.id, group.name);
        });

        await fetchOfflineStations();
    } catch (error) {
        console.error('Error fetching child groups:', error);
        showError('Failed to load groups.');
    }

    // Restore refresh button
    refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh Groups';
    refreshBtn.disabled = false;
}

async function fetchOfflineStations() {
    clearTrayContent();
    showLoading();

    for (const groupId of groupData.keys()) {
        try {
            const response = await fetch(`http://10.9.5.21:3000/groups/${groupId}/stations?status=offline`);
            if (!response.ok) throw new Error(`HTTP error ${response.status}`);

            const data = await response.json();
            updateGroupStations(groupId, data.results);
        } catch (error) {
            console.error(`Error fetching stations for group ${groupId}:`, error);
            showError(`Failed to fetch stations for group ${groupId}`);
        }
    }

    hideLoading();
    displayGroupTabs();
}

function updateGroupStations(groupId, stations) {
    if (!stationsByGroup.has(groupId)) {
        stationsByGroup.set(groupId, new Map());
    }

    const groupStations = stationsByGroup.get(groupId);
    stations.forEach(station => groupStations.set(station.name, station));
}

function displayGroupTabs() {
    const trayTabs = document.getElementById('trayTabs');

    // Keep the refresh button
    const refreshButton = trayTabs.querySelector('#btn');
    trayTabs.innerHTML = '';
    trayTabs.appendChild(refreshButton);

    // Add group tabs
    let activeTabCreated = false;
    groupData.forEach((groupName, groupId) => {
        const stationCount = stationsByGroup.get(groupId)?.size || 0;
        if (stationCount === 0) return;

        const tabButton = document.createElement('button');
        tabButton.id = `tab-${groupId}`;
        tabButton.className = 'tray-tab';
        tabButton.innerHTML = `
            <i class="fas fa-building"></i>
            <span>${groupName}</span> 
            <span class="count-badge">${stationCount}</span>
        `;
        tabButton.onclick = () => {
            // Remove active class from all tabs
            document.querySelectorAll('.tray-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Add active class to clicked tab
            tabButton.classList.add('active');
            
            showGroupContent(groupId);
        };
        trayTabs.appendChild(tabButton);
        
        // Activate first tab with stations
        if (!activeTabCreated && stationCount > 0) {
            tabButton.classList.add('active');
            showGroupContent(groupId);
            activeTabCreated = true;
        }
    });

    if (!activeTabCreated) {
        showEmptyState();
    }
}

function showGroupContent(groupId) {
    const trayContent = document.getElementById('trayContent');
    trayContent.innerHTML = '';

    const groupName = groupData.get(groupId);
    const headerDiv = document.createElement('div');
    headerDiv.innerHTML = `<h2 class="mb-4">${groupName} Offline Stations</h2>`;
    trayContent.appendChild(headerDiv);

    const stations = Array.from(stationsByGroup.get(groupId)?.values() || []);
    if (stations.length === 0) {
        trayContent.innerHTML += '<p>No offline stations found.</p>';
        return;
    }

    // Sort stations by the number part of their name (after the hyphen)
    stations.sort((a, b) => {
        // Extract the number after the hyphen from station names
        const getStationNumber = (name) => {
            const match = name.match(/-(\d+)$/);
            return match ? parseInt(match[1], 10) : Infinity; // Return Infinity for stations without number
        };
        
        const numA = getStationNumber(a.name);
        const numB = getStationNumber(b.name);
        
        return numA - numB; // Sort in ascending order
    });

    const stationGrid = document.createElement('div');
    stationGrid.className = 'station-grid';
    trayContent.appendChild(stationGrid);

    stations.forEach(station => {
        const stationCard = document.createElement('div');
        stationCard.className = 'station-card fade-in';
        stationCard.innerHTML = `
            <h3>${station.name}</h3>
            <p><i class="fas fa-network-wired"></i> ${station.ip_addresses[0] || 'N/A'}</p>
            <span class="status-badge">Offline</span>
            <span class="model-badge">${station.model || 'N/A'}</span>
        `;
        
        // Add click event to show more details
        stationCard.onclick = () => showStationDetails(station);

        stationGrid.appendChild(stationCard);
    });
}

function showStationDetails(station) {
    const detailsModal = document.createElement('div');
    detailsModal.className = 'station-details-modal';  // Modal wrapper
    detailsModal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Station Details: ${station.name}</h3>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <p><strong>IP Address:</strong> ${station.ip_addresses[0] || 'N/A'}</p>
                <p><strong>MAC Address:</strong> ${station.mac_addresses[0] || 'N/A'}</p>
                <p><strong>Operating System:</strong> ${station.operating_systems[0]?.name || 'N/A'} ${station.operating_systems[0]?.version || ''}</p>
                <p><strong>Hostname:</strong> ${station.host_name || 'N/A'}</p>
                <p><strong>Manufacturer:</strong> ${station.manufacturer || 'N/A'}</p>
                <p><strong>Model:</strong> ${station.model || 'N/A'}</p>
                <p><strong>Form Factor:</strong> ${station.form_factor || 'N/A'}</p>
                <p><strong>Remote Access Address:</strong> ${station.remote_access_address || 'N/A'}</p>
                <p><strong>Client Version:</strong> ${station.client_version || 'N/A'}</p>
                <p><strong>Allow Student Routing:</strong> ${station.allow_student_routing ? 'Yes' : 'No'}</p>
            </div>
        </div>
    `;

    // Close modal functionality
    const closeBtn = detailsModal.querySelector('.close-btn');
    closeBtn.onclick = () => detailsModal.remove();

    // Append modal to body and show it
    document.body.appendChild(detailsModal);
}

function clearTrayContent() {
    document.getElementById('trayContent').innerHTML = '';
    stationsByGroup.clear();
}

function showLoading() {
    document.getElementById('trayContent').innerHTML = '<div class="loading"><i class="fas fa-circle-notch fa-spin"></i></div>';
}

function hideLoading() {
    const loading = document.querySelector('.loading');
    if (loading) loading.remove();
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message fade-in';
    errorDiv.innerHTML = `
        <div class="error-title"><i class="fas fa-exclamation-circle"></i> Error</div>
        <p>${message}</p>
    `;
    
    const trayContent = document.getElementById('trayContent');
    trayContent.prepend(errorDiv);
    
    // Auto-remove error after 5 seconds
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.remove();
        }
    }, 5000);
}

function showEmptyState() {
    const trayContent = document.getElementById('trayContent');
    trayContent.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-desktop"></i>
            <h3>No offline stations found</h3>
            <p>All stations appear to be online</p>
        </div>
    `;
}
