<?php
session_start();
include "lidhja.php";

// Vetëm instruktorët (role_id = 2)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

// Merr të dhënat e instruktorit
$instructor_id = $_SESSION['user_id'];
$result = mysqli_query($lidhje, "SELECT emri, mbiemri, lenda FROM users WHERE id=$instructor_id");
$instructor = mysqli_fetch_assoc($result);
$instructor_lenda = $instructor['lenda'];

// Merr kursat që ligjeron instruktor
$courses_sql = "SELECT * FROM courses WHERE instructor_id=$instructor_id";
$courses_result = mysqli_query($lidhje, $courses_sql);

// Merr studentët për çdo kurs që ligjeron instruktor
$students = [];
if(mysqli_num_rows($courses_result) > 0){
    while($course = mysqli_fetch_assoc($courses_result)){
        $course_id = $course['id'];
        $students_sql = "
        SELECT u.id, u.emri, u.mbiemri, u.drejtimi, cs.nota, cs.detyra
        FROM users u
        LEFT JOIN courses_students cs ON cs.student_id=u.id AND cs.course_id=$course_id
        WHERE u.role_id=1
        ";
        $res = mysqli_query($lidhje, $students_sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['kursi'] = $course['titulli'];
            $students[] = $row;
        }
    }
}

// Merr njoftimet që instruktori ka dërguar ose ka marrë
$notifications_sql = "SELECT * FROM notifications WHERE user_id=$instructor_id ORDER BY created_at DESC";
$notifications_result = mysqli_query($lidhje, $notifications_sql);

// Merr Term & Policies
$terms_sql = "SELECT * FROM termspolicies ORDER BY created_at DESC";
$terms_result = mysqli_query($lidhje, $terms_sql);
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Dashboard Instruktor</title>
<style>
body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f0f4f8;
    color:#1e293b;
}
.admin-wrapper{display:flex;min-height:100vh;}
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
.sidebar a:hover{background:#0f172a;color:#fff;}

.main-content{
    flex:1;
    padding:40px;
    background:#f0f4f8;
}

.section-title{
    font-size:20px;
    color:#1e293b;
    padding-bottom:5px;
    border-bottom:2px solid #1e293b;
    margin-top:40px;
    margin-bottom:15px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#ffffff;
    box-shadow:0 8px 20px rgba(0,0,0,0.1);
}
th,td{
    padding:12px;
    text-align:left;
    border-bottom:1px solid #ddd;
}
th{
    background:#2563eb;
    color:#fff;
    font-weight:600;
}
tr:hover{background-color:#f1f5f9;}

.njoftim,.term,.grade-item{
    background:#ffffff;
    padding:15px;
    margin-bottom:10px;
    box-shadow:0 4px 10px rgba(0,0,0,0.05);
    color:#1e293b;
}
.njoftim{border-left:5px solid #ef4444;}
.term{border-left:5px solid #2563eb;}
.grade-item{border-left:5px solid #16a34a;}
.grade-item p{margin:5px 0;}

input[type=text]{width:80px;padding:5px;border:1px solid #ccc;border-radius:4px;}
button{padding:5px 10px;background:#2563eb;color:#fff;border:none;border-radius:4px;cursor:pointer;}
button:hover{background:#0f172a;}
</style>
</head>
<body>

<div class="admin-wrapper">

    <aside class="sidebar">
        <a href="instructordashboard.php">🏠 Dashboard</a>
        <a href="#students">🎓 Studentët</a>
        <a href="#grades">📝 Nota & Detyrat</a>
        <a href="#njoftime">🔔 Njoftimet</a>
        <a href="#terms">📄 Terms & Policies</a>
        <a href="logout.php">🚪 Dil</a>
    </aside>

    <div class="main-content">
        <h1>Mirë se erdhe, <?= htmlspecialchars($instructor['emri'].' '.$instructor['mbiemri']) ?> 🎓</h1>
        <h2>Lënda që ligjeron: <?= htmlspecialchars($instructor_lenda) ?></h2>

        <!-- Studentët -->
        <div class="students" id="students">
            <div class="section-title">🎓 Studentët e lëndës <?= htmlspecialchars($instructor_lenda) ?></div>
            <?php if(count($students) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Studenti</th>
                            <th>Kursi</th>
                            <th>Drejtimi</th>
                            <th>Nota</th>
                            <th>Detyra</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($students as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['emri'].' '.$s['mbiemri']) ?></td>
                            <td><?= htmlspecialchars($s['kursi']) ?></td>
                            <td><?= htmlspecialchars($s['drejtimi'] ?? '-') ?></td>
                            <td>
                                <form method="POST" action="update_grade.php">
                                    <input type="hidden" name="student_id" value="<?= $s['id'] ?>">
                                    <input type="hidden" name="course" value="<?= htmlspecialchars($s['kursi']) ?>">
                                    <input type="text" name="nota" value="<?= htmlspecialchars($s['nota'] ?? '') ?>">
                                    <button type="submit">Ruaj</button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="update_grade.php">
                                    <input type="hidden" name="student_id" value="<?= $s['id'] ?>">
                                    <input type="hidden" name="course" value="<?= htmlspecialchars($s['kursi']) ?>">
                                    <input type="text" name="detyra" value="<?= htmlspecialchars($s['detyra'] ?? '') ?>">
                                    <button type="submit">Ruaj</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align:center;">Nuk ka studentë për këtë kurs.</div>
            <?php endif; ?>
        </div>

        <!-- Nota & Detyrat -->
        <div class="grades" id="grades">
            <div class="section-title">📝 Nota & Detyrat</div>
            <?php foreach($students as $s): ?>
                <div class="grade-item">
                    <p><strong>Studenti:</strong> <?= htmlspecialchars($s['emri'].' '.$s['mbiemri']) ?></p>
                    <p><strong>Kursi:</strong> <?= htmlspecialchars($s['kursi']) ?></p>
                    <p><strong>Detyra:</strong> <?= htmlspecialchars($s['detyra'] ?? '-') ?></p>
                    <p><strong>Nota:</strong> <?= htmlspecialchars($s['nota'] ?? '-') ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Njoftimet -->
        <div class="njoftime" id="njoftime">
            <div class="section-title">🔔 Njoftimet</div>
            <?php if(mysqli_num_rows($notifications_result) > 0): ?>
                <?php while($n=mysqli_fetch_assoc($notifications_result)): ?>
                    <div class="njoftim">
                        <strong><?= htmlspecialchars($n['mesazhi']) ?></strong><br>
                        <span>⏰ <?= htmlspecialchars($n['created_at']) ?></span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align:center;">Nuk ka njoftime.</div>
            <?php endif; ?>
        </div>

        <!-- Terms & Policies -->
        <div class="terms" id="terms">
            <div class="section-title">📄 Terms & Policies</div>
            <?php if(mysqli_num_rows($terms_result) > 0): ?>
                <?php while($t=mysqli_fetch_assoc($terms_result)): ?>
                    <div class="term">
                        <h3><?= htmlspecialchars($t['titulli']) ?></h3>
                        <p><?= htmlspecialchars($t['pershkrimi']) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align:center;">Nuk ka Terms & Policies.</div>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>
