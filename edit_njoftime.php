<?php
session_start();
include "lidhja.php";

// Vetëm admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

if (!$lidhje) {
    die("Gabim lidhjeje me DB");
}

// Merr id e njoftimit
if (!isset($_GET['id'])) {
    die("Njoftimi nuk u gjet.");
}
$id = intval($_GET['id']);

// Merr njoftimin
$result = mysqli_query($lidhje, "SELECT * FROM notifications WHERE id=$id");
if (!$result || mysqli_num_rows($result) == 0) {
    die("Njoftimi nuk ekziston.");
}
$njoftim = mysqli_fetch_assoc($result);

$success = '';
$error = '';

if (isset($_POST['submit'])) {
    $mesazhi = mysqli_real_escape_string($lidhje, $_POST['mesazhi']);
    if (!empty($mesazhi)) {
        $update = "UPDATE notifications SET mesazhi='$mesazhi' WHERE id=$id";
        if (mysqli_query($lidhje, $update)) {
            $success = "Njoftimi u përditësua me sukses!";
            $njoftim['mesazhi'] = $mesazhi;
        } else {
            $error = "Gabim: " . mysqli_error($lidhje);
        }
    } else {
        $error = "Mesazhi nuk mund të jetë bosh.";
    }
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Edit Njoftim</title>
<style>
body { font-family: Arial; background:#f0f4f8; padding:30px; color:#1e293b; }
form { max-width:600px; margin:0 auto; background:#fff; padding:25px; box-shadow:0 8px 20px rgba(0,0,0,0.1); border-radius:10px; }
label { display:block; margin-bottom:8px; font-weight:600; }
textarea { width:100%; padding:10px; margin-bottom:20px; border:1px solid #cbd5e1; border-radius:6px; font-size:16px; }
button { background:#2563eb; color:white; padding:12px 25px; border:none; border-radius:8px; font-size:16px; cursor:pointer; transition:0.3s; }
button:hover { background:#1e40af; }
.success { color:green; margin-bottom:20px; text-align:center; }
.error { color:red; margin-bottom:20px; text-align:center; }
a { display:block; margin-top:15px; color:#2563eb; text-align:center; }
</style>
</head>
<body>

<h1 style="text-align:center;">📢 Edit Njoftim</h1>

<?php if($success) echo "<p class='success'>$success</p>"; ?>
<?php if($error) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
    <label>Mesazhi</label>
    <textarea name="mesazhi" rows="5"><?= htmlspecialchars($njoftim['mesazhi']) ?></textarea>
    <button type="submit" name="submit">Ruaj Ndryshimet</button>
</form>

<a href="njoftime.php">⏎ Kthehu tek njoftimet</a>

</body>
</html>
