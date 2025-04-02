<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>JWU Printer Page</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="description" content="" />
  <link rel="icon" type="image/x-icon" href="../ASSETS/favicon.ico">
  <link rel="stylesheet" href="PCStyle.css">
</head>
<script>
  // Check if the user is logged in
  if (sessionStorage.getItem('isLoggedIn') !== 'true') {
    window.location.href = '../index.html'; // Redirect to login page if not logged in
  } 
</script>

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

  #refreshButton {
    position: absolute;
    /* Position it relative to the body */
    top: 60px;
    /* Distance from the top */
    right: 120px;
    /* Distance from the right */
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

<body>
  <div class="container">
    <div class="header">
      <div class="logo">
        <a href="../INDEX-HTML/main.html">
          <img src="../ASSETS/logo64.png" width="64" height="64" alt="" />
        </a>
      </div>
      <div class="title">
        <h1>STS</h1>
        <p>Student Ticketing System: Tracking and Reporting for student IT Field activity</p>
      </div>
    </div>
    <div class="decorative-line"></div>
    <div class="ticket-section">
      <h2 style="margin: bottom 10px; "> Downcity Printer Checks </h2>

      <button class="switchButton" onclick="location.href='HSPrinterCheck.php'" id='campusSwitch'> Switch to Harborside
      </button>
      <div id="lastRun">
        <?php include('lastRun.php')?>
      </div>
      <button class="logbutton" id="refreshButton" onclick="refreshButton()"> Refresh </button>
      <div id="loading">Loading...</div> <!-- Loading indicator -->
      <div id="timer2">Time: 0s</div>

      <textarea id="full_send_output" placeholder="Output here" readonly></textarea>

      <div class="printer-container" name="worm_was_here"></div>

      <div id="downcityPrinters"></div>

      <div id="harborsidePrinters" hidden="true"></div>

    </div>
    <div class="decorative-line"></div>
    <div class="copyright">
      <p> &copy; Johnson & Wales University 2024<span id="year"></span></p>
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

</html>