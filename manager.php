<?php
require 'managerphp.php';
require 'header.php';
?>

<head>
    <title>Manager Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="manager.css">
    <div id="notification" style="background:#fffae6; padding:10px; border:1px solid #ccc; display:none;"></div>

</head>

<body>
    <h2>👤 Manager Dashboard - Institution Overview</h2>

    <div class="summary">
        <div class="card"><strong>Total Personnel:</strong> <?= $total['total'] ?></div>
        <div class="card"><strong>Present Today:</strong> <?= $present['present'] ?></div>
        <div class="card"><strong>Absent Today:</strong> <?= $absent['absent'] ?></div>
        <div class="card"><strong>Active:</strong> <?= $active['active'] ?></div>
        <div class="card"><strong>Inactive:</strong> <?= $inactive['inactive'] ?></div>
    </div>

    <div class="nav-tabs">
        <button onclick="showTab('personnel')">Personnel</button>
        <button onclick="showTab('attendance')">Today's Attendance</button>
        <button onclick="showTab('reports')">Supervisor Reports</button>
    </div>

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
                            <form method="POST" action="deactivate_user.php">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button class="deactivate-btn">Deactivate</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="activate_user.php">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button class="activate-btn">Activate</button>
                            </form>
                        <?php endif; ?>
                    </td>

                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div id="attendance" class="tab">
        <h3>Today's Attendance Logs</h3>
        <table>
            <tr>
                <th>Email</th>
                <th>Status</th>
                <th>Time In</th>
                <th>Time Out</th>
            </tr>
            <?php while ($a = mysqli_fetch_assoc($attendance_logs)): ?>
                <tr class="<?= $a['present'] ? 'present' : 'absent' ?>">
                    <td><?= $a['emailaddress'] . ' (' . $a['firstname'] . ' ' . $a['surname'] . ')' ?></td>
                    <td><?= $a['present'] ? 'Present' : 'Absent' ?></td>
                    <td><?= $a['timein'] ?></td>
                    <td><?= $a['timeout'] ?? '--' ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div id="reports" class="tab">
        <h3>Supervisor Reports</h3>
        <table>
            <tr>
                <th>Supervisor</th>
                <th>Title</th>
                <th>Details</th>
                <th>Date</th>
            </tr>
            <?php while ($r = mysqli_fetch_assoc($reports)): ?>
                <tr>
                    <td><?= $r['supervisor_email'] ?></td>
                    <td><?= $r['title'] ?></td>
                    <td><?= $r['details'] ?></td>
                    <td><?= $r['date_created'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        function showTab(id) {
            document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
            document.getElementById(id).classList.add('active');
        }

        setInterval(() => {
            fetch('check_reports.php')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success' && data.new_reports > 0) {
                        const note = document.getElementById('notification');
                        note.textContent = `${data.new_reports} new supervisor report(s) today`;
                        note.style.display = 'block';
                    }
                });
        }, 10000); // every 10 seconds
    </script>
</body>