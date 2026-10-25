<?php require '../config.php';need('guru');
if(isset($_GET['del'])){$s=$pdo->prepare("SELECT file FROM materi WHERE id=?");$s->execute([(int)$_GET['del']]);if($f=$s->fetchColumn())@unlink(__DIR__.'/../uploads/'.$f);
 $pdo->prepare("DELETE FROM materi WHERE id=?")->execute([(int)$_GET['del']]);flash('Materi dihapus');header('Location: materi.php');exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 $f=upload($_FILES['file']??[]);$k=$_POST['kelas_id']!==''?(int)$_POST['kelas_id']:null;$d=[$k,trim($_POST['judul']),trim($_POST['mapel']),trim($_POST['pertemuan']),trim($_POST['pembahasan']),trim($_POST['link'])];
 if(!empty($_POST['id'])){$id=(int)$_POST['id'];$pdo->prepare("UPDATE materi SET kelas_id=?,judul=?,mapel=?,pertemuan=?,pembahasan=?,link=? WHERE id=?")->execute([...$d,$id]);
  if($f)$pdo->prepare("UPDATE materi SET file=? WHERE id=?")->execute([$f,$id]);}
 else $pdo->prepare("INSERT INTO materi(kelas_id,judul,mapel,pertemuan,pembahasan,link,file) VALUES(?,?,?,?,?,?,?)")->execute([...$d,$f]);
 flash('Materi tersimpan');header('Location: materi.php');exit;}
$ed=null;if(isset($_GET['edit'])){$s=$pdo->prepare("SELECT * FROM materi WHERE id=?");$s->execute([(int)$_GET['edit']]);$ed=$s->fetch();}
$rows=$pdo->query("SELECT m.*,k.nama kelas FROM materi m LEFT JOIN kelas k ON k.id=m.kelas_id ORDER BY m.id DESC")->fetchAll();
head('Materi');?>
<form method="post" enctype="multipart/form-data" class="card form"><h3><?=$ed?'✏️ Edit':'➕ Tambah'?> Materi</h3><input type="hidden" name="id" value="<?=e($ed['id']??'')?>">
<div><label>Untuk kelas</label><select name="kelas_id"><option value="">Semua kelas</option><?=opts(kelasList(),$ed['kelas_id']??null)?></select></div>
<div><label>Judul materi</label><input name="judul" required value="<?=e($ed['judul']??'')?>"></div>
<div><label>Mata pelajaran</label><input name="mapel" required value="<?=e($ed['mapel']??'')?>"></div>
<div><label>Pertemuan</label><input name="pertemuan" placeholder="Pertemuan 1" value="<?=e($ed['pertemuan']??'')?>"></div>
<div><label>Link materi/video (opsional)</label><input name="link" value="<?=e($ed['link']??'')?>"></div>
<div><label>File (PDF/PPT/gambar/mp4, maks 10MB)</label><input type="file" name="file"></div>
<div class="full"><label>Pembahasan materi</label><textarea name="pembahasan" required><?=e($ed['pembahasan']??'')?></textarea></div>
<div class="full"><button class="btn"><?=$ed?'Update':'Simpan'?></button> <?php if($ed)echo "<a class='btn gray' href='materi.php'>Batal</a>";?></div></form>
<div class="card"><div class="tw"><table><tr><th>Materi</th><th>Mapel</th><th>Kelas</th><th>Lampiran</th><th>Aksi</th></tr>
<?php foreach($rows as $r)echo "<tr><td>".e($r['judul'])."<br><small>".e($r['pertemuan'])."</small></td><td>".e($r['mapel'])."</td><td><span class='badge'>".e($r['kelas']??'Semua')."</span></td><td>".($r['file']?"<a target='_blank' href='../uploads/".e($r['file'])."'>📎 File</a> ":'').($r['link']?"<a target='_blank' href='".e($r['link'])."'>🔗 Link</a>":'')."</td><td><a href='?edit={$r['id']}'>✏️</a> <a href='?del={$r['id']}' data-c='Hapus materi?'>🗑️</a></td></tr>";?></table></div></div>
<?php foot();
