<?php
require 'supervisorphp.php';
require 'header.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Supervisor Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="supervisor.css">
</head>

<body>
    <div class="nav-tabs">
        <button onclick="showTab('personnel')"> Personnel List</button>
        <button onclick="showTab('schedule')">📅 Prepare Schedule</button>
        <button onclick="showTab('reports')">📄 Scan Reports</button>
        <button onclick="showTab('submitReport')">📝 Submit Report</button>
    </div>

    <h2>👨‍✈️ Supervisor Dashboard</h2>
    <h3>Recent Supervisor Reports</h3>
    <div id="recentReports"></div>

    <!-- 🟢 Present Guards -->
    <div id="personnel" class="tab active">
        <h3>Personnel List</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($u = mysqli_fetch_assoc($users)): ?>
                <tr class="<?= $u['active'] ? '' : 'inactive' ?>">
                    <td><?= $u['firstname'] . ' ' . $u['surname'] ?></td>
                    <td><?= $u['emailaddress'] ?></td>
                    <td><?= $u['role'] ?></td>
                    <td><?= $u['active'] ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <?php if ($u['active']): ?>
                            <form method="POST" action="handle_attendance.php" style="display:inline;">
                                <input type="hidden" name="guard_id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="status" value="1">
                                <button type="submit"
                                    style="background-color:green;color:white;border:none;padding:5px 10px;margin-right:5px;">Present</button>
                            </form>
                            <form method="POST" action="handle_attendance.php" style="display:inline;">
                                <input type="hidden" name="guard_id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="status" value="0">
                                <button type="submit"
                                    style="background-color:red;color:white;border:none;padding:5px 10px;">Absent</button>
                            </form>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- 📅 Schedule a Guard -->
    <div id="schedule" class="tab">
        <h3>Prepare Guard Schedule</h3>
        <form method="POST" action="add_schedule.php">
            <label>Guard Email:</label>
            <select name="guard_email" required>
                <?php while ($guard = $result->fetch_assoc()): ?>
                    <option value="<?= $guard['emailaddress'] ?>">
                        <?= $guard['firstname'], ' ', $guard['surname'] ?> (<?= $guard['emailaddress'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Shift Start:</label>
            <input type="datetime-local" name="shift_start" required><br><br>

            <label>Shift End:</label>
            <input type="datetime-local" name="shift_end" required><br><br>

            <label>Scan Interval:</label>
            <select name="scan_interval" required>
                <option value="15">15 Minutes</option>
                <option value="30">30 Minutes</option>
                <option value="45">45 Minutes</option>
                <option value="60">1 Hour</option>
                <option value="120">2 Hours</option>
                <option value="180">3 Hours</option>
            </select><br><br>

            <label>Location:</label>
            <select name="location_id" required>
                <?php while ($loc = mysqli_fetch_assoc($locations)): ?>
                    <option value="<?= $loc['id'] ?>"><?= $loc['name'] ?></option>
                <?php endwhile; ?>
            </select><br><br>

            <button type="submit">Add Schedule</button>
        </form>

        <!-- ➕ Add New Location -->
        <h3>Add New Location</h3>
        <form method="POST" action="add_location.php">
            <input type="text" name="location_name" placeholder="New Location" required>
            <button type="submit">Add Location</button>
        </form>
    </div>

    <!-- 📄 Reports by Guards -->
    <div id="reports" class="tab">
        <h3>Guard Scan Reports</h3>
        <table>
            <tr>
                <th>Guard</th>
                <th>Location</th>
                <th>Scan Time</th>
                <th>Details</th>
            </tr>
            <?php while ($r = mysqli_fetch_assoc($guardReports)): ?>
                <tr>
                    <td><?= $r['guard_email'] ?></td>
                    <td><?= $r['location_name'] ?></td>
                    <td><?= $r['submitted_at'] ?></td>
                    <td><?= $r['details'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>


    <!-- 📄 Reports to manager -->
    <div id="submitReport" class="tab">
        <h3>Submit Report to Manager</h3>
        <form id="supervisorReportForm" action="submit_supervisor_report.php">
            <label for="reportTitle">Title:</label><br>
            <input type="text" id="reportTitle" name="title" required><br><br>

            <label for="reportDetails">Details:</label><br>
            <textarea id="reportDetails" name="details" rows="5" cols="40" required></textarea><br><br>

            <button type="submit">Send Report</button>
        </form>
        <p id="reportFeedback" style="margin-top:10px;"></p>
    </div>

    <script>
        document.getElementById('supervisorReportForm').addEventListener('submit', function (event) {
            event.preventDefault();

            const title = document.getElementById('reportTitle').value.trim();
            const details = document.getElementById('reportDetails').value.trim();
            const feedback = document.getElementById('reportFeedback');

            if (!title || !details) {
                feedback.textContent = 'Please fill in both fields.';
                feedback.style.color = 'red';
                return;
            }

            fetch('submit_supervisor_report.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include', // include cookies/session
                body: JSON.stringify({ title, details })
            })

                .then(res => res.json())
                .then(data => {
                    feedback.textContent = data.message;
                    feedback.style.color = data.status === 'success' ? 'green' : 'red';
                })
                .catch(() => {
                    feedback.textContent = 'Server error. Please try again.';
                    feedback.style.color = 'red';
                });
        });

        function showTab(id) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.getElementById(id).classList.add('active');
        }

        function loadRecentReports() {
            fetch('get_recent_supervisor_reports.php')
                .then(res => res.text())
                .then(html => {
                    document.getElementById('recentReports').innerHTML = html;
                });
        }
        loadRecentReports(); // initial load

        // After submitting the report
        document.getElementById('supervisorReportForm').addEventListener('submit', function (event) {
            // existing code...
            fetch('submit_supervisor_report.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title, details })
            })
                .then(res => res.json())
                .then(data => {
                    feedback.textContent = data.message;
                    feedback.style.color = data.status === 'success' ? 'green' : 'red';
                    if (data.status === 'success') {
                        loadRecentReports(); // refresh view
                    }
                });
        });
    </script>
</body>

</html>