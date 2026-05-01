<?php
session_start();
include "lidhja.php";

// Vetëm admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

// Kontrollo lidhjen me databazën
if (!$lidhje) {
    die("Gabim lidhjeje me databazën: " . mysqli_connect_error());
}

// Përpunimi i formës për Shto Term
$success = '';
$error = '';

if (isset($_POST['submit'])) {
    $titulli = mysqli_real_escape_string($lidhje, $_POST['titulli']);
    $pershkrimi = mysqli_real_escape_string($lidhje, $_POST['pershkrimi']);

    if (!empty($titulli) && !empty($pershkrimi)) {
        $sql = "INSERT INTO termspolicies (titulli, pershkrimi) VALUES ('$titulli', '$pershkrimi')";
        if (mysqli_query($lidhje, $sql)) {
            $success = "Termi u shtua me sukses!";
        } else {
            $error = "Gabim: " . mysqli_error($lidhje);
        }
    } else {
        $error = "Ju lutem plotësoni titullin dhe përshkrimin.";
    }
}

// Merr termat nga DB
$term_result = mysqli_query($lidhje, "SELECT * FROM termspolicies ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>📄 Menaxho Terms & Policies</title>
<style>
body { font-family: Arial; background:#f0f4f8; padding:20px; color:#1e293b; }
h1 { text-align:center; margin-bottom:20px; }

/* Tabela */
table { width:100%; border-collapse: collapse; background:#fff; box-shadow:0 4px 12px rgba(0,0,0,0.05); margin-bottom:30px; }
th, td { padding:12px; border-bottom:1px solid #ddd; text-align:left; }
th { background:#003399; color:white; } /* Ngjyra e re */
tr:hover { background:#f1f5f9; }

a.button { 
    display:inline-block; padding:6px 12px; border-radius:6px; color:white; text-decoration:none; font-weight:600; transition:0.3s; 
}
a.edit { background:#003399; }      /* Ngjyra e re */
a.edit:hover { background:#001f66; }
a.delete { background:#ef4444; }    
a.delete:hover { background:#b91c1c; }

/* Forma Shto Term */
form {
    max-width: 700px;
    margin: 0 auto;
    background:#fff;
    padding:20px 25px;
    box-shadow:0 8px 20px rgba(0,0,0,0.1);
    border-radius:10px;
}
label { display:block; margin-bottom:8px; font-weight:600; }
input[type="text"], textarea { width:100%; padding:10px; margin-bottom:15px; border:1px solid #cbd5e1; border-radius:6px; font-size:16px; }
button { 
    background:#10b981; /* jeshil mbetet */ 
    color:white; padding:12px 20px; border:none; border-radius:8px; font-size:16px; cursor:pointer; transition:0.3s; 
}
button:hover { background:#059669; }

.success { color:green; margin-bottom:20px; text-align:center; }
.error { color:red; margin-bottom:20px; text-align:center; }

</style>
</head>
<body>

<h1>📄 Menaxho Terms & Policies</h1>

<table>
    <thead>
        <tr>
            <th>Titulli</th>
            <th>Pershkrimi</th>
            <th>Krijuar më</th>
            <th>Veprime</th>
        </tr>
    </thead>
    <tbody>
        <?php if(mysqli_num_rows($term_result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($term_result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['titulli']) ?></td>
                    <td><?= htmlspecialchars($row['pershkrimi']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <a href="editterms.php?id=<?= $row['id'] ?>" class="button edit">Edit</a>
                        <a href="fshiterms.php?id=<?= $row['id'] ?>" class="button delete" onclick="return confirm('Jeni të sigurt për fshirje?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align:center;">Nuk ka Terms & Policies</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<h2 style="text-align:center; margin-bottom:15px;">➕ Shto Term të Ri</h2>

<?php if($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
<?php if($error): ?><p class="error"><?= $error ?></p><?php endif; ?>

<form method="POST" action="">
    <label>Titulli</label>
    <input type="text" name="titulli" placeholder="Shkruani titullin..." required>

    <label>Përshkrimi</label>
    <textarea name="pershkrimi" rows="5" placeholder="Shkruani përshkrimin..." required></textarea>

    <button type="submit" name="submit">Shto Term</button>
</form>

</body>
</html>
