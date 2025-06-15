<?php
// Aktifkan error reporting untuk debugging.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sertakan file koneksi database
include '../db.php';

// Pastikan koneksi database berhasil sebelum melanjutkan
if (!$conn) {
    die("Koneksi database gagal di index.php (nilai): " . mysqli_connect_error());
}

// Query untuk mengambil data nilai dengan JOIN ke tabel mahasiswa dan matakuliah
// PASTIKAN NAMA TABEL: 'nilai', 'mahasiswa', 'matakuliah'
// PASTIKAN NAMA KOLOM: 'ID_Nilai' (nilai), 'nim' (nilai & mahasiswa), 'nama' (mahasiswa),
//                      'kode_matkul' (nilai & matakuliah), 'nama_matkul' (matakuliah), 'nilai' (nilai)
$sql = "
    SELECT n.ID_Nilai AS id, m.nim, m.nama, mk.nama_matkul, n.nilai
    FROM nilai n
    JOIN mahasiswa m ON n.nim = m.nim
    JOIN matakuliah mk ON n.kode_matkul = mk.kode_matkul
";
$result = mysqli_query($conn, $sql);

// Periksa apakah query berhasil dijalankan
if (!$result) {
    // Jika query gagal, tampilkan pesan error dari MySQL dan hentikan eksekusi
    die("Query SELECT gagal: " . mysqli_error($conn) . "<br>Pastikan tabel 'nilai', 'mahasiswa', dan 'matakuliah' sudah ada dan nama kolom JOIN sudah benar.");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Nilai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Custom CSS untuk tampilan yang lebih menarik */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5; /* Warna latar belakang lembut */
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Sesuaikan agar konten di atas */
            min-height: 100vh;
            margin: 0;
            padding: 40px 20px; /* Padding untuk konten */
            box-sizing: border-box; /* Pastikan padding tidak menambah lebar */
        }
        .card-container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 900px; /* Lebar maksimum container untuk tabel */
            text-align: left; /* Sesuaikan teks di dalam container */
            animation: fadeIn 1s ease-out;
        }
        h2 {
            color: #343a40;
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-align: center; /* Pusatkan judul */
        }
        .btn-custom {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
        .table {
            margin-top: 20px;
            border-collapse: separate; /* Penting untuk border-radius pada sel */
            border-spacing: 0;
            border-radius: 10px; /* Sudut membulat pada tabel */
            overflow: hidden; /* Penting agar border-radius berfungsi */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .table thead th {
            background-color: #007bff; /* Warna header tabel */
            color: #ffffff;
            font-weight: 600;
            padding: 12px 15px;
            border-bottom: none; /* Hapus border bawah default */
        }
        .table tbody tr {
            background-color: #ffffff;
            transition: background-color 0.2s ease;
        }
        .table tbody tr:nth-of-type(even) {
            background-color: #f8f9fa; /* Warna latar belakang selang-seling */
        }
        .table tbody tr:hover {
            background-color: #e2f0ff; /* Warna hover baris */
        }
        .table td, .table th {
            padding: 12px 15px;
            vertical-align: middle;
            border-top: 1px solid #dee2e6; /* Border antar baris */
        }
        .table td:first-child, .table th:first-child {
            border-top-left-radius: 10px; /* Sudut membulat untuk sel pertama di header/body */
        }
        .table td:last-child, .table th:last-child {
            border-top-right-radius: 10px; /* Sudut membulat untuk sel terakhir di header/body */
        }
        /* Override border-radius untuk thead/tbody agar hanya di pojok tabel */
        .table thead tr:first-child th:first-child {
            border-top-left-radius: 10px;
        }
        .table thead tr:first-child th:last-child {
            border-top-right-radius: 10px;
        }
        .table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 10px;
        }
        .table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 10px;
        }

        .btn-sm {
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.85em;
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529; /* Warna teks gelap */
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(255, 193, 7, 0.3);
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(220, 53, 69, 0.3);
        }
        .action-buttons {
            display: flex;
            gap: 8px; /* Jarak antar tombol aksi */
            justify-content: center; /* Pusatkan tombol aksi */
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
            .table thead th, .table tbody td {
                font-size: 0.85em;
                padding: 8px 10px;
            }
            .btn-sm {
                padding: 4px 8px;
                font-size: 0.75em;
            }
        }
    </style>
</head>
<body>
    <div class="card-container">
        <h2>Data Nilai Mahasiswa</h2>
        <div class="d-flex justify-content-between mb-3">
            <a href="create.php" class="btn btn-primary btn-custom">
                <i class="fas fa-plus-circle me-2"></i> Input Nilai
            </a>
            <a href="../index.php" class="btn btn-secondary btn-custom">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Mata Kuliah</th>
                        <th>Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Periksa apakah ada baris data yang ditemukan
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nim'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['nama'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['nama_matkul'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['nilai'] ?? '') ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="edit.php?id=<?= htmlspecialchars($row['id'] ?? '') ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete.php?id=<?= htmlspecialchars($row['id'] ?? '') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus?')">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-4'>Tidak ada data nilai mahasiswa.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
