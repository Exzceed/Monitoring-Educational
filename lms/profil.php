<?php require 'config.php';if(!isset($_SESSION['u'])){header('Location: index.php');exit;}
$u=$_SESSION['u'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 $s=$pdo->prepare("SELECT password FROM users WHERE id=?");$s->execute([$u['id']]);
 if(!password_verify($_POST['lama'],$s->fetchColumn()))flash('Sandi lama salah');
 elseif(strlen($_POST['baru'])<4)flash('Sandi baru minimal 4 karakter');
 else{$pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($_POST['baru'],PASSWORD_DEFAULT),$u['id']]);flash('Sandi berhasil diganti');}
 header('Location: profil.php');exit;}
head('Profil');?>
<div class="cols"><div class="card"><h3>👤 Data Akun</h3><p><b>Nama</b><br><?=e($u['nama'])?></p><p><b>NISN / Username</b><br><?=e($u['nomor'])?></p><p><b>Peran</b><br><?=e(ucfirst($u['role']))?></p></div>
<form method="post" class="card"><h3>🔒 Ganti Sandi</h3><label>Sandi lama</label><input type="password" name="lama" required><label>Sandi baru</label><input type="password" name="baru" required><button class="btn">Simpan</button></form></div>
<?php foot();
