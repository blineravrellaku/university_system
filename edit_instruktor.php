<?php
session_start();
include "lidhja.php";

// Kontroll login dhe rol (vetëm admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die("Nuk keni qasje.");
}

$id = $_GET['id'] ?? null;
if (!$id) die("ID mungon.");

// Merr të dhënat ekzistuese të instruktorit
$instruktori = mysqli_fetch_assoc(mysqli_query(
    $lidhje,
    "SELECT * FROM users WHERE id=$id AND role_id=2"
));

if (!$instruktori) die("Instruktori nuk u gjet.");

// Për opsionet e klasave
$classes = mysqli_query($lidhje, "SELECT * FROM classes");

// Kur form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emri = mysqli_real_escape_string($lidhje, $_POST['emri']);
    $mbiemri = mysqli_real_escape_string($lidhje, $_POST['mbiemri']);
    $username = mysqli_real_escape_string($lidhje, $_POST['username']);
    $email = mysqli_real_escape_string($lidhje, $_POST['email']);
    $drejtimi = mysqli_real_escape_string($lidhje, $_POST['drejtimi']);
    $orari = mysqli_real_escape_string($lidhje, $_POST['orari']);
    $photo = mysqli_real_escape_string($lidhje, $_POST['photo']);
    $pershkrim = mysqli_real_escape_string($lidhje, $_POST['pershkrim']);
    $datelindja = $_POST['datelindja'];
    $vendlindja = mysqli_real_escape_string($lidhje, $_POST['vendlindja']);

    mysqli_query($lidhje, "
        UPDATE users SET
            emri='$emri',
            mbiemri='$mbiemri',
            username='$username',
            email='$email',
            drejtimi='$drejtimi',
            orari='$orari',
            photo='$photo',
            pershkrim='$pershkrim',
            datelindja='$datelindja',
            vendlindja='$vendlindja'
        WHERE id=$id AND role_id=2
    ");

    header("Location: admin_instruktoret.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Edito Instruktor</title>
<style>
body {
    font-family: system-ui, sans-serif;
    background: #f1f5f9;
}
.container {
    max-width: 600px;
    margin: 60px auto;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}
h2 {
    text-align: center;
    margin-bottom: 25px;
}
label {
    font-weight: 600;
}
input, select, textarea {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    margin-bottom: 18px;
    border-radius: 8px;
    border: 1px solid #cbd5f5;
}
textarea {
    min-height: 100px;
}
button {
    width: 100%;
    padding: 12px;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
}
button:hover {
    background: #1e40af;
}
.back {
    display: block;
    text-align: center;
    margin-top: 15px;
    text-decoration: none;
    color: #475569;
}
</style>
</head>

<body>

<div class="container">
<h2>Edito Instruktor</h2>

<form method="POST">
    <label>Emri</label>
    <input type="text" name="emri" value="<?= htmlspecialchars($instruktori['emri']) ?>" required>

    <label>Mbiemri</label>
    <input type="text" name="mbiemri" value="<?= htmlspecialchars($instruktori['mbiemri']) ?>" required>

    <label>Username</label>
    <input type="text" name="username" value="<?= htmlspecialchars($instruktori['username']) ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($instruktori['email']) ?>">

    <label>Drejtimi</label>
    <input type="text" name="drejtimi" value="<?= htmlspecialchars($instruktori['drejtimi']) ?>">

    <label>Orari</label>
    <input type="text" name="orari" value="<?= htmlspecialchars($instruktori['orari']) ?>">

    <label>Foto (URL ose path)</label>
    <input type="text" name="photo" value="<?= htmlspecialchars($instruktori['photo']) ?>">

    <label>Biografia</label>
    <textarea name="pershkrim"><?= htmlspecialchars($instruktori['pershkrim']) ?></textarea>

    <label>Datëlindja</label>
    <input type="date" name="datelindja" value="<?= $instruktori['datelindja'] ?>">

    <label>Vendlindja</label>
    <input type="text" name="vendlindja" value="<?= htmlspecialchars($instruktori['vendlindja']) ?>">

    <button type="submit">💾 Ruaj Ndryshimet</button>
</form>

<a href="admin_instruktoret.php" class="back">← Kthehu te lista e instruktorëve</a>
</div>

</body>
</html>
