<?php
session_start();
include "lidhja.php";

/* Vetëm admin */
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die("Access denied");
}

/* Regjistrimi */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $emri       = $_POST['emri'];
    $mbiemri    = $_POST['mbiemri'];
    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $drejtimi   = $_POST['drejtimi'];
    $orari      = $_POST['orari'];
    $datelindja = $_POST['datelindja'];
    $vendlindja = $_POST['vendlindja'];
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    /* FOTO */
    $photoPath = 'images/default.jpg'; // default
    if (!empty($_FILES['photo']['name'])) {
        $folder = "images/students/"; 
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photoName = uniqid("student_") . "." . $ext;
        $photoPath = $folder . $photoName;

        move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
    }

    /* Shtimi i studentit në DB */
    $stmt = mysqli_prepare($lidhje, "
        INSERT INTO users 
        (role_id, emri, mbiemri, username, email, drejtimi, orari, password, photo, datelindja, vendlindja)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $role_id = 1;
    mysqli_stmt_bind_param($stmt, "issssssssss", $role_id, $emri, $mbiemri, $username, $email, $drejtimi, $orari, $password, $photoPath, $datelindja, $vendlindja);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: admin_students.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Regjistro Student</title>
<style>
/* Dizajni i vjetër – mos prek */
body{font-family:Arial;background:#f1f5f9;padding:40px;}
.box{background:white;max-width:900px;margin:auto;padding:30px;border-radius:18px;box-shadow:0 10px 25px rgba(0,0,0,0.1);}
h2{text-align:center;margin-bottom:30px;}
.register-layout{display:flex;gap:40px;}
.form-left{flex:1;}
input,select,button{width:100%;padding:12px;margin-bottom:14px;border-radius:8px;border:1px solid #ccc;}
.form-right{width:260px;text-align:center;}
.form-right img{width:220px;height:220px;object-fit:cover;border-radius:18px;background:#e5e7eb;margin-bottom:12px;}
.form-right button{background:#2563eb;color:white;border:none;padding:12px;border-radius:10px;cursor:pointer;}
.form-right button:hover{background:#1e40af;}
.submit-btn{background:#2563eb;color:white;border:none;padding:14px;border-radius:10px;font-weight:bold;cursor:pointer;}
.submit-btn:hover{background:#1e40af;}
.back{display:block;text-align:center;margin-top:15px;}
</style>
</head>
<body>

<div class="box">
<h2>Regjistro Student</h2>

<form method="post" enctype="multipart/form-data">
<div class="register-layout">

    <!-- MAJTAS -->
    <div class="form-left">
        <input type="text" name="emri" placeholder="Emri" required>
        <input type="text" name="mbiemri" placeholder="Mbiemri" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email">
        <select name="drejtimi">
            <option>Web Development</option>
            <option>Software Engineering</option>
            <option>Networking</option>
            <option>Artificial Intelligence</option>
        </select>
        <input type="text" name="orari" placeholder="Orari (p.sh 14:00–18:00)">
        <input type="date" name="datelindja">
        <input type="text" name="vendlindja" placeholder="Vendlindja">
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="submit-btn">Regjistro Student</button>
        <a class="back" href="admin_students.php">⬅ Kthehu te studentët</a>
    </div>

    <!-- DJATHTAS -->
    <div class="form-right">
        <img id="preview" src="images/default.jpg">
        <button type="button" onclick="document.getElementById('photo').click()">Ngarko foton e studentit</button>
        <input type="file" name="photo" id="photo" accept="image/*" hidden onchange="previewImage(event)">
    </div>

</div>
</form>
</div>

<script>
function previewImage(e){
    const reader = new FileReader();
    reader.onload = () => {
        document.getElementById('preview').src = reader.result;
    };
    reader.readAsDataURL(e.target.files[0]);
}
</script>

</body>
</html>
