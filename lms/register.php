
<?php
require 'config.php';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = trim($_POST['nama'] ?? '');
    $i = trim($_POST['nomor'] ?? '');
    $k = (int) ($_POST['kelas_id'] ?? 0);
    $p = $_POST['password'] ?? '';

    if (!preg_match('/^\d{5,20}$/', $i)) {
        $err = 'NISN harus berupa angka 5–20 digit';
    } elseif (strlen($p) < 4) {
        $err = 'Sandi minimal 4 karakter';
    } elseif ($n === '' || $k <= 0) {
        $err = 'Nama dan kelas wajib diisi';
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users
                (nama, nomor, password, role, kelas_id)
                VALUES (?, ?, ?, 'siswa', ?)"
            );

            $stmt->execute([
                $n,
                $i,
                password_hash($p, PASSWORD_DEFAULT),
                $k
            ]);

            flash('Pendaftaran berhasil, silakan masuk');
            header('Location: index.php');
            exit;
        } catch (PDOException $x) {
            if ($x->getCode() === '23000') {
                $err = 'NISN sudah terdaftar';
            } else {
                $err = 'Pendaftaran gagal. Periksa konfigurasi database.';
            }
        }
    }
}

head('Daftar');
?>

<form method="post" class="card box">
    <h1>📝 Daftar Siswa</h1>

    <?php if ($err): ?>
        <div class="err">
            <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <label>Nama Siswa</label>
    <input name="nama" required>

    <label>NISN</label>
    <input name="nomor" required inputmode="numeric"
           maxlength="20" pattern="[0-9]{5,20}">

    <label>Kelas</label>
    <select name="kelas_id" required>
        <option value="">- Pilih kelas -</option>
        <?= opts(kelasList()) ?>
    </select>

    <label>Buat Sandi</label>
    <div class="pw">
        <input type="password" name="password" required>
        <span class="eye">👁</span>
    </div>

    <button class="btn" type="submit">Daftar</button>

    <p class="muted">
        Sudah punya akun? <a href="index.php">Masuk</a>
    </p>
</form>

<?php foot(); ?>
