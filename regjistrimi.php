<?php
session_start();
$error = "";
/* LIDHJA ME DB */
include "lidhja.php";

/* REGJISTRIM STUDENT */
if (isset($_POST['register'])) {

    $emri     = mysqli_real_escape_string($conn, $_POST['emri']);
    $mbiemri  = mysqli_real_escape_string($conn, $_POST['mbiemri']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);

    /* kontrollo nese username ekziston */
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");

    if (mysqli_num_rows($check) > 0) {
        $error = "Ky username ekziston!";
    } else {

        mysqli_query($conn, "INSERT INTO users
            (role_id, emri, mbiemri, username, email, password)
            VALUES
            (1, '$emri', '$mbiemri', '$username', '$email', '$password')
        ");

        header("Location: login.php?role=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>Regjistrim Student | CODE Academy</title>
    <link rel="stylesheet" href="style3.css">
</head>

<body class="login-page">

<div class="auth-header">
    <div class="auth-header-inner">
        <h1>CODE Academy</h1>
        <img src="user.png" alt="User">
    </div>
</div>

<div class="auth-wrapper">
    <div class="auth-box">

        <h2>Regjistrim Student</h2>

        <?php if ($error): ?>
            <div class="auth-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Emri</label>
            <input type="text" name="emri" required>

            <label>Mbiemri</label>
            <input type="text" name="mbiemri" required>

            <label>Username</label>
            <input type="text" name="username" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button name="register">Regjistrohu</button>
        </form>

        <div class="auth-link">
            <a href="login.php?role=1">Kthehu te Kyçja</a>
        </div>

    </div>
</div>

</body>
</html>
