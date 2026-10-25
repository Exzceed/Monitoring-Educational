<?php require '../config.php';need('guru');
if(isset($_GET['del'])){$pdo->prepare("DELETE FROM jadwal WHERE id=?")->execute([(int)$_GET['del']]);flash('Jadwal dihapus');header('Location: jadwal.php');exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 $d=[(int)$_POST['kelas_id'],$_POST['hari'],trim($_POST['waktu']),trim($_POST['mapel']),trim($_POST['guru'])];
 if(!empty($_POST['id']))$pdo->prepare("UPDATE jadwal SET kelas_id=?,hari=?,waktu=?,mapel=?,guru=? WHERE id=?")->execute([...$d,(int)$_POST['id']]);
 else $pdo->prepare("INSERT INTO jadwal(kelas_id,hari,waktu,mapel,guru) VALUES(?,?,?,?,?)")->execute($d);
 flash('Jadwal tersimpan');header('Location: jadwal.php');exit;}
$ed=null;if(isset($_GET['edit'])){$s=$pdo->prepare("SELECT * FROM jadwal WHERE id=?");$s->execute([(int)$_GET['edit']]);$ed=$s->fetch();}
$rows=$pdo->query("SELECT j.*,k.nama kelas FROM jadwal j JOIN kelas k ON k.id=j.kelas_id ORDER BY k.nama,FIELD(j.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'),j.waktu")->fetchAll();
head('Jadwal Pelajaran');?>
<form method="post" class="card form"><h3><?=$ed?'✏️ Edit':'➕ Tambah'?> Jadwal</h3><input type="hidden" name="id" value="<?=e($ed['id']??'')?>">
<div><label>Kelas</label><select name="kelas_id"><?=opts(kelasList(),$ed['kelas_id']??null)?></select></div>
<div><label>Hari</label><select name="hari"><?=hariOpts($ed['hari']??null)?></select></div>
<div><label>Waktu</label><input name="waktu" placeholder="07.00–08.00" required value="<?=e($ed['waktu']??'')?>"></div>
<div><label>Mata pelajaran</label><input name="mapel" required value="<?=e($ed['mapel']??'')?>"></div>
<div><label>Nama guru</label><input name="guru" required value="<?=e($ed['guru']??'')?>"></div>
<div class="full"><button class="btn"><?=$ed?'Update':'Simpan'?></button> <?php if($ed)echo "<a class='btn gray' href='jadwal.php'>Batal</a>";?></div></form>
<div class="card"><div class="tw"><table><tr><th>Kelas</th><th>Hari</th><th>Waktu</th><th>Mapel</th><th>Guru</th><th>Aksi</th></tr>
<?php foreach($rows as $r)echo "<tr><td><span class='badge'>".e($r['kelas'])."</span></td><td>{$r['hari']}</td><td>".e($r['waktu'])."</td><td>".e($r['mapel'])."</td><td>".e($r['guru'])."</td><td><a href='?edit={$r['id']}'>✏️</a> <a href='?del={$r['id']}' data-c='Hapus jadwal?'>🗑️</a></td></tr>";?></table></div></div>
<?php foot();
