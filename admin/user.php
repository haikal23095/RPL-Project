<?php
session_start();
require "../db.php";
$page = "user";

if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
}

$total_user_query = mysqli_query($kon, "SELECT COUNT(id_user) AS total FROM user");
$total_user_result = mysqli_fetch_assoc($total_user_query);
$total_user = $total_user_result['total'];


$target_dir = "../uploads/";

// Prosedur Hapus Data
if (isset($_POST["delete"])) {
    $id_user = mysqli_real_escape_string($kon, $_POST["id_user"]);
    mysqli_query($kon, "DELETE FROM user WHERE id_user = '$id_user'");
    echo '<script>alert("DATA BERHASIL DIHAPUS !"); window.location = "";</script>';
}

if (isset($_POST["submit"])) {
    $id_user = mysqli_real_escape_string($kon, $_POST["id_user"]);
    $email = mysqli_real_escape_string($kon, $_POST["email"]);
    $pwd = mysqli_real_escape_string($kon, $_POST["pwd"]);
    $nama = mysqli_real_escape_string($kon, $_POST["nama"]);
    $alamat = mysqli_real_escape_string($kon, $_POST["alamat"]);
    $no_tlp = mysqli_real_escape_string($kon, $_POST["no_tlp"]);

    // Handling file upload
    $foto = "";
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $foto = basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $foto;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
        $foto = "../uploads/" . $foto; // Simpan path relatif ke foto
    }

    if (empty($email) or empty($pwd) or empty($nama) or empty($alamat) or empty($no_tlp)) {
        echo '<script>alert("MAAF, DATA TERSEBUT MASIH KOSONG. SILAHKAN DI-ISI TERLEBIH DAHULU !"); window.location = "";</script>';
    } else {
        $kue = mysqli_query($kon, "SELECT * FROM user WHERE email = '$email' AND password = '$pwd'");
        $cek = mysqli_fetch_array($kue);

        if ($cek > 0) {
            echo '<script>alert("MAAF, DATA TERSEBUT SUDAH ADA. SILAHKAN ISI YANG LAIN !"); window.location = "";</script>';
        } else {
            mysqli_query($kon, "INSERT INTO user (email, password, nama, alamat, no_tlp, foto) VALUES ('$email', '$pwd', '$nama', '$alamat', '$no_tlp', '$foto')");
            echo '<script>alert("DATA BERHASIL DISIMPAN !"); window.location = "";</script>';
        }
    }
}

// Prosedur Update Data
if (isset($_POST["update"])) {
    $id_user = mysqli_real_escape_string($kon, $_POST["id_user"]);
    $email = mysqli_real_escape_string($kon, $_POST["email"]);
    $pwd = mysqli_real_escape_string($kon, $_POST["pwd"]);
    $nama = mysqli_real_escape_string($kon, $_POST["nama"]);
    $alamat = mysqli_real_escape_string($kon, $_POST["alamat"]);
    $no_tlp = mysqli_real_escape_string($kon, $_POST["no_tlp"]);
    
    // Handling file upload
    $foto = "";
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $foto = basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $foto;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
        $foto = "../uploads/" . $foto; // Simpan path relatif ke foto
    } else {
        $foto = mysqli_real_escape_string($kon, $_POST["foto_lama"]);
    }

    if (empty($email) or empty($pwd) or empty($nama) or empty($alamat) or empty($no_tlp) or empty($foto)) {
        echo '<script>alert("MAAF, DATA TERSEBUT MASIH KOSONG. SILAHKAN DI-ISI TERLEBIH DAHULU !"); window.location = "";</script>';
    } else {
        $update_query = "UPDATE user SET email = '$email', password = '$pwd', nama = '$nama', alamat = '$alamat', no_tlp = '$no_tlp', foto = '$foto' WHERE id_user = '$id_user'";
        
        mysqli_query($kon, $update_query);
        echo '<script>alert("DATA BERHASIL DI-UPDATE !"); window.location = "";</script>';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>DATA USER</title>
    <meta name="robots" content="noindex, nofollow" />
    <meta content="" name="description" />
    <meta content="" name="keywords" />
    <?php include 'aset.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
</head>
<body>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1;
        }
        .stat-item .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2D3A3A;
        }
        .stat-item .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        h5{
            font-size: 20px !important;
            color: #2D3A3A !important;
            font-weight: bold !important;
        }
    </style>
    
    <?php require "atas.php"; ?>

    <?php require "menu.php"; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-person"></i>&nbsp; DATA USER</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                    <li class="breadcrumb-item active"> DATA USER</li>
                </ol>
            </nav>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manajemen User</h5>
                            <div class="d-flex align-items-center">
                                <div class="stat-item me-5">
                                    <div class="text-muted">Semua User</div>
                                    <div>
                                        <span class="stat-number"><?= $total_user ?></span>
                                        <span class="stat-label">User</span>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="text-muted">Online User</div>
                                    <div>
                                        <span class="stat-number">2</span>
                                        <span class="stat-label">User</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <br>
                            <button type="button" class="btn-tambah" data-bs-toggle="modal" data-bs-target="#tambahData">
                                <i class="bi bi-plus"></i>&nbsp; TAMBAH USER
                            </button>
                            <br><br>
                            <table class="table datatable">
                                <thead> <tr>
                                        <th><center>NO</center></th>
                                        <th><center>EMAIL</center></th>
                                        <th><center>NAMA LENGKAP</center></th>
                                        <th><center>LEVEL</center></th>
                                        <th><center>ALAMAT</center></th>
                                        <th><center>NO. TELP</center></th>
                                        <th><center>LAST ACTIVE</center></th>
                                        <th><center>AKSI</center></th>
                                    </tr>
                                </thead>
                                <tbody> <?php
                                $sql = mysqli_query($kon, "SELECT * FROM user ORDER BY id_user DESC");
                                $no = 1;

                                while ($gb = mysqli_fetch_array($sql)) {
                                ?>
                                <tr>
                                    <td><center><?= $no++ ?></center></td> 
                                    <td><center><?= $gb["email"] ?></center></td>
                                    <td><center><?= $gb["nama"] ?></center></td>
                                    <td><center><?= $gb["level"] ?></center></td>
                                    <td><center><?= $gb["alamat"] ?></center></td>
                                    <td><center><?= $gb["no_tlp"] ?></center></td>
                                    <td><center><?= $gb["active"] ?></center></td>
                                    <td><center> <button type="button" data-bs-toggle="modal" data-bs-target="#editData<?= $gb["id_user"] ?>" class="btn btn-warning"><i class="bi bi-pencil-square"></i></button>
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#hapusData<?= $gb["id_user"] ?>" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                                    </center></td>
                                </tr>

                                <div class="modal fade" id="editData<?= $gb["id_user"] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><i class="bi bi-pencil-square"></i>&nbsp; EDIT USER</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" enctype="multipart/form-data">
                                                    <div class="form-group">
                                                        <label>EMAIL</label>
                                                        <input name="email" class="form-control" type="text" placeholder="Masukkan Email" value="<?= $gb["email"] ?>" required>
                                                    </div>
                                                    <br>
                                                    <div class="form-group">
                                                        <label>PASSWORD</label>
                                                        <input name="pwd" class="form-control" type="password" placeholder="Masukkan password" value="<?= $gb["password"] ?>" required>
                                                    </div>
                                                    <br>
                                                    <div class="form-group">
                                                        <label>NAMA</label>
                                                        <input name="nama" class="form-control" type="text" placeholder="Masukkan Nama" value="<?= $gb["nama"] ?>" required>
                                                    </div>
                                                    <br>
                                                    <div class="form-group">
                                                        <label>ALAMAT</label>
                                                        <textarea name="alamat" class="form-control" rows="7" placeholder="Masukkan alamat siswa" required><?= $gb["alamat"] ?></textarea>
                                                    </div>
                                                    <br>
                                                    <div class="form-group">
                                                        <label>NO. TELP</label>
                                                        <input name="no_tlp" class="form-control" type="number" placeholder="Masukkan nomor telepon siswa" value="<?= $gb["no_tlp"] ?>" required>
                                                    </div>
                                                    <br>
                                                    <div class="form-group">
                                                        <label>FOTO</label>
                                                        <input name="foto" class="form-control" type="file">
                                                        <input type="hidden" name="foto_lama" value="<?= $gb["foto"] ?>">
                                                    </div>
                                                    <input type="hidden" name="id_user" value="<?= $gb["id_user"] ?>">
                                            </div>
                                            <div class="modal-footer">
                                                <button name="update" type="submit" class="btn btn-success"><i class="bi bi-check-circle-fill"></i>&nbsp; SAVE</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="hapusData<?= $gb["id_user"] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><i class="bi bi-trash"></i>&nbsp; HAPUS USER</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h2 class="text-center">
                                                    Apakah Anda yakin ingin menghapus data ini ?
                                                </h2>
                                                <form method="post">
                                                    <input type="hidden" name="id_user" value="<?= $gb["id_user"] ?>">
                                            </div>
                                            <div class="modal-footer">
                                                <button name="delete" type="submit" class="btn btn-danger"><i class="bi bi-check-circle-fill"></i>&nbsp; HAPUS</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                </tbody>
                            </table>

                            <div class="modal fade" id="tambahData" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><i class="bi bi-plus"></i>&nbsp; TAMBAH USER</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label>EMAIL</label>
                                                    <input name="email" class="form-control" type="text" placeholder="Masukkan Email" required>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <label>PASSWORD</label>
                                                    <input name="pwd" class="form-control" type="password" placeholder="Masukkan password" required>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <label>NAMA LENGKAP</label>
                                                    <input name="nama" class="form-control" type="text" placeholder="Masukkan nama lengkap siswa" required>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <label>ALAMAT</label>
                                                    <textarea name="alamat" class="form-control" rows="7" placeholder="Masukkan alamat siswa" required></textarea>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <label>NO. TELP</label>
                                                    <input name="no_tlp" class="form-control" type="number" placeholder="Masukkan nomor telepon siswa" required>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <label>FOTO</label>
                                                    <input name="foto" class="form-control" type="file" required>
                                                </div>
                                                <br>
                                        </div>
                                        <div class="modal-footer">
                                            <button name="submit" type="submit" class="btn btn-success"><i class="bi bi-check-circle-fill"></i>&nbsp; SAVE</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>