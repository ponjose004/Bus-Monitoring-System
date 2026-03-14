<?php
// ── Database connection using Railway environment variables ──
// Railway automatically injects these — no hardcoding needed!
$host     = getenv('MYSQLHOST')     ?: 'localhost';
$user     = getenv('MYSQLUSER')     ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: '';
$dbname   = getenv('MYSQLDATABASE') ?: 'railway';
$port     = getenv('MYSQLPORT')     ?: 3306;

$con = new mysqli($host, $user, $password, $dbname, $port);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$query  = "SELECT * FROM bus_number_detection ORDER BY id DESC";
$result = mysqli_query($con, $query);
$total  = mysqli_num_rows($result);

// Count today's buses
$today        = date('Y-m-d');
$q_today      = "SELECT COUNT(*) as cnt FROM bus_number_detection WHERE In_date = '$today'";
$r_today      = mysqli_query($con, $q_today);
$today_count  = mysqli_fetch_assoc($r_today)['cnt'];

// Count currently inside (no out time)
$q_inside     = "SELECT COUNT(*) as cnt FROM bus_number_detection WHERE Out_time IS NULL";
$r_inside     = mysqli_query($con, $q_inside);
$inside_count = mysqli_fetch_assoc($r_inside)['cnt'];

mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="10">
    <title>Bus Monitoring System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
        }

        /* ── Header ── */
        header {
            background: linear-gradient(135deg, #1e40af, #7c3aed);
            padding: 20px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        header h1 span { color: #fbbf24; }
        .live-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.15);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        .dot {
            width: 10px; height: 10px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        /* ── Stats Cards ── */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 30px 40px 10px;
        }
        .card {
            background: #1e293b;
            border-radius: 12px;
            padding: 24px;
            border-left: 4px solid;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .card:nth-child(1) { border-color: #3b82f6; }
        .card:nth-child(2) { border-color: #22c55e; }
        .card:nth-child(3) { border-color: #f59e0b; }
        .card-label { font-size: 0.8rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        .card-value { font-size: 2.2rem; font-weight: 700; margin-top: 8px; }
        .card:nth-child(1) .card-value { color: #60a5fa; }
        .card:nth-child(2) .card-value { color: #4ade80; }
        .card:nth-child(3) .card-value { color: #fbbf24; }

        /* ── Table ── */
        .table-section { padding: 20px 40px 40px; }
        .table-section h2 {
            font-size: 1.1rem;
            color: #94a3b8;
            margin-bottom: 16px;
            font-weight: 500;
        }
        .table-wrap {
            background: #1e293b;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #0f172a; }
        thead th {
            padding: 14px 18px;
            text-align: left;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
        }
        tbody tr {
            border-top: 1px solid #334155;
            transition: background 0.2s;
        }
        tbody tr:hover { background: #273549; }
        tbody td { padding: 14px 18px; font-size: 0.92rem; }

        .bus-badge {
            background: #1e40af;
            color: #93c5fd;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-block;
        }
        .plate {
            font-family: 'Courier New', monospace;
            background: #fbbf24;
            color: #1e293b;
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.85rem;
        }
        .time-in  { color: #4ade80; }
        .time-out { color: #f87171; }
        .status-in  {
            background: #14532d;
            color: #4ade80;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .status-out {
            background: #450a0a;
            color: #f87171;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #475569;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<header>
    <h1>🚌 Bus <span>Monitoring</span> System</h1>
    <div class="live-badge">
        <div class="dot"></div>
        <span>Live — refreshes every 10s</span>
    </div>
</header>

<div class="stats">
    <div class="card">
        <div class="card-label">Total Detections</div>
        <div class="card-value"><?= $total ?></div>
    </div>
    <div class="card">
        <div class="card-label">Today's Buses</div>
        <div class="card-value"><?= $today_count ?></div>
    </div>
    <div class="card">
        <div class="card-label">Currently Inside</div>
        <div class="card-value"><?= $inside_count ?></div>
    </div>
</div>

<div class="table-section">
    <h2>Detection Log</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bus Number</th>
                    <th>License Plate</th>
                    <th>In Time</th>
                    <th>In Date</th>
                    <th>Out Time</th>
                    <th>Out Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0):
                // re-fetch since we closed connection — reconnect
                $con2 = new mysqli($host, $user, $password, $dbname, $port);
                $res2 = mysqli_query($con2, "SELECT * FROM bus_number_detection ORDER BY id DESC");
                while ($row = $res2->fetch_assoc()):
                    $has_out = !empty($row['Out_time']) && $row['Out_time'] !== '00:00:00';
            ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><span class="bus-badge"><?= htmlspecialchars($row['bus_number']) ?></span></td>
                    <td><span class="plate"><?= htmlspecialchars($row['licence_plate_number']) ?></span></td>
                    <td class="time-in"><?= $row['In_time'] ?? '—' ?></td>
                    <td><?= $row['In_date'] ?? '—' ?></td>
                    <td class="time-out"><?= $has_out ? $row['Out_time'] : '—' ?></td>
                    <td><?= $has_out ? $row['Out_Date'] : '—' ?></td>
                    <td>
                        <?php if ($has_out): ?>
                            <span class="status-out">Departed</span>
                        <?php else: ?>
                            <span class="status-in">Inside</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:#475569;">
                        No detections yet. Run the detection script to populate data.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<footer>
    Bus Monitoring System &mdash; YOLOv8 + Tesseract OCR &mdash; Built with Python &amp; PHP
</footer>

</body>
</html>
