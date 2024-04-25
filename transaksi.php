<?php
include "fungsi.php";

$host       = "localhost";
$user       = "root";
$pass       = "";
$db         = "sipenjualan_danuardi";

$koneksi    = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) { //cek koneksi
    die("Tidak bisa terkoneksi ke database");
}
$kode_transaksi = "";
$kode_brg       = "";
$nama_brg       = "";
$harga          = "";
$jumlah         = "";
$total_bayar    = "";
$tanggal           = "";
$sukses         = "";
$error          = "";

// Fungsi untuk menghasilkan kode transaksi baru secara unik
function generateUniqueTransactionCode($koneksi) {
    $query = mysqli_query($koneksi, "SELECT MAX(RIGHT(kode_transaksi, 4)) AS max_id FROM transaksi");
    $data = mysqli_fetch_assoc($query);
    $next_id = intval($data['max_id']) + 1;
    $next_code = 'TRX' . sprintf("%04s", $next_id);
    return $next_code;
}

if (isset($_GET['op'])) {
    $op = $_GET['op'];
} else {
    $op = "";
}
if($op == 'delete'){ // delete data
    $kode_transaksi         = $_GET['kode_transaksi'];
    $sql1                   = "delete from transaksi where kode_transaksi = '$kode_transaksi'";
    $q1                     = mysqli_query($koneksi,$sql1);
    if($q1){
        $sukses = "Berhasil hapus data";
    }else{
        $error  = "Gagal melakukan delete data";
    }
}




if ($op == 'edit') {
    $kode_transaksi    = $_GET['kode_transaksi'];
    $sql1 = "SELECT t.kode_transaksi, t.kode_brg, t.nama_brg, t.harga, t.jumlah, t.total_bayar, t.tanggal
    FROM transaksi t
    INNER JOIN barang b ON t.kode_brg = b.kode_brg
    WHERE t.kode_transaksi = '$kode_transaksi'";
    $q1                = mysqli_query($koneksi, $sql1);
    $r1                = mysqli_fetch_array($q1);
    $kode_transaksi    = $r1['kode_transaksi'];
    $kode_brg          = $r1['kode_brg'];
    $nama_brg          = $r1['nama_brg'];
    $harga             = $r1['harga'];
    $jumlah            = $r1['jumlah'];
    $total_bayar       = $r1['total_bayar'];
    $tanggal              = $r1['tanggal'];


    if ($kode_transaksi == '') {
        $error = "Data tidak ditemukan";
    }
}
if (isset($_POST['simpan'])) { //untuk create
    $kode_transaksi    = generateUniqueTransactionCode($koneksi);
    $kode_brg          = $_POST['kode_brg'];
    $nama_brg          = $_POST['nama_brg'];
    $harga             = $_POST['harga'];
    $jumlah            = $_POST['jumlah'];
    $total_bayar       = $_POST['total_bayar'];
    $tanggal              = $_POST['tanggal'];


    if ($kode_transaksi && $kode_brg && $nama_brg && $harga && $jumlah && $total_bayar && $tanggal) {
        if ($op == 'edit') { //untuk update

            $sql1 = "UPDATE transaksi SET kode_brg = '$kode_brg', nama_brg = '$nama_brg', harga = '$harga', jumlah = '$jumlah', total_bayar = '$total_bayar', tanggal = '$tanggal' WHERE kode_transaksi = '$kode_transaksi'";
            $q1         = mysqli_query($koneksi, $sql1);

            $sql2 = "UPDATE barang SET nama_brg = '$nama_brg', harga = '$harga', jumlah = '$jumlah' WHERE kode_brg = '$kode_brg'";
            $q2         = mysqli_query($koneksi, $sql2);
            if ($q1 && $q2) {
                $sukses = "Data berhasil diupdate";
            } else {
                $error  = "Data gagal diupdate";
            }
        } else { // Insert Data Transaksi
            // Menggunakan INNER JOIN untuk mengambil data barang yang sesuai dengan kode_brg
            $sql = "INSERT INTO transaksi(kode_transaksi, kode_brg, nama_brg, harga, jumlah, total_bayar, tanggal) 
                    SELECT '$kode_transaksi', b.kode_brg, b.nama_brg, b.harga, '$jumlah', '$total_bayar', '$tanggal'
                    FROM barang b
                    WHERE b.kode_brg = '$kode_brg'";
            $q1 = mysqli_query($koneksi, $sql);
            
            if ($q1) {
                $sukses = "Berhasil memasukkan data baru";
            } else {
                $error = "Gagal memasukkan data transaksi";
            }
        }
    }
        
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penjualan Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .mx-auto {
            width: 800px
        }

        .card {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="mx-auto">
        <!-- untuk memasukkan data -->
        <div class="card">
            <div class="card-header">
                Daftar Transaksi
            </div>
            <div class="card-body">
                <?php
                if ($error) {
                ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error ?>
                    </div>
                <?php
                    header("refresh:1;url=transaksi.php");//1 : detik
                }
                ?>
                <?php
                if ($sukses) {
                ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $sukses ?>
                    </div>
                <?php
                    header("refresh:5;url=transaksi.php");
                }



                ?>
                <form action="" method="POST">
                    <div class="mb-3 row">
                        <label for="kode_transaksi" class="col-sm-2 col-form-label">Kode Transaksi</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="kode_transaksi" required="required" name="kode_transaksi" value="<?php echo $kode_transaksi ?>" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                    <label for="kode_brg"  class="col-sm-2 col-form-label">Kode Barang:</label><br>
                        <div class="col-sm-10">
                        <select class="form-select" required="required" name="kode_brg" id="kode_brg">
                                <?php
                                // Ambil data kode barang untuk ditampilkan sebagai opsi
                                $sql_kode_brg = "SELECT kode_brg FROM barang";
                                $result_kode_brg = mysqli_query($koneksi, $sql_kode_brg);
                                while ($row_kode_brg = mysqli_fetch_array($result_kode_brg)) {
                                    $selected = ($row_kode_brg['kode_brg'] == $kode_brg) ? 'selected' : '';
                                    echo "<option value='" . $row_kode_brg['kode_brg'] . "' $selected>" . $row_kode_brg['kode_brg'] . "</option>";
                                }
                                ?>
                            </select>                       
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="nama_brg" class="col-sm-2 col-form-label">Nama Barang</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nama_brg" required="required" name="nama_brg" value="<?php echo $nama_brg ?>"readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="harga" class="col-sm-2 col-form-label">harga satuan</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="harga" required="required" name="harga" value="<?php echo $harga ?>"readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="jumlah" class="col-sm-2 col-form-label">Jumlah</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="jumlah" required="required" name="jumlah" value="<?php echo $jumlah ?>"readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                    <label for="total_bayar" class="col-sm-2 col-form-label">Total Bayar</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="total_bayar" required="required" name="total_bayar" value="<?php echo $total_bayar ?>" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="tanggal" class="col-sm-2 col-form-label">Tanggal</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="tanggal" required="required" name="tanggal" value="<?php echo $tanggal ?>">
                        </div>
                    </div>
                    <div class="col-12">
                    <td scope="row">
                        <input type="submit" name="simpan" value="Simpan Data" class="btn btn-primary" />
                        <a href="index.php"><button type="button" class="btn btn-secondary">Daftar Barang</button></a>
                    </td>
                    </div>
                </form>
            </div>
        </div>

        <!-- untuk mengeluarkan data -->
        <div class="card">
            <div class="card-header text-white bg-success">
                Data Transaksi
            </div>
            <div class="card-body" >
                            <table class="table" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Kode Transaksi</th>
                            <th scope="col">Kode Barang</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Total Bayar</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT t.*, b.nama_brg, b.harga, b.jumlah
                                FROM transaksi t
                                INNER JOIN barang b ON t.kode_brg = b.kode_brg
                                ORDER BY t.kode_transaksi DESC";

                        $result = mysqli_query($koneksi, $sql);
                        while ($r2 = mysqli_fetch_array($result)) {
                            $kode_transaksi    = $r2['kode_transaksi'];
                            $kode_brg          = $r2['kode_brg'];
                            $nama_brg          = $r2['nama_brg'];
                            $harga             = $r2['harga'];
                            $jumlah            = $r2['jumlah'];
                            $total_bayar       = $r2['total_bayar'];
                            $tanggal           = $r2['tanggal'];
                        

                        ?>
                            <tr>
                                <td scope="row"><?php echo $kode_transaksi ?></td>
                                <td scope="row"><?php echo $kode_brg ?></td>
                                <td scope="row"><?php echo $nama_brg ?></td>
                                <td scope="row"><?php echo $harga ?></td>
                                <td scope="row"><?php echo $jumlah ?></td>
                                <td scope="row"><?php echo $total_bayar ?></td>
                                <td scope="row"><?php echo date('d-M-Y', strtotime($tanggal)) ?></td>
                                <td scope="row">
                                    <a href="transaksi.php?op=delete&kode_transaksi=<?php echo $kode_transaksi?>" onclick="return confirm('Yakin mau delete data?')"><button type="button" class="btn btn-danger">Delete</button></a>            
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
    document.getElementById('kode_brg').addEventListener('change', function() {
        var kode_brg = this.value;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_barang.php?kode_brg=' + kode_brg, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var data = JSON.parse(xhr.responseText);
                document.getElementById('nama_brg').value = data.nama_brg;
                document.getElementById('harga').value = data.harga;
                document.getElementById('jumlah').value = data.jumlah;

                // Hitung total bayar
                var harga = parseFloat(data.harga);
                var jumlah = parseFloat(data.jumlah);
                var total_bayar = harga * jumlah;
                document.getElementById('total_bayar').value = total_bayar;
            }
        };
        xhr.send();
    });
</script>

</body>
</html>