<?php
session_start();
include "lidhja.php";

/* Vetëm admin */
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die("Access denied");
}

/* Merr instruktorët për dropdown */
$instruktoret = mysqli_query($lidhje, "SELECT id, emri, mbiemri, drejtimi FROM users WHERE role_id=2 ORDER BY emri");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulli       = mysqli_real_escape_string($lidhje, $_POST['titulli']);
    $pershkrimi    = mysqli_real_escape_string($lidhje, $_POST['pershkrimi']);
    $drejtimi      = mysqli_real_escape_string($lidhje, $_POST['drejtimi']);
    $ora           = mysqli_real_escape_string($lidhje, $_POST['ora']);
    $salle         = mysqli_real_escape_string($lidhje, $_POST['salle']);
    $instructor_id = (int)$_POST['instructor_id'];

    mysqli_query($lidhje, "
        INSERT INTO courses (titulli, pershkrimi, drejtimi, ora, salle, instructor_id)
        VALUES ('$titulli', '$pershkrimi', '$drejtimi', '$ora', '$salle', $instructor_id)
    ");

    header("Location: kurset.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Regjistro Kurs</title>
<style>
body{font-family:Arial;background:#f1f5f9;padding:40px;}
.box{background:white;max-width:600px;margin:auto;padding:30px;border-radius:18px;box-shadow:0 10px 25px rgba(0,0,0,0.1);}
input,textarea,select,button{width:100%;padding:12px;margin-bottom:14px;border-radius:8px;border:1px solid #ccc;}
button{background:#2563eb;color:white;border:none;padding:14px;border-radius:10px;font-weight:bold;cursor:pointer;}
button:hover{background:#1e40af;}
</style>
</head>
<body>

<div class="box">
<h2>Regjistro Kurs</h2>

<form method="post">
    <input name="titulli" placeholder="Titulli i kursit" required>
    <textarea name="pershkrimi" placeholder="Përshkrimi i kursit"></textarea>

    <select name="drejtimi" required>
        <option value="">Zgjidh Drejtimin</option>
        <option value="Web Development">Web Development</option>
        <option value="Software Engineering">Software Engineering</option>
        <option value="Networking">Networking</option>
        <option value="Artificial Intelligence">Artificial Intelligence</option>
    </select>

    <input name="ora" placeholder="Ora e kursit" required>
    <input name="salle" placeholder="Salla" required>

    <select name="instructor_id" required>
        <option value="">Zgjidh Instruktorin</option>
        <?php while($i = mysqli_fetch_assoc($instruktoret)): ?>
            <option value="<?= $i['id'] ?>">
                <?= htmlspecialchars($i['emri'] . ' ' . $i['mbiemri'] . ' (' . $i['drejtimi'] . ')') ?>
            </option>
        <?php endwhile; ?>
    </select>

    <button>Regjistro Kurs</button>
</form>
</div>

</body>
</html>
