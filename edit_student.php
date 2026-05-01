<?php
session_start();
include "lidhja.php";

// Kontroll login dhe rol (vetëm admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die("Nuk keni qasje.");
}

$id = $_GET['id'] ?? null;
if (!$id) die("ID mungon.");

// Merr të dhënat ekzistuese të studentit
$student = mysqli_fetch_assoc(mysqli_query(
    $lidhje,
    "SELECT * FROM users WHERE id=$id AND role_id=1"
));

if (!$student) die("Studenti nuk u gjet.");

// Për opsionet e klasave
$classes = mysqli_query($lidhje, "SELECT * FROM classes");

// Biografi default për secilin student
$default_biografi = [
    1 => "Jora ishte nxënëse e shkëlqyer në Gjimnazin 'Xhevdet Doda' me mesatare 5.0. Talent në AI dhe programim.",
    2 => "Arber u diplomua në Gjimnazin 'Odhise Paskali' me mesatare 4.8. I apasionuar pas robotikës.",
    3 => "Lirika ishte nxënëse e zellshme në Gjimnazin 'Sami Frashëri' me mesatare 5.0. Entuziaste për coding.",
    4 => "Flutra mbaroi në Gjimnazin 'Eqrem Çabej' me mesatare 4.9. Ka pasion për dizajn dhe inovacion.",
    5 => "Arta ishte nxënëse e shkëlqyer në Gjimnazin 'Isa Boletini' me mesatare 5.0. Aktiviste në laboratorë AI.",
    6 => "Dardan mbaroi në Gjimnazin 'Sami Frashëri' me mesatare 4.7. E orientuar në machine learning.",
    7 => "Valon ishte nxënës i shkëlqyer në Gjimnazin 'Gjon Buzuku' me mesatare 4.9. Kreativ në projekte.",
    8 => "Elira mbaroi në Gjimnazin 'Hivzi Sylejmani' me mesatare 5.0. Ka interes në web development dhe AI."
];

// Vendos biografinë nëse është bosh
if (empty($student['pershkrim'])) {
    $student['pershkrim'] = $default_biografi[$student['id']] ?? 'Nxënës i shkëlqyer me pasion për mësim.';
}

// Përgatit datëlindjen në formatin YYYY-MM-DD për input date
$datelindja = '';
if (!empty($student['datelindja']) && $student['datelindja'] != '0000-00-00') {
    $datelindja = date('Y-m-d', strtotime($student['datelindja']));
}

// Kur form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emri = mysqli_real_escape_string($lidhje, $_POST['emri']);
    $mbiemri = mysqli_real_escape_string($lidhje, $_POST['mbiemri']);
    $username = mysqli_real_escape_string($lidhje, $_POST['username']);
    $email = mysqli_real_escape_string($lidhje, $_POST['email']);
    $class_id = $_POST['class_id'];
    $pershkrim = mysqli_real_escape_string($lidhje, $_POST['pershkrim']);
    $datelindja_post = $_POST['datelindja'];
    $vendlindja = mysqli_real_escape_string($lidhje, $_POST['vendlindja']);

    mysqli_query($lidhje, "
        UPDATE users SET
            emri='$emri',
            mbiemri='$mbiemri',
            username='$username',
            email='$email',
            class_id='$class_id',
            pershkrim='$pershkrim',
            datelindja='$datelindja_post',
            vendlindja='$vendlindja'
        WHERE id=$id AND role_id=1
    ");

    header("Location: admin_students.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Edito Studentin</title>
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
button:hover { background: #1e40af; }
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
<h2>Edito Studentin</h2>

<form method="POST">
    <label>Emri</label>
    <input type="text" name="emri" value="<?= htmlspecialchars($student['emri'] ?? '') ?>" required>

    <label>Mbiemri</label>
    <input type="text" name="mbiemri" value="<?= htmlspecialchars($student['mbiemri'] ?? '') ?>" required>

    <label>Username</label>
    <input type="text" name="username" value="<?= htmlspecialchars($student['username'] ?? '') ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($student['email'] ?? '') ?>">

    <label>Drejtimi / Orari / Lab</label>
    <select name="class_id" required>
        <?php while($c = mysqli_fetch_assoc($classes)) : ?>
            <option value="<?= $c['class_id'] ?>" <?= ($c['class_id'] == $student['class_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['drejtimi'] . " | " . $c['ora'] . " | " . $c['salle']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Biografia</label>
    <textarea name="pershkrim"><?= htmlspecialchars($student['pershkrim']) ?></textarea>

    <label>Datëlindja</label>
    <input type="date" name="datelindja" value="<?= $datelindja ?>">

    <label>Vendlindja</label>
    <input type="text" name="vendlindja" value="<?= htmlspecialchars($student['vendlindja'] ?? '') ?>">

    <button type="submit">💾 Ruaj Ndryshimet</button>
</form>

<a href="admin_students.php" class="back">← Kthehu te lista e studentëve</a>
</div>

</body>
</html>
