<?php
session_start();
include "lidhja.php";

/* Vetëm admin */
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die("Access denied");
}

/* Merr kurset + instruktorët */
$sql = "
SELECT 
    c.id AS course_id,
    c.titulli,
    c.drejtimi,
    c.ora,
    c.salle,
    u.emri,
    u.mbiemri
FROM courses c
JOIN users u ON u.id = c.instructor_id
ORDER BY c.drejtimi, c.titulli
";

$result = mysqli_query($lidhje, $sql);

$kurset = [];
while ($row = mysqli_fetch_assoc($result)) {
    $kurset[$row['drejtimi']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Kurset</title>

<style>
body{font-family:Arial;background:#f1f5f9;padding:30px}
h1{color:#0f172a}

/* TOP BAR */
.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}
.btn-add{
    background:#003399; /* Ngjyra e re */
    color:white;
    padding:12px 20px;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
    transition:0.3s;
}
.btn-add:hover{background:#001f66} /* Hover më i errët */

/* SEKSIONET E DREJTIMEVE */
.drejtimi{margin-top:40px}
.drejtimi h2{
    color:#003399; /* Ngjyra e re */
    border-bottom:3px solid #003399; /* Ngjyra e re */
    padding-bottom:6px;
}

/* KARTAT */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
    gap:25px;
    margin-top:20px;
}

.card{
    background:white;
    padding:20px;
    border-radius:18px;
    box-shadow:0 12px 25px rgba(0,0,0,.1);
}

.card h3{margin:0 0 10px;color:#1e293b}
.card p{margin:6px 0;color:#475569}

.actions{
    display:flex;
    gap:10px;
    margin-top:15px;
}
.actions a{
    flex:1;
    padding:10px;
    text-align:center;
    border-radius:8px;
    color:white;
    font-weight:600;
    text-decoration:none;
    transition:0.3s;
}
.edit{background:#003399} /* Ngjyra e re */
.edit:hover{background:#001f66} /* Hover më i errët */
.delete{background:#ef4444}
.delete:hover{background:#b91c1c}
</style>
</head>

<body>

<div class="top-bar">
    <h1>Kurset Aktuale 📚</h1>
    <a href="regjistrokurs.php" class="btn-add">+ Regjistro Kurs</a>
</div>

<?php foreach($kurset as $drejtimi => $lista): ?>
<div class="drejtimi">
    <h2><?= htmlspecialchars($drejtimi) ?></h2>
    <div class="cards">
        <?php foreach($lista as $k): ?>
        <div class="card">
            <h3><?= htmlspecialchars($k['titulli']) ?></h3>
            <p><strong>Instruktori:</strong> <?= htmlspecialchars($k['emri'].' '.$k['mbiemri']) ?></p>
            <p><strong>Ora:</strong> <?= htmlspecialchars($k['ora']) ?></p>
            <p><strong>Salla:</strong> <?= htmlspecialchars($k['salle']) ?></p>

            <div class="actions">
                <a class="edit" href="editkurs.php?id=<?= $k['course_id'] ?>">Edito</a>
                <a class="delete" href="fshikurs.php?id=<?= $k['course_id'] ?>"
                   onclick="return confirm('Je e sigurt?')">Fshi</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

</body>
</html>
