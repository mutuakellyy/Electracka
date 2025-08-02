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

            if (id === "scan") {
                startQRScanner();
            }
        }

        function startQRScanner() {
            const qrCodeScanner = new Html5Qrcode("reader");

            qrCodeScanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                (decodedText, decodedResult) => {
                    document.getElementById("scanResult").textContent = "📦 QR Scanned: " + decodedText;
                    qrCodeScanner.stop();

                    let parsedData;
                    try {
                        parsedData = JSON.parse(decodedText);
                    } catch (e) {
                        document.getElementById("scanResult").textContent = "❌ Invalid QR Code format. Expected JSON.";
                        return;
                    }

                    const location_id = parsedData.location_id;
                    const institution_id = parsedData.institution_id;

                    if (!navigator.geolocation) {
                        alert("❌ Geolocation not supported on this device.");
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        position => {
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;

                            fetch("scan_location.php", {
                                method: "POST",
                                headers: { "Content-Type": "application/json" },
                                body: JSON.stringify({
                                    location_id,
                                    institution_id,
                                    latitude,
                                    longitude
                                })
                            })
                                .then(res => res.json())
                                .then(response => {
                                    document.getElementById("scanResult").textContent = response.message;

                                    // Save scan details for report form
                                    localStorage.setItem("lastScan", Date.now());
                                    localStorage.setItem("lastScanId", response.scan_id);
                                    localStorage.setItem("lastLocationId", response.location_id);

                                    // Auto-select location in report dropdown
                                    document.getElementById('locationSelect').value = response.location_id;
                                })
                                .catch(() => {
                                    document.getElementById("scanResult").textContent = "❌ Scan failed. Please try again.";
                                });

                        },
                        error => {
                            document.getElementById("scanResult").textContent = "❌ GPS Error: " + error.message;
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                },
                errorMessage => {
                    // Optional: silently handle failed scans
                }
            ).catch(err => {
                document.getElementById("scanResult").textContent = "❌ Scanner could not start.";
                console.error(err);
            });
        }

        // Report submission logic
        document.getElementById('reportForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = new FormData(this);
            const scanId = localStorage.getItem('lastScanId');

            if (scanId) {
                form.append('scan_id', scanId);
            }

            fetch('submit_guard_report.php', {
                method: 'POST',
                body: form
            })
                .then(res => res.text())
                .then(msg => {
                    document.getElementById('reportFeedback').textContent = msg;

                    // Optional cleanup
                    localStorage.removeItem('lastScanId');
                    localStorage.removeItem('lastLocationId');
                })
                .catch(() => {
                    document.getElementById('reportFeedback').textContent = 'Offline — report saved locally.';
                });
        });


        // Scan interval reminder
        setInterval(() => {
            const lastScan = parseInt(localStorage.getItem('lastScan') || '0');
            const minutesSince = (Date.now() - lastScan) / 60000;

            if (scanInterval > 0 && minutesSince > scanInterval) {
                alert(`Reminder: You missed your ${scanInterval}-minute scan interval!`);
            }
        }, 60000); // check every 60 seconds

    </script>
</body>