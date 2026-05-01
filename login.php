<?php
session_start();

/* =========================
   DB CONNECTION
========================= */
$conn = mysqli_connect("localhost", "root", "", "kursi_db");
if (!$conn) {
    die("Gabim lidhjeje me databazën");
}

$error = "";


// Merr rolin nga GET
$role = $_GET['role'] ?? null;
$role = $role !== null ? (int)$role : null;


// Proceson login
if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // për prodhim reale përdor password_hash
    $role_id  = (int) $_POST['role_id'];

    $sql = "SELECT * FROM users 
            WHERE username='$username' 
            AND password='$password' 
            AND role_id=$role_id";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['username'] = $user['username'];

        // REDIRECT SIPAS ROLEVE
        if ($role_id === 1) {
            header("Location: studentdashboard.php");
        } elseif ($role_id === 2) {
            header("Location: instructordashboard.php");
        } elseif ($role_id === 3) {
            header("Location: admindashboard.php");
        }
        exit();
    } else {
        $error = "Username ose password i pasaktë!";
    }
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>CODE Academy | Kyçje</title>
    <link rel="stylesheet" href="style2.css?v=999">
</head>

<body class="login-page">

<div class="login-wrapper">

    <!-- LEFT SIDE -->
    <div class="login-left">
        <h1>CODE Academy</h1>
        <p>
            Platformë edukative për studentë, instruktorë dhe administratorë.
            Mësoni, praktikoni dhe ndërtoni të ardhmen tuaj në teknologji.
        </p>
    </div>

    <!-- RIGHT SIDE -->
    <div class="login-right">

        <!-- Nëse role nuk është zgjedhur -->
        <?php if ($role !== 1 && $role !== 2 && $role !== 3): ?>

            <h2>Zgjidh Rolin</h2>

            <a href="login.php?role=1" class="login-btn student">
                Kyçu si Student
            </a>

            <a href="login.php?role=2" class="login-btn instructor">
                Kyçu si Instruktor
            </a>

            <a href="login.php?role=3" class="login-btn admin">
                Kyçu si Admin
            </a>

        <?php else: ?>

            <!-- Forma login për rolin e zgjedhur -->
            <h2>
                <?= $role === 1 ? 'Kyçje Student' : ($role === 2 ? 'Kyçje Instruktor' : 'Kyçje Admin') ?>
            </h2>

            <?php if ($error): ?>
                <div class="login-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <input type="hidden" name="role_id" value="<?= $role ?>">

                <label>Username</label>
                <input type="text" name="username" required placeholder="Shkruaj username">

                <label>Password</label>
                <input type="password" name="password" required placeholder="Shkruaj password">

                <button type="submit" name="login" class="btn-login">Kyçu</button>
            </form>

            <div class="login-links">
                <a href="login.php" class="back-link">← Ndrysho rolin</a>
                <?php if ($role === 1): ?>
                    <a href="regjistrimi.php" class="auth-link">
                    </a>
                <?php endif; ?>
            </div>

        <?php endif; ?>

    </div>
</div>

</body>
</html>
