<?php require '../config.php';need('guru');$kl=kelasList();
$kid=(int)($_GET['kelas']??$_POST['kelas']??($kl[0]['id']??0));$tgl=$_GET['tanggal']??$_POST['tanggal']??date('Y-m-d');
$ST=['Hadir','Sakit','Izin','Tanpa keterangan'];
if(isset($_GET['hapus'])){$pdo->prepare("DELETE FROM absensi WHERE user_id=? AND tanggal=?")->execute([(int)$_GET['hapus'],$tgl]);flash('Data kehadiran dihapus');header("Location: absensi.php?kelas=$kid&tanggal=$tgl");exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(isset($_POST['semua']))$pdo->prepare("INSERT INTO absensi(user_id,tanggal,status) SELECT id,?,'Hadir' FROM users WHERE kelas_id=? AND role='siswa' ON DUPLICATE KEY UPDATE status='Hadir'")->execute([$tgl,$kid]);
 else foreach($_POST['st']??[] as $uid=>$st){if(!in_array($st,$ST))continue;$pdo->prepare("INSERT INTO absensi(user_id,tanggal,status,catatan) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status),catatan=VALUES(catatan)")->execute([(int)$uid,$tgl,$st,trim($_POST['cat'][$uid]??'')]);}
 flash('Kehadiran tersimpan');header("Location: absensi.php?kelas=$kid&tanggal=$tgl");exit;}
$s=$pdo->prepare("SELECT u.id,u.nama,a.status,a.catatan FROM users u LEFT JOIN absensi a ON a.user_id=u.id AND a.tanggal=? WHERE u.kelas_id=? AND u.role='siswa' ORDER BY u.nama");$s->execute([$tgl,$kid]);$rows=$s->fetchAll();
head('Kehadiran');?>
<form class="card form"><div><label>Kelas</label><select name="kelas" onchange="this.form.submit()"><?=opts($kl,$kid)?></select></div><div><label>Tanggal</label><input type="date" name="tanggal" value="<?=e($tgl)?>" onchange="this.form.submit()"></div></form>
<form method="post" class="card"><input type="hidden" name="kelas" value="<?=$kid?>"><input type="hidden" name="tanggal" value="<?=e($tgl)?>">
<div class="tw"><table><tr><th>Nama</th><th>Status (bisa diubah)</th><th>Catatan</th><th></th></tr>
<?php foreach($rows as $r){echo "<tr><td>".e($r['nama'])."</td><td>";
 foreach($ST as $st)echo "<label class='chip ".str_replace(' ','',$st)."'><input type='radio' name='st[{$r['id']}]' value='$st'".(($r['status']??'Tanpa keterangan')==$st?' checked':'')."><span>$st</span></label>";
 echo "</td><td><input name='cat[{$r['id']}]' value='".e($r['catatan']??'')."' placeholder='Opsional'></td><td>".($r['status']?"<a href='?kelas=$kid&tanggal=".e($tgl)."&hapus={$r['id']}' data-c='Hapus data kehadiran ini?'>🗑️</a>":'')."</td></tr>";}?></table></div>
<?=$rows?"<button class='btn'>💾 Simpan Perubahan</button> <button class='btn gray' name='semua' value='1' data-c='Tandai semua siswa Hadir?'>✅ Tandai Semua Hadir</button>":"<p class='muted'>Belum ada siswa di kelas ini.</p>"?></form>
<?php foot();
