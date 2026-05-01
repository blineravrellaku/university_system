<?php
session_start();
include "lidhja.php";

// Vetëm student
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Kontrollo lidhjen me databazën
if (!$lidhje) {
    die("Gabim lidhjeje me databazën: " . mysqli_connect_error());
}

// Merr të dhënat e studentit
$student_id = $_SESSION['user_id'];
$result = mysqli_query($lidhje, "SELECT drejtimi, emri, mbiemri FROM users WHERE id=$student_id");
$student = mysqli_fetch_assoc($result);
$drejtimi_student = $student['drejtimi'];

// Merr kursat për drejtimin e studentit
$sql = "
SELECT c.id, c.titulli, c.pershkrimi, c.ora, c.salle, 
       u.emri AS instr_emri, u.mbiemri AS instr_mbiemri,
       cs.nota, cs.detyra
FROM courses c
LEFT JOIN users u ON c.instructor_id = u.id
LEFT JOIN courses_students cs ON cs.course_id=c.id AND cs.student_id=$student_id
WHERE c.drejtimi = '$drejtimi_student'
ORDER BY STR_TO_DATE(c.ora, '%H:%i') ASC
";
$kursat = mysqli_query($lidhje, $sql);

// Merr njoftimet e studentit
$njoftime_sql = "SELECT * FROM notifications WHERE user_id = $student_id ORDER BY created_at DESC";
$njoftime_result = mysqli_query($lidhje, $njoftime_sql);

// Merr Term & Policies
$terms_sql = "SELECT * FROM termspolicies ORDER BY created_at DESC";
$terms_result = mysqli_query($lidhje, $terms_sql);

// Merr notat dhe detyrat e studentit
$grades_sql = "
SELECT c.titulli, cs.nota, cs.detyra
FROM courses_students cs
LEFT JOIN courses c ON cs.course_id=c.id
WHERE cs.student_id=$student_id
";
$grades_result = mysqli_query($lidhje, $grades_sql);
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Dashboard Student</title>
<style>
/* Body dhe font */
body{
    margin:0;
    font-family: 'Arial', sans-serif;
    background:#f0f4f8;
    color:#1e293b;
}

/* Wrapper për sidebar dhe main content */
.admin-wrapper{
    display:flex;
    min-height:100vh;
}

/* Sidebar */
.sidebar{
    width:220px;
    background:#1e293b;
    padding:20px;
    display:flex;
    flex-direction:column;
    gap:15px;
}
.sidebar a{
    color:#cbd5e1;
    text-decoration:none;
    padding:12px;
    border-radius:8px;
    display:block;
    transition:0.3s;
}
.sidebar a:hover{
    background:#0f172a;
    color:#fff;
}

/* Main content qendër dhe cards */
.main-content{
    flex:1;
    padding:40px;
    background:#f0f4f8;
    max-width: 1000px;
    margin: 0 auto;
    display:flex;
    flex-direction:column;
    gap:30px;
}

/* Seksionet me card */
.section{
    background: linear-gradient(145deg, #ffffff, #e6f0ff);
    border-radius:15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    padding:25px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.section:hover{
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}

/* Titujt e seksioneve */
.section-title{
    font-size:22px;
    font-weight:600;
    color:#1e293b;
    margin-bottom:20px;
    border-bottom:2px solid #1e293b;
    padding-bottom:5px;
}

/* Tabela e orarit */
table{
    width:100%;
    border-collapse:collapse;
    background:#f9faff;
    border-radius:10px;
    overflow:hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
th, td{
    padding:12px 15px;
    text-align:left;
    border-bottom:1px solid #e0e7ff;
}
th{
    background:#3b82f6;
    color:#fff;
    font-weight:600;
}
tr:hover{background-color:#e0efff;}

/* Kutitë e njoftimeve, notave dhe termave */
.njoftim, .grade-item, .term{
    background:#ffffff;
    padding:15px 20px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
    margin-bottom:15px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.njoftim:hover, .grade-item:hover, .term:hover{
    transform: translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}
.njoftim{border-left:5px solid #ef4444;}
.grade-item{border-left:5px solid #16a34a;}
.term{border-left:5px solid #2563eb;}
.grade-item p{margin:5px 0;}
</style>
</head>
<body>

<div class="admin-wrapper">

    <aside class="sidebar">
        <a href="studentdashboard.php">🏠 Dashboard</a>
        <a href="#grades">📝 Nota & Detyrat</a>
        <a href="#njoftime">🔔 Njoftimet</a>
        <a href="#terms">📄 Terms & Policies</a>
        <a href="logout.php">🚪 Dil</a>
    </aside>

    <div class="main-content">
        <h1> <?= htmlspecialchars($student['emri'].' '.$student['mbiemri']) ?> </h1>
        <h2>Orari i Drejtimit: <?= htmlspecialchars($drejtimi_student) ?></h2>

        <!-- Orari -->
        <div class="orari section">
            <div class="section-title">📅 Orari i Drejtimit</div>
            <table>
                <thead>
                    <tr>
                        <th>Lënda</th>
                        <th>Pershkrimi</th>
                        <th>Ora</th>
                        <th>Salla</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($kursat) > 0): ?>
                    <?php while($k=mysqli_fetch_assoc($kursat)): ?>
                    <tr>
                        <td><?= htmlspecialchars($k['titulli']) ?></td>
                        <td><?= htmlspecialchars($k['pershkrimi']) ?></td>
                        <td><?= htmlspecialchars($k['ora']) ?></td>
                        <td><?= htmlspecialchars($k['salle']) ?></td>
                        <td><?= htmlspecialchars($k['nota'] ?? '-') ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">Nuk ka kurse të disponueshme.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Nota & Detyrat -->
        <div class="grades section" id="grades">
            <div class="section-title">📝 Nota & Detyrat</div>
            <?php if(mysqli_num_rows($grades_result) > 0): ?>
                <?php while($g=mysqli_fetch_assoc($grades_result)): ?>
                    <div class="grade-item">
                        <p><strong>Kursi:</strong> <?= htmlspecialchars($g['titulli']) ?></p>
                        <p><strong>Detyra:</strong> <?= htmlspecialchars($g['detyra'] ?? '-') ?></p>
                        <p><strong>Nota:</strong> <?= htmlspecialchars($g['nota'] ?? '-') ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align:center;">Nuk ka nota apo detyra.</div>
            <?php endif; ?>
        </div>

        <!-- Njoftimet -->
        <div class="njoftime section" id="njoftime">
            <div class="section-title">🔔 Njoftimet e tua</div>
            <?php if(mysqli_num_rows($njoftime_result) > 0): ?>
                <?php while($n=mysqli_fetch_assoc($njoftime_result)): ?>
                    <div class="njoftim">
                        <strong><?= htmlspecialchars($n['mesazhi']) ?></strong><br>
                        <span>⏰ <?= htmlspecialchars($n['created_at']) ?></span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align:center; font-weight:bold;">Nuk keni njoftime.</div>
            <?php endif; ?>
        </div>

        <!-- Terms & Policies -->
        <div class="terms section" id="terms">
            <div class="section-title">📄 Terms & Policies</div>
            <?php if(mysqli_num_rows($terms_result) > 0): ?>
                <?php while($t=mysqli_fetch_assoc($terms_result)): ?>
                    <div class="term">
                        <h3><?= htmlspecialchars($t['titulli']) ?></h3>
                        <p><?= htmlspecialchars($t['pershkrimi']) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align:center; font-weight:bold;">Nuk ka Terms & Policies.</div>
            <?php endif; ?>
        </div>

    </div>

</div>

</body>
</html>
