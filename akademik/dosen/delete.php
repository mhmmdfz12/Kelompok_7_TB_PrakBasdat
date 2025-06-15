<?php
// Aktifkan error reporting untuk debugging.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db.php';

// Pastikan koneksi database berhasil sebelum melanjutkan
if (!$conn) {
    die("<div class='alert alert-danger'>Koneksi database gagal di delete.php (dosen): " . mysqli_connect_error() . "</div>");
}

// Ambil NIP dari parameter URL
$nip = isset($_GET['nip']) ? htmlspecialchars($_GET['nip']) : '';

// Periksa apakah NIP tidak kosong
if (empty($nip)) {
    die("<div class='alert alert-danger'>NIP tidak ditemukan. Tidak dapat menghapus data.</div>");
}

// Mulai transaksi untuk memastikan semua operasi berhasil atau tidak sama sekali
mysqli_begin_transaction($conn);

try {
    // 1. Hapus data dari tabel 'matakuliah' yang terkait dengan NIP dosen ini
    // Penting: Jika ada tabel lain yang merujuk ke dosen (selain matakuliah),
    // Anda perlu menambahkan DELETE statement untuk tabel-tabel tersebut di sini.
    $sql_delete_matkul = "DELETE FROM matakuliah WHERE nip_dosen = ?";
    if ($stmt_matkul = mysqli_prepare($conn, $sql_delete_matkul)) {
        mysqli_stmt_bind_param($stmt_matkul, "s", $nip);
        if (!mysqli_stmt_execute($stmt_matkul)) {
            throw new Exception("Error menghapus data Mata Kuliah terkait: " . mysqli_stmt_error($stmt_matkul));
        }
        mysqli_stmt_close($stmt_matkul);
    } else {
        throw new Exception("Error mempersiapkan statement DELETE Mata Kuliah: " . mysqli_error($conn));
    }

    // 2. Hapus data dari tabel 'dosen'
    $sql_delete_dosen = "DELETE FROM dosen WHERE nip = ?";
    if ($stmt_dosen = mysqli_prepare($conn, $sql_delete_dosen)) {
        mysqli_stmt_bind_param($stmt_dosen, "s", $nip);
        if (mysqli_stmt_execute($stmt_dosen)) {
            if (mysqli_stmt_affected_rows($stmt_dosen) > 0) {
                mysqli_commit($conn); // Commit transaksi jika semua berhasil
                header("Location: index.php");
                exit();
            } else {
                // Jika tidak ada baris dosen yang terpengaruh, mungkin NIP tidak ditemukan
                throw new Exception("Data dosen dengan NIP: " . $nip . " tidak ditemukan atau sudah terhapus.");
            }
        } else {
            throw new Exception("Error menghapus data Dosen: " . mysqli_stmt_error($stmt_dosen));
        }
        mysqli_stmt_close($stmt_dosen);
    } else {
        throw new Exception("Error mempersiapkan statement DELETE Dosen: " . mysqli_error($conn));
    }

} catch (Exception $e) {
    mysqli_rollback($conn); // Rollback transaksi jika ada error
    echo "<div class='alert alert-danger'>Terjadi kesalahan saat menghapus data: " . $e->getMessage() . "</div>";
} finally {
    mysqli_close($conn); // Tutup koneksi di akhir
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hapus Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <a href="index.php" class="btn btn-secondary mt-3">Kembali ke Data Dosen</a>
</body>
</html>
