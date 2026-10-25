<?php require '../config.php';need('guru');
if(isset($_GET['del'])){$s=$pdo->prepare("SELECT foto FROM dokumentasi WHERE id=?");$s->execute([(int)$_GET['del']]);if($f=$s->fetchColumn())@unlink(__DIR__.'/../uploads/'.$f);
 $pdo->prepare("DELETE FROM dokumentasi WHERE id=?")->execute([(int)$_GET['del']]);flash('Dokumentasi dihapus');header('Location: dokumentasi.php');exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 $f=upload($_FILES['foto']??[],true);$k=$_POST['kelas_id']!==''?(int)$_POST['kelas_id']:null;$d=[$k,$_POST['tanggal'],trim($_POST['judul']),trim($_POST['deskripsi']),trim($_POST['rekomendasi'])];
 if(!empty($_POST['id'])){$id=(int)$_POST['id'];$pdo->prepare("UPDATE dokumentasi SET kelas_id=?,tanggal=?,judul=?,deskripsi=?,rekomendasi=? WHERE id=?")->execute([...$d,$id]);
  if($f)$pdo->prepare("UPDATE dokumentasi SET foto=? WHERE id=?")->execute([$f,$id]);}
 else $pdo->prepare("INSERT INTO dokumentasi(kelas_id,tanggal,judul,deskripsi,rekomendasi,foto) VALUES(?,?,?,?,?,?)")->execute([...$d,$f]);
 flash($f||empty($_FILES['foto']['name'])?'Dokumentasi tersimpan':'Tersimpan, tetapi foto ditolak (format JPG/PNG/WEBP, maks 10MB)');header('Location: dokumentasi.php');exit;}
$ed=null;if(isset($_GET['edit'])){$s=$pdo->prepare("SELECT * FROM dokumentasi WHERE id=?");$s->execute([(int)$_GET['edit']]);$ed=$s->fetch();}
$rows=$pdo->query("SELECT d.*,k.nama kelas FROM dokumentasi d LEFT JOIN kelas k ON k.id=d.kelas_id ORDER BY d.tanggal DESC,d.id DESC")->fetchAll();
head('Dokumentasi');?>
<form method="post" enctype="multipart/form-data" class="card form"><h3><?=$ed?'✏️ Edit':'➕ Tambah'?> Dokumentasi</h3><input type="hidden" name="id" value="<?=e($ed['id']??'')?>">
<div><label>Untuk kelas</label><select name="kelas_id"><option value="">Semua kelas</option><?=opts(kelasList(),$ed['kelas_id']??null)?></select></div>
<div><label>Tanggal kegiatan</label><input type="date" name="tanggal" required value="<?=e($ed['tanggal']??date('Y-m-d'))?>"></div>
<div><label>Judul kegiatan</label><input name="judul" required value="<?=e($ed['judul']??'')?>"></div>
<div><label>Foto (JPG/PNG/WEBP, maks 10MB)</label><input type="file" name="foto" accept="image/*"></div>
<div class="full"><label>Deskripsi kegiatan</label><textarea name="deskripsi"><?=e($ed['deskripsi']??'')?></textarea></div>
<div class="full"><label>Rekomendasi guru untuk orang tua</label><textarea name="rekomendasi"><?=e($ed['rekomendasi']??'')?></textarea></div>
<div class="full"><button class="btn"><?=$ed?'Update':'Simpan'?></button> <?php if($ed)echo "<a class='btn gray' href='dokumentasi.php'>Batal</a>";?></div></form>
<div class="grid g2"><?php foreach($rows as $r)echo "<div class='card gal'>".($r['foto']?"<img src='../uploads/".e($r['foto'])."' alt=''>":"<div class='noimg'>📷</div>")."<span class='badge'>".e($r['kelas']??'Semua')."</span> <small>".date('d M Y',strtotime($r['tanggal']))."</small><h3>".e($r['judul'])."</h3><p>".e($r['deskripsi'])."</p><p><b>Rekomendasi:</b> ".e($r['rekomendasi'])."</p><a href='?edit={$r['id']}'>✏️ Edit</a> · <a href='?del={$r['id']}' data-c='Hapus dokumentasi?'>🗑️ Hapus</a></div>";?></div>
<?php foot();
