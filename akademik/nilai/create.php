<?php
// Aktifkan error reporting untuk debugging.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sertakan file koneksi database
include '../db.php';

// Pastikan koneksi database berhasil sebelum melanjutkan
if (!$conn) {
    die("Koneksi database gagal di create.php (nilai): " . mysqli_connect_error());
}

// Inisialisasi array untuk menyimpan daftar mahasiswa
$mahasiswa_list = [];
// Query untuk mengambil data mahasiswa
$sql_mhs = "SELECT nim, nama FROM mahasiswa ORDER BY nama ASC";
$result_mhs = mysqli_query($conn, $sql_mhs);

if ($result_mhs) {
    while ($row_mhs = mysqli_fetch_assoc($result_mhs)) {
        $mahasiswa_list[] = $row_mhs;
    }
    mysqli_free_result($result_mhs); // Bebaskan memori hasil query
} else {
    echo "<div class='alert alert-warning'>Peringatan: Gagal memuat daftar mahasiswa: " . mysqli_error($conn) . "</div>";
}


// Inisialisasi array untuk menyimpan daftar mata kuliah
$matakuliah_list = [];
// Query untuk mengambil data mata kuliah
$sql_matkul = "SELECT kode_matkul, nama_matkul FROM matakuliah ORDER BY nama_matkul ASC";
$result_matkul = mysqli_query($conn, $sql_matkul);

if ($result_matkul) {
    while ($row_matkul = mysqli_fetch_assoc($result_matkul)) {
        $matakuliah_list[] = $row_matkul;
    }
    mysqli_free_result($result_matkul); // Bebaskan memori hasil query
} else {
    echo "<div class='alert alert-warning'>Peringatan: Gagal memuat daftar mata kuliah: " . mysqli_error($conn) . "</div>";
}


// Proses form jika ada pengiriman POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pastikan kunci array ada sebelum mengaksesnya
    $nim = isset($_POST['nim']) ? htmlspecialchars($_POST['nim']) : '';
    $kode = isset($_POST['kode_matkul']) ? htmlspecialchars($_POST['kode_matkul']) : '';
    $nilai = isset($_POST['nilai']) ? htmlspecialchars($_POST['nilai']) : '';

    // Validasi dasar (opsional tapi sangat disarankan)
    if (empty($nim) || empty($kode) || $nilai === '') { // Periksa nilai kosong, termasuk 0
        echo "<div class='alert alert-danger'>Semua bidang wajib diisi!</div>";
    } else {
        // Menggunakan Prepared Statements untuk keamanan dan mencegah SQL Injection
        // Pastikan nama tabel 'nilai' dan kolom 'nim', 'kode_matkul', 'nilai' sudah benar
        $sql_insert = "INSERT INTO nilai (nim, kode_matkul, nilai) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql_insert)) {
            // "ssd" -> s=string (nim), s=string (kode_matkul), d=double (nilai)
            // Gunakan 'd' untuk nilai desimal/float, atau 'i' jika nilai selalu integer
            mysqli_stmt_bind_param($stmt, "ssd", $nim, $kode, $nilai);
            if (mysqli_stmt_execute($stmt)) {
                // Jika insert berhasil, redirect ke index.php
                header("Location: index.php");
                exit(); // Penting untuk menghentikan eksekusi script setelah header
            } else {
                // Jika insert gagal, tampilkan pesan error
                echo "<div class='alert alert-danger'>Error menyimpan nilai: " . mysqli_stmt_error($stmt) . "</div>";
            }
            mysqli_stmt_close($stmt); // Tutup statement
        } else {
            echo "<div class='alert alert-danger'>Error mempersiapkan statement: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Input Nilai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Custom CSS untuk tampilan yang lebih menarik */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5; /* Warna latar belakang lembut */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Pastikan body mengisi seluruh tinggi viewport */
            margin: 0;
            padding: 40px 20px; /* Padding untuk konten */
            box-sizing: border-box;
        }
        .card-container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 600px; /* Lebar maksimum container untuk form */
            text-align: left;
            animation: fadeIn 1s ease-out;
        }
        h2 {
            color: #343a40;
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-align: center; /* Pusatkan judul */
        }
        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        .mb-3 label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: block; /* Pastikan label di baris baru */
        }
        .btn-custom {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-right: 10px; /* Jarak antar tombol */
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(40, 167, 69, 0.3);
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(108, 117, 125, 0.3);
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        /* Animasi Fade In */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsif untuk layar kecil */
        @media (max-width: 768px) {
            .card-container {
                padding: 25px;
                margin: 20px;
            }
            h2 {
                font-size: 1.8em;
            }
            .btn-custom {
                padding: 8px 15px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <div class="card-container">
        <h2>Input Nilai Mahasiswa</h2>
        <form method="post">
            <div class="mb-3">
                <label for="nim_mahasiswa">Mahasiswa</label>
                <select name="nim" id="nim_mahasiswa" class="form-control" required>
                    <option value="">-- Pilih Mahasiswa --</option>
                    <?php foreach($mahasiswa_list as $m): ?>
                        <option value="<?= htmlspecialchars($m['nim'] ?? '') ?>">
                            <?= htmlspecialchars($m['nim'] ?? '') . " - " . htmlspecialchars($m['nama'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="kode_matkul_input">Mata Kuliah</label>
                <select name="kode_matkul" id="kode_matkul_input" class="form-control" required>
                    <option value="">-- Pilih Mata Kuliah --</option>
                    <?php foreach($matakuliah_list as $mk): ?>
                        <option value="<?= htmlspecialchars($mk['kode_matkul'] ?? '') ?>">
                            <?= htmlspecialchars($mk['nama_matkul'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="nilai_input">Nilai</label>
                <input type="number" name="nilai" id="nilai_input" class="form-control" min="0" max="100" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-success btn-custom">
                <i class="fas fa-save me-2"></i> Simpan
            </button>
            <a href="index.php" class="btn btn-secondary btn-custom">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </form>
    </div>
</body>
</html>
