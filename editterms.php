<?php
session_start();
include "lidhja.php";

// Vetëm admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

// Merr ID nga GET
if (!isset($_GET['id'])) {
    header("Location: manage_termspolicies.php");
    exit();
}

$id = intval($_GET['id']);

// Merr të dhënat e term-it
$result = mysqli_query($lidhje, "SELECT * FROM termspolicies WHERE id=$id");
if (!$result || mysqli_num_rows($result) == 0) {
    die("Term-i nuk u gjet!");
}
$term = mysqli_fetch_assoc($result);

// Përpunimi i formës
$success = '';
$error = '';
if (isset($_POST['submit'])) {
    $titulli = mysqli_real_escape_string($lidhje, $_POST['titulli']);
    $pershkrimi = mysqli_real_escape_string($lidhje, $_POST['pershkrimi']);

    if (!empty($titulli) && !empty($pershkrimi)) {
        $update_sql = "UPDATE termspolicies SET titulli='$titulli', pershkrimi='$pershkrimi' WHERE id=$id";
        if (mysqli_query($lidhje, $update_sql)) {
            $success = "Term-i u përditësua me sukses!";
            $term['titulli'] = $titulli;
            $term['pershkrimi'] = $pershkrimi;
        } else {
            $error = "Gabim: " . mysqli_error($lidhje);
        }
    } else {
        $error = "Ju lutem plotësoni të gjitha fushat.";
    }
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Edit Term</title>
<style>
body { font-family: Arial; background:#f0f4f8; padding:30px; color:#1e293b; }
form { max-width:600px; margin:0 auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 8px 20px rgba(0,0,0,0.1); }
input, textarea { width:100%; padding:10px; margin-bottom:15px; border:1px solid #cbd5e1; border-radius:6px; font-size:16px; }
button { background:#2563eb; color:white; padding:12px 20px; border:none; border-radius:8px; font-size:16px; cursor:pointer; }
button:hover { background:#1e40af; }
.success { color:green; text-align:center; margin-bottom:15px; }
.error { color:red; text-align:center; margin-bottom:15px; }
</style>
</head>
<body>

<h1 style="text-align:center;">✏️ Edit Term</h1>

<?php if($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
<?php if($error): ?><p class="error"><?= $error ?></p><?php endif; ?>

<form method="POST" action="">
    <label>Titulli</label>
    <input type="text" name="titulli" value="<?= htmlspecialchars($term['titulli']) ?>" required>

    <label>Përshkrimi</label>
    <textarea name="pershkrimi" rows="5" required><?= htmlspecialchars($term['pershkrimi']) ?></textarea>

    <button type="submit" name="submit">Ruaj Ndryshimet</button>
</form>

<p style="text-align:center; margin-top:20px;">
    <a href="manage_termspolicies.php" style="color:#2563eb;">⬅ Kthehu te lista e Term-eve</a>
</p>

</body>
</html>
