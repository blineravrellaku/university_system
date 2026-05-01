<?php
session_start();
include "lidhja.php";

/* Vetëm admin */
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die("Access denied");
}

/* Merr instruktorët + klasat */
$sql = "
SELECT 
    u.id,
    u.emri,
    u.mbiemri,
    u.photo,
    u.pershkrim,
    u.datelindja,
    u.vendlindja,
    u.drejtimi AS user_drejtimi,
    c.drejtimi AS class_drejtimi,
    c.ora,
    c.salle
FROM users u
LEFT JOIN classes c ON u.class_id = c.class_id
WHERE u.role_id = 2
ORDER BY u.emri
";

$result = mysqli_query($lidhje, $sql);

$instruktoret = [];
while ($row = mysqli_fetch_assoc($result)) {
    if (empty($row['pershkrim'])) {
        $row['pershkrim'] = "Biografia nuk është e disponueshme.";
    }
    $row['pershkrim'] = '<p>'.nl2br(htmlspecialchars($row['pershkrim'])).'</p>';

    $drejtimi = $row['class_drejtimi'] ?? ($row['user_drejtimi'] ?? "Pa Drejtim");
    $instruktoret[$drejtimi][] = $row;
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Menaxho Instruktorët</title>
<style>
body { font-family: Arial; background:#f1f5f9; padding:30px; }

/* TOP BAR */
.top-bar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}
.top-bar h1 { color:#0f172a; margin:0; font-size:28px; }
.btn-add { background:#003399; color:white; padding:12px 18px; border-radius:10px; text-decoration:none; font-weight:600; transition:0.3s; display:inline-block; }
.btn-add:hover { background:#001f66; }

/* CARDS */
.drejtimi { margin-top:50px; }
.drejtimi h2 { color:#003399; border-bottom:3px solid #003399; padding-bottom:6px; }
.cards { margin-top:25px; display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:25px; }
.card { background:white; border-radius:18px; padding:20px; display:flex; justify-content:space-between; box-shadow:0 12px 25px rgba(0,0,0,0.1); transition:0.3s; }
.card:hover { transform:translateY(-6px); }
.card-left { width:65%; }
.card-left h3 { margin:0; color:#1e293b; }
.card-left p { margin:6px 0; font-size:14px; color:#475569; }
.card-right img { width:120px; height:120px; border-radius:16px; object-fit:cover; background:#f1f5f9; padding:6px; cursor:pointer; }

.actions { margin-top:10px; }
.actions a { padding:7px 14px; border-radius:6px; text-decoration:none; font-size:13px; color:white; margin-right:6px; }
.edit { background:#003399; }
.edit:hover { background:#001f66; }
.delete { background:#ef4444; }
.delete:hover { background:#b91c1c; }

/* MODAL */
.modal { display:none; position:fixed; inset:0; background: rgba(0,0,0,0.6); align-items:center; justify-content:center; }
.modal-box { background:white; width:90%; max-width:520px; border-radius:20px; padding:25px; text-align:center; position:relative; }
.modal-box img { width:260px; height:260px; object-fit:cover; border-radius:20px; margin-bottom:15px; }
.modal-box h3 { margin-bottom:10px; color:#0f172a; }
.modal-box p { font-size:15px; color:#334155; line-height:1.6; }
.close { position:absolute; top:14px; right:18px; font-size:26px; cursor:pointer; }
</style>
</head>
<body>

<!-- TOP BAR -->
<div class="top-bar">
    <h1>Menaxho Instruktorët 🧑‍🏫</h1>
    <a href="regjistroinstruktor.php" class="btn-add">+ Regjistro Instruktor</a>
</div>

<!-- LISTA E INSTRUKTOREVE -->
<?php foreach($instruktoret as $drejtimi=>$lista): ?>
<div class="drejtimi">
<h2><?= htmlspecialchars($drejtimi) ?></h2>
<div class="cards">
<?php foreach($lista as $i): ?>
<div class="card">
    <div class="card-left">
        <h3><?= htmlspecialchars($i['emri'].' '.$i['mbiemri']) ?></h3>
        <p><strong>Biografia:</strong> <?= $i['pershkrim'] ?></p>
        <p><strong>Orari:</strong> <?= htmlspecialchars($i['ora'] ?? '-') ?></p>
        <p><strong>Salla:</strong> <?= htmlspecialchars($i['salle'] ?? '-') ?></p>
        <div class="actions">
            <a class="edit" href="edit_instruktor.php?id=<?= $i['id'] ?>">Edito</a>
            <a class="delete" href="delete_instruktor.php?id=<?= $i['id'] ?>" onclick="return confirm('Je e sigurt?')">Fshi</a>
        </div>
    </div>
    <div class="card-right">
        <img src="<?= htmlspecialchars($i['photo'] ?? 'images/default.jpg') ?>"
             onclick='openModal(
                 <?= json_encode($i['photo'] ?? 'images/default.jpg') ?>,
                 <?= json_encode($i['emri'].' '.$i['mbiemri']) ?>,
                 <?= json_encode($i['pershkrim']) ?>,
                 <?= json_encode($i['datelindja'] ?? '-') ?>,
                 <?= json_encode($i['vendlindja'] ?? '-') ?>
             )'>
    </div>
</div>
<?php endforeach; ?>
</div>
</div>
<?php endforeach; ?>

<!-- MODAL -->
<div id="modal" class="modal">
  <div class="modal-box">
      <span class="close" onclick="closeModal()">&times;</span>
      <img id="modalFoto" src="" alt="Foto Instruktori">
      <h3 id="modalEmri"></h3>
      <div id="modalBio"></div>
      <p><strong>Datëlindja:</strong> <span id="modalDatelindja"></span></p>
      <p><strong>Vendlindja:</strong> <span id="modalVendlindja"></span></p>
  </div>
</div>

<script>
function openModal(foto, emri, bio, datelindja, vendlindja){
    document.getElementById('modalFoto').src = foto;
    document.getElementById('modalEmri').textContent = emri;
    document.getElementById('modalBio').innerHTML = bio;
    document.getElementById('modalDatelindja').textContent = datelindja;
    document.getElementById('modalVendlindja').textContent = vendlindja;
    document.getElementById('modal').style.display='flex';
}

function closeModal(){
    document.getElementById('modal').style.display='none';
}

window.onclick = function(e){
    if(e.target.id=='modal') closeModal();
}
</script>

</body>
</html>
