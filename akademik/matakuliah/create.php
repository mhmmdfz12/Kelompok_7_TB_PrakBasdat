<?php
// Aktifkan error reporting untuk debugging.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sertakan file koneksi database
include '../db.php';

// Pastikan koneksi database berhasil sebelum melanjutkan
if (!$conn) {
    die("Koneksi database gagal di create.php (matakuliah): " . mysqli_connect_error());
}

// Inisialisasi array untuk menyimpan daftar dosen
$dosen_list = [];

// Query untuk mengambil data dosen dari tabel 'dosen'
// Pastikan nama kolom 'nip' dan 'nama_dosen' sesuai dengan database Anda
$sql_dosen = "SELECT nip, nama_dosen FROM dosen ORDER BY nama_dosen ASC";
$result_dosen = mysqli_query($conn, $sql_dosen);

// Periksa apakah query dosen berhasil dijalankan
if ($result_dosen) {
    // Ambil setiap baris data dosen dan simpan ke dalam array
    while ($row_dosen = mysqli_fetch_assoc($result_dosen)) {
        $dosen_list[] = $row_dosen;
    }
    mysqli_free_result($result_dosen); // Bebaskan memori hasil query
} else {
    // Tampilkan error jika query dosen gagal
    echo "<div class='alert alert-warning'>Error mengambil data dosen: " . mysqli_error($conn) . "</div>";
}


// Proses form jika ada pengiriman POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pastikan kunci array ada sebelum mengaksesnya
    // Gunakan nama 'name' dari input HTML Anda
    $kode_matkul = isset($_POST['kode_matkul']) ? htmlspecialchars($_POST['kode_matkul']) : '';
    $nama_matkul = isset($_POST['nama_matkul']) ? htmlspecialchars($_POST['nama_matkul']) : '';
    $sks = isset($_POST['sks']) ? htmlspecialchars($_POST['sks']) : '';
    $nip_dosen = isset($_POST['nip_dosen']) ? htmlspecialchars($_POST['nip_dosen']) : '';

    // Validasi dasar (opsional tapi sangat disarankan)
    if (empty($kode_matkul) || empty($nama_matkul) || empty($sks) || empty($nip_dosen)) {
        echo "<div class='alert alert-danger'>Semua bidang wajib diisi!</div>";
    } else {
        // Query INSERT untuk menyimpan data mata kuliah
        // Pastikan nama tabel adalah 'matakuliah' (huruf kecil semua)
        // Pastikan nama kolom adalah 'kode_matkul', 'nama_matkul', 'sks', 'nip_dosen' (sesuai definisi tabel)
        $sql_insert = "INSERT INTO matakuliah (kode_matkul, nama_matkul, sks, nip_dosen) VALUES (?, ?, ?, ?)";

        // Menggunakan Prepared Statements untuk keamanan dan mencegah SQL Injection
        if ($stmt = mysqli_prepare($conn, $sql_insert)) {
            mysqli_stmt_bind_param($stmt, "ssis", $kode_matkul, $nama_matkul, $sks, $nip_dosen); // s=string, i=integer
            if (mysqli_stmt_execute($stmt)) {
                // Jika insert berhasil, redirect ke index.php
                header("Location: index.php");
                exit(); // Penting untuk menghentikan eksekusi script setelah header
            } else {
                // Jika insert gagal, tampilkan pesan error
                echo "<div class='alert alert-danger'>Error: " . mysqli_stmt_error($stmt) . "</div>";
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
    <title>Tambah Mata Kuliah</title>
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
        <h2>Tambah Mata Kuliah</h2>
        <form method="post">
            <div class="mb-3">
                <label for="kode_matkul">Kode Mata Kuliah</label>
                <input type="text" name="kode_matkul" id="kode_matkul" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="nama_matkul">Nama Mata Kuliah</label>
                <input type="text" name="nama_matkul" id="nama_matkul" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="sks">SKS</label>
                <input type="number" name="sks" id="sks" class="form-control" required min="1">
            </div>
            <div class="mb-3">
                <label for="nip_dosen">Dosen Pengampu</label>
                <select name="nip_dosen" id="nip_dosen" class="form-control" required>
                    <option value="">-- Pilih Dosen --</option>
                    <?php
                    // Loop untuk menampilkan setiap dosen sebagai opsi di dropdown
                    foreach ($dosen_list as $dosen) {
                        // Pastikan kunci 'nip' dan 'nama_dosen' ada sebelum diakses
                        $nip_dosen_option = isset($dosen['nip']) ? htmlspecialchars($dosen['nip']) : '';
                        $nama_dosen_option = isset($dosen['nama_dosen']) ? htmlspecialchars($dosen['nama_dosen']) : '';
                        echo "<option value='{$nip_dosen_option}'>{$nama_dosen_option}</option>";
                    }
                    ?>
                </select>
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
