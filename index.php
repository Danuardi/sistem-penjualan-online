<?php
$host       = "localhost";
$user       = "root";
$pass       = "";
$db         = "sipenjualan_danuardi";

$koneksi    = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) { //koneksi
    die("Tidak bisa koneksi ke database");
}
$kode_brg       = "";
$nama_brg       = "";
$merk           = "";
$harga          = "";
$jumlah         = "";
$sukses         = "";
$error          = "";
$error1         = "";


if (isset($_GET['op'])) {
    $op = $_GET['op'];
} else {
    $op = "";
}

// Tambahkan bagian ini untuk menghapus semua data barang
if(isset($_GET['op']) && $_GET['op'] == 'hapus_semua') {
    $sql_check_empty = "SELECT COUNT(*) as total FROM barang";
    $result_check_empty = mysqli_query($koneksi, $sql_check_empty);
    $row = mysqli_fetch_assoc($result_check_empty);
    $total_barang = $row['total'];

    if($total_barang > 0) {
        $sql_delete_all = "DELETE FROM barang";
        $result_delete_all = mysqli_query($koneksi, $sql_delete_all);
        
        if($result_delete_all) {
            $sukses = "Berhasil menghapus semua data barang";
        } else {
            $error = "Gagal menghapus semua data barang";
        }
    } else {
        $error1 = "Tidak ada data barang yang bisa dihapus";
    }
}


if($op == 'delete'){ // delete data
    $kode_brg         = $_GET['kode_brg'];
    $sql1       = "delete from barang where kode_brg = '$kode_brg'";
    $q1         = mysqli_query($koneksi,$sql1);
    if($q1){
        $sukses = "Berhasil hapus data";
    }else{
        $error  = "Gagal melakukan delete data";
    }
} 
if ($op == 'edit') {
    $kode_brg    = $_GET['kode_brg'];
    $sql1       = "select * from barang where kode_brg = '$kode_brg'";
    $q1         = mysqli_query($koneksi, $sql1);
    $r1         = mysqli_fetch_array($q1);
    $nama_brg   = $r1['nama_brg'];
    $merk       = $r1['merk'];
    $harga      = $r1['harga'];
    $jumlah     = $r1['jumlah'];

    if ($kode_brg == '') {
        $error = "Data tidak ditemukan";
    }
}
if (isset($_POST['simpan'])) { //Create Data Barang
    $kode_brg   = $_POST['kode_brg'];
    $nama_brg   = $_POST['nama_brg'];
    $merk       = $_POST['merk'];
    $harga      = $_POST['harga'];
    $jumlah     = $_POST['jumlah'];

    if ($nama_brg && $merk && $harga && $jumlah) {
        if ($op == 'edit') { // update Data Barang
            $sql1 = "UPDATE barang SET nama_brg = '$nama_brg', merk = '$merk', harga = '$harga', jumlah = '$jumlah' WHERE kode_brg = '$kode_brg'";
            $q1 = mysqli_query($koneksi, $sql1);
            if ($q1) {
                $sukses = "Data berhasil diupdate";
            } else {
                $error = "Data gagal diupdate";
            }
        } else { 
            // insert Data Barang
            // mengambil data barang dengan kode paling besar
            $query = mysqli_query($koneksi, "SELECT max(kode_brg) as kodeTerbesar FROM barang");
            $data = mysqli_fetch_array($query);
            $kode_brg = $data['kodeTerbesar'];
 
            // mengambil angka dari kode barang terbesar, menggunakan fungsi substr
            // dan diubah ke integer dengan (int)
            $urutan = (int) substr($kode_brg, 3, 3);
            $urutan++;

            // membentuk kode barang baru
            $huruf = "BRG";
            $kode_brg = $huruf . sprintf("%03s", $urutan);

            $sql1 = "INSERT INTO barang (kode_brg, nama_brg, merk, harga, jumlah) VALUES ('$kode_brg', '$nama_brg', '$merk', '$harga', '$jumlah')";
            $q1 = mysqli_query($koneksi, $sql1);
            if ($q1) {
                $sukses = "Berhasil memasukkan data baru";
            } else {
                $error = "Gagal memasukkan data";
            }
        }
    } else {
        $error = "Silakan masukkan semua data";
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
        <!-- form data -->
        <div class="card">
            <div class="card-header">
                Daftar Barang
            </div>
                    <!-- notif -->
            <div class="card-body">
                <?php
                if ($error) {
                ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error ?>
                    </div>
                <?php
                    header("refresh:1;url=index.php");//1 : detik
                }
                ?>
                <?php
                if ($error1) {
                ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error1 ?>
                    </div>
                <?php
                    header("refresh:4;url=index.php");//1 : detik
                }
                ?>
                <?php
                if ($sukses) {
                ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $sukses ?>
                    </div>
                <?php
                    header("refresh:2;url=index.php");
                }
                ?>
                <form action="" method="POST">
                    <div class="mb-3 row">
                        <label for="kode_brg" class="col-sm-2 col-form-label">Kode Barang</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" required="required" id="kode_brg" name="kode_brg" value="<?php echo $kode_brg ?>" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="nama_brg" class="col-sm-2 col-form-label">Nama Barang</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nama_brg" name="nama_brg" value="<?php echo $nama_brg ?>">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="merk" class="col-sm-2 col-form-label">Merk</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control"  id="merk" name="merk" value="<?php echo $merk ?>">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="harga" class="col-sm-2 col-form-label">harga</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="harga" name="harga" value="<?php echo $harga ?>">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="jumlah" class="col-sm-2 col-form-label">Jumlah</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?php echo $jumlah ?>">
                        </div>
                    </div>
                    <div class="col-12">
                    <td scope="row">
                        <input type="submit" name="simpan" value="Simpan Data" class="btn btn-primary" />
                        <a href="transaksi.php"><button type="button" class="btn btn-success">Daftar Transaksi</button></a>                      
                            <a href="index.php?op=hapus_semua" onclick="return confirm('Yakin ingin menghapus semua data barang?')">
                                <button type="button" class="btn btn-danger">Hapus Semua Data Barang</button>
                            </a>                       
                        </td>
                    </div>
                </form>
            </div>
        </div>

        <!-- output data Barang-->
        <div class="card">
            <div class="card-header text-white bg-secondary">
                Data Barang
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            
                            <th scope="col">Kode Barang</th>
                            <th scope="col">Barang</th>
                            <th scope="col">Merk</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql2   = "select * from barang order by kode_brg desc";
                        $q2     = mysqli_query($koneksi, $sql2);
                        $urut   = 1;
                        while ($r2 = mysqli_fetch_array($q2)) {
                            $kode_brg   = $r2['kode_brg'];
                            $nama_brg   = $r2['nama_brg'];
                            $merk       = $r2['merk'];
                            $harga      = $r2['harga'];
                            $jumlah     = $r2['jumlah'];

                        ?>
                            <tr>
                                <th scope="row"><?php echo $kode_brg ?></th>
                                <td scope="row"><?php echo $nama_brg ?></td>
                                <td scope="row"><?php echo $merk ?></td>
                                <td scope="row"><?php echo "Rp. ".number_format($harga)." ,-"; ?></td>
                                <td scope="row"><?php echo $jumlah ?></td>
                                <td scope="row">
                                    <a href="index.php?op=edit&kode_brg=<?php echo $kode_brg ?>"><button type="button" class="btn btn-warning">Edit</button></a>
                                    <a href="index.php?op=delete&kode_brg=<?php echo $kode_brg?>" onclick="return confirm('Yakin mau delete data?')"><button type="button" class="btn btn-danger">Delete</button></a>            
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

</body>

</html>
