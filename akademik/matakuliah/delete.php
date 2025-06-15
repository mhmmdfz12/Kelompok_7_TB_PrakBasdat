<?php
include '../db.php';
$kode = $_GET['kode'];
mysqli_query($conn, "DELETE FROM matakuliah WHERE kode_matkul='$kode'");
header("Location: index.php");
?>
