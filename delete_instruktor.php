<?php
session_start();
include "lidhja.php";

// Kontroll login dhe rol
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die("Access denied");
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // sigurimi i integer

    // Kontroll para fshirjes: a ekziston instruktori me këtë id?
    $check = mysqli_query($lidhje, "SELECT * FROM users WHERE id=$id LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        // Sigurohu që është instruktori (role_id = 2)
        if ($row['role_id'] == 2) {
            $sql = "DELETE FROM users WHERE id = $id";
            if (mysqli_query($lidhje, $sql)) {
                header("Location: admin_instruktoret.php");
                exit();
            } else {
                die("Gabim gjatë fshirjes: " . mysqli_error($lidhje));
            }
        } else {
            die("Ky përdorues nuk është instruktori!");
        }
    } else {
        die("Instruktori nuk ekziston.");
    }
} else {
    die("Id e pavlefshme.");
}
?>

