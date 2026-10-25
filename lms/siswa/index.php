<?php require '../config.php';
need('siswa');
$u = $_SESSION['u'];
$kid = $u['kelas_id'];
$today = date('Y-m-d');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = in_array($_POST['status'], ['Hadir', 'Sakit', 'Izin']) ? $_POST['status'] : 'Hadir';
    $pdo->prepare("INSERT INTO absensi(user_id,tanggal,status,catatan) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE catatan=IF(status='Tanpa keterangan',VALUES(catatan),catatan),status=IF(status='Tanpa keterangan',VALUES(status),status)")->execute([$u['id'], $today, $st, trim($_POST['catatan'])]);
    flash('Absensi hari ini tercatat: ' . $st);
    header('Location: index.php');
    exit;
}
$q = function ($sql, $p) use ($pdo) {
    $s = $pdo->prepare($sql);
    $s->execute($p);
    return $s->fetchAll(); };
$ab = $q("SELECT * FROM absensi WHERE user_id=? ORDER BY tanggal DESC", [$u['id']]);
$hi = null;
foreach ($ab as $r)
    if ($r['tanggal'] === $today)
        $hi = $r;
$cnt = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Tanpa keterangan' => 0];
foreach ($ab as $r)
    $cnt[$r['status']]++;
$tot = count($ab);
$pct = $tot ? round($cnt['Hadir'] / $tot * 100) : 0;
$jd = $q("SELECT * FROM jadwal WHERE kelas_id=? ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'),waktu", [$kid]);
$hr = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][date('N')];
$blm = $q("SELECT COUNT(*) n FROM tugas t WHERE t.kelas_id=? AND NOT EXISTS(SELECT 1 FROM pengumpulan p WHERE p.tugas_id=t.id AND p.user_id=?)", [$kid, $u['id']])[0]['n'];
head('Beranda'); ?>
<div class="hero card">
    <div>
        <h2>Halo, <?= e($u['nama']) ?> 👋</h2>
        <p>NISN <?= e($u['nomor']) ?> · Kelas
            <?= e($q("SELECT nama FROM kelas WHERE id=?", [$kid])[0]['nama'] ?? '-') ?><br>Kehadiranmu <b><?= $pct ?>%</b> ·
            Tugas belum dikumpulkan: <b><?= $blm ?></b></p>
    </div>
    <div class="ring" style="--p:<?= $pct ?>"><span><?= $pct ?>%</span></div>
</div>
<div class="card">
    <h3>✅ Absen Hari Ini (<?= $hr ?>, <?= date('d M Y') ?>)</h3>
    <?php if ($hi && $hi['status'] !== 'Tanpa keterangan')
        echo "<p>Absensimu sudah tercatat: <span class='chip " . str_replace(' ', '', $hi['status']) . " on'>" . e($hi['status']) . "</span></p>";
    else { ?>
        <form method="post" class="af"><select name="status">
                <option>Hadir</option>
                <option>Sakit</option>
                <option>Izin</option>
            </select><input name="catatan" placeholder="Catatan (opsional)"><button class="btn sm">Kirim Absen</button>
        </form><?php } ?>
</div>
<div class="grid">
    <?php foreach (['Hadir' => '✅', 'Sakit' => '🤒', 'Izin' => '📩', 'Tanpa keterangan' => '❌'] as $s => $i)
        echo "<div class='card stat'><div class='ic'>$i</div><span class='count' data-n='{$cnt[$s]}'>0</span><p>$s</p></div>"; ?>
</div>
<div class="cols">
    <div class="card">
        <h3>🗓️ Jadwal Pelajaran</h3>
        <div class="tw">
            <table>
                <tr>
                    <th>Hari</th>
                    <th>Waktu</th>
                    <th>Mapel</th>
                    <th>Guru</th>
                </tr>
                <?php foreach ($jd as $r)
                    echo "<tr" . ($r['hari'] === $hr ? " style='font-weight:700'" : '') . "><td>{$r['hari']}</td><td>" . e($r['waktu']) . "</td><td>" . e($r['mapel']) . "</td><td>" . e($r['guru']) . "</td></tr>"; ?>
            </table>
        </div>
    </div>
    <div class="card">
        <h3>🗓️ Riwayat Kehadiran</h3>
        <div class="tw">
            <table>
                <tr>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
                <?php foreach (array_slice($ab, 0, 8) as $r)
                    echo "<tr><td>" . date('d M Y', strtotime($r['tanggal'])) . "</td><td><span class='chip " . str_replace(' ', '', $r['status']) . " on'>{$r['status']}</span></td></tr>"; ?>
            </table>
        </div>
    </div>
</div>
<!-- Menu Modul Pembelajaran -->
<div class="card">
    <h3>📚 Modul Pembelajaran</h3>
    <p>
        Pelajari materi sesuai kelasmu, baca penjelasan,
        buka file materi, dan tonton video pembelajaran.
    </p>
    <a href="modul.php" class="btn">
        Buka Modul Pembelajaran →
    </a>
</div>

<?php foot(); ?>

<?php foot();
