<?php require '../config.php';
need('siswa');
$s = $pdo->prepare("SELECT * FROM materi WHERE kelas_id IS NULL OR kelas_id=? ORDER BY id DESC");
$s->execute([$_SESSION['u']['kelas_id']]);
$rows = $s->fetchAll();
head('Materi'); ?>
<div class="grid g2">
    <?php foreach ($rows as $r)
        echo "<div class='card mat'><span class='badge'>" . e($r['mapel']) . "</span> <small>" . e($r['pertemuan']) . "</small><h3>" . e($r['judul']) . "</h3><p>" . nl2br(e($r['pembahasan'])) . "</p>" . ($r['file'] ? "<a class='btn sm' target='_blank' href='../uploads/" . e($r['file']) . "'>📎 Unduh</a> " : '') . ($r['link'] ? "<a class='btn sm' target='_blank' href='" . e($r['link']) . "'>🔗 Buka</a>" : '') . "</div>";
    if (!$rows)
        echo "<p class='muted'>Belum ada materi.</p>"; ?>
</div>
<?php foot();
