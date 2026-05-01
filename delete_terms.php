<?php
session_start();
include "lidhja.php";

// Vetëm admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

// Merr ID nga GET
if (!isset($_GET['id'])) {
    header("Location: manage_termspolicies.php");
    exit();
}

$id = intval($_GET['id']);

// Fshi term-in
$delete_sql = "DELETE FROM termspolicies WHERE id=$id";
if (mysqli_query($lidhje, $delete_sql)) {
    header("Location: manage_termspolicies.php?success=1");
    exit();
} else {
    die("Gabim: " . mysqli_error($lidhje));
}
?>
