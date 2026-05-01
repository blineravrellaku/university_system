<?php
session_start();
include "lidhja.php";

/* Vetëm admin */
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die("Access denied");
}

/* Kontroll ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID e kursit nuk është valide");
}

$kurs_id = (int)$_GET['id'];

/* Kontrollo nëse kursi ekziston */
$check = mysqli_query($lidhje, "SELECT id FROM courses WHERE id = $kurs_id");
if (mysqli_num_rows($check) === 0) {
    die("Kursi nuk u gjet");
}

/* Fshij kursin */
mysqli_query($lidhje, "DELETE FROM courses WHERE id = $kurs_id");

/* Kthehu te lista e kurseve */
header("Location: kurset.php");
exit;
