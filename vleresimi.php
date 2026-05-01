<?php
session_start();
include "lidhja.php";

// Vetëm admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

// Merr të gjithë studentët
$students = mysqli_query($lidhje, "SELECT id, emri, mbiemri, drejtimi FROM users WHERE role_id=1");

// Nëse u dërgua forma për të vendosur nota/detyra
if(isset($_POST['save_grade'])){
    $student_id = intval($_POST['student_id']);
    $course_id = intval($_POST['course_id']);
    $nota = $_POST['nota'] ?? '';
    $detyra = $_POST['detyra'] ?? '';

    // Kontrollo nëse ekziston rreshti në courses_students
    $stmt_check = $lidhje->prepare("SELECT * FROM courses_students WHERE student_id=? AND course_id=?");
    $stmt_check->bind_param("ii", $student_id, $course_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if($result_check->num_rows > 0){
        // UPDATE
        $stmt_update = $lidhje->prepare("UPDATE courses_students SET nota=?, detyra=? WHERE student_id=? AND course_id=?");
        $stmt_update->bind_param("ssii", $nota, $detyra, $student_id, $course_id);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // INSERT
        $stmt_insert = $lidhje->prepare("INSERT INTO courses_students (student_id, course_id, nota, detyra) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("iiss", $student_id, $course_id, $nota, $detyra);
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    // Krijo njoftim për studentin me prepared statement
    $mesazh = "U vendos nota '$nota' dhe detyra '$detyra' për kursin tuaj.";
    $stmt_notify = $lidhje->prepare("INSERT INTO notifications (user_id, mesazhi, created_at) VALUES (?, ?, NOW())");
    $stmt_notify->bind_param("is", $student_id, $mesazh);
    $stmt_notify->execute();
    $stmt_notify->close();

    $success = "Vlerësimi u ruajt me sukses!";
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Vlerësimi i Studentëve | Admin</title>
<style>
body {font-family:Arial,sans-serif;background:#f1f5f9;margin:0;padding:20px;color:#1e293b;}
.admin-wrapper {display:flex; min-height:100vh;}
.sidebar {width:220px; background:#1e293b; padding:20px; display:flex; flex-direction:column; gap:15px;}
.sidebar a {color:#cbd5e1; text-decoration:none; padding:12px; border-radius:8px; display:block; transition:0.3s;}
.sidebar a:hover {background:#0f172a; color:#fff;}
.main-content {flex:1; padding:40px;}
.main-content h1 {color:#0f172a; margin-bottom:15px;}
table {width:100%; border-collapse:collapse; background:#fff; box-shadow:0 8px 20px rgba(0,0,0,0.05); margin-top:20px;}
th, td {padding:12px; border-bottom:1px solid #e5e7eb; text-align:left;}
th {background:#1e293b; color:#cbd5e1; font-weight:600;}
tr:hover {background:#f1f5f9;}
input[type=text]{width:80px; padding:5px; border:1px solid #ccc; border-radius:4px;}
button{padding:5px 10px; background:#2563eb; color:#fff; border:none; border-radius:4px; cursor:pointer;}
button:hover{background:#0f172a;}
.success{color:green; margin-bottom:15px;}
</style>
</head>
<body>

<div class="admin-wrapper">

    <aside class="sidebar">
        <a href="admindashboard.php">📊 Dashboard</a>
        <a href="admin_students.php">🎓 Studentët</a>
        <a href="admin_instruktoret.php">👨‍🏫 Instruktorët</a>
        <a href="kurset.php">📚 Kurset</a>
        <a href="orari.php">🕒 Orari</a>
        <a href="njoftime.php">🔔 Njoftime</a>
        <a href="vleresimiadmin.php">📝 Detyrat & Notat</a>
        <a href="manage_termspolicies.php">📜 Terms & Policies</a>
    </aside>

    <div class="main-content">
        <h1>Vlerësimi i Studentëve</h1>
        <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>

        <table>
        <thead>
            <tr>
                <th>Studenti</th>
                <th>Drejtimi</th>
                <th>Kursi</th>
                <th>Nota</th>
                <th>Detyra</th>
                <th>Vepro</th>
            </tr>
        </thead>
        <tbody>
        <?php while($s = mysqli_fetch_assoc($students)):
            // Merr kurset e studentit për drejtimin e tij
            $courses_stmt = $lidhje->prepare("SELECT c.id, c.titulli, cs.nota, cs.detyra 
                FROM courses c 
                LEFT JOIN courses_students cs ON cs.course_id=c.id AND cs.student_id=? 
                WHERE c.drejtimi=?");
            $courses_stmt->bind_param("is", $s['id'], $s['drejtimi']);
            $courses_stmt->execute();
            $courses_result = $courses_stmt->get_result();
            while($c = $courses_result->fetch_assoc()):
        ?>
            <tr>
                <td><?= htmlspecialchars($s['emri'].' '.$s['mbiemri']) ?></td>
                <td><?= htmlspecialchars($s['drejtimi']) ?></td>
                <td><?= htmlspecialchars($c['titulli']) ?></td>
                <form method="POST">
                <td><input type="text" name="nota" value="<?= htmlspecialchars($c['nota'] ?? '') ?>"></td>
                <td><input type="text" name="detyra" value="<?= htmlspecialchars($c['detyra'] ?? '') ?>"></td>
                <td>
                    <input type="hidden" name="student_id" value="<?= $s['id'] ?>">
                    <input type="hidden" name="course_id" value="<?= $c['id'] ?>">
                    <button type="submit" name="save_grade">Ruaj</button>
                </td>
                </form>
            </tr>
        <?php 
            endwhile;
            $courses_stmt->close();
        endwhile; ?>
        </tbody>
        </table>

    </div>

</div>

</body>
</html>
