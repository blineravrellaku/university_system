<?php
session_start();
include "lidhja.php"; // $lidhje

// Vetëm admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

// Merr listën e studentëve
$students_result = mysqli_query($lidhje, "SELECT id, emri, mbiemri FROM users WHERE role_id = 1 ORDER BY emri ASC");

// Përpunimi i formës
$success = '';
$error = '';

if (isset($_POST['submit'])) {
    $student_id = intval($_POST['student_id']);
    $mesazhi = mysqli_real_escape_string($lidhje, $_POST['mesazhi']);

    if (!empty($student_id) && !empty($mesazhi)) {
        $sql_insert = "INSERT INTO notifications (user_id, mesazhi) VALUES ($student_id, '$mesazhi')";
        if (mysqli_query($lidhje, $sql_insert)) {
            $success = "Njoftimi u dërgua me sukses!";
        } else {
            $error = "Gabim: " . mysqli_error($lidhje);
        }
    } else {
        $error = "Ju lutem zgjidhni studentin dhe shkruani mesazhin.";
    }
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Shto Njoftime</title>
<style>
body { font-family: Arial, sans-serif; background:#f0f4f8; padding:30px; color:#1e293b; }
h1 { text-align:center; color:#0f172a; margin-bottom:30px; }
form { max-width:600px; margin:0 auto; background:#fff; padding:25px 30px; box-shadow:0 8px 20px rgba(0,0,0,0.1); border-radius:10px; }
label { display:block; margin-bottom:8px; font-weight:600; }
select, textarea { width:100%; padding:10px; margin-bottom:20px; border:1px solid #cbd5e1; border-radius:6px; font-size:16px; }
button { background:#2563eb; color:white; padding:12px 25px; border:none; border-radius:8px; font-size:16px; cursor:pointer; transition:0.3s; }
button:hover { background:#1e40af; }
.success { color:green; margin-bottom:20px; text-align:center; }
.error { color:red; margin-bottom:20px; text-align:center; }
</style>
</head>
<body>

<h1>📢 Shto Njoftime për Studentët</h1>

<?php if($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
<?php if($error): ?><p class="error"><?= $error ?></p><?php endif; ?>

<form method="POST" action="">
    <label>Zgjidh Studentin</label>
    <select name="student_id" required>
        <option value="">-- Zgjidh --</option>
        <?php while($s=mysqli_fetch_assoc($students_result)): ?>
            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['emri'].' '.$s['mbiemri']) ?></option>
        <?php endwhile; ?>
    </select>

    <label>Mesazhi</label>
    <textarea name="mesazhi" rows="5" placeholder="Shkruani njoftimin..." required></textarea>

    <button type="submit" name="submit">Dërgo Njoftimin</button>
</form>

</body>
</html>
