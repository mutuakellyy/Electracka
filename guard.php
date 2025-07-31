<?php 
require 'guardphp.php';
require 'header.php';
?>

<head>
    <title>Guard Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="guard.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

</head>

<body>
    <h1><?php echo 'Welcome'; ?><span class="min-dtn"><?php echo " "; ?><?php echo $firstname; ?></span></h1>

    <!-- Navigation Tabs -->
    <!-- Navigation Tabs -->
    <div class="nav-tabs">
        <button onclick="showTab('schedule')">📅 My Schedule</button>
        <button onclick="showTab('scan')">📷 Scan Location</button>
        <button onclick="showTab('report')">📝 Submit Report</button>
    </div>


    <!-- Schedule -->
    <div id="schedule" class="tab active">
        <h3>Assigned Shifts</h3>
        <table>
            <tr>
                <th>Location</th>
                <th>Start</th>
                <th>End</th>
                <th>Interval</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($schedules)): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['shift_start'] ?></td>
                    <td><?= $row['shift_end'] ?></td>
                    <td><?= $row['scan_interval'] ?> mins</td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Scan -->
    <!-- Scan -->
    <div id="scan" class="tab">
        <h3>Scan Location QR</h3>
        <div id="reader" style="width:300px; margin:20px auto;"></div>
        <p id="scanResult">Awaiting scan...</p>
    </div>


    <!-- Report -->
    <div id="report" class="tab">
        <h3>Report After Scan</h3>
        <form id="reportForm">
            <select name="location_id" id="locationSelect" required>
                <option disabled selected>Select Location</option>
                <?php while ($loc = mysqli_fetch_assoc($locations)): ?>
                    <option value="<?= $loc['id'] ?>"><?= $loc['name'] ?></option>
                <?php endwhile; ?>
            </select><br><br>
            <textarea name="details" rows="4" cols="30" placeholder="Report Details" required></textarea><br>
            <button type="submit">Send Report</button>
        </form>
        <p id="reportFeedback"></p>
    </div>

    <script>
        // Tab switcher
        function showTab(id) {
            document.querySelectorAll('.tab').forEach(e => e.classList.remove('active'));
            document.getElementById(id).classList.add('active');
        }

        // QR scan simulation (simplified)

        function showTab(id) {
            document.querySelectorAll('.tab').forEach(e => e.classList.remove('active'));
            document.getElementById(id).classList.add('active');

            // Activate scanner only when "scan" tab is shown
            if (id === "scan") {
                startQRScanner();
            }
        }

        function startQRScanner() {
            const qrCodeScanner = new Html5Qrcode("reader");

            qrCodeScanner.start(
                { facingMode: "environment" }, // use rear camera
                {
                    fps: 10,
                    qrbox: 250
                },
                (decodedText, decodedResult) => {
                    document.getElementById("scanResult").textContent = "✅ Scanned: " + decodedText;
                    qrCodeScanner.stop(); // Stop scanning after success

                    // Save scan timestamp for interval tracking
                    localStorage.setItem("lastScan", Date.now());
                },
                (errorMessage) => {
                    // Optional: log errors or show warnings
                }
            ).catch(err => {
                document.getElementById("scanResult").textContent = "❌ Unable to start scanner.";
                console.error(err);
            });
        }
        // Report submission (AJAX with offline fallback)
        document.getElementById('reportForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = new FormData(this);
            fetch('submit_guard_report.php', {
                method: 'POST',
                body: form
            })
                .then(res => res.text())
                .then(msg => document.getElementById('reportFeedback').textContent = msg)
                .catch(() => {
                    localStorage.setItem('pendingReport', JSON.stringify(Object.fromEntries(form.entries())));
                    document.getElementById('reportFeedback').textContent = 'Offline — report saved locally.';
                });
        });

        // Reminder checker
        setInterval(() => {
            const lastScan = parseInt(localStorage.getItem('lastScan') || '0');
            const minutesSince = (Date.now() - lastScan) / 60000;
            if (minutesSince > 45) { // example interval
                alert('Reminder: You missed your scan interval!');
            }
        }, 30000);
    </script>
</body>