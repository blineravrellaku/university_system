<?php
session_start();
include "lidhja.php";

/* Vetëm admin */
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die("Access denied");
}

/* Merr klasat për select */
$classes = [];
$res = mysqli_query($lidhje, "SELECT class_id, drejtimi, ora, salle FROM classes ORDER BY drejtimi, ora");
while($row = mysqli_fetch_assoc($res)){
    $classes[$row['drejtimi']][] = $row;
}

/* Regjistrimi */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emri       = $_POST['emri'];
    $mbiemri    = $_POST['mbiemri'];
    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $pershkrim  = $_POST['pershkrim'];
    $drejtimi   = $_POST['drejtimi'];
    $class_id   = $_POST['lenda'];
    $datelindja = $_POST['datelindja'];
    $vendlindja = $_POST['vendlindja'];
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    /* FOTO */
    $photoPath = NULL;
    if (!empty($_FILES['photo']['name'])) {
        $folder = "images/instructors/";
        if (!is_dir($folder)) mkdir($folder, 0777, true);
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photoName = uniqid("instruktor_") . "." . $ext;
        $photoPath = $folder . $photoName;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
    }

    /* Merr emrin e lendes nga class_id */
    $lenda_name = '';
    if($class_id){
        $res2 = mysqli_query($lidhje, "SELECT drejtimi FROM classes WHERE class_id='$class_id'");
        $row2 = mysqli_fetch_assoc($res2);
        if($row2) $lenda_name = $row2['drejtimi'];
    }

    /* INSERT */
    mysqli_query($lidhje, "
        INSERT INTO users
        (role_id, emri, mbiemri, username, email, password, photo, pershkrim, drejtimi, lenda, datelindja, vendlindja, class_id)
        VALUES
        (2,'$emri','$mbiemri','$username','$email','$password','$photoPath','$pershkrim','$drejtimi','$lenda_name','$datelindja','$vendlindja','$class_id')
    ");

    header("Location: admin_instruktoret.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Regjistro Instruktor</title>
<style>
body{font-family:Arial;background:#f1f5f9;padding:40px;}
.box{background:white;max-width:900px;margin:auto;padding:30px;border-radius:18px;box-shadow:0 10px 25px rgba(0,0,0,0.1);}
.register-layout{display:flex;gap:40px;}
.form-left{flex:1;}
input,textarea,select,button{width:100%;padding:12px;margin-bottom:14px;border-radius:8px;border:1px solid #ccc;}
.form-right{width:260px;text-align:center;}
.form-right img{width:220px;height:220px;object-fit:cover;border-radius:18px;background:#e5e7eb;margin-bottom:12px;cursor:pointer;}
.submit-btn{background:#2563eb;color:white;border:none;padding:14px;border-radius:10px;font-weight:bold;cursor:pointer;}
.submit-btn:hover{background:#1e40af;}
</style>
</head>
<body>

<div class="box">
<h2>Regjistro Instruktor</h2>

<form method="post" enctype="multipart/form-data">
<div class="register-layout">

<div class="form-left">
    <input name="emri" placeholder="Emri" required>
    <input name="mbiemri" placeholder="Mbiemri" required>
    <input name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email">

    <!-- DREJTIMI -->
    <select name="drejtimi" id="drejtimi" required>
        <option value="">Zgjidh Drejtimin</option>
        <?php foreach(array_keys($classes) as $d): ?>
            <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
        <?php endforeach; ?>
    </select>

    <!-- LENDA -->
    <select name="lenda" id="lenda" required>
        <option value="">Zgjidh Lëndën</option>
    </select>

    <!-- Biografia -->
    <textarea name="pershkrim" placeholder="Biografia / Info personale"></textarea>

    <input type="date" name="datelindja">
    <input name="vendlindja" placeholder="Vendlindja">
    <input type="password" name="password" placeholder="Password" required>

    <button class="submit-btn">Regjistro Instruktor</button>
</div>

<div class="form-right">
    <img id="preview" src="images/default.jpg"
         onclick="document.getElementById('photo').click()">
    <input type="file" name="photo" id="photo" hidden accept="image/*"
           onchange="previewImage(event)">
</div>

</div>
</form>
</div>

<script>
const classes = <?= json_encode($classes) ?>;

document.getElementById('drejtimi').addEventListener('change', function() {
    const drejtimi = this.value;
    const lendaSelect = document.getElementById('lenda');
    lendaSelect.innerHTML = '<option value="">Zgjidh Lëndën</option>';
    if (classes[drejtimi]) {
        classes[drejtimi].forEach(c => {
            const option = document.createElement('option');
            option.value = c.class_id;
            option.textContent = c.drejtimi + " (" + c.ora + ", " + c.salle + ")";
            lendaSelect.appendChild(option);
        });
    }
});

function previewImage(e){
    const r = new FileReader();
    r.onload = ()=> document.getElementById('preview').src = r.result;
    r.readAsDataURL(e.target.files[0]);
}
</script>

</body>
</html>
