<?php
session_start();

/* Kontroll login */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* Vetëm admin */
if ($_SESSION['role_id'] != 3) {
    die("Access denied");
}

include "lidhja.php"; // Lidhja me DB

/* Merr studentët nga DB */
$sql = "
SELECT 
    id,
    emri,
    mbiemri,
    username,
    email,
    drejtimi,
    orari,
    salle,
    photo
FROM users
WHERE role_id = 1
ORDER BY drejtimi, emri
";

$result = mysqli_query($lidhje, $sql);

$studentet = [];
while ($row = mysqli_fetch_assoc($result)) {
    $drejtimi = $row['drejtimi'] ?? 'Pa Drejtim';
    $studentet[$drejtimi][] = $row;
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Menaxho Studentët | Admin</title>
<style>
body { font-family: Arial; background:#f1f5f9; padding:30px; }
h1 { color:#0f172a; }

.drejtimi { margin-top:50px; }
.drejtimi h2 {
    color:#003399; /* ndryshuar nga #2563eb në #003399 */
    border-bottom:3px solid #003399; /* ndryshuar gjithashtu */
    padding-bottom:6px;
}

.cards {
    margin-top:25px;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
    gap:25px;
}

.card {
    background:white;
    border-radius:18px;
    padding:20px;
    display:flex;
    justify-content:space-between;
    box-shadow:0 12px 25px rgba(0,0,0,0.1);
    transition:0.3s;
}
.card:hover { transform:translateY(-6px); }

.card-left { width:65%; }
.card-left h3 { margin:0; color:#003399; } /* ndryshuar nga #1e293b */
.card-left p { margin:6px 0; font-size:14px; color:#475569; }

.card-right img {
    width:120px;
    height:120px;
    border-radius:16px;
    object-fit:cover;
    background:#f1f5f9;
    padding:6px;
    cursor:pointer;
}

.actions { margin-top:10px; }
.actions a {
    padding:7px 14px;
    border-radius:6px;
    text-decoration:none;
    font-size:13px;
    color:white;
    margin-right:6px;
}
.edit { background:#003399; } /* ndryshuar nga #2563eb */
.edit:hover { background:#002080; } /* blu më e errët për hover */
.delete { background:#ef4444; }
.delete:hover { background:#b91c1c; }

.top-bar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}
.btn-add {
    background:#003399; /* ndryshuar nga #2563eb */
    color:white;
    padding:12px 18px;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
}
.btn-add:hover { background:#002080; } /* blu më e errët për hover */

</style>
</head>
<body>

<div class="top-bar">
    <h1>Menaxho Studentët 🎓</h1>
    <a href="regjistrostudent.php" class="btn-add">+ Regjistro Student</a>
</div>

<?php foreach ($studentet as $drejtimi => $lista): ?>
<div class="drejtimi">
    <h2><?= htmlspecialchars($drejtimi) ?></h2>

    <div class="cards">
    <?php foreach ($lista as $i): ?>

        <?php
        // FOTO NGA DATABASE – nëse nuk ka vendos default
        $foto = (!empty($i['photo']) && file_exists($i['photo']))
            ? $i['photo']
            : 'images/default.jpg';
        ?>

        <div class="card">
            <div class="card-left">
                <h3><?= htmlspecialchars($i['emri'].' '.$i['mbiemri']) ?></h3>
                <p><strong>Username:</strong> <?= htmlspecialchars($i['username']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($i['email']) ?></p>
                <p><strong>Orari:</strong> <?= htmlspecialchars($i['orari'] ?? '-') ?></p>
                <p><strong>Salla:</strong> <?= htmlspecialchars($i['salle'] ?? '-') ?></p>

                <div class="actions">
                    <a class="edit" href="edit_student.php?id=<?= $i['id'] ?>">Edito</a>
                    <a class="delete" href="delete_student.php?id=<?= $i['id'] ?>"
                       onclick="return confirm('Je i sigurt?')">Fshi</a>
                </div>
            </div>

            <div class="card-right">
                <img src="<?= htmlspecialchars($foto) ?>" alt="Foto Student">
            </div>
        </div>

    <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

</body>
</html>

