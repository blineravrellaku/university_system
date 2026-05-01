<?php
session_start();
include "lidhja.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die("Access denied");
}

/* Përditësimi i kursit */
if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['kurs_id'])) {
    $id = (int)$_POST['kurs_id'];
    $titulli = mysqli_real_escape_string($lidhja,$_POST['titulli']);
    $pershkrimi = mysqli_real_escape_string($lidhja,$_POST['pershkrimi']);
    $drejtimi = mysqli_real_escape_string($lidhja,$_POST['drejtimi']);
    $ora = mysqli_real_escape_string($lidhja,$_POST['ora']);
    $salle = mysqli_real_escape_string($lidhja,$_POST['salle']);
    $instructor_id = (int)$_POST['instructor_id'];

    mysqli_query($lidhja, "
        UPDATE courses
        SET titulli='$titulli',
            pershkrimi='$pershkrimi',
            drejtimi='$drejtimi',
            ora='$ora',
            salle='$salle',
            instructor_id=$instructor_id
        WHERE id=$id
    ");
}

/* Merr të gjithë kurset dhe instruktorët */
$kurs_res = mysqli_query($lidhja,"
SELECT c.id, c.titulli, c.pershkrimi, c.drejtimi, c.ora, c.salle, u.emri, u.mbiemri, u.id as instructor_id
FROM courses c
LEFT JOIN users u ON u.id=c.instructor_id
ORDER BY c.drejtimi, c.ora
");
$orari = [];
while($r=mysqli_fetch_assoc($kurs_res)){
    $orari[$r['drejtimi']][]=$r;
}

/* Merr të gjithë instruktorët */
$instruktoret = mysqli_query($lidhja,"SELECT id, emri, mbiemri, drejtimi FROM users WHERE role_id=2 ORDER BY emri");

/* Gjej konfliktet */
$conflicts=[];
$chk = mysqli_query($lidhja,"SELECT ora,salle,COUNT(*) c FROM courses GROUP BY ora,salle HAVING c>1");
while($c=mysqli_fetch_assoc($chk)){
    $conflicts[$c['ora'].$c['salle']] = true;
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Editor i Orarit</title>
<style>
body{font-family:Arial;background:#f8fafc;padding:40px;}
h1{font-size:36px;color:#020617;margin-bottom:25px;}
.drejtimi{margin-top:40px;}
.drejtimi h2{color:#1e40af;border-bottom:3px solid #1e40af;padding-bottom:6px;}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:25px;margin-top:20px;}
.card{background:white;border-radius:18px;padding:20px;box-shadow:0 12px 25px rgba(0,0,0,.1);}
.card input, .card textarea, .card select{width:100%;padding:10px;margin-bottom:10px;border-radius:8px;border:1px solid #ccc;}
.card button{background:#2563eb;color:white;padding:12px;border:none;border-radius:10px;font-weight:600;cursor:pointer;width:100%;}
.card button:hover{background:#1e40af;}
.badge{display:inline-block;background:#e0e7ff;color:#1e3a8a;padding:6px 12px;border-radius:999px;margin-bottom:10px;}
.warning{background:#fee2e2;color:#b91c1c;padding:8px;border-radius:12px;font-weight:700;margin-bottom:10px;}
</style>
</head>
<body>

<h1>📅 Editor i Orarit Akademik</h1>

<?php foreach($orari as $drejtimi=>$kurse): ?>
<div class="drejtimi">
<h2><?= $drejtimi ?></h2>
<div class="grid">
<?php foreach($kurse as $k): ?>
<form class="card" method="post">
<input type="hidden" name="kurs_id" value="<?= $k['id'] ?>">
<span class="badge"><?= $k['ora'] ?> | <?= $k['salle'] ?></span>

<input name="titulli" value="<?= htmlspecialchars($k['titulli']) ?>" required>
<textarea name="pershkrimi" placeholder="Përshkrimi"><?= htmlspecialchars($k['pershkrimi']) ?></textarea>

<select name="drejtimi" required>
    <option value="Web Development" <?= $k['drejtimi']=='Web Development'?'selected':'' ?>>Web Development</option>
    <option value="Software Engineering" <?= $k['drejtimi']=='Software Engineering'?'selected':'' ?>>Software Engineering</option>
    <option value="Networking" <?= $k['drejtimi']=='Networking'?'selected':'' ?>>Networking</option>
    <option value="Artificial Intelligence" <?= $k['drejtimi']=='Artificial Intelligence'?'selected':'' ?>>Artificial Intelligence</option>
</select>

<input name="ora" value="<?= htmlspecialchars($k['ora']) ?>" placeholder="Ora" required>
<input name="salle" value="<?= htmlspecialchars($k['salle']) ?>" placeholder="Salla" required>

<select name="instructor_id" required>
    <option value="">Zgjidh Instruktorin</option>
    <?php
    mysqli_data_seek($instruktoret,0); // rikthe pointer-in e rezultatit
    while($i=mysqli_fetch_assoc($instruktoret)):
    ?>
    <option value="<?= $i['id'] ?>" <?= $i['id']==$k['instructor_id']?'selected':'' ?>>
        <?= htmlspecialchars($i['emri'].' '.$i['mbiemri'].' ('.$i['drejtimi'].')') ?>
    </option>
    <?php endwhile; ?>
</select>

<?php if(isset($conflicts[$k['ora'].$k['salle']])): ?>
<div class="warning">⚠️ Konflikt orari në këtë sallë</div>
<?php endif; ?>

<button>Ruaj Ndryshimet</button>
</form>
<?php endforeach; ?>
</div>
</div>
<?php endforeach; ?>

</body>
</html>
