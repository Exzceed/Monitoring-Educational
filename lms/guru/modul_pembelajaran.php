<?php
require '../config.php'; need('guru');

if (isset($_GET['del'])) {
    $id = (int) $_GET['del'];
    $q = $pdo->prepare('SELECT file FROM modul_pembelajaran WHERE id=?');
    $q->execute([$id]);
    if ($f = $q->fetchColumn()) @unlink(__DIR__ . '/../uploads/' . basename($f));
    $pdo->prepare('DELETE FROM modul_pembelajaran WHERE id=?')->execute([$id]);
    flash('Modul pembelajaran dihapus'); header('Location: modul_pembelajaran.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $kelas = ($_POST['kelas_id'] ?? '') !== '' ? (int) $_POST['kelas_id'] : null;
    $judul = trim($_POST['judul'] ?? '');
    $mapel = trim($_POST['mapel'] ?? '');
    $pertemuan = trim($_POST['pertemuan'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $link = trim($_POST['link'] ?? '');
    if ($judul === '' || $mapel === '' || $deskripsi === '') {
        flash('Judul, mata pelajaran, dan deskripsi wajib diisi'); header('Location: modul_pembelajaran.php'); exit;
    }
    if ($link !== '' && !filter_var($link, FILTER_VALIDATE_URL)) {
        flash('Format link tidak valid'); header('Location: modul_pembelajaran.php'); exit;
    }
    $file = upload($_FILES['file'] ?? []);
    if ($id) {
        $pdo->prepare('UPDATE modul_pembelajaran SET kelas_id=?, judul=?, mapel=?, pertemuan=?, deskripsi=?, link=? WHERE id=?')
            ->execute([$kelas, $judul, $mapel, $pertemuan, $deskripsi, $link, $id]);
        if ($file) {
            $q = $pdo->prepare('SELECT file FROM modul_pembelajaran WHERE id=?'); $q->execute([$id]);
            if ($old = $q->fetchColumn()) @unlink(__DIR__ . '/../uploads/' . basename($old));
            $pdo->prepare('UPDATE modul_pembelajaran SET file=? WHERE id=?')->execute([$file, $id]);
        }
        flash('Modul pembelajaran berhasil diperbarui');
    } else {
        $pdo->prepare('INSERT INTO modul_pembelajaran(kelas_id,judul,mapel,pertemuan,deskripsi,link,file) VALUES(?,?,?,?,?,?,?)')
            ->execute([$kelas, $judul, $mapel, $pertemuan, $deskripsi, $link, $file]);
        flash('Modul pembelajaran berhasil ditambahkan');
    }
    header('Location: modul_pembelajaran.php'); exit;
}

$ed = null;
if (isset($_GET['edit'])) { $q = $pdo->prepare('SELECT * FROM modul_pembelajaran WHERE id=?'); $q->execute([(int) $_GET['edit']]); $ed = $q->fetch(); }
$rows = $pdo->query('SELECT m.*, k.nama AS kelas FROM modul_pembelajaran m LEFT JOIN kelas k ON k.id=m.kelas_id ORDER BY m.id DESC')->fetchAll();
head('Modul Pembelajaran');
?>
<div class="card"><h3>📘 Kelola Modul Pembelajaran</h3><p class="muted">Tambahkan modul berisi materi, panduan belajar, file, atau tautan video untuk kelas yang dipilih.</p></div>
<form method="post" enctype="multipart/form-data" class="card form">
<h3><?= $ed ? '✏️ Edit Modul' : '➕ Tambah Modul Baru' ?></h3>
<input type="hidden" name="id" value="<?= e($ed['id'] ?? '') ?>">
<div><label>Untuk kelas</label><select name="kelas_id"><option value="">Semua kelas</option><?= opts(kelasList(), $ed['kelas_id'] ?? null) ?></select></div>
<div><label>Judul modul</label><input name="judul" required value="<?= e($ed['judul'] ?? '') ?>" placeholder="Contoh: Modul Mengenal Tumbuhan"></div>
<div><label>Mata pelajaran</label><input name="mapel" required value="<?= e($ed['mapel'] ?? '') ?>" placeholder="Contoh: IPAS"></div>
<div><label>Pertemuan / bab</label><input name="pertemuan" value="<?= e($ed['pertemuan'] ?? '') ?>" placeholder="Bab 1 atau Pertemuan 1"></div>
<div><label>Link video / sumber (opsional)</label><input type="url" name="link" value="<?= e($ed['link'] ?? '') ?>" placeholder="https://..."></div>
<div><label>File modul (PDF, dokumen, PPT, gambar, MP4; maks. 10 MB)</label><input type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.mp4"></div>
<div class="full"><label>Deskripsi / isi modul</label><textarea name="deskripsi" required placeholder="Tuliskan tujuan belajar, ringkasan materi, dan petunjuk untuk siswa..."><?= e($ed['deskripsi'] ?? '') ?></textarea></div>
<div class="full"><button class="btn"><?= $ed ? 'Simpan Perubahan' : 'Simpan Modul' ?></button> <?php if ($ed): ?><a class="btn gray" href="modul_pembelajaran.php">Batal</a><?php endif; ?></div>
</form>
<div class="card"><h3>📚 Daftar Modul</h3><div class="tw"><table><tr><th>Judul modul</th><th>Mapel</th><th>Kelas</th><th>File / Link</th><th>Aksi</th></tr>
<?php foreach ($rows as $r): ?>
<tr><td><b><?= e($r['judul']) ?></b><br><small><?= e($r['pertemuan']) ?></small><br><small><?= e($r['deskripsi']) ?></small></td><td><?= e($r['mapel']) ?></td><td><span class="badge"><?= e($r['kelas'] ?? 'Semua kelas') ?></span></td><td><?php if ($r['file']): ?><a target="_blank" rel="noopener" href="../uploads/<?= rawurlencode(basename($r['file'])) ?>">📎 Buka file</a><?php endif; ?> <?php if ($r['link']): ?><a target="_blank" rel="noopener" href="<?= e($r['link']) ?>">🔗 Buka link</a><?php endif; ?><?php if (!$r['file'] && !$r['link']): ?>—<?php endif; ?></td><td><a href="?edit=<?= (int)$r['id'] ?>">✏️</a> <a href="?del=<?= (int)$r['id'] ?>" data-c="Hapus modul ini?">🗑️</a></td></tr>
<?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="5">Belum ada modul pembelajaran.</td></tr><?php endif; ?>
</table></div></div>
<?php foot(); ?>
