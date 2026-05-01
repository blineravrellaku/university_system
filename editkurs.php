<?php
$conn = mysqli_connect("localhost", "root", "", "kursi_db");
if (!$conn) {
    die("Gabim DB");
}

/* 1. merr ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID e kursit mungon ose nuk eshte valide");
}
$kurs_id = (int)$_GET['id'];

/* 2. merr kursin PA JOIN */
$sql = "SELECT * FROM courses WHERE id = $kurs_id";
$res = mysqli_query($conn, $sql);

if (mysqli_num_rows($res) === 0) {
    die("Kursi nuk u gjet.");
}
$kurs = mysqli_fetch_assoc($res);

/* 3. merr instruktorin e kursit */
$instruktor = null;
$iid = (int)$kurs['instructor_id'];
$ri = mysqli_query($conn, "SELECT emri, mbiemri FROM users WHERE id=$iid");
if ($ri && mysqli_num_rows($ri) > 0) {
    $instruktor = mysqli_fetch_assoc($ri);
}

/* 4. merr instruktoret e drejtimit */
$drejtimi = mysqli_real_escape_string($conn, $kurs['drejtimi']);
$instruktoret = mysqli_query(
    $conn,
    "SELECT id, emri, mbiemri 
     FROM users 
     WHERE role_id=2 AND drejtimi='$drejtimi'"
);

/* 5. update */
if (isset($_POST['ruaj'])) {
    $titulli = mysqli_real_escape_string($conn, $_POST['titulli']);
    $pershkrimi = mysqli_real_escape_string($conn, $_POST['pershkrimi']);
    $ora = mysqli_real_escape_string($conn, $_POST['ora']);
    $salle = mysqli_real_escape_string($conn, $_POST['salle']);
    $instructor_id = (int)$_POST['instructor_id'];

    mysqli_query($conn, "
        UPDATE courses SET
            titulli='$titulli',
            pershkrimi='$pershkrimi',
            ora='$ora',
            salle='$salle',
            instructor_id=$instructor_id
        WHERE id=$kurs_id
    ");

    header("Location: kurset.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Edito Kurs</title>
<style>
body{background:#f3f4f6;font-family:Arial}
.box{width:600px;margin:40px auto;background:#fff;padding:30px;border-radius:10px}
label{font-weight:bold;margin-top:15px;display:block}
input,textarea,select{width:100%;padding:10px;margin-top:5px}
button{margin-top:20px;padding:12px;width:100%;background:#2563eb;color:#fff;border:none}
a{text-decoration:none;color:#2563eb;font-weight:bold}
</style>
</head>
<body>

<div class="box">
<h2>Edito Kurs</h2>

<form method="post">
<label>Titulli</label>
<input name="titulli" value="<?= $kurs['titulli'] ?>" required>

<label>Përshkrimi</label>
<textarea name="pershkrimi" rows="6"><?= $kurs['pershkrimi'] ?></textarea>

<label>Drejtimi</label>
<input value="<?= $kurs['drejtimi'] ?>" disabled>

<label>Ora</label>
<input name="ora" value="<?= $kurs['ora'] ?>">

<label>Salla</label>
<input name="salle" value="<?= $kurs['salle'] ?>">

<label>Instruktori</label>
<select name="instructor_id">
<?php while($i=mysqli_fetch_assoc($instruktoret)){ ?>
<option value="<?= $i['id'] ?>"
<?= $i['id']==$kurs['instructor_id']?'selected':'' ?>>
<?= $i['emri']." ".$i['mbiemri'] ?>
</option>
<?php } ?>
</select>

<button name="ruaj">Ruaj Ndryshimet</button>
</form>

<br>
<a href="kurset.php">⬅ Kthehu te kurset</a>
</div>

</body>
</html>
