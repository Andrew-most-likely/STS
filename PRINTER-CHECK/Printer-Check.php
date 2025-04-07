<!DOCTYPE html>
<html lang="en">
<script>
    // Check if the user is logged in (i.e., if 'userName' exists in sessionStorage)
    if (sessionStorage.getItem('userName') === null || sessionStorage.getItem('userName') === undefined) {
        // If user is undefined, redirect or show a message
        window.location.href = '../login.html';  // Redirect to the login page or display a message
    } else {
        // Proceed with the application logic if user is defined
        console.log('User is logged in:', sessionStorage.getItem('userName'));
    }
    // Check if the user is logged in
    if (sessionStorage.getItem('isLoggedIn') !== 'true') {
        window.location.href = '../index.html'; // Redirect to login page if not logged in
    } 
</script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ITFS</title>
    <link rel="icon" type="image/x-icon" href="../ASSETS/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="PCStyle.css">
    <link rel="stylesheet" href="../Styles.css">
</head>
<style>
.box-container {
    flex-direction: initial;
    align-items: initial;   
}
form {
    margin-top: initial;
    background-color: initial;
    padding: initial;
    border-radius: initial;
    box-shadow: initial;
    max-width: initial;
    width: initial;
}
select:not(.search-container select) {
    padding: 3px; 
    margin-bottom: initial; 
    border: initial; 
    border-radius: 5px; 
    box-sizing: initial; 
    font-size: initial;
    width: initial;
}
title h1 {
  margin: 0;
  font-size: 28px;
  color: var(--text-color);
}
.container {
  width: 90%;
  max-width: 97%;
  margin: 20px;
  padding: 20px;
  background-color: var(--topnav-bg);
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
title p {
  margin: 5px 0;
  font-size: 16px;
  color: var(--text-muted);
}
td {
    color: black;
}
table th {
    color: black;
}
</style>
<body onload="fetchChildGroups(); loadTheme();">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div>
            <div class="sidebar-header">
                <button class="toggle-btn" id="toggleBtn">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="sidebar-title">Student ITFS</div>
            </div>

            <div class="nav-items">
                <button id="tab1" class="sidebar-button" onclick="location.href='../FORM-HTML/EQ-Drop.html'">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Equipment Return</span>
                </button>
                <button id="tab2" class="sidebar-button" onclick="location.href='../FORM-HTML/Delivery-Intake.html'">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Delivery Intake</span>
                </button>
                <button id="tab3" class="sidebar-button" onclick="location.href='../FORM-HTML/E-Waste.html'">
                    <i class="fa-solid fa-recycle"></i>
                    <span>E-waste</span>
                </button>
                <button id="tab4" class="sidebar-button" onclick="location.href='../FORM-HTML/Room-Check.html'">
                    <i class="fa-solid fa-building"></i>
                    <span>Room Check Form</span>
                </button>
                <button id="tab5" class="sidebar-button" onclick="location.href='../FORM-HTML/Field-Work.html'">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                    <span>Field Work</span>
                </button>
                <button id="tab6" class="sidebar-button" onclick="location.href='#'">
                    <i class="fa-solid fa-print"></i>
                    <span>Printer Check</span>
                </button>
                <button id="tab7" class="sidebar-button" onclick="location.href='../INDEX-HTML/main.html'">
                    <i class="fa-regular fa-chart-bar"></i>
                    <span>Worker Stats</span>
                </button>

                <div class="decorative-line"></div>

                <button id="tab8" class="sidebar-button" onclick="location.href='../LAB-STATS/Lab-Stats.html'">
                    <i class="fa-solid fa-flask"></i>
                    <span>Lab Stats</span>
                </button>
                <button id="tab9" class="sidebar-button" onclick="location.href='../../inventory/main.php'">
                    <i class="fa-solid fa-warehouse"></i>
                    <span>Inventory</span>
                </button>
                <button id="tab10" class="sidebar-button" onclick="location.href='../INDEX-HTML/Raw-Data.html'">
                    <i class="fa-solid fa-file"></i>
                    <span>Raw Data</span>
                </button>
            </div>
        </div>

        <div class="sidebar-copyright">
            <img src="../ASSETS/logo-Black&White.png" class="sidebar-img" id="sidebarLogo" alt="Logo">
            <p>JWU 2025 ©</p>
        </div>
    </div>

    <!-- Overlay for mobile when sidebar is active -->
    <div class="overlay" id="overlay"></div>

    <!-- Top Navigation Bar -->
    <div class="topnav">

        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search for assets, tickets, or users...">
            <button data-emoji="✨" type="submit" class="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
        <div class="profile" id="profile">
            <div class="profile-icon" id="profileIcon">U</div>
            <div class="dropdown" id="dropdown">
                <div class="username">
                    <h2 id="usernameDisplay"></h2>
                </div>
                <!-- Theme toggle switch -->
                <div class="theme-toggle">
                    <span>Dark Mode</span>
                    <label class="toggle-switch">
                        <input type="checkbox" id="themeToggle">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <a href="#" class="dropdown-item">Schedule <i class="fa-solid fa-calendar-days" width="20"
                        height="20"></i></a>
                <a href="#" onclick="logout()" class="dropdown-item">Sign Out <i class="fa-solid fa-right-from-bracket"
                        width="20" height="20"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <div class="box-container">
           

            <style>
                #loading {
                  display: none;
                  /* Initially hidden */
                  margin-top: 10px;
                  color: blue;
                }
              
                #full_send_output {
                  display: none;
                  /* Initially hidden */
                  margin-top: 10px;
                  width: 100%;
                  height: 100px;
                }
              

                #LastRun {
                  position: absolute;
                  /* Position it relative to the body */
                  top: 70px;
                  /* Distance from the top */
                  right: 120px;
                  /* Distance from the right */
                }
              </style>
              
                <div class="container">


                  <div class="ticket-section">
                    <h2 style="margin: bottom 10px; "> Downcity Printer Checks </h2>
                    <div class="decorative-line"></div>
                    <button class="switchButton" onclick="location.href='Printer-CheckHS.php'" id='campusSwitch'> Switch to Harborside
                    </button>
                    <button class="logbutton" id="refreshButton" onclick="refreshButton()"> Refresh </button>
                    <div id="lastRun">
                      <?php include('lastRun.php')?>
                    </div>

                    <div id="loading">Loading...</div> <!-- Loading indicator -->
                    <div id="timer2">Time: 0s</div>
              
                    <textarea id="full_send_output" placeholder="Output here" readonly></textarea>
              
                    <div class="printer-container" name="worm_was_here"></div>
              
                    <div id="downcityPrinters"></div>
              
                    <div id="harborsidePrinters" hidden="true"></div>
              
                  </div>

                </div>
              </body>
              
              <script>
              
                  <?php
                    include('CDchecks.php');
                    include("DCPGrab.php");
                  ?>
              
              </script>
              
              <script>
                  function checkCurrentDay(ip, title) {
                    if (typeof currentDayChecks !== 'undefined' && Array.isArray(currentDayChecks)) {
                      currentDayChecks.forEach(function (check) {
                        if (ip == check.IP_Address) {
                          title.style.backgroundColor = "#007bff";
                          title.innerHTML = title.innerHTML + " ✔️";
                        }
                      });
                    } else {
                      console.log('currentDayChecks is not defined or is empty.');
                    }
                  }
              
                let timerInterval;
              
                function refreshButton() {
                  const button = document.getElementById('refreshButton');
                  const loadingIndicator = document.getElementById('loading');
                  const timer = document.getElementById('timer2');
                  let startTime = Date.now();
              
                  timerInterval = setInterval(() => {
                    let elapsedTime = Math.floor((Date.now() - startTime) / 1000);
                    timer.textContent = `Time: ${elapsedTime}s`;
                  }, 1000);
              
                  button.disabled = true;
                  loadingIndicator.style.display = 'block'
                  // Make a request to the URL
                  fetch('http://10.9.5.21:8080/full_send', {
                    method: 'POST',
                    mode: "no-cors"
                  })
                    .then(data => {
                      document.getElementById('full_send_output').value = data;
                      full_send_output.style.display = 'block';
                      loadingIndicator.style.display = 'none';
                      clearInterval(timerInterval);
                      fetch('timestamp.php', { method: 'POST' })
                        .then(response => response.text())
                        .then(result => { console.log('Timestamp added: ' + result); })
                        .catch(error => console.error('Error:', error));
                    })
              
                    .catch(error => {
                      const outputArea = document.getElementById('full_send_output'); // Get the output area
                      outputArea.value = `Full send failed: ${error}`; // Use backticks for template literals
                      outputArea.style.display = 'block'; // Show the output area
                      loadingIndicator.style.display = 'none'; // Hide the loading indicator
                    });
                }
              
                // Expanding Buttons
                function toggleSection(sectionId, arindex1Id) {
                  var sectionContent = document.getElementById(sectionId);
                  var arindex1 = document.getElementById(arindex1Id);
              
                  // Toggle the display property directly
                  if (sectionContent.style.display === "none" || sectionContent.style.display === "") {
                    sectionContent.style.display = "block";
                    arindex1.textContent = "";
                  } else {
                    sectionContent.style.display = "none";
                    arindex1.textContent = "";
                  }
                }
              
                function createHiddenInput(inputName, inputValue, inputTarget) {
                  let newInput = document.createElement("input");
                  newInput.name = inputName
                  if (inputValue == "-") {
                    newInput.Value = ""
                  } else {
                    newInput.value = inputValue
                  }
                  newInput.type = "hidden" // FOR TROUBLE SHOOTING SHOWS ITEM VALUE BOXES [text] = show [hidden] = hidden 
                  inputTarget.append(newInput)
                }
              
                // Generate HTML Table from Printer List
                function createDiv(a, b, c) {
                  var section = document.createElement("div");
                  var title = document.createElement("div");
                  var span = document.createElement("span");
                  var tableContent = document.createElement("div");
                  var tableHead = document.createElement("h3");
                  var emptyTable = document.createElement("table");
                  var form = document.createElement("form");
                  var input = document.createElement("select");
                  var option = document.createElement("option");
                  var submit = document.createElement("button");
                  let footer = document.getElementsByTagName('footer')[0];
                    <?php include("PCDropdown.php"); ?>
              
                    section.className = "section";
              
                  title.className = 'section-title';
                  title.setAttribute('onclick', "toggleSection('section" + a + "', 'arindex11')");
                  title.innerHTML = b
                  title.style.backgroundColor = "#CC2222";
              
                  span.id = 'arindex11';
                  span.className = 'arindex1';
                  span.innerHTML = ''
              
                  tableContent.id = 'section' + a;
                  tableContent.className = 'section-content';
              
                  form.id = "form" + a;
                  form.action = "PcSubmit.php"
                  form.method = "post"
              
                  input.id = 'select' + a;
                  input.name = 'name';
              
                  option.innerHTML = "Select Name Here";
                  option.disabled = true;
                  option.selected = true;
              
                  submit.type = "submit"
                  submit.innerHTML = ' Input Name to Log Printer Check ';
                  submit.id = "logButton" + a;
                  submit.className = "submitButton";
              
                  title.append(span);
                  input.append(option);
                  activeStudents.forEach(function (student) {
                    let studentOption = document.createElement("option");
                    studentOption.value = student.student_name;
                    studentOption.innerHTML = student.student_name;
                    input.append(studentOption);
                  });
                  form.append(input, submit);
                  tableContent.append(tableHead, emptyTable, form);
                  section.append(title, tableContent);
                  document.getElementById("downcityPrinters").append(section);
                }
              
                let i = 1;
                studentPrinters.forEach(function (printer) {
                  createDiv(i, printer.install_location, printer.install_campus);
                  var table = document.createElement("table");
                  table.id = printer.IP_Address
                  table.innerHTML = "<tr>" +
                    "<th>" + "Printer Information" + "</th>" +
                    "<td> <a target = blank href = //" + printer.IP_Address + ">" + printer.IP_Address + "</a> </td>" +
                    "<td>" + printer.Serial_Number + "</td>" +
                    "<td>" + printer.jwu_asset + "</td>" +
                    "<td>" + printer.install_campus + "</td>" +
                    "<td>" + "-" + "</td>" + // Model Number
                    "</tr>" +
                    "<tr>" +
                    "<th>" + "Toner Cartridge Levels" + "</th>" +
                    "<td>" + "Printer Not Found on Network, Check In Person" + "</td>" + // Black Cartridge
                    "<td>" + "-" + "</td>" + // Cyan Cartridge
                    "<td>" + "-" + "</td>" + // Yellow Cartridge
                    "<td>" + "-" + "</td>" + // Magenta Cartridge
                    "<td>" + "-" + "</td>" + //EMPTY CELL TO FILL TABLE
                    "</tr>" +
                    "<tr>" +
                    "<th>" + "Toner Drum Levels" + "</th>" +
                    "<td>" + "-" + "</td>" + // Black Drum
                    "<td>" + "-" + "</td>" + // Cyan Drum
                    "<td>" + "-" + "</td>" + // Yellow Drum
                    "<td>" + "-" + "</td>" + // Magenta Drum
                    "<td>" + "-" + "</td>" + //EMPTY CELL TO FILL TABLE
                    "</tr>" +
                    "<tr>" +
                    "<th>" + "Kit Levels and Waste Toner" + "</th>" +
                    "<td>" + "-" + "</td>" + // Maintenance Kit
                    "<td>" + "-" + "</td>" + // Transfer Kit
                    "<td>" + "-" + "</td>" + // Fuser Kit
                    "<td>" + "-" + "</td>" + // Waste Toner
                    "<td>" + "-" + "</td>" + //EMPTY CELL TO FILL TABLE
                    "</tr>" +
                    "<tr>" +
                    "<th>" + "Paper Tray Levels" + "</th>" +
                    "<td>" + "-" + "</td>" + // Paper Tray 2
                    "<td>" + "-" + "</td>" + // Paper Tray 3
                    "<td>" + "-" + "</td>" + // Paper Tray 4
                    "<td>" + "-" + "</td>" + // Paper Tray 5
                    "<td>" + "-" + "</td>" + // EMPTY CELL TO FILL TABLE FORMAT
                    "</tr>";
                  var targetDiv = document.getElementById("section" + i);
                  var targetEmpty = targetDiv.children[1];
                  targetDiv.replaceChild(table, targetEmpty);
              
                  let targetForm = table.parentNode.children[2]
                  createHiddenInput("IP Address", printer.IP_Address, targetForm);
                  createHiddenInput("Install Location", printer.install_location, targetForm);
                  i++
                });
              
                // Append Printer Data to HTML
                fetch('treeview.json')
                  .then(response => response.json())
                  .then(data => {
                    data.forEach(function (pulledData) {
                      // Find the table corresponding to the specific RootNode value (e.g., "10.12.213.1231")
                      var target = document.getElementById(pulledData.RootNode);
              
                      //Data Index
                      if (target) {
                        var targetForm = target.parentNode.children[2]
              
                        let modelNumber = pulledData.Children[1].Text
                        let modelNumberCell = target.rows[0].cells[5]
                        modelNumberCell.innerHTML = modelNumber
                        createHiddenInput("Model Number", modelNumber, targetForm);
              
                        let targetTitle = target.parentNode.parentNode.children[0]
                        targetTitle.style.backgroundColor = "#808080"
              
                        checkCurrentDay(target.id, targetTitle);
              
                        const printerParts = [
                          { index1: 1, index2: 1, part: "Black Cartridge", defaultValue: "Error Reading Printer, Attempt Connection with IP" },
                          { index1: 1, index2: 2, part: "Cyan Cartridge", defaultValue: "-" },
                          { index1: 1, index2: 3, part: "Yellow Cartridge", defaultValue: "-" },
                          { index1: 1, index2: 4, part: "Magenta Cartridge", defaultValue: "-" },
                          { index1: 2, index2: 1, part: "Black Drum", defaultValue: "-" },
                          { index1: 2, index2: 2, part: "Cyan Drum", defaultValue: "-" },
                          { index1: 2, index2: 3, part: "Yellow Drum", defaultValue: "-" },
                          { index1: 2, index2: 4, part: "Magenta Drum", defaultValue: "-" },
                          { index1: 3, index2: 1, part: "Maintenance Kit", defaultValue: "-" },
                          { index1: 3, index2: 2, part: "Transfer Kit", defaultValue: "-" },
                          { index1: 3, index2: 3, part: "Fuser Kit", defaultValue: "-" },
                          { index1: 3, index2: 4, part: "Waste Toner", defaultValue: "-" },
                          { index1: 4, index2: 1, part: "Tray 2", defaultValue: "Error Reading Printer, Attempt Connection with IP" },
                          { index1: 4, index2: 2, part: "Tray 3", defaultValue: "-" },
                          { index1: 4, index2: 3, part: "Tray 4", defaultValue: "-" },
                          { index1: 4, index2: 4, part: "Tray 5", defaultValue: "-" }
                        ];
              
                        // Find Data in JSON File
                        printerParts.forEach(({ index1, index2, part, defaultValue }) => {
                          let targetCell = target.rows[index1].cells[index2]
                          let partValue = pulledData.Children.find(child => child.Text.includes(part))?.Text || defaultValue;
              
                          targetCell.innerHTML = partValue
                          if (partValue.includes("10%") || partValue.includes(" 0%") || partValue.includes("Empty") || partValue.includes("Depleted")) {
                            targetCell.style.backgroundColor = "#FF8888"
                          }
                          else if (partValue.includes("20")) {
                            targetCell.style.backgroundColor = "#FFFF88"
                          }
                          createHiddenInput(part, partValue, targetForm);
                        });
              
                      } else {
                        console.log(`Printer with RootNode ${pulledData.RootNode} not found in the table.`);
                      }
                    })
                  })
                  // In case of error
                  .catch(error => console.error('Error fetching JSON:', error));
              </script>
            
        </div>
    </div>

    <!-- Footer -->

    <script>
        // Toggle sidebar functionality
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleBtn');
            const overlay = document.getElementById('overlay');
            const profileIcon = document.getElementById('profileIcon');
            const dropdown = document.getElementById('dropdown');
            const usernameDisplay = document.getElementById('usernameDisplay');
            const themeToggle = document.getElementById('themeToggle');
            const sidebarLogo = document.getElementById('sidebarLogo');
            const tabs = document.querySelectorAll('.tab');

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

            // Toggle button for sidebar
            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
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
            window.location.href = 'index.html';
        };

        // Placeholder for fetchChildGroups function
        function fetchChildGroups() {
            console.log("Fetching child groups...");
            // Your existing function code would go here
        }
    </script>
    <script src="../INDEX-JS/testindexsearch.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/js-confetti@latest/dist/js-confetti.browser.js'></script>
    <script>
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

    </script>
</body>

</html>