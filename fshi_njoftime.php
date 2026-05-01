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

// Merr id e njoftimit
if (!isset($_GET['id'])) {
    die("Njoftimi nuk u gjet.");
}
$id = intval($_GET['id']);

// Fshi njoftimin
if (mysqli_query($lidhje, "DELETE FROM notifications WHERE id=$id")) {
    header("Location: njoftime.php");
    exit();
} else {
    die("Gabim gjatë fshirjes: " . mysqli_error($lidhje));
}
?>
