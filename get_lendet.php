<?php
include "lidhja.php";

$drejtimi = $_GET['drejtimi'] ?? '';
$lendet = [];

if($drejtimi) {
    $res = mysqli_query($lidhje, "SELECT DISTINCT titulli FROM courses WHERE drejtimi='".mysqli_real_escape_string($lidhje,$drejtimi)."' ORDER BY titulli ASC");
    while($row = mysqli_fetch_assoc($res)) {
        $lendet[] = $row['titulli'];
    }
}
echo json_encode($lendet);
