<?php
// Aktifkan error reporting untuk debugging.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db.php';

// Pastikan koneksi database berhasil sebelum melanjutkan
if (!$conn) {
    die("<div class='alert alert-danger'>Koneksi database gagal di delete.php: " . mysqli_connect_error() . "</div>");
}

// Ambil NIM dari parameter URL
$nim = isset($_GET['nim']) ? htmlspecialchars($_GET['nim']) : '';

// Periksa apakah NIM tidak kosong
if (empty($nim)) {
    die("<div class='alert alert-danger'>NIM tidak ditemukan. Tidak dapat menghapus data.</div>");
}

// Mulai transaksi untuk memastikan semua operasi berhasil atau tidak sama sekali
mysqli_begin_transaction($conn);

try {
    // 1. Hapus data dari tabel 'ipk' yang terkait dengan NIM ini
    $sql_delete_ipk = "DELETE FROM ipk WHERE nim = ?";
    if ($stmt_ipk = mysqli_prepare($conn, $sql_delete_ipk)) {
        mysqli_stmt_bind_param($stmt_ipk, "s", $nim);
        if (!mysqli_stmt_execute($stmt_ipk)) {
            throw new Exception("Error menghapus data IPK: " . mysqli_stmt_error($stmt_ipk));
        }
        mysqli_stmt_close($stmt_ipk);
    } else {
        throw new Exception("Error mempersiapkan statement DELETE IPK: " . mysqli_error($conn));
    }

    // 2. Hapus data dari tabel 'nilai' yang terkait dengan NIM ini
    $sql_delete_nilai = "DELETE FROM nilai WHERE nim = ?";
    if ($stmt_nilai = mysqli_prepare($conn, $sql_delete_nilai)) {
        mysqli_stmt_bind_param($stmt_nilai, "s", $nim);
        if (!mysqli_stmt_execute($stmt_nilai)) {
            throw new Exception("Error menghapus data Nilai: " . mysqli_stmt_error($stmt_nilai));
        }
        mysqli_stmt_close($stmt_nilai);
    } else {
        throw new Exception("Error mempersiapkan statement DELETE Nilai: " . mysqli_error($conn));
    }

    // 3. Hapus data dari tabel 'mahasiswa'
    $sql_delete_mahasiswa = "DELETE FROM mahasiswa WHERE nim = ?";
    if ($stmt_mhs = mysqli_prepare($conn, $sql_delete_mahasiswa)) {
        mysqli_stmt_bind_param($stmt_mhs, "s", $nim);
        if (mysqli_stmt_execute($stmt_mhs)) {
            if (mysqli_stmt_affected_rows($stmt_mhs) > 0) {
                mysqli_commit($conn); // Commit transaksi jika semua berhasil
                header("Location: index.php");
                exit();
            } else {
                // Jika tidak ada baris mahasiswa yang terpengaruh, mungkin NIM tidak ditemukan
                throw new Exception("Data mahasiswa dengan NIM: " . $nim . " tidak ditemukan atau sudah terhapus.");
            }
        } else {
            throw new Exception("Error menghapus data Mahasiswa: " . mysqli_stmt_error($stmt_mhs));
        }
        mysqli_stmt_close($stmt_mhs);
    } else {
        throw new Exception("Error mempersiapkan statement DELETE Mahasiswa: " . mysqli_error($conn));
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
    <title>Hapus Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <a href="index.php" class="btn btn-secondary mt-3">Kembali ke Data Mahasiswa</a>
</body>
</html>
