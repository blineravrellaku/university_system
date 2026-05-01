<?php
session_start();

/* Kontroll login */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* Kontroll rol – vetëm ADMIN (role_id = 3) */
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

/* Lidhje DB për të marrë të dhëna */
include "lidhja.php";

/* Numri i studentëve */
$student_count = mysqli_fetch_assoc(mysqli_query($lidhje, "SELECT COUNT(*) as num FROM users WHERE role_id=1"))['num'];

/* Numri i instruktorëve */
$instruktor_count = mysqli_fetch_assoc(mysqli_query($lidhje, "SELECT COUNT(*) as num FROM users WHERE role_id=2"))['num'];

/* Numri i kurseve */
$kurs_count = mysqli_fetch_assoc(mysqli_query($lidhje, "SELECT COUNT(*) as num FROM courses"))['num'];

/* Njoftimet e fundit (5 të fundit) */
$njoftime_result = mysqli_query($lidhje, "
    SELECT n.id, n.mesazhi, n.created_at, u.emri, u.mbiemri
    FROM notifications n
    LEFT JOIN users u ON n.user_id=u.id
    ORDER BY n.created_at DESC
    LIMIT 5
");

/* Orari i kurseve (10 të fundit) */
$orari_result = mysqli_query($lidhje, "
    SELECT c.titulli, c.drejtimi, c.ora, c.salle, u.emri, u.mbiemri
    FROM courses c
    LEFT JOIN users u ON u.id=c.instructor_id
    ORDER BY c.drejtimi, c.ora
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | CODE Academy</title>
    <link rel="stylesheet" href="admin.css"> <!-- Sidebar CSS -->
    <link rel="stylesheet" href="admin2.css"> <!-- Pjesa qendrore CSS -->
</head>
<body>

<div class="admin-wrapper">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="admindashboard.php">📊 Dashboard</a></li>
            <li><a href="admin_students.php">🎓 Studentët</a></li>
            <li><a href="admin_instruktoret.php">👨‍🏫 Instruktorët</a></li>
            <li><a href="kurset.php">📚 Kurset</a></li>
            <li><a href="orari.php">🕒 Orari</a></li>
            <li><a href="njoftime.php">🔔 Njoftime</a></li>
             <li><a href="vleresimi.php">📝 Detyrat & Notat</a></li>
            <li><a href="manage_termspolicies.php">📜 Terms & Policies</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>
    
<main class="main-content">
<main class="main-content">

    <h1>Mirësevini Admin 👋</h1>

    <!-- BUTONAT STATISTIKË -->
    <div class="dashboard-cards">
        <a href="admin_students.php" class="dash-card">
            <h3>🎓 Studentë</h3>
            <p><?= $student_count ?></p>
        </a>

        <a href="admin_instruktoret.php" class="dash-card">
            <h3>👨‍🏫 Instruktorë</h3>
            <p><?= $instruktor_count ?></p>
        </a>

        <a href="kurset.php" class="dash-card">
            <h3>📝 Kurset</h3>
            <p><?= $kurs_count ?></p>
        </a>
    </div>


        <!-- ORARI I KURSEVE -->
        <!-- ORARI I KURSEVE -->
<div class="orari-kurseve latest-section">
    <h2>🕒 Orari i Kurseve</h2>
    <table class="latest-table">
        <thead>
            <tr>
                <th>Ditet e Javes</th>
                <th>Drejtimi</th>
                <th>Lënda</th>
                <th>Orë</th>
                <th>Salla</th>
                <th>Instruktori</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $days = ["Hënë, Hënë", "E Martë, E Martë", "E Mërkurë, E Mërkurë", "E Enjte, E Enjte"];
            $i = 0;
            while($k=mysqli_fetch_assoc($orari_result)): 
            ?>
                <tr>
                    <td><?= $days[$i % count($days)] ?></td> <!-- Kolona e ditëve ciklike -->
                    <td><?= htmlspecialchars($k['drejtimi']) ?></td>
                    <td><?= htmlspecialchars($k['titulli']) ?></td>
                    <td><?= htmlspecialchars($k['ora']) ?></td>
                    <td><?= htmlspecialchars($k['salle']) ?></td>
                    <td><?= htmlspecialchars($k['emri'].' '.$k['mbiemri']) ?></td>
                </tr>
            <?php 
            $i++;
            endwhile; 
            ?>
        </tbody>
    </table>
</div>


        <!-- NJOFTIMET E FUNDIT -->
        <div class="njoftime-fundit latest-section">
            <h2>🔔 Njoftimet e Fundit</h2>
            <table class="latest-table">
                <thead>
                    <tr>
                        <th>Marrësi</th>
                        <th>Mesazhi</th>
                        <th>Krijuar më</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($n=mysqli_fetch_assoc($njoftime_result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($n['emri'].' '.$n['mbiemri']) ?></td>
                            <td><?= htmlspecialchars($n['mesazhi']) ?></td>
                            <td><?= htmlspecialchars($n['created_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </main>

</div>

</body>
</html>
