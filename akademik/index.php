<!DOCTYPE html>
<html>
<head>
    <title>Sistem Akademik Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="mb-4">Sistem Akademik Mahasiswa</h2>
    <div class="list-group">
        <a href="mahasiswa/index.php" class="list-group-item list-group-item-action">Kelola Data Mahasiswa</a>
        <a href="dosen/index.php" class="list-group-item list-group-item-action">Kelola Data Dosen</a>
        <a href="matakuliah/index.php" class="list-group-item list-group-item-action">Kelola Mata Kuliah</a>
        <a href="nilai/index.php" class="list-group-item list-group-item-action">Input & Lihat Nilai Mahasiswa</a>
        <a href="ipk/index.php" class="list-group-item list-group-item-action">Lihat IPK Mahasiswa</a>
    </div>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Sistem Akademik Mahasiswa</title>
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
        }
        .card-container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 600px; /* Lebar maksimum container */
            text-align: center;
            animation: fadeIn 1s ease-out; /* Animasi fade-in */
        }
        h2 {
            color: #343a40;
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .list-group-item {
            display: flex;
            align-items: center;
            justify-content: center; /* Pusatkan konten */
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 10px;
            background-color: #e9ecef; /* Warna latar belakang item */
            color: #495057;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            text-decoration: none; /* Hapus underline default */
        }
        .list-group-item:hover {
            background-color: #007bff; /* Warna hover biru */
            color: #ffffff;
            transform: translateY(-3px); /* Efek angkat saat hover */
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.2);
        }
        .list-group-item i {
            margin-right: 15px;
            font-size: 1.2em;
            color: #6c757d; /* Warna ikon default */
            transition: color 0.3s ease;
        }
        .list-group-item:hover i {
            color: #ffffff; /* Warna ikon saat hover */
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
            .list-group-item {
                font-size: 0.95em;
                padding: 12px 15px;
            }
        }

        /* Animasi Fade In */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="card-container">
        <h2>Sistem Akademik Mahasiswa</h2>
        <div class="list-group">
            <a href="mahasiswa/index.php" class="list-group-item list-group-item-action">
                <i class="fas fa-user-graduate"></i> Kelola Data Mahasiswa
            </a>
            <a href="dosen/index.php" class="list-group-item list-group-item-action">
                <i class="fas fa-chalkboard-teacher"></i> Kelola Data Dosen
            </a>
            <a href="matakuliah/index.php" class="list-group-item list-group-item-action">
                <i class="fas fa-book"></i> Kelola Mata Kuliah
            </a>
            <a href="nilai/index.php" class="list-group-item list-group-item-action">
                <i class="fas fa-clipboard-check"></i> Input & Lihat Nilai Mahasiswa
            </a>
            <a href="ipk/index.php" class="list-group-item list-group-item-action">
                <i class="fas fa-chart-line"></i> Lihat IPK Mahasiswa
            </a>
        </div>
    </div>
</body>
</html>
