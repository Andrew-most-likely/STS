<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../ASSETS/favicon.ico">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../INDEX-CSS/FieldWork.css">
    <title>Admin Dashboard - Field Work Requests</title>
</head>
<!-- Add page loader -->
<div class="page-loader">
    <div class="spinner"></div>
</div>

<div class="header">
    <div class="logo">
        <a href="../INDEX-HTML/main.html">
            <img src="../ASSETS/logo64.png" width="64" height="64" alt="" />
        </a>
    </div>
    <div class="title">
        <h1>STS Admin Dashboard</h1>
        <p>Review and manage field work submissions</p>
    </div>
</div>
<div class="decorative-line"></div>

<div class="filter-section">
    <label for="status-filter">Filter by Status:</label>
    <select id="status-filter" onchange="filterRequests()">
        <option value="all">All Requests</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="denied">Denied</option>
    </select>

    <label for="date-filter">Filter by Date:</label>
    <input type="date" id="date-filter" onchange="filterRequests()">
</div>

<div id="requests-container">
    <?
        include('../db_connection.php');
        
        // Establish connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // SQL query with a CASE to prioritize 'pending' requests
        $sql = "SELECT * FROM Field_Work 
                ORDER BY 
                    CASE status 
                        WHEN 'pending' THEN 0 
                        WHEN 'approved' THEN 1
                        WHEN 'denied' THEN 2 
                        ELSE 3 
                    END, 
                    completion_time DESC";
        
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $statusClass = 'status-' . ($row['status'] ?? 'pending');
                echo '<div class="request-card" data-status="'.($row['status'] ?? 'pending').'" data-date="'.$row['completion_time'].'" id="card-'.$row['id'].'">';
                echo '<h3>Ticket: ' . htmlspecialchars($row['Ticket_Number']) . '</h3>';
                echo '<p><strong>Submitted by:</strong> ' . htmlspecialchars($row['Name']) . '</p>';
                echo '<p><strong>Work Description:</strong> ' . htmlspecialchars($row['Work_Description']) . '</p>';
                echo '<p><strong>Difficulty:</strong> <span class="difficulty-rating">' . str_repeat('★', $row['Difficulty']) . '</span></p>';
                echo '<p><strong>Submission Time:</strong> ' . htmlspecialchars($row['completion_time']) . '</p>';
                echo '<p><strong>Status:</strong> <span class="'.$statusClass.'">' . ($row['status'] ?? 'Pending') . '</span></p>';
                
                if(!isset($row['status']) || $row['status'] == 'pending') {
                    echo '<div class="action-buttons">';
                    echo '<button class="btn approve-btn" onclick="updateStatus('.$row['id'].", 'approved'".')">';
                    echo '<span class="spinner"></span><span class="btn-text">Approve</span>';
                    echo '</button>';
                    echo '<button class="btn deny-btn" onclick="updateStatus('.$row['id'].", 'denied'".')">';
                    echo '<span class="spinner"></span><span class="btn-text">Deny</span>';
                    echo '</button>';
                    echo '</div>';
                }
                echo '</div>';
            }
        } else {
            echo '<div class="no-requests">No requests found</div>';
        }
        
        $conn->close(); // Close the connection
        ?>
</div>

<script>
    // Show loading state when page loads
    document.addEventListener('DOMContentLoaded', () => {
        const loader = document.querySelector('.page-loader');
        loader.classList.add('active');
        setTimeout(() => {
            loader.classList.remove('active');
        }, 500);
    });

    function updateStatus(id, status) {
        const card = document.getElementById(`card-${id}`);
        const button = card.querySelector(`.${status === 'approved' ? 'approve' : 'deny'}-btn`);
        const otherButton = card.querySelector(`.${status === 'approved' ? 'deny' : 'approve'}-btn`);

        // Disable both buttons and show loading state
        button.classList.add('loading');
        button.disabled = true;
        otherButton.disabled = true;

        // First start the fade out animation
        setTimeout(() => {
            card.classList.add('removing');
        }, 100);

        // Send the request
        fetch('update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}&status=${status}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Wait for animation to complete before removing
                    setTimeout(() => {
                        card.remove();
                        // Check if we need to show no requests message
                        const remainingCards = document.querySelectorAll('.request-card:not(.removing)');
                        if (remainingCards.length === 0) {
                            const container = document.getElementById('requests-container');
                            container.innerHTML = '<div class="no-requests">No requests found</div>';
                        }
                    }, 800); // Match this to the CSS transition duration
                } else {
                    alert('Error updating status');
                    card.classList.remove('removing');
                    button.classList.remove('loading');
                    button.disabled = false;
                    otherButton.disabled = false;
                }
            })
            .catch(error => {
                alert('Error updating status');
                card.classList.remove('removing');
                button.classList.remove('loading');
                button.disabled = false;
                otherButton.disabled = false;
            });
    }

    function filterRequests() {
        const loader = document.querySelector('.page-loader');
        loader.classList.add('active');

        const statusFilter = document.getElementById('status-filter').value;
        const dateFilter = document.getElementById('date-filter').value;
        const cards = document.getElementsByClassName('request-card');
        let visibleCount = 0;

        setTimeout(() => {
            for (let card of cards) {
                let showCard = true;

                if (statusFilter !== 'all' && card.dataset.status !== statusFilter) {
                    showCard = false;
                }

                if (dateFilter && card.dataset.date.split(' ')[0] !== dateFilter) {
                    showCard = false;
                }

                if (showCard) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0) scale(1)';
                    }, 50);
                    visibleCount++;
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(-30px) scale(0.95)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 800);
                }
            }

            // Show no requests message if needed
            const container = document.getElementById('requests-container');
            const noRequestsMsg = container.querySelector('.no-requests');

            if (visibleCount === 0 && !noRequestsMsg) {
                container.innerHTML += '<div class="no-requests">No requests found</div>';
            } else if (visibleCount > 0 && noRequestsMsg) {
                noRequestsMsg.remove();
            }

            loader.classList.remove('active');
        }, 300);
    }
</script>
</body>

</html>