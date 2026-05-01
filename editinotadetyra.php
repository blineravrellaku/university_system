<div class="students" id="students">
    <div class="section-title">🎓 Studentët e lëndës <?= htmlspecialchars($instructor_lenda) ?></div>
    <?php if(mysqli_num_rows($students_result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Studenti</th>
                    <th>Drejtimi</th>
                    <th>Nota</th>
                    <th>Detyra</th>
                    <th>Veprime</th>
                </tr>
            </thead>
            <tbody>
            <?php while($s=mysqli_fetch_assoc($students_result)): ?>
                <tr>
                    <td><?= htmlspecialchars($s['emri'].' '.$s['mbiemri']) ?></td>
                    <td><?= htmlspecialchars($s['drejtimi']) ?></td>
                    <td>
                        <form method="POST" action="editnotadetyra.php">
                            <input type="hidden" name="student_id" value="<?= $s['id'] ?>">
                            <input type="hidden" name="course" value="<?= htmlspecialchars($instructor_lenda) ?>">
                            <input type="text" name="nota" value="<?= htmlspecialchars($s['nota'] ?? '') ?>" style="width:50px;">
                    </td>
                    <td>
                            <input type="text" name="detyra" value="<?= htmlspecialchars($s['detyra'] ?? '') ?>">
                    </td>
                    <td>
                            <button type="submit">Ruaj</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align:center;">Nuk ka studentë për këtë lëndë.</div>
    <?php endif; ?>
</div>
