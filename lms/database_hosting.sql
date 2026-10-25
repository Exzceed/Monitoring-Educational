CREATE TABLE kelas(id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(30) NOT NULL);
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) NOT NULL, nomor VARCHAR(30) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL, role ENUM('guru','siswa') DEFAULT 'siswa', kelas_id INT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(kelas_id) REFERENCES kelas(id) ON DELETE SET NULL);
CREATE TABLE jadwal(id INT AUTO_INCREMENT PRIMARY KEY, kelas_id INT NOT NULL, hari VARCHAR(10), waktu VARCHAR(20), mapel VARCHAR(80), guru VARCHAR(80),
 FOREIGN KEY(kelas_id) REFERENCES kelas(id) ON DELETE CASCADE);
CREATE TABLE nilai(id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, mapel VARCHAR(80), harian INT, ulangan INT, akhir INT, catatan VARCHAR(255),
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE);
CREATE TABLE absensi(id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, tanggal DATE NOT NULL,
 status ENUM('Hadir','Sakit','Izin','Tanpa keterangan') DEFAULT 'Hadir', catatan VARCHAR(150), UNIQUE KEY uq(user_id,tanggal),
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE);
CREATE TABLE tugas(id INT AUTO_INCREMENT PRIMARY KEY, kelas_id INT NOT NULL, judul VARCHAR(150), mapel VARCHAR(80),
 jenis ENUM('Individu','Kelompok','Praktik','Proyek') DEFAULT 'Individu', deadline DATE, petunjuk TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(kelas_id) REFERENCES kelas(id) ON DELETE CASCADE);
CREATE TABLE pengumpulan(id INT AUTO_INCREMENT PRIMARY KEY, tugas_id INT NOT NULL, user_id INT NOT NULL, catatan TEXT, file VARCHAR(255),
 waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP, nilai INT NULL, komentar VARCHAR(255) NULL, UNIQUE KEY uq(tugas_id,user_id),
 FOREIGN KEY(tugas_id) REFERENCES tugas(id) ON DELETE CASCADE, FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE);
CREATE TABLE materi(id INT AUTO_INCREMENT PRIMARY KEY, kelas_id INT NULL, judul VARCHAR(150), mapel VARCHAR(80), pertemuan VARCHAR(40),
 pembahasan TEXT, link VARCHAR(255), file VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE modul_pembelajaran(id INT AUTO_INCREMENT PRIMARY KEY, kelas_id INT NULL, judul VARCHAR(150) NOT NULL, mapel VARCHAR(80) NOT NULL, pertemuan VARCHAR(40) NULL, deskripsi TEXT NOT NULL, link VARCHAR(255) NULL, file VARCHAR(255) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(kelas_id) REFERENCES kelas(id) ON DELETE SET NULL);
CREATE TABLE dokumentasi(id INT AUTO_INCREMENT PRIMARY KEY, kelas_id INT NULL, tanggal DATE, judul VARCHAR(150), deskripsi TEXT, rekomendasi TEXT,
 foto VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
INSERT INTO kelas(nama) VALUES('4A'),('4B');
-- Sandi semua akun contoh: password (WAJIB diganti)
INSERT INTO users(nama,nomor,password,role,kelas_id) VALUES
('Guru Kelas IV','guru','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','guru',NULL,NULL),
('Nabila Putri','0000000001','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','siswa',1,'Rina Sari');
INSERT INTO jadwal(kelas_id,hari,waktu,mapel,guru) VALUES(1,'Senin','07.00–08.00','Bahasa Indonesia','Ibu Siti'),(1,'Senin','08.00–09.00','Matematika','Ibu Rina'),(1,'Selasa','07.00–08.00','IPAS','Pak Budi');
INSERT INTO nilai(user_id,mapel,harian,ulangan,akhir,catatan) VALUES(2,'Matematika',82,85,84,'Latihan soal secara rutin');
INSERT INTO tugas(kelas_id,judul,mapel,jenis,deadline,petunjuk) VALUES(1,'Latihan soal halaman 12–15','Matematika','Individu',DATE_ADD(CURDATE(),INTERVAL 3 DAY),'Kerjakan soal pada buku latihan, lalu foto dan kumpulkan.');
INSERT INTO materi(kelas_id,judul,mapel,pertemuan,pembahasan) VALUES(1,'Menyayangi Tumbuhan','Bahasa Indonesia','Pertemuan 1','Belajar menjaga lingkungan dan manfaat tumbuhan.');
INSERT INTO dokumentasi(kelas_id,tanggal,judul,deskripsi,rekomendasi) VALUES(1,CURDATE(),'Kegiatan belajar kelompok','Anak berdiskusi dan bekerja sama dengan teman.','Dorong anak untuk menyampaikan pendapat dan berlatih mandiri.');
