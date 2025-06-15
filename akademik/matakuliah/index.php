<?php
// Aktifkan error reporting untuk debugging.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sertakan file koneksi database
include '../db.php';

// Pastikan koneksi database berhasil sebelum melanjutkan
if (!$conn) {
    die("Koneksi database gagal di index.php (matakuliah): " . mysqli_connect_error());
}

// Jalankan query untuk mengambil semua data dari tabel matakuliah
$sql = "SELECT kode_matkul, nama_matkul, sks, nip_dosen FROM matakuliah";
$result = mysqli_query($conn, $sql);

// Periksa apakah query berhasil dijalankan
if (!$result) {
    die("Query SELECT gagal: " . mysqli_error($conn) . "<br>Pastikan tabel 'matakuliah' sudah ada di database Anda.");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Mata Kuliah</title>
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
            max-width: 900px; /* Lebar maksimum container */
            text-align: left;
            animation: fadeIn 1s ease-out;
        }
        h2 {
            color: #343a40;
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-align: center;
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
        .table-bordered {
            border-radius: 10px;
            overflow: hidden; /* Penting untuk rounded corners pada tabel */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .table-bordered thead {
            background-color: #007bff;
            color: white;
        }
        .table-bordered th {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 2px solid #dee2e6;
        }
        .table-bordered td {
            padding: 12px 15px;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
        }
        .table-bordered tbody tr:nth-child(even) {
            background-color: #f8f9fa; /* Warna latar belakang selang-seling */
        }
        .table-bordered tbody tr:hover {
            background-color: #e2f0ff; /* Warna hover pada baris */
            transition: background-color 0.2s ease;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 6px;
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            transform: translateY(-1px);
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-1px);
        }
        .text-center {
            text-align: center;
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
                display: block; /* Tombol jadi block di layar kecil */
                width: 100%;
                margin-bottom: 10px;
                margin-right: 0;
            }
            .table-responsive {
                overflow-x: auto; /* Agar tabel bisa discroll horizontal */
            }
        }
    </style>
</head>
<body>
    <div class="card-container">
        <h2>Data Mata Kuliah</h2>
        <div class="mb-3 d-flex justify-content-start">
            <a href="create.php" class="btn btn-primary btn-custom">
                <i class="fas fa-plus-circle me-2"></i> Tambah Matakuliah
            </a>
            <a href="../index.php" class="btn btn-secondary btn-custom">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>SKS</th>
                        <th>Dosen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['kode_matkul']) ?></td>
                        <td><?= htmlspecialchars($row['nama_matkul']) ?></td>
                        <td><?= htmlspecialchars($row['sks']) ?></td>
                        <td><?= htmlspecialchars($row['nip_dosen']) ?></td>
                        <td>
                            <a href="edit.php?kode=<?= htmlspecialchars($row['kode_matkul']) ?>" class="btn btn-warning btn-sm me-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete.php?kode=<?= htmlspecialchars($row['kode_matkul']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-4'><i class='fas fa-info-circle me-2'></i>Tidak ada data mata kuliah.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>