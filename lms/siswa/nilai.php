<?php require '../config.php';need('siswa');$u=$_SESSION['u'];
$s=$pdo->prepare("SELECT * FROM nilai WHERE user_id=? ORDER BY mapel");$s->execute([$u['id']]);$rows=$s->fetchAll();
$s=$pdo->prepare("SELECT t.judul,t.mapel,p.nilai,p.komentar FROM pengumpulan p JOIN tugas t ON t.id=p.tugas_id WHERE p.user_id=? AND p.nilai IS NOT NULL");$s->execute([$u['id']]);$tg=$s->fetchAll();
$avg=$rows?round(array_sum(array_column($rows,'akhir'))/count($rows)):0;
head('Nilai');?>
<div class="hero card"><div><h2>Rata-rata Nilai Akhir</h2><p>Terus semangat belajar ya! ⭐</p></div><div class="ring" style="--p:<?=$avg?>"><span><?=$avg?></span></div></div>
<div class="card"><h3>🎓 Nilai Mata Pelajaran</h3><div class="tw"><table><tr><th>Mapel</th><th>Harian</th><th>Ulangan/Praktik</th><th>Akhir</th><th>Catatan Guru</th></tr><?php foreach($rows as $r)echo "<tr><td>".e($r['mapel'])."</td><td>{$r['harian']}</td><td>{$r['ulangan']}</td><td><span class='badge ok'>{$r['akhir']}</span></td><td>".e($r['catatan'])."</td></tr>";if(!$rows)echo "<tr><td colspan='5' class='muted'>Belum ada nilai.</td></tr>";?></table></div></div>
<div class="card"><h3>📝 Nilai Tugas</h3><div class="tw"><table><tr><th>Tugas</th><th>Mapel</th><th>Nilai</th><th>Komentar</th></tr><?php foreach($tg as $r)echo "<tr><td>".e($r['judul'])."</td><td>".e($r['mapel'])."</td><td><span class='badge ok'>{$r['nilai']}</span></td><td>".e($r['komentar'])."</td></tr>";if(!$tg)echo "<tr><td colspan='4' class='muted'>Belum ada tugas yang dinilai.</td></tr>";?></table></div></div>
<?php foot();
