<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "sipenjualan_danuardi";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die("Tidak bisa terkoneksi ke database");
}

if (isset($_GET['kode_brg'])) {
    $kode_brg = $_GET['kode_brg'];
    
    // Query untuk mendapatkan data harga dan jumlah berdasarkan kode barang
    $sql = "SELECT nama_brg, harga, jumlah FROM barang WHERE kode_brg = '$kode_brg'";
    $result = mysqli_query($koneksi, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        
        // Mengembalikan data dalam format JSON
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Data tidak ditemukan']);
    }
} else {
    echo json_encode(['error' => 'Parameter kode_brg tidak diberikan']);
}
?>
