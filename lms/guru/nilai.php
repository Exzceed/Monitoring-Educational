<?php require '../config.php';need('guru');
if(isset($_GET['del'])){$pdo->prepare("DELETE FROM nilai WHERE id=?")->execute([(int)$_GET['del']]);flash('Nilai dihapus');header('Location: nilai.php');exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 $h=min(100,max(0,(int)$_POST['harian']));$u=min(100,max(0,(int)$_POST['ulangan']));
 $d=[(int)$_POST['user_id'],trim($_POST['mapel']),$h,$u,(int)round(($h+$u)/2),trim($_POST['catatan'])];
 if(!empty($_POST['id']))$pdo->prepare("UPDATE nilai SET user_id=?,mapel=?,harian=?,ulangan=?,akhir=?,catatan=? WHERE id=?")->execute([...$d,(int)$_POST['id']]);
 else $pdo->prepare("INSERT INTO nilai(user_id,mapel,harian,ulangan,akhir,catatan) VALUES(?,?,?,?,?,?)")->execute($d);
 flash('Nilai tersimpan');header('Location: nilai.php');exit;}
$ed=null;if(isset($_GET['edit'])){$s=$pdo->prepare("SELECT * FROM nilai WHERE id=?");$s->execute([(int)$_GET['edit']]);$ed=$s->fetch();}
$sis=$pdo->query("SELECT u.id,CONCAT(u.nama,' (',IFNULL(k.nama,'-'),')') t FROM users u LEFT JOIN kelas k ON k.id=u.kelas_id WHERE role='siswa' ORDER BY k.nama,u.nama")->fetchAll();
$rows=$pdo->query("SELECT n.*,u.nama,k.nama kelas FROM nilai n JOIN users u ON u.id=n.user_id LEFT JOIN kelas k ON k.id=u.kelas_id ORDER BY k.nama,u.nama,n.mapel")->fetchAll();
head('Nilai Siswa');?>
<form method="post" class="card form"><h3><?=$ed?'✏️ Edit':'➕ Input'?> Nilai</h3><input type="hidden" name="id" value="<?=e($ed['id']??'')?>">
<div><label>Siswa</label><select name="user_id" required><?=opts($sis,$ed['user_id']??null,'t')?></select></div>
<div><label>Mata pelajaran</label><input name="mapel" required value="<?=e($ed['mapel']??'')?>"></div>
<div><label>Nilai harian (0–100)</label><input type="number" name="harian" min="0" max="100" required value="<?=e($ed['harian']??'')?>"></div>
<div><label>Ulangan/praktik (0–100)</label><input type="number" name="ulangan" min="0" max="100" required value="<?=e($ed['ulangan']??'')?>"></div>
<div class="full"><label>Catatan / rekomendasi (nilai akhir = rata-rata otomatis)</label><textarea name="catatan"><?=e($ed['catatan']??'')?></textarea></div>
<div class="full"><button class="btn"><?=$ed?'Update':'Simpan'?></button> <?php if($ed)echo "<a class='btn gray' href='nilai.php'>Batal</a>";?></div></form>
<input class="search" placeholder="🔍 Cari siswa / mapel..." data-t="tbl">
<div class="card"><div class="tw"><table id="tbl"><tr><th>Siswa</th><th>Mapel</th><th>Harian</th><th>Ulangan</th><th>Akhir</th><th>Catatan</th><th>Aksi</th></tr>
<?php foreach($rows as $r)echo "<tr><td>".e($r['nama'])." <span class='badge'>".e($r['kelas'])."</span></td><td>".e($r['mapel'])."</td><td>{$r['harian']}</td><td>{$r['ulangan']}</td><td><b>{$r['akhir']}</b></td><td>".e($r['catatan'])."</td><td><a href='?edit={$r['id']}'>✏️</a> <a href='?del={$r['id']}' data-c='Hapus nilai?'>🗑️</a></td></tr>";?></table></div></div>
<?php foot();
