<?php
// Aktifkan error reporting untuk debugging.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db.php';

// Pastikan koneksi database berhasil sebelum melanjutkan
if (!$conn) {
    die("Koneksi database gagal di edit.php: " . mysqli_connect_error());
}

// Ambil NIM dari parameter URL
$nim = isset($_GET['nim']) ? htmlspecialchars($_GET['nim']) : '';

// Periksa apakah NIM tidak kosong
if (empty($nim)) {
    die("<div class='alert alert-danger'>NIM tidak ditemukan. Kembali ke halaman utama.</div>");
}

// Jalankan query untuk mengambil data mahasiswa berdasarkan NIM
// Menggunakan Prepared Statements untuk keamanan
$sql_select = "SELECT nim, nama, alamat, tanggal_lahir FROM mahasiswa WHERE nim = ?";

if ($stmt_select = mysqli_prepare($conn, $sql_select)) {
    mysqli_stmt_bind_param($stmt_select, "s", $nim);
    mysqli_stmt_execute($stmt_select);
    $result_select = mysqli_stmt_get_result($stmt_select);
    $data = mysqli_fetch_assoc($result_select);
    mysqli_stmt_close($stmt_select);

    // Jika data tidak ditemukan, tampilkan pesan error
    if (!$data) {
        die("<div class='alert alert-danger'>Data mahasiswa dengan NIM: " . $nim . " tidak ditemukan.</div>");
    }
} else {
    die("<div class='alert alert-danger'>Error mempersiapkan statement SELECT: " . mysqli_error($conn) . "</div>");
}

// Proses form jika ada pengiriman POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '';
    $alamat = isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : '';
    $tgl = isset($_POST['tanggal_lahir']) ? htmlspecialchars($_POST['tanggal_lahir']) : '';

    // Validasi dasar
    if (empty($nama) || empty($alamat) || empty($tgl)) {
        echo "<div class='alert alert-danger'>Semua bidang wajib diisi!</div>";
    } else {
        // Jalankan query UPDATE menggunakan Prepared Statements
        $sql_update = "UPDATE mahasiswa SET nama=?, alamat=?, tanggal_lahir=? WHERE nim=?";

        if ($stmt_update = mysqli_prepare($conn, $sql_update)) {
            mysqli_stmt_bind_param($stmt_update, "ssss", $nama, $alamat, $tgl, $nim); // 'ssss' karena semua adalah string
            if (mysqli_stmt_execute($stmt_update)) {
                // Jika update berhasil, redirect ke index.php
                header("Location: index.php");
                exit(); // Penting untuk menghentikan eksekusi script setelah header
            } else {
                // Jika update gagal, tampilkan pesan error
                echo "<div class='alert alert-danger'>Error updating record: " . mysqli_stmt_error($stmt_update) . "</div>";
            }
            mysqli_stmt_close($stmt_update);
        } else {
            echo "<div class='alert alert-danger'>Error mempersiapkan statement UPDATE: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Mahasiswa</title>
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
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
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
        .form-control-static {
            padding: 10px 15px;
            border: 1px solid #e9ecef; /* Border lembut untuk teks statis */
            background-color: #e9ecef; /* Background lembut */
            border-radius: 8px;
            margin-bottom: 15px;
            color: #495057;
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
        <h2>Edit Mahasiswa</h2>
        <form method="post">
            <div class="mb-3">
                <label>NIM</label>
                <p class="form-control-static"><?= htmlspecialchars($data['nim'] ?? '') ?></p>
                <input type="hidden" name="nim" value="<?= htmlspecialchars($data['nim'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="nama_input">Nama</label>
                <input type="text" name="nama" id="nama_input" value="<?= htmlspecialchars($data['nama'] ?? '') ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="alamat_input">Alamat</label>
                <textarea name="alamat" id="alamat_input" class="form-control" required><?= htmlspecialchars($data['alamat'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="tanggal_lahir_input">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir_input" value="<?= htmlspecialchars($data['tanggal_lahir'] ?? '') ?>" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-custom">
                <i class="fas fa-save me-2"></i> Update
            </button>
            <a href="index.php" class="btn btn-secondary btn-custom">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </form>
    </div>
</body>
</html>
