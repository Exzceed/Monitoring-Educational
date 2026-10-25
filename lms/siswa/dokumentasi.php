<?php require '../config.php';need('siswa');
$s=$pdo->prepare("SELECT * FROM dokumentasi WHERE kelas_id IS NULL OR kelas_id=? ORDER BY tanggal DESC,id DESC");$s->execute([$_SESSION['u']['kelas_id']]);$rows=$s->fetchAll();
head('Dokumentasi');?>
<div class="grid g2"><?php foreach($rows as $r)echo "<div class='card gal'>".($r['foto']?"<img src='../uploads/".e($r['foto'])."' alt=''>":"<div class='noimg'>📷</div>")."<small>".date('d M Y',strtotime($r['tanggal']))."</small><h3>".e($r['judul'])."</h3><p>".e($r['deskripsi'])."</p><p><b>💬 Pesan guru:</b> ".e($r['rekomendasi'])."</p></div>";if(!$rows)echo "<p class='muted'>Belum ada dokumentasi.</p>";?></div>
<?php foot();
