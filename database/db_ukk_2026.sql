-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2026 at 06:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ukk_2026`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GenerateDummyDataSchool` ()   BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE tingkat_val VARCHAR(20);
    DECLARE jurusan_val VARCHAR(100);

    -- 1. Insert 100 Users (Campuran Guru dan Siswa)[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO users (name, email, password, role, created_at, updated_at)
        VALUES (CONCAT('User ', i), CONCAT('user', i, '@sekolah.com'), 'password123', IF(i <= 50, 'guru', 'siswa'), NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 2. Insert 100 Tahun Ajaran[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO tahun_ajaran (nama, tanggal_mulai, tanggal_selesai, status_aktif, created_at, updated_at)
        VALUES (CONCAT('TA 20', LPAD(i, 2, '0'), '/20', LPAD(i+1, 2, '0')), CONCAT('20', LPAD(i, 2, '0'), '-07-01'), CONCAT('20', LPAD(i+1, 2, '0'), '-06-30'), IF(i=100, 1, 0), NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 3. Insert 50 Guru[cite: 1]
    SET i = 1;
    WHILE i <= 50 DO
        INSERT INTO guru (nip, nama, email, status_aktif, user_id, created_at, updated_at)
        VALUES (CONCAT('1980010120050110', LPAD(i, 2, '0')), CONCAT('Guru ', i), CONCAT('guru', i, '@sekolah.com'), 1, i, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 4. Insert 100 Siswa[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO siswa (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif, created_at, updated_at)
        VALUES (CONCAT('100', LPAD(i, 3, '0')), CONCAT('0050000', LPAD(i, 3, '0')), CONCAT('Siswa ', i), IF(i % 2 = 0, 'L', 'P'), '2005-01-01', CONCAT('Alamat Siswa ', i), 1, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 5. Insert 100 Kelas (Tingkat X, XI, XII & Jurusan RPL, TKJ, BD, TKR, TSM)[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        -- Rotasi Tingkat
        IF i % 3 = 1 THEN SET tingkat_val = 'X';
        ELSEIF i % 3 = 2 THEN SET tingkat_val = 'XI';
        ELSE SET tingkat_val = 'XII';
        END IF;

        -- Rotasi Jurusan
        IF i % 5 = 1 THEN SET jurusan_val = 'RPL';
        ELSEIF i % 5 = 2 THEN SET jurusan_val = 'TKJ';
        ELSEIF i % 5 = 3 THEN SET jurusan_val = 'BD';
        ELSEIF i % 5 = 4 THEN SET jurusan_val = 'TKR';
        ELSE SET jurusan_val = 'TSM';
        END IF;

        INSERT INTO kelas (nama, tingkat, jurusan, status_aktif, created_at, updated_at)
        VALUES (CONCAT(tingkat_val, ' ', jurusan_val, ' ', CEIL(i / 15)), tingkat_val, jurusan_val, 1, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 6. Insert 100 Kelas Siswa (Mapping Siswa ke Kelas)[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO kelas_siswa (siswa_id, tahun_ajaran_id, kelas_id, tanggal_mulai, tanggal_selesai, status_aktif, created_at, updated_at)
        VALUES (i, 100, i, '2025-07-01', '2026-06-30', 1, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 7. Insert 100 Wali Kelas[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO wali_kelas (tahun_ajaran_id, kelas_id, guru_id, tanggal_mulai, tanggal_selesai, status_aktif, created_at, updated_at)
        VALUES (100, i, IF(i <= 50, i, i - 50), '2025-07-01', '2026-06-30', 1, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 8. Insert 100 Pelanggaran Kategori[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO pelanggaran_kategori (nama, deskripsi, status_aktif, created_at, updated_at)
        VALUES (CONCAT('Kategori Pelanggaran ', i), CONCAT('Deskripsi untuk kategori ', i), 1, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 9. Insert 100 Pelanggaran[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO pelanggaran (pelanggaran_kategori_id, kode, nama, poin, deskripsi, status_aktif, created_at, updated_at)
        VALUES (i, CONCAT('P', LPAD(i, 3, '0')), CONCAT('Nama Pelanggaran ', i), (i % 10) * 5 + 5, CONCAT('Detail pelanggaran ', i), 1, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 10. Insert 100 Pelanggaran Siswa (History)[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO pelanggaran_siswa (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id, nama_kelas, pelanggaran_id, nama_pelanggaran, pelanggaran_kategori_id, guru_id, nama_guru, tanggal, keterangan, poin, tindakan, status, created_at, updated_at)
        VALUES (
            100, i, CONCAT('Siswa ', i), i, CONCAT('Kelas ', i), i, CONCAT('Nama Pelanggaran ', i), i,
            IF(i <= 50, i, i - 50), CONCAT('Guru ', IF(i <= 50, i, i - 50)),
            CURDATE(), 'Terbukti melanggar aturan', (i % 10) * 5 + 5, 'Diberikan peringatan lisan', 'Selesai', NOW(), NOW()
        );
        SET i = i + 1;
    END WHILE;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `t_guru`
--

CREATE TABLE `t_guru` (
  `id` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_guru`
--

INSERT INTO `t_guru` (`id`, `nip`, `nama`, `email`, `status_aktif`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '197001011995031001', 'Drs. H. Ahmad Dahlan, M.Pd.', 'ahmad.dahlan@sekolah.sch.id', 1, 101, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(2, '197202101998022001', 'Dra. Hj. Siti Nurjanah, M.M.', 'siti.nurjanah@sekolah.sch.id', 1, 102, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(3, '198003152005011002', 'Budi Santoso, S.Pd.', 'budi.santoso@sekolah.sch.id', 1, 103, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(4, '198304202008042003', 'Sri Wahyuni, S.Kom.', 'sri.wahyuni@sekolah.sch.id', 1, 104, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(5, '198505052010011004', 'Eko Prasetyo, S.T.', 'eko.prasetyo@sekolah.sch.id', 1, 105, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(6, '198806122012022005', 'Ratna Sari, S.Pd.', 'ratna.sari@sekolah.sch.id', 1, 106, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(7, '198207222006041006', 'Heri Kurniawan, M.T.', 'heri.kurniawan@sekolah.sch.id', 1, 107, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(8, '198908182014022007', 'Dewi Lestari, S.Si.', 'dewi.lestari@sekolah.sch.id', 1, 108, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(9, '199009092015031008', 'Agus Setiawan, S.Pd.', 'agus.setiawan@sekolah.sch.id', 1, 109, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(10, '199210082018012009', 'Maya Indah, S.E.', 'maya.indah@sekolah.sch.id', 1, 110, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(11, '198611032009021010', 'Rifan Hidayat, M.Kom.', 'rifan.hidayat@sekolah.sch.id', 1, 111, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(12, '199112192017042011', 'Fitriani, S.Pd.', 'fitriani@sekolah.sch.id', 1, 112, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(13, '198401282008011012', 'Hendra Wijaya, S.T.', 'hendra.wijaya@sekolah.sch.id', 1, 113, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(14, '197902142003122013', 'Nurul Hidayah, S.Ag.', 'nurul.hidayah@sekolah.sch.id', 1, 114, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(15, '198703072010011014', 'Bambang Sukoco, S.Pd.', 'bambang.sukoco@sekolah.sch.id', 1, 115, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(16, '198104222006042015', 'Rina Marlina, M.Pd.', 'rina.marlina@sekolah.sch.id', 1, 116, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(17, '199305112019031016', 'Dede Ismail, S.Kom.', 'dede.ismail@sekolah.sch.id', 1, 117, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(18, '199006022015022017', 'Yulia Rahmawati, S.Pd.', 'yulia.rahmawati@sekolah.sch.id', 1, 118, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(19, '198807292012011018', 'Andri Ferdiansyah, S.T.', 'andri.ferdiansyah@sekolah.sch.id', 1, 119, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(20, '199208172018022019', 'Anita Permata, S.E.', 'anita.permata@sekolah.sch.id', 1, 120, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(21, '198309052007011020', 'Aris Munandar, S.Pd.', 'aris.munandar@sekolah.sch.id', 1, 121, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(22, '199110212016042021', 'Cici Paramida, S.Pd.', 'cici.paramida@sekolah.sch.id', 1, 122, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(23, '198011162005021022', 'Daniel Christian, M.T.', 'daniel.christian@sekolah.sch.id', 1, 123, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(24, '198212042006042023', 'Dian Sastro, M.Pd.', 'dian.sastro@sekolah.sch.id', 1, 124, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(25, '199401092020011024', 'Ferry Irawan, S.Kom.', 'ferry.irawan@sekolah.sch.id', 1, 125, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(26, '199302232019022025', 'Gita Gutawa, S.Sn.', 'gita.gutawa@sekolah.sch.id', 1, 126, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(27, '197503141999031026', 'Hasan Basri, S.Ag., M.Pd.I.', 'hasan.basri@sekolah.sch.id', 1, 127, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(28, '198904302014022027', 'Ika Kartika, S.Pd.', 'ika.kartika@sekolah.sch.id', 1, 128, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(29, '198105182006041028', 'Joko Susilo, M.Or.', 'joko.susilo@sekolah.sch.id', 1, 129, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(30, '199006112015022029', 'Kartika Putri, S.Si.', 'kartika.putri@sekolah.sch.id', 1, 130, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(31, '198607072009011030', 'Lukman Hakim, S.T.', 'lukman.hakim@sekolah.sch.id', 1, 131, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(32, '198708202010022031', 'Marlina Susanti, S.Pd.', 'marlina.susanti@sekolah.sch.id', 1, 132, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(33, '199509132020031032', 'Naufal Rizky, S.Kom.', 'naufal.rizky@sekolah.sch.id', 1, 133, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(34, '198210022006042033', 'Olivia Zalianty, M.Pd.', 'olivia.zalianty@sekolah.sch.id', 1, 134, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(35, '198411252008011034', 'Panji Petualang, S.Hut.', 'panji.petualang@sekolah.sch.id', 1, 135, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(36, '199212102018022035', 'Qorry Sandioriva, S.Pd.', 'qorry.sandioriva@sekolah.sch.id', 1, 136, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(37, '198101052005011036', 'Rahmat Hidayat, M.Kom.', 'rahmat.hidayat@sekolah.sch.id', 1, 137, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(38, '199302172019022037', 'Siska Amelia, S.Si.', 'siska.amelia@sekolah.sch.id', 1, 138, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(39, '198503292010011038', 'Taufik Hidayat, S.Pd.', 'taufik.hidayat@sekolah.sch.id', 1, 139, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(40, '198004122005022039', 'Utami Dewi, M.Hum.', 'utami.dewi@sekolah.sch.id', 1, 140, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(41, '198905242014011040', 'Vicky Prasetyo, S.E.', 'vicky.prasetyo@sekolah.sch.id', 1, 141, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(42, '199106152016022041', 'Winda Viska, S.Pd.', 'winda.viska@sekolah.sch.id', 1, 142, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(43, '197607082000031042', 'Yudi Latif, M.A., Ph.D.', 'yudi.latif@sekolah.sch.id', 1, 143, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(44, '199008312015022043', 'Zaskia Gotik, S.Pd.', 'zaskia.gotik@sekolah.sch.id', 1, 144, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(45, '198209192006041044', 'Ade Rai, S.Or.', 'ade.rai@sekolah.sch.id', 1, 145, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(46, '198810062012022045', 'Bunga Citra, M.Pd.', 'bunga.citra@sekolah.sch.id', 1, 146, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(47, '198711282010011046', 'Cakra Khan, S.Sn.', 'cakra.khan@sekolah.sch.id', 1, 147, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(48, '199212142018022047', 'Dian Sastro, S.E.', 'dian.sastrowardoyo@sekolah.sch.id', 1, 148, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(49, '198901212014011048', 'Eza Gionino, S.T.', 'eza.gionino@sekolah.sch.id', 1, 149, '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(50, '199402082020022049', 'Fatin Shidqia, S.Pd.', 'fatin.shidqia@sekolah.sch.id', 1, 150, '2026-10-02 03:46:27', '2026-10-02 03:46:27');

-- --------------------------------------------------------

--
-- Table structure for table `t_kelas`
--

CREATE TABLE `t_kelas` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `tingkat` varchar(20) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_kelas`
--

INSERT INTO `t_kelas` (`id`, `nama`, `tingkat`, `jurusan`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'X RPL 1', 'X', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(2, 'X TKJ 1', 'X', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(3, 'X BD 1', 'X', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(4, 'X TKR 1', 'X', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(5, 'X TSM 1', 'X', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(6, 'XI RPL 1', 'XI', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(7, 'XI TKJ 1', 'XI', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(8, 'XI BD 1', 'XI', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(9, 'XI TKR 1', 'XI', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(10, 'XI TSM 1', 'XI', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(11, 'XII RPL 1', 'XII', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(12, 'XII TKJ 1', 'XII', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(13, 'XII BD 1', 'XII', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(14, 'XII TKR 1', 'XII', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(15, 'XII TSM 1', 'XII', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(16, 'X RPL 2', 'X', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(17, 'X TKJ 2', 'X', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(18, 'X BD 2', 'X', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(19, 'X TKR 2', 'X', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(20, 'X TSM 2', 'X', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(21, 'XI RPL 2', 'XI', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(22, 'XI TKJ 2', 'XI', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(23, 'XI BD 2', 'XI', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(24, 'XI TKR 2', 'XI', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(25, 'XI TSM 2', 'XI', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(26, 'XII RPL 2', 'XII', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(27, 'XII TKJ 2', 'XII', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(28, 'XII BD 2', 'XII', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(29, 'XII TKR 2', 'XII', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(30, 'XII TSM 2', 'XII', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(31, 'X RPL 3', 'X', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(32, 'X TKJ 3', 'X', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(33, 'X BD 3', 'X', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(34, 'X TKR 3', 'X', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(35, 'X TSM 3', 'X', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(36, 'XI RPL 3', 'XI', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(37, 'XI TKJ 3', 'XI', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(38, 'XI BD 3', 'XI', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(39, 'XI TKR 3', 'XI', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(40, 'XI TSM 3', 'XI', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(41, 'XII RPL 3', 'XII', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(42, 'XII TKJ 3', 'XII', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(43, 'XII BD 3', 'XII', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(44, 'XII TKR 3', 'XII', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(45, 'XII TSM 3', 'XII', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(46, 'X RPL 4', 'X', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(47, 'X TKJ 4', 'X', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(48, 'X BD 4', 'X', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(49, 'X TKR 4', 'X', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(50, 'X TSM 4', 'X', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(51, 'XI RPL 4', 'XI', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(52, 'XI TKJ 4', 'XI', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(53, 'XI BD 4', 'XI', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(54, 'XI TKR 4', 'XI', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(55, 'XI TSM 4', 'XI', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(56, 'XII RPL 4', 'XII', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(57, 'XII TKJ 4', 'XII', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(58, 'XII BD 4', 'XII', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(59, 'XII TKR 4', 'XII', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(60, 'XII TSM 4', 'XII', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(61, 'X RPL 5', 'X', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(62, 'X TKJ 5', 'X', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(63, 'X BD 5', 'X', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(64, 'X TKR 5', 'X', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(65, 'X TSM 5', 'X', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(66, 'XI RPL 5', 'XI', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(67, 'XI TKJ 5', 'XI', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(68, 'XI BD 5', 'XI', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(69, 'XI TKR 5', 'XI', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(70, 'XI TSM 5', 'XI', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(71, 'XII RPL 5', 'XII', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(72, 'XII TKJ 5', 'XII', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(73, 'XII BD 5', 'XII', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(74, 'XII TKR 5', 'XII', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(75, 'XII TSM 5', 'XII', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(76, 'X RPL 6', 'X', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(77, 'X TKJ 6', 'X', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(78, 'X BD 6', 'X', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(79, 'X TKR 6', 'X', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(80, 'X TSM 6', 'X', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(81, 'XI RPL 6', 'XI', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(82, 'XI TKJ 6', 'XI', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(83, 'XI BD 6', 'XI', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(84, 'XI TKR 6', 'XI', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(85, 'XI TSM 6', 'XI', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(86, 'XII RPL 6', 'XII', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(87, 'XII TKJ 6', 'XII', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(88, 'XII BD 6', 'XII', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(89, 'XII TKR 6', 'XII', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(90, 'XII TSM 6', 'XII', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(91, 'X RPL 7', 'X', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(92, 'X TKJ 7', 'X', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(93, 'X BD 7', 'X', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(94, 'X TKR 7', 'X', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(95, 'X TSM 7', 'X', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(96, 'XI RPL 7', 'XI', 'RPL', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(97, 'XI TKJ 7', 'XI', 'TKJ', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(98, 'XI BD 7', 'XI', 'BD', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(99, 'XI TKR 7', 'XI', 'TKR', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44'),
(100, 'XI TSM 7', 'XI', 'TSM', 1, '2026-09-28 06:01:44', '2026-09-28 06:01:44');

-- --------------------------------------------------------

--
-- Table structure for table `t_kelas_siswa`
--

CREATE TABLE `t_kelas_siswa` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) DEFAULT NULL,
  `tahun_ajaran_id` int(11) DEFAULT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_pelanggaran`
--

CREATE TABLE `t_pelanggaran` (
  `id` int(11) NOT NULL,
  `pelanggaran_kategori_id` int(11) DEFAULT NULL,
  `kode` varchar(30) DEFAULT NULL,
  `nama` varchar(30) DEFAULT NULL,
  `poin` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_pelanggaran`
--

INSERT INTO `t_pelanggaran` (`id`, `pelanggaran_kategori_id`, `kode`, `nama`, `poin`, `deskripsi`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 1, 'P01', 'Pelanggaran 1', 5, 'Des 1', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(2, 2, 'P02', 'Pelanggaran 2', 5, 'Des 2', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(3, 3, 'P03', 'Pelanggaran 3', 5, 'Des 3', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(4, 4, 'P04', 'Pelanggaran 4', 5, 'Des 4', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(5, 5, 'P05', 'Pelanggaran 5', 5, 'Des 5', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(6, 6, 'P06', 'Pelanggaran 6', 5, 'Des 6', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(7, 7, 'P07', 'Pelanggaran 7', 5, 'Des 7', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(8, 8, 'P08', 'Pelanggaran 8', 5, 'Des 8', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(9, 9, 'P09', 'Pelanggaran 9', 5, 'Des 9', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(10, 10, 'P10', 'Pelanggaran 10', 5, 'Des 10', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(11, 11, 'P11', 'Pelanggaran 11', 5, 'Des 11', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(12, 12, 'P12', 'Pelanggaran 12', 5, 'Des 12', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(13, 13, 'P13', 'Pelanggaran 13', 5, 'Des 13', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(14, 14, 'P14', 'Pelanggaran 14', 5, 'Des 14', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(15, 15, 'P15', 'Pelanggaran 15', 5, 'Des 15', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(16, 16, 'P16', 'Pelanggaran 16', 5, 'Des 16', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(17, 17, 'P17', 'Pelanggaran 17', 5, 'Des 17', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(18, 18, 'P18', 'Pelanggaran 18', 5, 'Des 18', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(19, 19, 'P19', 'Pelanggaran 19', 5, 'Des 19', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(20, 20, 'P20', 'Pelanggaran 20', 5, 'Des 20', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(21, 21, 'P21', 'Pelanggaran 21', 5, 'Des 21', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(22, 22, 'P22', 'Pelanggaran 22', 5, 'Des 22', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(23, 23, 'P23', 'Pelanggaran 23', 5, 'Des 23', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(24, 24, 'P24', 'Pelanggaran 24', 5, 'Des 24', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(25, 25, 'P25', 'Pelanggaran 25', 5, 'Des 25', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(26, 26, 'P26', 'Pelanggaran 26', 5, 'Des 26', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(27, 27, 'P27', 'Pelanggaran 27', 5, 'Des 27', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(28, 28, 'P28', 'Pelanggaran 28', 5, 'Des 28', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(29, 29, 'P29', 'Pelanggaran 29', 5, 'Des 29', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(30, 30, 'P30', 'Pelanggaran 30', 5, 'Des 30', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(31, 31, 'P31', 'Pelanggaran 31', 5, 'Des 31', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(32, 32, 'P32', 'Pelanggaran 32', 5, 'Des 32', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(33, 33, 'P33', 'Pelanggaran 33', 5, 'Des 33', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(34, 34, 'P34', 'Pelanggaran 34', 5, 'Des 34', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(35, 35, 'P35', 'Pelanggaran 35', 5, 'Des 35', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(36, 36, 'P36', 'Pelanggaran 36', 5, 'Des 36', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(37, 37, 'P37', 'Pelanggaran 37', 5, 'Des 37', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(38, 38, 'P38', 'Pelanggaran 38', 5, 'Des 38', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(39, 39, 'P39', 'Pelanggaran 39', 5, 'Des 39', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(40, 40, 'P40', 'Pelanggaran 40', 5, 'Des 40', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(41, 41, 'P41', 'Pelanggaran 41', 5, 'Des 41', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(42, 42, 'P42', 'Pelanggaran 42', 5, 'Des 42', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(43, 43, 'P43', 'Pelanggaran 43', 5, 'Des 43', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(44, 44, 'P44', 'Pelanggaran 44', 5, 'Des 44', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(45, 45, 'P45', 'Pelanggaran 45', 5, 'Des 45', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(46, 46, 'P46', 'Pelanggaran 46', 5, 'Des 46', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(47, 47, 'P47', 'Pelanggaran 47', 5, 'Des 47', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(48, 48, 'P48', 'Pelanggaran 48', 5, 'Des 48', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(49, 49, 'P49', 'Pelanggaran 49', 5, 'Des 49', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(50, 50, 'P50', 'Pelanggaran 50', 5, 'Des 50', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(51, 51, 'P51', 'Pelanggaran 51', 5, 'Des 51', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(52, 52, 'P52', 'Pelanggaran 52', 5, 'Des 52', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(53, 53, 'P53', 'Pelanggaran 53', 5, 'Des 53', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(54, 54, 'P54', 'Pelanggaran 54', 5, 'Des 54', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(55, 55, 'P55', 'Pelanggaran 55', 5, 'Des 55', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(56, 56, 'P56', 'Pelanggaran 56', 5, 'Des 56', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(57, 57, 'P57', 'Pelanggaran 57', 5, 'Des 57', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(58, 58, 'P58', 'Pelanggaran 58', 5, 'Des 58', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(59, 59, 'P59', 'Pelanggaran 59', 5, 'Des 59', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(60, 60, 'P60', 'Pelanggaran 60', 5, 'Des 60', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(61, 61, 'P61', 'Pelanggaran 61', 5, 'Des 61', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(62, 62, 'P62', 'Pelanggaran 62', 5, 'Des 62', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(63, 63, 'P63', 'Pelanggaran 63', 5, 'Des 63', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(64, 64, 'P64', 'Pelanggaran 64', 5, 'Des 64', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(65, 65, 'P65', 'Pelanggaran 65', 5, 'Des 65', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(66, 66, 'P66', 'Pelanggaran 66', 5, 'Des 66', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(67, 67, 'P67', 'Pelanggaran 67', 5, 'Des 67', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(68, 68, 'P68', 'Pelanggaran 68', 5, 'Des 68', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(69, 69, 'P69', 'Pelanggaran 69', 5, 'Des 69', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(70, 70, 'P70', 'Pelanggaran 70', 5, 'Des 70', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(71, 71, 'P71', 'Pelanggaran 71', 5, 'Des 71', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(72, 72, 'P72', 'Pelanggaran 72', 5, 'Des 72', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(73, 73, 'P73', 'Pelanggaran 73', 5, 'Des 73', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(74, 74, 'P74', 'Pelanggaran 74', 5, 'Des 74', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(75, 75, 'P75', 'Pelanggaran 75', 5, 'Des 75', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(76, 76, 'P76', 'Pelanggaran 76', 5, 'Des 76', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(77, 77, 'P77', 'Pelanggaran 77', 5, 'Des 77', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(78, 78, 'P78', 'Pelanggaran 78', 5, 'Des 78', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(79, 79, 'P79', 'Pelanggaran 79', 5, 'Des 79', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(80, 80, 'P80', 'Pelanggaran 80', 5, 'Des 80', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(81, 81, 'P81', 'Pelanggaran 81', 5, 'Des 81', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(82, 82, 'P82', 'Pelanggaran 82', 5, 'Des 82', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(83, 83, 'P83', 'Pelanggaran 83', 5, 'Des 83', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(84, 84, 'P84', 'Pelanggaran 84', 5, 'Des 84', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(85, 85, 'P85', 'Pelanggaran 85', 5, 'Des 85', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(86, 86, 'P86', 'Pelanggaran 86', 5, 'Des 86', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(87, 87, 'P87', 'Pelanggaran 87', 5, 'Des 87', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(88, 88, 'P88', 'Pelanggaran 88', 5, 'Des 88', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(89, 89, 'P89', 'Pelanggaran 89', 5, 'Des 89', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(90, 90, 'P90', 'Pelanggaran 90', 5, 'Des 90', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(91, 91, 'P91', 'Pelanggaran 91', 5, 'Des 91', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(92, 92, 'P92', 'Pelanggaran 92', 5, 'Des 92', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(93, 93, 'P93', 'Pelanggaran 93', 5, 'Des 93', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(94, 94, 'P94', 'Pelanggaran 94', 5, 'Des 94', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(95, 95, 'P95', 'Pelanggaran 95', 5, 'Des 95', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(96, 96, 'P96', 'Pelanggaran 96', 5, 'Des 96', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(97, 97, 'P97', 'Pelanggaran 97', 5, 'Des 97', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(98, 98, 'P98', 'Pelanggaran 98', 5, 'Des 98', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(99, 99, 'P99', 'Pelanggaran 99', 5, 'Des 99', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31'),
(100, 100, 'P100', 'Pelanggaran 100', 5, 'Des 100', 1, '2026-09-28 05:58:31', '2026-09-28 05:58:31');

-- --------------------------------------------------------

--
-- Table structure for table `t_pelanggaran_kategori`
--

CREATE TABLE `t_pelanggaran_kategori` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_pelanggaran_kategori`
--

INSERT INTO `t_pelanggaran_kategori` (`id`, `nama`, `deskripsi`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'Kategori 1', 'Deskripsi 1', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(2, 'Kategori 2', 'Deskripsi 2', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(3, 'Kategori 3', 'Deskripsi 3', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(4, 'Kategori 4', 'Deskripsi 4', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(5, 'Kategori 5', 'Deskripsi 5', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(6, 'Kategori 6', 'Deskripsi 6', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(7, 'Kategori 7', 'Deskripsi 7', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(8, 'Kategori 8', 'Deskripsi 8', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(9, 'Kategori 9', 'Deskripsi 9', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(10, 'Kategori 10', 'Deskripsi 10', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(11, 'Kategori 11', 'Deskripsi 11', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(12, 'Kategori 12', 'Deskripsi 12', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(13, 'Kategori 13', 'Deskripsi 13', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(14, 'Kategori 14', 'Deskripsi 14', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(15, 'Kategori 15', 'Deskripsi 15', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(16, 'Kategori 16', 'Deskripsi 16', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(17, 'Kategori 17', 'Deskripsi 17', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(18, 'Kategori 18', 'Deskripsi 18', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(19, 'Kategori 19', 'Deskripsi 19', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(20, 'Kategori 20', 'Deskripsi 20', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(21, 'Kategori 21', 'Deskripsi 21', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(22, 'Kategori 22', 'Deskripsi 22', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(23, 'Kategori 23', 'Deskripsi 23', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(24, 'Kategori 24', 'Deskripsi 24', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(25, 'Kategori 25', 'Deskripsi 25', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(26, 'Kategori 26', 'Deskripsi 26', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(27, 'Kategori 27', 'Deskripsi 27', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(28, 'Kategori 28', 'Deskripsi 28', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(29, 'Kategori 29', 'Deskripsi 29', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(30, 'Kategori 30', 'Deskripsi 30', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(31, 'Kategori 31', 'Deskripsi 31', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(32, 'Kategori 32', 'Deskripsi 32', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(33, 'Kategori 33', 'Deskripsi 33', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(34, 'Kategori 34', 'Deskripsi 34', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(35, 'Kategori 35', 'Deskripsi 35', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(36, 'Kategori 36', 'Deskripsi 36', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(37, 'Kategori 37', 'Deskripsi 37', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(38, 'Kategori 38', 'Deskripsi 38', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(39, 'Kategori 39', 'Deskripsi 39', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(40, 'Kategori 40', 'Deskripsi 40', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(41, 'Kategori 41', 'Deskripsi 41', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(42, 'Kategori 42', 'Deskripsi 42', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(43, 'Kategori 43', 'Deskripsi 43', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(44, 'Kategori 44', 'Deskripsi 44', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(45, 'Kategori 45', 'Deskripsi 45', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(46, 'Kategori 46', 'Deskripsi 46', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(47, 'Kategori 47', 'Deskripsi 47', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(48, 'Kategori 48', 'Deskripsi 48', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(49, 'Kategori 49', 'Deskripsi 49', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(50, 'Kategori 50', 'Deskripsi 50', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(51, 'Kategori 51', 'Deskripsi 51', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(52, 'Kategori 52', 'Deskripsi 52', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(53, 'Kategori 53', 'Deskripsi 53', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(54, 'Kategori 54', 'Deskripsi 54', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(55, 'Kategori 55', 'Deskripsi 55', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(56, 'Kategori 56', 'Deskripsi 56', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(57, 'Kategori 57', 'Deskripsi 57', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(58, 'Kategori 58', 'Deskripsi 58', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(59, 'Kategori 59', 'Deskripsi 59', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(60, 'Kategori 60', 'Deskripsi 60', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(61, 'Kategori 61', 'Deskripsi 61', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(62, 'Kategori 62', 'Deskripsi 62', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(63, 'Kategori 63', 'Deskripsi 63', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(64, 'Kategori 64', 'Deskripsi 64', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(65, 'Kategori 65', 'Deskripsi 65', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(66, 'Kategori 66', 'Deskripsi 66', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(67, 'Kategori 67', 'Deskripsi 67', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(68, 'Kategori 68', 'Deskripsi 68', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(69, 'Kategori 69', 'Deskripsi 69', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(70, 'Kategori 70', 'Deskripsi 70', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(71, 'Kategori 71', 'Deskripsi 71', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(72, 'Kategori 72', 'Deskripsi 72', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(73, 'Kategori 73', 'Deskripsi 73', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(74, 'Kategori 74', 'Deskripsi 74', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(75, 'Kategori 75', 'Deskripsi 75', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(76, 'Kategori 76', 'Deskripsi 76', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(77, 'Kategori 77', 'Deskripsi 77', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(78, 'Kategori 78', 'Deskripsi 78', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(79, 'Kategori 79', 'Deskripsi 79', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(80, 'Kategori 80', 'Deskripsi 80', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(81, 'Kategori 81', 'Deskripsi 81', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(82, 'Kategori 82', 'Deskripsi 82', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(83, 'Kategori 83', 'Deskripsi 83', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(84, 'Kategori 84', 'Deskripsi 84', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(85, 'Kategori 85', 'Deskripsi 85', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(86, 'Kategori 86', 'Deskripsi 86', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(87, 'Kategori 87', 'Deskripsi 87', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(88, 'Kategori 88', 'Deskripsi 88', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(89, 'Kategori 89', 'Deskripsi 89', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(90, 'Kategori 90', 'Deskripsi 90', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(91, 'Kategori 91', 'Deskripsi 91', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(92, 'Kategori 92', 'Deskripsi 92', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(93, 'Kategori 93', 'Deskripsi 93', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(94, 'Kategori 94', 'Deskripsi 94', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(95, 'Kategori 95', 'Deskripsi 95', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(96, 'Kategori 96', 'Deskripsi 96', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(97, 'Kategori 97', 'Deskripsi 97', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(98, 'Kategori 98', 'Deskripsi 98', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(99, 'Kategori 99', 'Deskripsi 99', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08'),
(100, 'Kategori 100', 'Deskripsi 100', 1, '2026-09-28 05:58:08', '2026-09-28 05:58:08');

-- --------------------------------------------------------

--
-- Table structure for table `t_pelanggaran_siswa`
--

CREATE TABLE `t_pelanggaran_siswa` (
  `id` int(11) NOT NULL,
  `tahun_ajaran_id` int(11) DEFAULT NULL,
  `siswa_id` int(11) DEFAULT NULL,
  `nama_siswa` varchar(150) NOT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `nama_kelas` varchar(100) NOT NULL,
  `pelanggaran_id` int(11) DEFAULT NULL,
  `nama_pelanggaran` int(11) NOT NULL,
  `pelanggaran_kategori_id` int(11) DEFAULT NULL,
  `guru_id` int(11) DEFAULT NULL,
  `nama_guru` varchar(150) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `keterangan` text NOT NULL,
  `poin` int(11) NOT NULL,
  `tindakan` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_siswa`
--

CREATE TABLE `t_siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(20) DEFAULT NULL,
  `nisn` varchar(20) DEFAULT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `jenis_kelamin` varchar(4) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_siswa`
--

INSERT INTO `t_siswa` (`id`, `nis`, `nisn`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, '2026001', '0080000001', 'Ahmad Rizky Pratama', 'L', '2008-01-15', 'Jl. KH. Z. Mustofa No. 12, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(2, '2026002', '0080000002', 'Anisa Nur Syafiqah', 'P', '2008-02-10', 'Jl. Tentara Pelajar No. 45, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(3, '2026003', '0080000003', 'Bayu Setiawan', 'L', '2008-03-22', 'Jl. HZ. Mustofa No. 88, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(4, '2026004', '0080000004', 'Citra Dewi Lestari', 'P', '2008-04-18', 'Jl. RE. Martadinata No. 34, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(5, '2026005', '0080000005', 'Daffa Maulana Ibrahim', 'L', '2008-05-05', 'Jl. Sutisna Senjaya No. 101, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(6, '2026006', '0080000006', 'Dina Aulia Putri', 'P', '2008-06-30', 'Jl. Yudanegara No. 15, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(7, '2026007', '0080000007', 'Fajar Ramadhan', 'L', '2008-07-12', 'Jl. Otto Iskandardinata No. 23, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(8, '2026008', '0080000008', 'Farah Nabilah', 'P', '2008-08-25', 'Jl. Dr. Sukardjo No. 67, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(9, '2026009', '0080000009', 'Gilang Kartiko', 'L', '2008-09-14', 'Jl. Perintis Kemerdekaan No. 50, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(10, '2026010', '0080000010', 'Hana Zahra Alifa', 'P', '2008-10-08', 'Jl. Moch. Hatta No. 12, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(11, '2026011', '0080000011', 'Irfan Syahputra', 'L', '2008-11-03', 'Jl. Gunung Sabeulah No. 8, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(12, '2026012', '0080000012', 'Indah Permatasari', 'P', '2008-12-19', 'Jl. Paseh No. 99, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(13, '2026013', '0080000013', 'Julian Dwi Perkasa', 'L', '2008-01-28', 'Jl. Cihideung No. 41, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(14, '2026014', '0080000014', 'Kania Maharani', 'P', '2008-02-14', 'Jl. Galunggung No. 17, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(15, '2026015', '0080000015', 'Lutfi Hakim', 'L', '2008-03-07', 'Jl. Nagarawangi No. 3, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(16, '2026016', '0080000016', 'Laila Nurhaliza', 'P', '2008-04-22', 'Jl. Bebedahan No. 29, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(17, '2026017', '0080000017', 'Muhammad Alif Akbar', 'L', '2008-05-11', 'Jl. Cisitu No. 5, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(18, '2026018', '0080000018', 'Nabila Rahmawati', 'P', '2008-06-02', 'Jl. Awipari No. 62, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(19, '2026019', '0080000019', 'Naufal Azmi', 'L', '2008-07-29', 'Jl. Tamansari No. 11, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(20, '2026020', '0080000020', 'Nurul Aini', 'P', '2008-08-17', 'Jl. Gobras No. 80, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(21, '2026021', '0080000021', 'Oktavian Saputra', 'L', '2008-09-05', 'Jl. Kawalu No. 14, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(22, '2026022', '0080000022', 'Putri Ayu Wandira', 'P', '2008-10-21', 'Jl. Cibeureum No. 37, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(23, '2026023', '0080000023', 'Rafi Ahmad Fauzi', 'L', '2008-11-16', 'Jl. Indihiang No. 90, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(24, '2026024', '0080000024', 'Rania Salsabila', 'P', '2008-12-04', 'Jl. Bungursari No. 21, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(25, '2026025', '0080000025', 'Reza Kurniawan', 'L', '2007-01-09', 'Jl. Mangkubumi No. 73, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(26, '2026026', '0080000026', 'Rina Anggraeni', 'P', '2007-02-23', 'Jl. Cipedes No. 19, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(27, '2026027', '0080000027', 'Riyan Hidayat', 'L', '2007-03-14', 'Jl. Sukarame No. 55, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(28, '2026028', '0080000028', 'Salma Fauziyah', 'P', '2007-04-30', 'Jl. Singaparna No. 102, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(29, '2026029', '0080000029', 'Satria Wijaya', 'L', '2007-05-18', 'Jl. Cisayong No. 6, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(30, '2026030', '0080000030', 'Siti Nurhaliza', 'P', '2007-06-11', 'Jl. Rajapolah No. 48, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(31, '2026031', '0080000031', 'Taufik Hidayatullah', 'L', '2007-07-07', 'Jl. Jamanis No. 31, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(32, '2026032', '0080000032', 'Tania Kirana', 'P', '2007-08-20', 'Jl. Ciawi No. 84, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(33, '2026033', '0080000033', 'Umar Faruq', 'L', '2007-09-13', 'Jl. Manonjaya No. 27, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(34, '2026034', '0080000034', 'Vania Amanda', 'P', '2007-10-02', 'Jl. Cineam No. 9, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(35, '2026035', '0080000035', 'Wahyu Hidayat', 'L', '2007-11-25', 'Jl. Taraju No. 63, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(36, '2026036', '0080000036', 'Wulan Dari', 'P', '2007-12-10', 'Jl. Salawu No. 18, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(37, '2026037', '0080000037', 'Yusuf Bachtiar', 'L', '2009-01-05', 'Jl. Bojongasih No. 42, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(38, '2026038', '0080000038', 'Yulia Safitri', 'P', '2009-02-17', 'Jl. Culamega No. 71, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(39, '2026039', '0080000039', 'Zackya Firdaus', 'L', '2009-03-29', 'Jl. Cipatujah No. 16, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(40, '2026040', '0080000040', 'Zahra Amelia', 'P', '2009-04-12', 'Jl. Karangnunggal No. 83, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(41, '2026041', '0080000041', 'Aditya Nugraha', 'L', '2008-05-24', 'Jl. Cikatomas No. 39, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(42, '2026042', '0080000042', 'Bella Cantika', 'P', '2008-06-15', 'Jl. Salopa No. 95, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(43, '2026043', '0080000043', 'Chandra Kirana', 'L', '2008-07-08', 'Jl. Jatiwaras No. 22, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(44, '2026044', '0080000044', 'Dewi Sartika', 'P', '2008-08-31', 'Jl. Sukaraja No. 57, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(45, '2026045', '0080000045', 'Eka Putra Pratama', 'L', '2008-09-19', 'Jl. Padakembang No. 4, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(46, '2026046', '0080000046', 'Fitri Handayani', 'P', '2008-10-06', 'Jl. Leuwisari No. 76, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(47, '2026047', '0080000047', 'Genta Buana', 'L', '2008-11-28', 'Jl. Sariwangi No. 13, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(48, '2026048', '0080000048', 'Hesti Purwanti', 'P', '2008-12-14', 'Jl. Cigalontang No. 68, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(49, '2026049', '0080000049', 'Ilham Ramadhan', 'L', '2009-01-21', 'Jl. Sukahenning No. 30, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59'),
(50, '2026050', '0080000050', 'Jessica Aurelia', 'P', '2009-02-08', 'Jl. Pagerageung No. 91, Tasikmalaya', 1, '2026-10-02 03:43:59', '2026-10-02 03:43:59');

-- --------------------------------------------------------

--
-- Table structure for table `t_tahun_ajaran`
--

CREATE TABLE `t_tahun_ajaran` (
  `id` int(11) NOT NULL,
  `nama` varchar(20) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_tahun_ajaran`
--

INSERT INTO `t_tahun_ajaran` (`id`, `nama`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'TA 1', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(2, 'TA 2', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(3, 'TA 3', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(4, 'TA 4', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(5, 'TA 5', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(6, 'TA 6', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(7, 'TA 7', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(8, 'TA 8', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(9, 'TA 9', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(10, 'TA 10', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(11, 'TA 11', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(12, 'TA 12', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(13, 'TA 13', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(14, 'TA 14', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(15, 'TA 15', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(16, 'TA 16', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(17, 'TA 17', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(18, 'TA 18', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(19, 'TA 19', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(20, 'TA 20', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(21, 'TA 21', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(22, 'TA 22', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(23, 'TA 23', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(24, 'TA 24', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(25, 'TA 25', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(26, 'TA 26', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(27, 'TA 27', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(28, 'TA 28', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(29, 'TA 29', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(30, 'TA 30', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(31, 'TA 31', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(32, 'TA 32', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(33, 'TA 33', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(34, 'TA 34', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(35, 'TA 35', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(36, 'TA 36', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(37, 'TA 37', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(38, 'TA 38', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(39, 'TA 39', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(40, 'TA 40', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(41, 'TA 41', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(42, 'TA 42', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(43, 'TA 43', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(44, 'TA 44', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(45, 'TA 45', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(46, 'TA 46', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(47, 'TA 47', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(48, 'TA 48', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(49, 'TA 49', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(50, 'TA 50', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(51, 'TA 51', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(52, 'TA 52', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(53, 'TA 53', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(54, 'TA 54', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(55, 'TA 55', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(56, 'TA 56', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(57, 'TA 57', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(58, 'TA 58', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(59, 'TA 59', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(60, 'TA 60', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(61, 'TA 61', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(62, 'TA 62', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(63, 'TA 63', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(64, 'TA 64', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(65, 'TA 65', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(66, 'TA 66', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(67, 'TA 67', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(68, 'TA 68', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(69, 'TA 69', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(70, 'TA 70', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(71, 'TA 71', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(72, 'TA 72', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(73, 'TA 73', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(74, 'TA 74', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(75, 'TA 75', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(76, 'TA 76', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(77, 'TA 77', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(78, 'TA 78', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(79, 'TA 79', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(80, 'TA 80', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(81, 'TA 81', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(82, 'TA 82', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(83, 'TA 83', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(84, 'TA 84', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(85, 'TA 85', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(86, 'TA 86', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(87, 'TA 87', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(88, 'TA 88', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(89, 'TA 89', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(90, 'TA 90', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(91, 'TA 91', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(92, 'TA 92', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(93, 'TA 93', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(94, 'TA 94', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(95, 'TA 95', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(96, 'TA 96', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(97, 'TA 97', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(98, 'TA 98', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(99, 'TA 99', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27'),
(100, 'TA 100', '2026-07-01', '2027-06-30', 1, '2026-09-28 05:57:27', '2026-09-28 05:57:27');

-- --------------------------------------------------------

--
-- Table structure for table `t_users`
--

CREATE TABLE `t_users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_users`
--

INSERT INTO `t_users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@ukk2026.com', '2026-10-02 01:48:51', '$2y$10$RS53mKxMjN8wP7lPw1ZSn.4ef06mI0yzBHBRvUyBJzUpM4830o.Au', '', 'Admin', '2026-10-02 01:48:51', '2026-10-02 01:48:51'),
(2, 'Bapak Guru', 'guru@ukk2026.com', '2026-10-02 01:57:34', '$2y$10$GHyInVTPyZNxGFAWxrCvQe3b1nI.0jkzVWd.Zcg4waGWGjx06qsZS', '', 'Guru', '2026-10-02 01:57:34', '2026-10-02 01:57:34'),
(3, 'Ibu Wali Kelas', 'walikelas@ukk2026.com', '2026-10-02 01:57:34', '$2y$10$UuGg3CXnKYPiX0zEPXaxJOnan8OSv9p9Pv5xfOdOu7efAlAvy/Jxa', '', 'Wali Kelas', '2026-10-02 01:57:34', '2026-10-02 01:57:34'),
(4, 'nonok', 'nonok1@gmail.com', '2026-10-02 03:25:49', 'guru123', '', 'guru', '2026-10-02 03:25:49', '2026-10-02 03:25:49'),
(101, 'Drs. H. Ahmad Dahlan, M.Pd.', 'ahmad.dahlan@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(102, 'Dra. Hj. Siti Nurjanah, M.M.', 'siti.nurjanah@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(103, 'Budi Santoso, S.Pd.', 'budi.santoso@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(104, 'Sri Wahyuni, S.Kom.', 'sri.wahyuni@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(105, 'Eko Prasetyo, S.T.', 'eko.prasetyo@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(106, 'Ratna Sari, S.Pd.', 'ratna.sari@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(107, 'Heri Kurniawan, M.T.', 'heri.kurniawan@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(108, 'Dewi Lestari, S.Si.', 'dewi.lestari@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(109, 'Agus Setiawan, S.Pd.', 'agus.setiawan@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(110, 'Maya Indah, S.E.', 'maya.indah@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(111, 'Rifan Hidayat, M.Kom.', 'rifan.hidayat@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(112, 'Fitriani, S.Pd.', 'fitriani@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(113, 'Hendra Wijaya, S.T.', 'hendra.wijaya@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(114, 'Nurul Hidayah, S.Ag.', 'nurul.hidayah@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(115, 'Bambang Sukoco, S.Pd.', 'bambang.sukoco@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(116, 'Rina Marlina, M.Pd.', 'rina.marlina@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(117, 'Dede Ismail, S.Kom.', 'dede.ismail@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(118, 'Yulia Rahmawati, S.Pd.', 'yulia.rahmawati@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(119, 'Andri Ferdiansyah, S.T.', 'andri.ferdiansyah@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(120, 'Anita Permata, S.E.', 'anita.permata@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(121, 'Aris Munandar, S.Pd.', 'aris.munandar@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(122, 'Cici Paramida, S.Pd.', 'cici.paramida@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(123, 'Daniel Christian, M.T.', 'daniel.christian@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(124, 'Dian Sastro, M.Pd.', 'dian.sastro@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(125, 'Ferry Irawan, S.Kom.', 'ferry.irawan@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(126, 'Gita Gutawa, S.Sn.', 'gita.gutawa@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(127, 'Hasan Basri, S.Ag., M.Pd.I.', 'hasan.basri@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(128, 'Ika Kartika, S.Pd.', 'ika.kartika@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(129, 'Joko Susilo, M.Or.', 'joko.susilo@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(130, 'Kartika Putri, S.Si.', 'kartika.putri@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(131, 'Lukman Hakim, S.T.', 'lukman.hakim@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(132, 'Marlina Susanti, S.Pd.', 'marlina.susanti@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(133, 'Naufal Rizky, S.Kom.', 'naufal.rizky@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(134, 'Olivia Zalianty, M.Pd.', 'olivia.zalianty@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(135, 'Panji Petualang, S.Hut.', 'panji.petualang@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(136, 'Qorry Sandioriva, S.Pd.', 'qorry.sandioriva@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(137, 'Rahmat Hidayat, M.Kom.', 'rahmat.hidayat@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(138, 'Siska Amelia, S.Si.', 'siska.amelia@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(139, 'Taufik Hidayat, S.Pd.', 'taufik.hidayat@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(140, 'Utami Dewi, M.Hum.', 'utami.dewi@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(141, 'Vicky Prasetyo, S.E.', 'vicky.prasetyo@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(142, 'Winda Viska, S.Pd.', 'winda.viska@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(143, 'Yudi Latif, M.A., Ph.D.', 'yudi.latif@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(144, 'Zaskia Gotik, S.Pd.', 'zaskia.gotik@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(145, 'Ade Rai, S.Or.', 'ade.rai@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(146, 'Bunga Citra, M.Pd.', 'bunga.citra@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(147, 'Cakra Khan, S.Sn.', 'cakra.khan@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(148, 'Dian Sastro, S.E.', 'dian.sastrowardoyo@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(149, 'Eza Gionino, S.T.', 'eza.gionino@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27'),
(150, 'Fatin Shidqia, S.Pd.', 'fatin.shidqia@sekolah.sch.id', '2026-10-02 03:46:27', 'guru123', '', 'guru', '2026-10-02 03:46:27', '2026-10-02 03:46:27');

-- --------------------------------------------------------

--
-- Table structure for table `t_wali_kelas`
--

CREATE TABLE `t_wali_kelas` (
  `id` int(11) NOT NULL,
  `tahun_ajaran_id` int(11) DEFAULT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `guru_id` int(11) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_guru`
--
ALTER TABLE `t_guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `t_kelas`
--
ALTER TABLE `t_kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `tahun_ajaran_id` (`tahun_ajaran_id`);

--
-- Indexes for table `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelanggaran_kategori_id` (`pelanggaran_kategori_id`);

--
-- Indexes for table `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`),
  ADD KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `pelanggaran_id` (`pelanggaran_id`),
  ADD KEY `pelanggaran_kategori_id` (`pelanggaran_kategori_id`);

--
-- Indexes for table `t_siswa`
--
ALTER TABLE `t_siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_users`
--
ALTER TABLE `t_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_guru`
--
ALTER TABLE `t_guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `t_kelas`
--
ALTER TABLE `t_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `t_siswa`
--
ALTER TABLE `t_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `t_users`
--
ALTER TABLE `t_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `t_guru`
--
ALTER TABLE `t_guru`
  ADD CONSTRAINT `t_guru_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `t_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  ADD CONSTRAINT `t_kelas_siswa_ibfk_1` FOREIGN KEY (`id`) REFERENCES `t_siswa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_kelas_siswa_ibfk_2` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `t_guru` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_3` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_5` FOREIGN KEY (`pelanggaran_id`) REFERENCES `t_pelanggaran` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_6` FOREIGN KEY (`siswa_id`) REFERENCES `t_siswa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_7` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  ADD CONSTRAINT `t_wali_kelas_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_wali_kelas_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `t_guru` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
