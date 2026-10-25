
<?php
require '../config.php';
need('guru');

$me = $_SESSION['u']['id'];

// Hapus pengguna
if (isset($_GET['del'])) {
    $id = (int) $_GET['del'];

    if ($id !== $me) {
        $stmt = $pdo->prepare(
            "DELETE FROM users WHERE id = ? AND id <> ?"
        );
        $stmt->execute([$id, $me]);
        flash('Pengguna berhasil dihapus');
    }

    header('Location: siswa.php');
    exit;
}

// Tambah atau edit pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $n  = trim($_POST['nama'] ?? '');
    $i  = trim($_POST['nomor'] ?? '');
    $p  = $_POST['password'] ?? '';
    $roleInput = $_POST['role'] ?? 'siswa';

    // Pengguna tidak boleh mengubah peran dirinya sendiri menjadi siswa
    $r = ($roleInput === 'guru' || $id === $me)
        ? 'guru'
        : 'siswa';

    $k = $r === 'guru'
        ? null
        : (int) ($_POST['kelas_id'] ?? 0);

    try {
        if ($id) {
            // Update data pengguna
            $stmt = $pdo->prepare(
                "UPDATE users
                 SET nama = ?, nomor = ?, role = ?, kelas_id = ?
                 WHERE id = ?"
            );

            $stmt->execute([$n, $i, $r, $k, $id]);

            // Update sandi jika diisi
            if ($p !== '') {
                $stmt = $pdo->prepare(
                    "UPDATE users SET password = ? WHERE id = ?"
                );

                $stmt->execute([
                    password_hash($p, PASSWORD_DEFAULT),
                    $id
                ]);
            }
        } else {
            // Tambah pengguna baru
            $stmt = $pdo->prepare(
                "INSERT INTO users
                 (nama, nomor, password, role, kelas_id)
                 VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $n,
                $i,
                password_hash($p !== '' ? $p : $i, PASSWORD_DEFAULT),
                $r,
                $k
            ]);
        }

        flash('Data berhasil disimpan');
    } catch (PDOException $x) {
        flash('NISN/username sudah terdaftar');
    }

    header('Location: siswa.php');
    exit;
}

// Ambil data untuk diedit
$ed = null;

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([(int) $_GET['edit']]);
    $ed = $stmt->fetch();
}

// Ambil daftar pengguna
$rows = $pdo->query(
    "SELECT u.*, k.nama AS kelas
     FROM users u
     LEFT JOIN kelas k ON k.id = u.kelas_id
     ORDER BY u.role, k.nama, u.nama"
)->fetchAll();

head('Siswa & Guru');
?>

<form method="post" class="card form">
    <h3>
        <?= $ed ? '✏️ Edit' : '➕ Tambah' ?> Pengguna
    </h3>

    <input
        type="hidden"
        name="id"
        value="<?= e($ed['id'] ?? '') ?>"
    >

    <div>
        <label>Nama</label>
        <input
            name="nama"
            required
            value="<?= e($ed['nama'] ?? '') ?>"
        >
    </div>

    <div>
        <label>NISN / Username Guru</label>
        <input
            name="nomor"
            required
            value="<?= e($ed['nomor'] ?? '') ?>"
        >
    </div>

    <div>
        <label>Peran</label>
        <select name="role">
            <option value="siswa"
                <?= ($ed['role'] ?? '') === 'siswa' ? 'selected' : '' ?>>
                Siswa
            </option>

            <option value="guru"
                <?= ($ed['role'] ?? '') === 'guru' ? 'selected' : '' ?>>
                Guru
            </option>
        </select>
    </div>

    <div>
        <label>Kelas (Siswa)</label>
        <select name="kelas_id">
            <option value="">- Pilih kelas -</option>
            <?= opts(kelasList(), $ed['kelas_id'] ?? null) ?>
        </select>
    </div>

    <div>
        <label>Sandi</label>
        <input
            type="password"
            name="password"
            placeholder="<?= $ed
                ? 'Kosongkan jika tidak diganti'
                : 'Kosong = sama dengan NISN' ?>"
        >
    </div>

    <div class="full">
        <button class="btn" type="submit">
            <?= $ed ? 'Update' : 'Tambah' ?>
        </button>

        <?php if ($ed): ?>
            <a class="btn gray" href="siswa.php">Batal</a>
        <?php endif; ?>
    </div>
</form>

<input
    class="search"
    placeholder="🔍 Cari nama / NISN..."
    data-t="tbl"
>

<div class="card">
    <div class="tw">
        <table id="tbl">
            <tr>
                <th>Nama</th>
                <th>NISN / Username</th>
                <th>Peran</th>
                <th>Kelas</th>
                <th>Aksi</th>
            </tr>

            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= e($r['nama']) ?></td>
                    <td><?= e($r['nomor']) ?></td>
                    <td>
                        <span class="badge">
                            <?= e($r['role']) ?>
                        </span>
                    </td>
                    <td><?= e($r['kelas'] ?? '-') ?></td>
                    <td>
                        <a href="?edit=<?= (int) $r['id'] ?>">✏️</a>

                        <?php if ($r['id'] != $me): ?>
                            <a
                                href="?del=<?= (int) $r['id'] ?>"
                                data-c="Hapus pengguna ini beserta nilai & absensinya?"
                            >🗑️</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php foot(); ?>
