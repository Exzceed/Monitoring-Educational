
<?php
require '../config.php';
need('siswa');

$u = $_SESSION['u'];
$kid = $u['kelas_id'] ?? null;

// Ambil modul untuk kelas siswa dan modul semua kelas
$sql = "
    SELECT m.*, k.nama AS nama_kelas
    FROM modul_pembelajaran m
    LEFT JOIN kelas k ON k.id = m.kelas_id
    WHERE m.kelas_id = ? OR m.kelas_id IS NULL
    ORDER BY m.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$kid]);
$modul = $stmt->fetchAll(PDO::FETCH_ASSOC);

head('Modul Pembelajaran');
?>

<div class="card">
    <h2>📚 Modul Pembelajaran</h2>
    <p class="muted">
        Halo, <?= e($u['nama']) ?>!
        Berikut materi pembelajaran untuk kelasmu.
    </p>
</div>

<div class="card">
    <a href="index.php" class="btn gray">← Kembali ke Beranda</a>
</div>

<?php if (empty($modul)): ?>

    <div class="card">
        <h3>📖 Belum Ada Modul</h3>
        <p class="muted">
            Belum ada modul pembelajaran yang tersedia
            untuk kelas kamu. Silakan periksa kembali nanti.
        </p>
    </div>

<?php else: ?>

    <?php foreach ($modul as $m): ?>
        <div class="card">

            <h3>📘 <?= e($m['judul']) ?></h3>

            <p>
                <b>Mata pelajaran:</b>
                <?= e($m['mapel'] ?? '-') ?>
            </p>

            <p>
                <b>Pertemuan / Bab:</b>
                <?= e($m['pertemuan'] ?: '-') ?>
            </p>

            <p>
                <b>Kelas:</b>
                <?= e($m['nama_kelas'] ?? 'Semua kelas') ?>
            </p>

            <hr>

            <h4>📝 Deskripsi dan Materi</h4>
            <div style="white-space: pre-wrap; line-height: 1.8;">
                <?= e($m['deskripsi']) ?>
            </div>

            <?php if (!empty($m['file'])): ?>
                <p style="margin-top: 18px;">
                    <a class="btn"
                       href="../uploads/<?= rawurlencode(basename($m['file'])) ?>"
                       target="_blank"
                       rel="noopener noreferrer">
                        📎 Buka / Unduh File Materi
                    </a>
                </p>
            <?php endif; ?>

            <?php if (!empty($m['link'])): ?>
                <p>
                    <a class="btn"
                       href="<?= e($m['link']) ?>"
                       target="_blank"
                       rel="noopener noreferrer">
                        ▶ Buka Video / Sumber Belajar
                    </a>
                </p>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>

<?php endif; ?>

<?php foot(); ?>
