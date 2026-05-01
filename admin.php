<?php
session_start();
include "lidhja.php";  // Kjo e lidh skedarin me bazën e të dhënave

// Kontroll login & rol admin
if(!isset($_SESSION['user']) || $_SESSION['roli'] != 'admin'){
    header("Location: login.html");
    exit();
}
?>


<div class="content">
    <h2>Lista e Studentëve</h2>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Emri</th>
            <th>Mbiemri</th>
            <th>Veprime</th>
        </tr>
        <?php
        $students = mysqli_query($lidhje,"SELECT * FROM student");
        while($row = mysqli_fetch_assoc($students)){
        ?>
        <tr>
            <td><?= $row['StudentID'] ?></td>
            <td><?= $row['Emri'] ?></td>
            <td><?= $row['Mbiemri'] ?></td>
            <td>
                <a href="editstudent.php?id=<?= $row['StudentID'] ?>"><button>Edito</button></a>
                <a href="fshistudent.php?id=<?= $row['StudentID'] ?>" onclick="return confirm('A je i sigurt?')"><button>Fshi</button></a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <h2>Lista e Instruktorëve</h2>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Emri</th>
            <th>Mbiemri</th>
            <th>Veprime</th>
        </tr>
        <?php
        $instruktor = mysqli_query($lidhje,"SELECT * FROM instruktor");
        while($row = mysqli_fetch_assoc($instruktor)){
        ?>
        <tr>
            <td><?= $row['InstruktorID'] ?></td>
            <td><?= $row['Emri'] ?></td>
            <td><?= $row['Mbiemri'] ?></td>
            <td>
                <a href="editinstruktor.php?id=<?= $row['InstruktorID'] ?>"><button>Edito</button></a>
                <a href="fshiinstruktor.php?id=<?= $row['InstruktorID'] ?>" onclick="return confirm('A je i sigurt?')"><button>Fshi</button></a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
