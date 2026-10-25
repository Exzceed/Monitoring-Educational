<?php require 'config.php';
if(isset($_SESSION['u'])){header('Location: '.home());exit;}
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $s=$pdo->prepare("SELECT * FROM users WHERE nomor=?");$s->execute([trim($_POST['nomor'])]);$u=$s->fetch();
 if($u&&password_verify($_POST['password'],$u['password'])){$_SESSION['u']=$u;header('Location: '.home());exit;}
 $err='NISN atau sandi salah';}
head('Masuk');?>
<form method="post" class="card box"><h1>🌷 ME LMS</h1><p class="muted">Masuk dengan NISN dan sandi</p>
<?php if($err)echo "<div class='err'>$err</div>";?>
<label>NISN</label><input name="nomor" required>
<label>Sandi</label><div class="pw"><input type="password" name="password" required><span class="eye">👁</span></div>
<button class="btn">Masuk</button><p class="muted">Belum punya akun? <a href="register.php">Daftar siswa</a></p></form>
<?php foot();
