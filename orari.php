]<?php
session_start();
$conn = mysqli_connect("localhost","root","","kursi_db");
if(!$conn){ die("Gabim DB"); }

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die("Access denied");
}

/* FILTER */
$where = "1";
if (!empty($_GET['ora'])) {
    $ora = mysqli_real_escape_string($conn, $_GET['ora']);
    $where .= " AND c.ora='$ora'";
}
if (!empty($_GET['salle'])) {
    $salle = mysqli_real_escape_string($conn, $_GET['salle']);
    $where .= " AND c.salle='$salle'";
}

/* KURSET */
$sql = "
SELECT 
 c.id, c.titulli, c.drejtimi, c.ora, c.salle,
 u.emri, u.mbiemri
FROM courses c
LEFT JOIN users u ON u.id=c.instructor_id
WHERE $where
ORDER BY c.drejtimi, c.ora
";
$res = mysqli_query($conn,$sql);

/* GRUPIM */
$orari=[];
while($r=mysqli_fetch_assoc($res)){
    $orari[$r['drejtimi']][]=$r;
}

/* KONFLIKT */
$conflicts=[];
$chk=mysqli_query($conn,"
SELECT ora,salle,COUNT(*) c
FROM courses
GROUP BY ora,salle
HAVING c>1
");
while($c=mysqli_fetch_assoc($chk)){
    $conflicts[$c['ora'].$c['salle']] = true;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Orari Akademik</title>
<style>
body{
    font-family:'Segoe UI',sans-serif;
    background:#f0f4f8;
    padding:40px;
    display:flex;
    justify-content:center;
}
.container{
    width:90%;
    max-width:1200px;
}
h1{
    font-size:42px;
    color:#0f172a;
    text-align:center;
    margin-bottom:35px;
}
.filter{
    display:flex;
    justify-content:center;
    gap:15px;
    margin-bottom:40px;
}
select,button{
    padding:12px 16px;
    font-size:16px;
    border-radius:12px;
    border:1px solid #cbd5f5;
}
button{
    background:#003399; /* Ngjyra e re */
    color:white;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}
button:hover{background:#001f66} /* Hover më i errët */

.drejtimi{
    margin-top:50px;
}
.drejtimi h2{
    font-size:32px;
    color:#003399; /* Ngjyra e re */
    border-bottom:4px solid #003399; /* Ngjyra e re */
    padding-bottom:8px;
    margin-bottom:25px;
    text-align:center;
}
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
    gap:30px;
    justify-items:center;
}
.card{
    background:white;
    border-radius:22px;
    padding:28px;
    box-shadow:0 18px 40px rgba(0,0,0,.12);
    width:100%;
    max-width:350px;
    text-align:center;
    transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover{
    transform:translateY(-6px);
    box-shadow:0 25px 50px rgba(0,0,0,.15);
}
.card h3{
    font-size:22px;
    margin:14px 0;
}
.card p{
    font-size:16px;
    margin:6px 0;
}
.badge{
    display:inline-block;
    background:#003399; /* Ngjyra e re */
    color:white;
    font-weight:600;
    padding:8px 14px;
    border-radius:999px;
    font-size:14px;
    margin-bottom:14px;
}
.warning{
    background:#fee2e2;
    color:#b91c1c;
    padding:10px;
    border-radius:12px;
    font-weight:700;
    margin-bottom:12px;
}
.students{
    margin-top:12px;
    font-size:14px;
    color:#334155;
}
.actions{
    display:flex;
    gap:12px;
    margin-top:18px;
}
.actions a{
    flex:1;
    padding:10px;
    text-align:center;
    border-radius:12px;
    font-size:14px;
    font-weight:600;
    text-decoration:none;
    color:white;
    transition:0.3s;
}
.edit{background:#003399;} /* Ngjyra e re */
.edit:hover{background:#001f66;}
.delete{background:#dc2626;}
.delete:hover{background:#991b1b;}
</style>
</head>
<body>
<div class="container">
<h1>📅 Orari Akademik – Universiteti</h1>

<form class="filter" method="get">
<select name="ora">
<option value="">Filtro sipas orës</option>
<option <?= (isset($_GET['ora']) && $_GET['ora']=='08:00-10:00')?'selected':'' ?>>08:00-10:00</option>
<option <?= (isset($_GET['ora']) && $_GET['ora']=='10:00-12:00')?'selected':'' ?>>10:00-12:00</option>
<option <?= (isset($_GET['ora']) && $_GET['ora']=='12:00-14:00')?'selected':'' ?>>12:00-14:00</option>
<option <?= (isset($_GET['ora']) && $_GET['ora']=='14:00-16:00')?'selected':'' ?>>14:00-16:00</option>
</select>

<select name="salle">
<option value="">Filtro sipas sallës</option>
<option <?= (isset($_GET['salle']) && $_GET['salle']=='Lab I')?'selected':'' ?>>Lab I</option>
<option <?= (isset($_GET['salle']) && $_GET['salle']=='Lab II')?'selected':'' ?>>Lab II</option>
<option <?= (isset($_GET['salle']) && $_GET['salle']=='Lab III')?'selected':'' ?>>Lab III</option>
<option <?= (isset($_GET['salle']) && $_GET['salle']=='Lab IV')?'selected':'' ?>>Lab IV</option>
</select>

<button>Filtro</button>
</form>

<?php foreach($orari as $drejtimi=>$kurse): ?>
<div class="drejtimi">
<h2><?= $drejtimi ?></h2>

<div class="grid">
<?php foreach($kurse as $k): ?>
<div class="card">
<span class="badge"><?= $k['salle'] ?> | <?= $k['ora'] ?></span>

<h3><?= $k['titulli'] ?></h3>

<p><strong>Instruktori:</strong> <?= $k['emri'].' '.$k['mbiemri'] ?></p>

<?php if(isset($conflicts[$k['ora'].$k['salle']])): ?>
<div class="warning">⚠️ Konflikt orari në këtë sallë</div>
<?php endif; ?>

<div class="students">
<strong>Studentët e drejtimit:</strong><br>
<?php
$st=mysqli_query($conn,"
SELECT emri,mbiemri
FROM users
WHERE role_id=1 AND drejtimi='".$k['drejtimi']."'
");
while($s=mysqli_fetch_assoc($st)){
    echo "• ".$s['emri']." ".$s['mbiemri']."<br>";
}
?>
</div>

<div class="actions">
<a class="edit" href="editkurs.php?id=<?= $k['id'] ?>">Edit</a>
<a class="delete" href="fshikurs.php?id=<?= $k['id'] ?>"
onclick="return confirm('Fshij kursin?')">Fshi</a>
</div>

</div>
<?php endforeach; ?>
</div>
</div>
<?php endforeach; ?>

</div>
</body>
</html>
