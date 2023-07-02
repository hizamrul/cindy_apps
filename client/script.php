<?php
// Konfigurasi koneksi database
$host = 'localhost'; // Ganti dengan nama host Anda
$user = 'root'; // Ganti dengan nama pengguna MySQL Anda
$pass = ''; // Ganti dengan kata sandi MySQL Anda
$db   = 'lstm'; // Ganti dengan nama database Anda

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $pass, $db);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Query untuk mengambil data dari tabel daftar_perusahaan
$sql = "SELECT * FROM daftar_perusahaan";
$result = $conn->query($sql);

// Memeriksa apakah query berhasil dieksekusi
if ($result === false) {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Memeriksa apakah ada data yang diambil
if ($result->num_rows > 0) {
    // Menampilkan data per baris
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"] . "<br>";
        echo "Nama Perusahaan: " . $row["nama_perusahaan"] . "<br>";
        echo "kode: " . $row["kode"] . "<br>";
        echo "<br>";
    }
} else {
    echo "Tidak ada data yang ditemukan.";
}

// Menutup koneksi database
$conn->close();
?>
