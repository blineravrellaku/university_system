<?php
session_start();
include "lidhja.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die("Nuk keni qasje.");
}

$id = $_GET['id'] ?? null;
if (!$id) die("ID mungon.");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    mysqli_query($lidhje, "DELETE FROM users WHERE id=$id");
    header("Location: admin_students.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Fshi Studentin</title>

<style>
body {
    font-family: system-ui, sans-serif;
    background: #fee2e2;
}
.box {
    max-width: 400px;
    margin: 100px auto;
    background: white;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
button {
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-size: 15px;
}
.delete {
    background: #ef4444;
    color: white;
}
.cancel {
    background: #94a3b8;
    color: white;
    margin-left: 10px;
}
</style>
</head>

<body>

<div class="box">
<h2>⚠️ Paralajmërim</h2>
<p>A je e sigurt që dëshiron ta fshish këtë student?</p>

<form method="POST">
    <button class="delete">Po, Fshije</button>
    <a href="admin_students.php">
        <button type="button" class="cancel">Anulo</button>
    </a>
</form>
</div>

</body>
</html>
