-- Membuat database dengan nama 'akademik'
-- (tidak tercantum di sini, tetapi asumsinya database sudah dibuat dan aktif)

DELIMITER $$

-- Membuat prosedur untuk menampilkan daftar mahasiswa berdasarkan dosen pengampu
CREATE DEFINER=`root`@`localhost` PROCEDURE `daftar_mahasiswa_per_dosen` (IN `nipDosen` VARCHAR(10))
BEGIN
    -- Menampilkan mahasiswa yang mengambil mata kuliah yang diajar oleh dosen tertentu
    SELECT DISTINCT M.NIM, M.Nama
    FROM Mahasiswa M
    JOIN Nilai N ON M.NIM = N.NIM
    JOIN matakuliah MK ON N.Kode_Matkul = MK.Kode_Matkul
    WHERE MK.NIP = nipDosen;
END$$

-- Membuat prosedur untuk menampilkan daftar mahasiswa yang mengambil suatu mata kuliah tertentu
CREATE DEFINER=`root`@`localhost` PROCEDURE `daftar_mahasiswa_per_mk` (IN `kodeMK` VARCHAR(10))
BEGIN
    SELECT M.NIM, M.Nama
    FROM Mahasiswa M
    JOIN Nilai N ON M.NIM = N.NIM
    WHERE N.Kode_Matkul = kodeMK;
END$$

DELIMITER ;

-- --------------------------------------------------------

-- Struktur tabel untuk menyimpan data dosen
CREATE TABLE `dosen` (
  `NIP` varchar(10) NOT NULL,                    -- Nomor Induk Pegawai (Primary Key)
  `Nama_Dosen` varchar(100) DEFAULT NULL,       -- Nama lengkap dosen
  `Email` varchar(100) DEFAULT NULL             -- Alamat email dosen
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Struktur tabel untuk menyimpan data IPK mahasiswa
CREATE TABLE `ipk` (
  `NIM` varchar(10) NOT NULL,                   -- Nomor Induk Mahasiswa (Primary Key)
  `Total_SKS` int(11) DEFAULT 0,                -- Total SKS yang telah ditempuh
  `Total_Bobot` decimal(10,2) DEFAULT 0.00,     -- Total nilai bobot (nilai x SKS)
  `IPK` decimal(4,2) DEFAULT 0.00               -- Nilai IPK mahasiswa
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Struktur tabel untuk menyimpan data mahasiswa
CREATE TABLE `mahasiswa` (
  `NIM` varchar(10) NOT NULL,                   -- Nomor Induk Mahasiswa (Primary Key)
  `Nama` varchar(100) DEFAULT NULL,             -- Nama lengkap mahasiswa
  `Alamat` text DEFAULT NULL,                   -- Alamat tempat tinggal
  `Tanggal_Lahir` date DEFAULT NULL             -- Tanggal lahir mahasiswa
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Struktur tabel untuk menyimpan data mata kuliah
CREATE TABLE `matakuliah` (
  `kode_matkul` varchar(20) NOT NULL,           -- Kode unik mata kuliah (Primary Key)
  `nama_matkul` varchar(100) NOT NULL,          -- Nama mata kuliah
  `sks` int(11) NOT NULL,                       -- Jumlah SKS
  `nip_dosen` varchar(20) DEFAULT NULL          -- NIP dosen pengampu (relasi ke tabel dosen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Struktur tabel untuk menyimpan nilai mahasiswa
CREATE TABLE `nilai` (
  `ID_Nilai` int(11) NOT NULL,                  -- ID unik nilai (Primary Key, Auto Increment)
  `NIM` varchar(10) DEFAULT NULL,               -- NIM mahasiswa (relasi ke tabel mahasiswa)
  `Kode_Matkul` varchar(10) DEFAULT NULL,       -- Kode mata kuliah (relasi ke tabel matakuliah)
  `Nilai` decimal(5,2) DEFAULT NULL,            -- Nilai akhir yang diperoleh
  `Semester` varchar(10) DEFAULT NULL           -- Semester pengambilan mata kuliah
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Trigger untuk menghitung IPK secara otomatis setiap kali nilai baru dimasukkan
DELIMITER $$
CREATE TRIGGER `hitung_ipk_setelah_nilai` AFTER INSERT ON `nilai` FOR EACH ROW
BEGIN
    DECLARE bobot_ipk DECIMAL(3,2);
    DECLARE sks INT;
    DECLARE total_sks INT;
    DECLARE total_bobot DECIMAL(10,2);

    -- Mengambil jumlah SKS dari mata kuliah terkait
    SELECT sks INTO sks 
    FROM matakuliah 
    WHERE kode_matkul = NEW.kode_matkul;

    -- Mengonversi nilai ke bobot IPK
    IF NEW.Nilai >= 85 THEN
        SET bobot_ipk = 4.00;
    ELSEIF NEW.Nilai >= 75 THEN
        SET bobot_ipk = 3.00;
    ELSEIF NEW.Nilai >= 65 THEN
        SET bobot_ipk = 2.00;
    ELSEIF NEW.Nilai >= 50 THEN
        SET bobot_ipk = 1.00;
    ELSE
        SET bobot_ipk = 0.00;
    END IF;

    -- Menghitung nilai bobot (nilai x SKS)
    SET @nilai_bobot = bobot_ipk * sks;

    -- Jika data IPK mahasiswa sudah ada, maka update
    IF EXISTS (SELECT 1 FROM ipk WHERE nim = NEW.NIM) THEN
        SELECT Total_SKS, Total_Bobot INTO total_sks, total_bobot 
        FROM ipk WHERE nim = NEW.NIM;

        SET total_sks = total_sks + sks;
        SET total_bobot = total_bobot + @nilai_bobot;

        UPDATE ipk
        SET Total_SKS = total_sks,
            Total_Bobot = total_bobot,
            IPK = total_bobot / total_sks
        WHERE nim = NEW.NIM;
    ELSE
        -- Jika belum ada, maka buat entri baru
        INSERT INTO ipk (NIM, Total_SKS, Total_Bobot, IPK)
        VALUES (NEW.NIM, sks, @nilai_bobot, @nilai_bobot / sks);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

-- Menentukan primary key untuk tabel dosen
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`NIP`);

-- Menentukan primary key untuk tabel ipk
ALTER TABLE `ipk`
  ADD PRIMARY KEY (`NIM`);

-- Menentukan primary key untuk tabel mahasiswa
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`NIM`);

-- Menentukan primary key dan foreign key untuk tabel matakuliah
ALTER TABLE `matakuliah`
  ADD PRIMARY KEY (`kode_matkul`),
  ADD KEY `nip_dosen` (`nip_dosen`);

-- Menentukan primary key dan foreign key untuk tabel nilai
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`ID_Nilai`),
  ADD KEY `NIM` (`NIM`),
  ADD KEY `Kode_Matkul` (`Kode_Matkul`);

-- Menentukan auto increment untuk ID_Nilai pada tabel nilai
ALTER TABLE `nilai`
  MODIFY `ID_Nilai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

-- --------------------------------------------------------

-- Menambahkan foreign key (relasi) antar tabel
ALTER TABLE `ipk`
  ADD CONSTRAINT `ipk_ibfk_1` FOREIGN KEY (`NIM`) REFERENCES `mahasiswa` (`NIM`);

ALTER TABLE `matakuliah`
  ADD CONSTRAINT `matakuliah_ibfk_1` FOREIGN KEY (`nip_dosen`) REFERENCES `dosen` (`NIP`);

ALTER TABLE `nilai`
  ADD CONSTRAINT `nilai_ibfk_1` FOREIGN KEY (`NIM`) REFERENCES `mahasiswa` (`NIM`),
  ADD CONSTRAINT `nilai_ibfk_2` FOREIGN KEY (`Kode_Matkul`) REFERENCES `matakuliah` (`kode_matkul`);

-- Menyelesaikan perintah SQL
COMMIT;
