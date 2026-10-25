<?php
session_start();
// BASE otomatis: /lms di Laragon, kosong jika di root hosting
$_d = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
define('BASE', rtrim(substr(str_replace('\\', '/', __DIR__), strlen($_d)), '/'));
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
if (preg_match('/^(localhost|127\.|.*\.test)/', $host) || PHP_SAPI === 'cli')
    $db = ['localhost', 'lms_sd4', 'root', ''];
else
    $db = ['sql203.infinityfree.com', 'if0_43270037_lms', 'if0_if0_43270037', 'ZsLDrW9fjK']; // <-- ISI DATA DARI INFINITYFREE
$pdo = new PDO("mysql:host={$db[0]};dbname={$db[1]};charset=utf8mb4", $db[2], $db[3], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
const HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
function e($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}
function home()
{
    return BASE . (($_SESSION['u']['role'] ?? '') === 'guru' ? '/guru/' : '/siswa/');
}
function need($r)
{
    if (($_SESSION['u']['role'] ?? '') !== $r) {
        header('Location: ' . BASE . '/index.php');
        exit;
    }
}
function flash($m = null)
{
    if ($m) {
        $_SESSION['f'] = $m;
        return;
    }
    if (isset($_SESSION['f'])) {
        echo "<div class='toast'>" . e($_SESSION['f']) . "</div>";
        unset($_SESSION['f']);
    }
}
function kelasList()
{
    global $pdo;
    return $pdo->query("SELECT * FROM kelas ORDER BY nama")->fetchAll();
}
function opts($rows, $sel = null, $f = 'nama')
{
    $o = '';
    foreach ($rows as $r)
        $o .= "<option value='{$r['id']}'" . ($sel == $r['id'] ? ' selected' : '') . ">" . e($r[$f]) . "</option>";
    return $o;
}
function hariOpts($sel = null)
{
    $o = '';
    foreach (HARI as $h)
        $o .= "<option" . ($sel === $h ? ' selected' : '') . ">$h</option>";
    return $o;
}
function upload($f, $img = false)
{
    if (empty($f['name']) || $f['error'] !== 0)
        return null;
    $x = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $ok = $img ? ['jpg', 'jpeg', 'png', 'gif', 'webp'] : ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'mp4'];
    if (!in_array($x, $ok) || $f['size'] > 10 * 1024 * 1024)
        return null;
    $n = uniqid() . '.' . $x;
    move_uploaded_file($f['tmp_name'], __DIR__ . '/uploads/' . $n);
    return $n;
}
function head($t)
{
    $u = $_SESSION['u'] ?? null;
    $r = $u['role'] ?? '';
    $b = BASE;
    $cur = str_replace('pengumpulan.php', 'tugas.php', $_SERVER['SCRIPT_NAME']);
    $nav = [];
    if ($r === 'guru')
        $nav = ['Utama' => ['guru/index.php' => '🏠 Dashboard'], 'Kelola' => ['guru/siswa.php' => '👧 Siswa & Guru', 'guru/jadwal.php' => '🗓️ Jadwal Pelajaran', 'guru/nilai.php' => '🎓 Nilai Siswa', 'guru/absensi.php' => '✅ Kehadiran', 'guru/tugas.php' => '📝 Tugas', 'guru/materi.php' => '📚 Materi', 'guru/modul_pembelajaran.php' => '📘 Modul Pembelajaran', 'guru/dokumentasi.php' => '📸 Dokumentasi']];
    
if ($r === 'siswa')
    $nav = ['Menu' => [
        'siswa/index.php' => '🏠 Beranda',
        'siswa/tugas.php' => '📝 Tugas',
        'siswa/nilai.php' => '🎓 Nilai',
        'siswa/materi.php' => '📚 Materi',
        'siswa/modul.php' => '📘 Modul Pembelajaran',
        'siswa/dokumentasi.php' => '📸 Dokumentasi'
    ]];

    if ($u)
        $nav['Akun'] = ['profil.php' => '⚙️ Profil'];
    echo "<!doctype html><html lang='id'><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'><title>" . e($t) . " | ME LMS</title><link rel='stylesheet' href='$b/assets/style.css'><script>document.documentElement.dataset.theme=localStorage.t||'light'</script></head><body class='" . ($u ? 'app' : 'auth') . "'>";
    if ($u) {
        echo "<div class='shell'><aside id='sb'><div class='logo'><i>ME</i> <span>Monitoring Education</span></div>";
        foreach ($nav as $g => $items) {
            echo "<div class='grp'>$g</div>";
            foreach ($items as $k => $v)
                echo "<a href='$b/$k'" . (substr($cur, -strlen("/$k")) === "/$k" ? " class='act'" : '') . ">$v</a>";
        }
        echo "<a class='out' href='$b/logout.php'>🚪 Keluar</a></aside><div class='content'><header class='top'><button id='menu'>☰</button><h1>" . e($t) . "</h1><div class='usr'><button id='theme' title='Tema'>🌓</button><div class='av'>" . e(strtoupper(substr($u['nama'], 0, 1))) . "</div><div><b>" . e($u['nama']) . "</b><small>" . e(ucfirst($r)) . "</small></div></div></header><main>";
    } else
        echo "<main>";
    flash();
}
function foot()
{
    echo "</main>" . (isset($_SESSION['u']) ? "</div></div>" : '') . "<script src='" . BASE . "/assets/app.js'></script></body></html>";
}
