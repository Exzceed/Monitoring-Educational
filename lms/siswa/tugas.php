<?php require '../config.php';need('siswa');$u=$_SESSION['u'];$kid=$u['kelas_id'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 $s=$pdo->prepare("SELECT id FROM tugas WHERE id=? AND kelas_id=?");$s->execute([(int)$_POST['tugas'],$kid]);
 $f=upload($_FILES['file']??[]);$cat=trim($_POST['catatan']);
 if(!$s->fetch())flash('Tugas tidak ditemukan');
 elseif(!$f&&$cat==='')flash('Isi catatan atau pilih file (PDF/JPG/PNG, maks 10MB)');
 else{$pdo->prepare("INSERT INTO pengumpulan(tugas_id,user_id,catatan,file) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE catatan=IF(nilai IS NULL,VALUES(catatan),catatan),file=IF(nilai IS NULL,COALESCE(VALUES(file),file),file),waktu=IF(nilai IS NULL,NOW(),waktu)")->execute([(int)$_POST['tugas'],$u['id'],$cat,$f]);flash('Tugas berhasil dikumpulkan 🎉');}
 header('Location: tugas.php');exit;}
$s=$pdo->prepare("SELECT t.*,p.catatan,p.file,p.waktu,p.nilai,p.komentar FROM tugas t LEFT JOIN pengumpulan p ON p.tugas_id=t.id AND p.user_id=? WHERE t.kelas_id=? ORDER BY t.deadline DESC");$s->execute([$u['id'],$kid]);$rows=$s->fetchAll();
head('Tugas');?>
<div class="grid g2"><?php foreach($rows as $r){$late=!$r['waktu']&&$r['deadline']<date('Y-m-d');
 echo "<div class='card mat'><span class='badge'>".e($r['mapel'])."</span> <span class='badge'>{$r['jenis']}</span><h3>".e($r['judul'])."</h3><p>".e($r['petunjuk'])."</p><p><small>Tenggat: <b>".date('d M Y',strtotime($r['deadline']))."</b></small></p>";
 if($r['nilai']!==null)echo "<p><span class='badge ok'>Nilai: {$r['nilai']}</span> ".e($r['komentar'])."</p>";
 elseif($r['waktu'])echo "<p><span class='badge ok'>Sudah dikumpulkan</span> <small>".date('d M H:i',strtotime($r['waktu']))."</small></p>";
 elseif($late)echo "<p><span class='badge' style='background:#ffe1e8;color:#c0204f'>Lewat tenggat</span></p>";
 if($r['nilai']===null)echo "<form method='post' enctype='multipart/form-data'><input type='hidden' name='tugas' value='{$r['id']}'><input name='catatan' placeholder='Jawaban / catatan' value='".e($r['catatan']??'')."'><input type='file' name='file'><button class='btn sm'>".($r['waktu']?'Kirim Ulang':'Kumpulkan')."</button></form>";
 echo "</div>";}
if(!$rows)echo "<p class='muted'>Belum ada tugas.</p>";?></div>
<?php foot();
