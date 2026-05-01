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

// Përpunimi i formës për të shtuar njoftim
$success = '';
$error = '';

if (isset($_POST['submit'])) {
    $user_id = intval($_POST['user_id']);
    $mesazhi = mysqli_real_escape_string($lidhje, $_POST['mesazhi']);

    if (!empty($user_id) && !empty($mesazhi)) {
        $sql_insert = "INSERT INTO notifications (user_id, mesazhi) VALUES ($user_id, '$mesazhi')";
        if (mysqli_query($lidhje, $sql_insert)) {
            $success = "Njoftimi u dërgua me sukses!";
        } else {
            $error = "Gabim: " . mysqli_error($lidhje);
        }
    } else {
        $error = "Ju lutem zgjidhni marrësin dhe shkruani mesazhin.";
    }
}

// Merr listën e studentëve dhe instruktorëve për select
$users_result = mysqli_query($lidhje, "
    SELECT id, emri, mbiemri, role_id 
    FROM users 
    WHERE role_id IN (1,2) 
    ORDER BY role_id, emri ASC
");

// Merr të gjitha njoftimet
$sql = "SELECT n.id, n.mesazhi, n.created_at, u.emri, u.mbiemri, u.role_id
        FROM notifications n
        LEFT JOIN users u ON n.user_id = u.id
        ORDER BY n.created_at DESC";
$result = mysqli_query($lidhje, $sql);
if (!$result) die("Gabim: " . mysqli_error($lidhje));
?>
<!DOCTYPE html>
<html lang="sq">
<head>
<meta charset="UTF-8">
<title>Menaxho Njoftime</title>
<style>
body { 
    font-family: Arial, sans-serif; 
    background:#f0f4f8; 
    padding:30px; 
    color:#1e293b; 
    display:flex; 
    justify-content:center; 
}
.container { width:100%; max-width:900px; }
h1 { text-align:center; color:#0f172a; margin-bottom:25px; font-size:36px; }

/* Forma */
form { background:#fff; padding:25px; box-shadow:0 8px 20px rgba(0,0,0,0.1); border-radius:10px; margin-bottom:30px; }
label { display:block; margin-bottom:8px; font-weight:600; }
select, textarea { width:100%; padding:10px; margin-bottom:15px; border:1px solid #cbd5e1; border-radius:6px; font-size:16px; }
button { 
    background:#003399; /* Ngjyra e re */ 
    color:white; 
    padding:12px 25px; 
    border:none; 
    border-radius:8px; 
    font-size:16px; 
    cursor:pointer; 
    transition:0.3s; 
}
button:hover { background:#001f66; } /* Hover më i errët */

.success { color:green; margin-bottom:15px; text-align:center; font-weight:600; }
.error { color:red; margin-bottom:15px; text-align:center; font-weight:600; }

/* Tabela */
table { width:100%; border-collapse:collapse; background:#fff; box-shadow:0 8px 20px rgba(0,0,0,0.1); table-layout: fixed; }
th, td { padding:12px 15px; border-bottom:1px solid #ddd; text-align:left; word-wrap: break-word; }
th { background:#003399; color:#fff; font-weight:600; } /* Ngjyra e re */
th:nth-child(1) { width:25%; } /* Marrësi */
th:nth-child(2) { width:45%; } /* Mesazhi */
th:nth-child(3) { width:15%; } /* Krijuar më */
th:nth-child(4) { width:15%; } /* Veprime */
tr:hover { background-color:#f1f5f9; }
a { text-decoration:none; margin-right:10px; font-weight:bold; }
a.delete { color:#ef4444; }
.role-badge { font-size:12px; color:#fff; padding:2px 8px; border-radius:6px; margin-left:5px; }
.student { background:#10b981; }
.instruktor { background:#6366f1; }
</style>
</head>
<body>
<div class="container">

<h1>📢 Menaxho Njoftime</h1>

<form method="POST">
    <?php if($success) echo "<p class='success'>$success</p>"; ?>
    <?php if($error) echo "<p class='error'>$error</p>"; ?>
    
    <label>Zgjidh Marrësin (Student / Instruktor)</label>
    <select name="user_id" required>
        <option value="">-- Zgjidh --</option>
        <?php while($u = mysqli_fetch_assoc($users_result)): ?>
            <option value="<?= $u['id'] ?>">
                <?= htmlspecialchars($u['emri'].' '.$u['mbiemri']) ?>
                <?= $u['role_id']==1? '(Student)':'(Instruktor)' ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Mesazhi</label>
    <textarea name="mesazhi" rows="4" placeholder="Shkruani njoftimin..." required></textarea>

    <button type="submit" name="submit">Dërgo Njoftimin</button>
</form>

<table>
    <thead>
        <tr>
            <th>Marrësi</th>
            <th>Mesazhi</th>
            <th>Krijuar më</th>
            <th>Veprime</th>
        </tr>
    </thead>
    <tbody>
    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($n = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($n['emri'].' '.$n['mbiemri']) ?>
                    <?php if($n['role_id']==1): ?>
                        <span class="role-badge student">Student</span>
                    <?php else: ?>
                        <span class="role-badge instruktor">Instruktor</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($n['mesazhi']) ?></td>
                <td><?= htmlspecialchars($n['created_at']) ?></td>
                <td>
                    <a href="edit_njoftime.php?id=<?= $n['id'] ?>">Edit</a>
                    <a class="delete" href="fshi_njoftime.php?id=<?= $n['id'] ?>" onclick="return confirm('A je i sigurt që dëshiron ta fshish këtë njoftim?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4" style="text-align:center;">Nuk ka njoftime.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

</div>
</body>
</html>
