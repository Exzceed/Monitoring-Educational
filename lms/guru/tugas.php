<?php require '../config.php';need('guru');
if(isset($_GET['del'])){$pdo->prepare("DELETE FROM tugas WHERE id=?")->execute([(int)$_GET['del']]);flash('Tugas dihapus');header('Location: tugas.php');exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 $d=[(int)$_POST['kelas_id'],trim($_POST['judul']),trim($_POST['mapel']),$_POST['jenis'],$_POST['deadline'],trim($_POST['petunjuk'])];
 if(!empty($_POST['id']))$pdo->prepare("UPDATE tugas SET kelas_id=?,judul=?,mapel=?,jenis=?,deadline=?,petunjuk=? WHERE id=?")->execute([...$d,(int)$_POST['id']]);
 else $pdo->prepare("INSERT INTO tugas(kelas_id,judul,mapel,jenis,deadline,petunjuk) VALUES(?,?,?,?,?,?)")->execute($d);
 flash('Tugas tersimpan');header('Location: tugas.php');exit;}
$ed=null;if(isset($_GET['edit'])){$s=$pdo->prepare("SELECT * FROM tugas WHERE id=?");$s->execute([(int)$_GET['edit']]);$ed=$s->fetch();}
$rows=$pdo->query("SELECT t.*,k.nama kelas,(SELECT COUNT(*) FROM pengumpulan p WHERE p.tugas_id=t.id) masuk FROM tugas t JOIN kelas k ON k.id=t.kelas_id ORDER BY t.deadline DESC")->fetchAll();
head('Tugas');?>
<form method="post" class="card form"><h3><?=$ed?'✏️ Edit':'➕ Buat'?> Tugas</h3><input type="hidden" name="id" value="<?=e($ed['id']??'')?>">
<div><label>Kelas</label><select name="kelas_id"><?=opts(kelasList(),$ed['kelas_id']??null)?></select></div>
<div><label>Judul tugas</label><input name="judul" required value="<?=e($ed['judul']??'')?>"></div>
<div><label>Mata pelajaran</label><input name="mapel" required value="<?=e($ed['mapel']??'')?>"></div>
<div><label>Jenis</label><select name="jenis"><?php foreach(['Individu','Kelompok','Praktik','Proyek'] as $j)echo "<option".(($ed['jenis']??'')===$j?' selected':'').">$j</option>";?></select></div>
<div><label>Tenggat</label><input type="date" name="deadline" required value="<?=e($ed['deadline']??date('Y-m-d'))?>"></div>
<div class="full"><label>Petunjuk tugas</label><textarea name="petunjuk"><?=e($ed['petunjuk']??'')?></textarea></div>
<div class="full"><button class="btn"><?=$ed?'Update':'Simpan'?></button> <?php if($ed)echo "<a class='btn gray' href='tugas.php'>Batal</a>";?></div></form>
<div class="card"><div class="tw"><table><tr><th>Judul</th><th>Kelas</th><th>Jenis</th><th>Tenggat</th><th>Masuk</th><th>Aksi</th></tr>
<?php foreach($rows as $r)echo "<tr><td>".e($r['judul'])."<br><small>".e($r['mapel'])."</small></td><td><span class='badge'>".e($r['kelas'])."</span></td><td>{$r['jenis']}</td><td>".date('d M Y',strtotime($r['deadline']))."</td><td><a class='btn sm' href='pengumpulan.php?tugas={$r['id']}'>📥 {$r['masuk']}</a></td><td><a href='?edit={$r['id']}'>✏️</a> <a href='?del={$r['id']}' data-c='Hapus tugas beserta pengumpulannya?'>🗑️</a></td></tr>";?></table></div></div>
<?php foot();
