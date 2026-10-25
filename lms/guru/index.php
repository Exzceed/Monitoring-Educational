<?php require '../config.php';need('guru');
$c=fn($q)=>$pdo->query($q)->fetchColumn();
head('Dashboard Guru');?>
<div class="grid">
<div class="card stat"><div class="ic">👧</div><span class="count" data-n="<?=$c("SELECT COUNT(*) FROM users WHERE role='siswa'")?>">0</span><p>Siswa</p></div>
<div class="card stat"><div class="ic">✅</div><span class="count" data-n="<?=$c("SELECT COUNT(*) FROM absensi WHERE tanggal=CURDATE() AND status='Hadir'")?>">0</span><p>Hadir Hari Ini</p></div>
<div class="card stat"><div class="ic">📝</div><span class="count" data-n="<?=$c("SELECT COUNT(*) FROM tugas")?>">0</span><p>Tugas</p></div>
<div class="card stat"><div class="ic">📥</div><span class="count" data-n="<?=$c("SELECT COUNT(*) FROM pengumpulan WHERE nilai IS NULL")?>">0</span><p>Perlu Dinilai</p></div></div>
<div class="card"><h3>📥 Tugas Masuk (belum dinilai)</h3><?php $r=$pdo->query("SELECT p.tugas_id,u.nama,t.judul,p.waktu FROM pengumpulan p JOIN users u ON u.id=p.user_id JOIN tugas t ON t.id=p.tugas_id WHERE p.nilai IS NULL ORDER BY p.waktu DESC LIMIT 8")->fetchAll();
foreach($r as $x)echo "<div class='item'><div><b>".e($x['nama'])."</b><br><small>".e($x['judul'])." · ".date('d M H:i',strtotime($x['waktu']))."</small></div><a class='btn sm' href='pengumpulan.php?tugas={$x['tugas_id']}'>Nilai</a></div>";if(!$r)echo "<p class='muted'>Tidak ada tugas yang menunggu penilaian.</p>";?></div>
<div class="card"><h3>🚀 Mulai dari sini</h3><p>1. Tambah siswa. 2. Isi jadwal. 3. Buat tugas dan materi. 4. Catat nilai dan kehadiran. 5. Unggah dokumentasi foto dan rekomendasi guru.</p></div>
<?php foot();
