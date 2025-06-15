<?php
// Aktifkan error reporting untuk debugging.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db.php';

// Pastikan koneksi database berhasil sebelum melanjutkan
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Query untuk menghitung IPK secara otomatis
// IPK dihitung sebagai SUM(nilai * sks) / SUM(sks)
// Menggunakan LEFT JOIN agar mahasiswa tanpa nilai tetap muncul dengan IPK 0.00
$sql_ipk = "
    SELECT
        m.nim,
        m.nama,
        COALESCE(SUM(
            CASE
                WHEN n.nilai >= 85 THEN 4.0
                WHEN n.nilai >= 75 THEN 3.0
                WHEN n.nilai >= 65 THEN 2.0
                WHEN n.nilai >= 50 THEN 1.0
                ELSE 0.0
            END * mk.sks
        ) / NULLIF(SUM(mk.sks), 0), 0) AS ipk
    FROM
        mahasiswa m
    LEFT JOIN
        nilai n ON m.nim = n.nim
    LEFT JOIN
        matakuliah mk ON n.kode_matkul = mk.kode_matkul
    GROUP BY
        m.nim, m.nama
    ORDER BY
        m.nama ASC
";

$result_ipk = mysqli_query($conn, $sql_ipk);

// Periksa apakah query berhasil dijalankan
if (!$result_ipk) {
    die("Query IPK gagal: " . mysqli_error($conn) . "<br>Pastikan tabel 'mahasiswa', 'nilai', dan 'matakuliah' sudah ada dan nama kolom JOIN sudah benar.");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Data IPK Mahasiswa</title>
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
            max-width: 800px; /* Lebar maksimum container untuk tabel IPK */
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
        }
    </style>
</head>
<body>
    <div class="card-container">
        <h2>Data IPK Mahasiswa</h2>
        <div class="d-flex justify-content-start mb-3">
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
                        <th>IPK</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result_ipk) > 0) {
                        while($row = mysqli_fetch_assoc($result_ipk)):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nim'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['nama'] ?? '') ?></td>
                        <td><?= number_format($row['ipk'] ?? 0, 2) ?></td>
                    </tr>
                    <?php
                        endwhile;
                    } else {
                        echo "<tr><td colspan='3' class='text-center py-4'><i class='fas fa-info-circle me-2'></i>Tidak ada data IPK mahasiswa.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
