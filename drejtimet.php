<?php
include "db.php";

$id = $_GET['id'];

$sql = "SELECT * FROM courses WHERE id = $id";
$result = mysqli_query($conn, $sql);
$course = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>Drejtimi | CODE Academy</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">CODE Academy</div>
    <ul class="nav-links">
        <li><a href="index.html">Home</a></li>
    </ul>
</nav>

<section class="about" style="margin-top:100px">

<?php if ($course): ?>

    <h2><?php echo $course['titulli']; ?></h2>

    <p>
        <?php echo $course['pershkrimi']; ?>
    </p>

<?php else: ?>

    <h2>Drejtimi nuk u gjet</h2>
    <p>Drejtimi që kërkuat nuk ekziston.</p>

<?php endif; ?>

<br>
<a href="index.html" class="btn-nav">← Kthehu në Home</a>

</section>

</body>
</html>
