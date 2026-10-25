<?php require '../config.php';need('guru');
$tid=(int)($_GET['tugas']??$_POST['tugas']??0);
$t=$pdo->prepare("SELECT t.*,k.nama kelas FROM tugas t JOIN kelas k ON k.id=t.kelas_id WHERE t.id=?");$t->execute([$tid]);$t=$t->fetch();
if(!$t){header('Location: tugas.php');exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 foreach($_POST['n']??[] as $uid=>$v)$pdo->prepare("UPDATE pengumpulan SET nilai=?,komentar=? WHERE tugas_id=? AND user_id=?")->execute([$v===''?null:min(100,max(0,(int)$v)),trim($_POST['k'][$uid]??''),$tid,(int)$uid]);
 flash('Penilaian tersimpan');header("Location: pengumpulan.php?tugas=$tid");exit;}
$s=$pdo->prepare("SELECT u.id,u.nama,p.catatan,p.file,p.waktu,p.nilai,p.komentar FROM users u LEFT JOIN pengumpulan p ON p.user_id=u.id AND p.tugas_id=? WHERE u.kelas_id=? AND u.role='siswa' ORDER BY u.nama");$s->execute([$tid,$t['kelas_id']]);$rows=$s->fetchAll();
head('Pengumpulan Tugas');?>
<form method="post" class="card"><input type="hidden" name="tugas" value="<?=$tid?>"><h3><?=e($t['judul'])?> <span class="badge"><?=e($t['kelas'])?></span></h3>
<div class="tw"><table><tr><th>Siswa</th><th>Jawaban</th><th>Nilai</th><th>Komentar</th></tr>
<?php foreach($rows as $r){echo "<tr><td>".e($r['nama'])."</td><td>";
 if($r['waktu'])echo ($r['file']?"<a target='_blank' href='".BASE."/uploads/".e($r['file'])."'>📎 File</a> ":'').e($r['catatan'])."<br><small>".date('d M H:i',strtotime($r['waktu']))."</small>";else echo "<span class='badge'>Belum mengumpulkan</span>";
 echo "</td><td>".($r['waktu']?"<input type='number' min='0' max='100' name='n[{$r['id']}]' value='".e($r['nilai']??'')."'>":'-')."</td><td>".($r['waktu']?"<input name='k[{$r['id']}]' value='".e($r['komentar']??'')."'>":'')."</td></tr>";}?></table></div>
<button class="btn">💾 Simpan Nilai</button> <a class="btn gray" href="tugas.php">Kembali</a></form>
<?php foot();
