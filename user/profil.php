<?php
session_start();
error_reporting(E_ALL); // Aktifkan semua pelaporan error
ini_set('display_errors', 1); // Tampilkan error di layar

require "../db.php"; // Pastikan path ke file db.php benar (dari user/profil.php ke db.php)
$page = "profil"; // Set variabel $page untuk menandai halaman ini sebagai 'profil'

if (!isset($_SESSION["user"])) { // Cek sesi untuk user
    header("Location: ../login.php");
    exit;
}

// Gunakan nama user dari session untuk mengambil data user yang login
// Asumsi $_SESSION["user"] menyimpan NAMA user. Sesuaikan jika Anda menyimpan ID.
$user_nama = $_SESSION["user"];
$query = $kon->prepare("SELECT * FROM user WHERE nama = ?");
$query->bind_param("s", $user_nama);
$query->execute();
$result = $query->get_result();

if (!$result || $result->num_rows == 0) {
    $_SESSION['error'] = "Data profil pengguna tidak ditemukan.";
    header("Location: index.php"); // Alihkan ke halaman dashboard user jika tidak ditemukan
    exit;
}

$user_data = $result->fetch_assoc(); // Menggunakan $user_data untuk konsistensi

// --- Handle form submission untuk update profil (termasuk foto) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $id_user = $user_data['id_user'];
    $nama = mysqli_real_escape_string($kon, $_POST['nama']);
    
    // Path foto profil saat ini dari database
    $current_foto_db_path = $user_data['foto']; 
    $new_foto_db_path = $current_foto_db_path; // Default: tidak ada perubahan foto

    // Cek apakah ada file foto baru yang diupload
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto_profil']['tmp_name'];
        $uploaded_file_name = basename($_FILES['foto_profil']['name']);
        
        // --- Mendapatkan path absolut yang aman untuk direktori uploads ---
        // Asumsi struktur:  project-root/uploads/
        //                   project-root/user/profil.php
        $upload_dir_absolute = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'uploads');

        if ($upload_dir_absolute === false) {
             $_SESSION['error'] = "Error: Direktori unggahan tidak ditemukan atau tidak dapat diakses. Pastikan folder 'uploads' ada dan dapat ditulis.";
             header("Location: profil.php");
             exit;
        }

        $target_file_absolute = $upload_dir_absolute . DIRECTORY_SEPARATOR . $uploaded_file_name;

        // Validasi dasar file
        $imageFileType = strtolower(pathinfo($target_file_absolute, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION['error'] = "Gagal unggah: Format file tidak diizinkan. Hanya JPG, JPEG, PNG, GIF.";
            header("Location: profil.php");
            exit;
        }
        
        // Cek ukuran file (contoh: maks 5MB)
        if ($_FILES['foto_profil']['size'] > 5000000) { 
            $_SESSION['error'] = "Gagal unggah: Ukuran file terlalu besar (maks 5MB).";
            header("Location: profil.php");
            exit;
        }

        // Pindahkan file yang diupload
        if (move_uploaded_file($file_tmp, $target_file_absolute)) {
            // Jika upload berhasil, perbarui path yang akan disimpan di database
            $new_foto_db_path = '../uploads/' . $uploaded_file_name;
        } else {
            $_SESSION['error'] = "Gagal mengunggah gambar profil. Pastikan folder 'uploads' memiliki izin tulis atau cek konfigurasi PHP.";
            header("Location: profil.php");
            exit;
        }
    }

    // --- Query update database ---
    $stmt_update = $kon->prepare("UPDATE user SET nama = ?, foto = ? WHERE id_user = ?");
    $stmt_update->bind_param("ssi", $nama, $new_foto_db_path, $id_user);

    if ($stmt_update->execute()) {
        $_SESSION['success'] = "Profil berhasil diperbarui!";
        // Perbarui $_SESSION["user"] jika nama berubah
        if ($_SESSION["user"] !== $nama) {
            $_SESSION["user"] = $nama;
        }
        // Perbarui $user_data untuk tampilan di halaman saat ini
        $user_data['nama'] = $nama;
        $user_data['foto'] = $new_foto_db_path; // Update foto juga
    } else {
        $_SESSION['error'] = "Gagal memperbarui profil: " . $stmt_update->error;
    }
    header("Location: profil.php");
    exit;
}

// --- Handle DELETE foto (menggunakan form terpisah) ---
if (isset($_POST['delete_profile_photo'])) {
    $id_user = $user_data['id_user'];
    $current_foto_path = $user_data['foto']; // Path dari database

    // Pastikan path tidak kosong dan bukan default placeholder
    if (!empty($current_foto_path) && strpos($current_foto_path, 'uploads/') !== false) {
        // Path absolut ke file foto yang akan dihapus
        $absolute_delete_path = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $current_foto_path);

        if ($absolute_delete_path && file_exists($absolute_delete_path) && is_writable($absolute_delete_path)) {
            if (unlink($absolute_delete_path)) {
                // Hapus path foto dari database (set ke string kosong)
                $stmt_delete_photo = $kon->prepare("UPDATE user SET foto = '' WHERE id_user = ?");
                $stmt_delete_photo->bind_param("i", $id_user);
                if ($stmt_delete_photo->execute()) {
                    $_SESSION['success'] = "Gambar profil berhasil dihapus!";
                    $user_data['foto'] = ''; // Update data di halaman
                } else {
                    $_SESSION['error'] = "Gagal menghapus path foto dari database: " . $stmt_delete_photo->error;
                }
            } else {
                $_SESSION['error'] = "Gagal menghapus file gambar dari server. Cek izin file.";
            }
        } else {
            $_SESSION['error'] = "File gambar tidak ditemukan atau tidak dapat dihapus.";
        }
    } else {
        $_SESSION['error'] = "Tidak ada gambar profil yang dapat dihapus.";
    }
    header("Location: profil.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Profil Saya</title>
    <meta name="robots" content="noindex, nofollow" />
    <meta content="" name="description" />
    <meta content="" name="keywords" />

    <!-- Google Fonts - Menggunakan Inter sebagai font utama -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <!-- Template Main CSS File -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <!-- Font Awesome untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
            color: #2D3A3A !important;
        }
        /* Style untuk pagetitle yang dikomentari di kode Anda, tapi penting untuk konsistensi */
        .pagetitle {
            margin-bottom: 25px;
            padding-left: 15px;
            /* Flexbox untuk mensejajarkan H1 dan Nav/Breadcrumb secara horizontal */
            display: flex;
            justify-content: space-between; /* Untuk meletakkan elemen di ujung-ujung */
            align-items: center; /* Untuk mensejajarkan vertikal */
        }
        .pagetitle h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #343a40;
            display: flex;
            align-items: center;
            margin-bottom: 0; /* Hapus margin-bottom default agar tidak ada spasi di bawah H1 */
        }
        .pagetitle h1 .bi {
            font-size: 1.5em;
            margin-right: 10px;
            color: #0d6efd;
        }
        .pagetitle nav {
            /* Pastikan nav tidak memiliki lebar 100% agar bisa sejajar */
            flex-shrink: 0; /* Agar nav tidak menyusut terlalu banyak */
        }
        .pagetitle .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin: 0; /* Hapus margin default breadcrumb */
        }
        .pagetitle .breadcrumb-item {
            font-size: 0.9em;
        }
        .pagetitle .breadcrumb-item a {
            color: #6c757d;
            text-decoration: none;
        }
        .pagetitle .breadcrumb-item.active {
            color: #495057;
            font-weight: 600;
        }
        .btn-secondary {
            color: #763D2D !important;
            background-color: transparent !important;
            border: 1px solid #763D2D !important;
            border-radius: 0.375rem; /* Bootstrap default for btn-sm */
            padding: 10px 15px; /* Bootstrap default for btn-sm */
            font-size: 0.875rem; /* Bootstrap default for btn-sm */
            transition: all 0.2s ease-in-out;
        }

        .btn-secondary:hover {
            background-color: #763D2D !important;
            color: #fff !important;
            border: 1px solid transparent !important;
        }
        /* Container utama di tengah */
        .profile-container {
            max-width: 900px; /* Menyesuaikan lebar card utama di gambar */
            margin: 10px auto;
            padding: 0 15px;
        }

        /* Card utama profil */
        .profile-card {
            border-radius: 15px; /* Sudut membulat */
            box-shadow: 0 5px 15px rgba(0,0,0,0.08); /* Bayangan lembut */
            background-color: #fff; /* Latar belakang putih */
            padding: 30px;
        }

        /* Judul "PROFIL SAYA" */
        .profile-title {
            font-size: 24px;
            font-weight: 600;
            color: #2D3A3A;
            margin-bottom: 10px;
        }
        .profile-subtitle {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 30px;
        }

        /* Bagian kiri (informasi teks) */
        .profile-info-section label {
            font-weight: 500;
            color: #2D3A3A;
            margin-bottom: 5px;
            display: block; /* Pastikan label di baris baru */
        }
        .profile-info-section .info-value {
            font-size: 16px;
            color: #495057;
            background-color: #f8f9fa; /* Latar belakang input disabled/read-only */
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 15px; /* Jarak antar info field */
            width: 100%;
            box-sizing: border-box; /* Pastikan padding tidak menambah lebar */
        }
        .profile-info-section .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .profile-info-section .info-row .info-value-inline {
            flex-grow: 1; /* Ambil sisa ruang */
            font-size: 16px;
            color: #495057;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 15px;
        }
        .profile-info-section .btn-change {
            background-color: transparent;
            border: 1px solid #6c757d;
            color: #6c757d;
            border-radius: 8px;
            padding: 5px 12px;
            font-size: 14px;
            margin-left: 10px;
            white-space: nowrap; /* Jangan biarkan teks pecah */
            transition: all 0.2s ease-in-out;
        }
        .profile-info-section .btn-change:hover {
            background-color: #6c757d;
            color: #fff;
        }
        .profile-info-section textarea.info-value {
            resize: vertical; /* Memungkinkan textarea diubah ukurannya secara vertikal */
            min-height: 80px; /* Tinggi minimum */
        }

        /* Bagian kanan (gambar profil dan tombolnya) */
        .profile-image-section {
            text-align: center;
        }
        .profile-image-section .profile-img {
            width: 150px; /* Ukuran gambar profil */
            height: 150px;
            border-radius: 10%; /* Membulat seperti di gambar */
            object-fit: cover; /* Pastikan gambar mengisi area tanpa distorsi */
            border: 3px solid #EFAA31; /* Border warna orange/emas */
            padding: 3px; /* Sedikit padding sebelum border */
            margin-bottom: 20px;
        }
        .profile-image-section .btn-image-action {
            width: 100%;
            max-width: 180px; /* Batasi lebar tombol gambar */
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 10px; /* Jarak antar tombol */
            transition: all 0.2s ease-in-out;
        }
        .profile-image-section .btn-ganti-gambar {
            background-color: #EFAA31; /* Warna tombol Ganti Gambar */
            border-color: #EFAA31;
            color: #fff;
        }
        .profile-image-section .btn-ganti-gambar:hover {
            background-color: #e09f2b;
            border-color: #e09f2b;
        }
        .profile-image-section .btn-hapus-gambar {
            background-color: #763D2D; /* Warna tombol Hapus */
            border-color: #763D2D;
            color: #fff;
        }
        .profile-image-section .btn-hapus-gambar:hover {
            background-color: #653625;
            border-color: #653625;
        }

        /* Tombol Simpan di bagian bawah */
        .btn-simpan-profile {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            background-color: #1A877E; /* Warna tombol simpan dari gambar (hijau tua) */
            border-color: #1A877E;
            color: #fff;
            margin-top: 20px; /* Jarak atas dari bagian sebelumnya */
        }
        .btn-simpan-profile:hover {
            background-color: #167a70; /* Sedikit lebih gelap saat hover */
            border-color: #167a70;
        }
        .btn-primary { /* Style untuk tombol "Simpan" di modal */
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: bold;
            background-color: #1A877E; /* Warna biru Bootstrap */
            border-color: #1A877E;
            color: #fff;
            margin-top: 0; 
        }
        .btn-primary:hover {
            background-color: #1A877E; 
            border-color: #1A877E;
        }
        /* Responsifitas dasar */
        @media (max-width: 768px) {
            .profile-image-section {
                order: -1; /* Pindahkan gambar ke atas di layar kecil */
                margin-bottom: 30px;
            }
            .profile-info-section {
                text-align: center; /* Tengahkan teks di layar kecil */
            }
            .profile-info-section .info-row {
                flex-direction: column; /* Ubah ke kolom untuk baris info */
                align-items: flex-start; /* Sejajarkan ke kiri */
                margin-bottom: 10px;
            }
            .profile-info-section .info-row .info-value-inline {
                width: 100%;
                margin-bottom: 10px;
            }
            .profile-info-section .btn-change {
                margin-left: 0;
                width: 100%;
                margin-top: 5px;
            }
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <?php require "atas.php"; // Asumsi atas.php berada di direktori yang sama atau path relatif ini ?>

    <!-- SIDEBAR -->
    <?php require "profil_menu.php"; // Asumsi profil_menu.php berada di direktori yang sama atau path relatif ini ?>
    
    <main id="main" class="main">
        <div class="profile-container">
            
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php elseif (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="profile-card">
                <h2 class="profile-title">PROFIL SAYA</h2>
                <p class="profile-subtitle">Silakan masukkan informasi Anda</p>

                <form method="POST" enctype="multipart/form-data" action="profil.php">
                    <div class="row">
                        <!-- Bagian Kiri (Informasi Teks) -->
                        <div class="col-md-7 profile-info-section">
                            <div class="mb-3">
                                <label for="nama_lengkap">Nama Lengkap</label>
                                <input type="text" class="form-control info-value" id="nama_lengkap" name="nama" value="<?= htmlspecialchars($user_data['nama']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email_display">Email</label>
                                <div class="info-row">
                                    <!-- Display masked email -->
                                    <input type="text" class="form-control info-value-inline" id="email_display" value="<?= substr(htmlspecialchars($user_data['email']), 0, 1) . '*********' . substr(htmlspecialchars($user_data['email']), -8) ?>" disabled>
                                    <button type="button" class="btn-change" data-bs-toggle="modal" data-bs-target="#editEmailModal">ganti</button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="no_telepon_display">No. Telepon</label>
                                <div class="info-row">
                                    <!-- Display masked phone number -->
                                    <input type="text" class="form-control info-value-inline" id="no_telepon_display" value="<?= substr(htmlspecialchars($user_data['no_tlp']), 0, 2) . '*********' . substr(htmlspecialchars($user_data['no_tlp']), -2) ?>" disabled>
                                    <button type="button" class="btn-change" data-bs-toggle="modal" data-bs-target="#editPhoneModal">ganti</button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="alamat_display">Alamat</label>
                                <div class="info-row">
                                    <textarea class="form-control info-value-inline" id="alamat_display" rows="3" disabled><?= htmlspecialchars($user_data['alamat']) ?></textarea>
                                    <button type="button" class="btn-change" data-bs-toggle="modal" data-bs-target="#editAddressModal">ganti</button>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian Kanan (Gambar Profil dan Aksi) -->
                        <div class="col-md-5 profile-image-section">
                            <img src="<?= !empty($user_data['foto']) ? htmlspecialchars($user_data['foto']) : 'https://placehold.co/150x150/cccccc/333333?text=No+Photo'; ?>" alt="Profile Image" class="profile-img">
                            <input type="file" id="foto_profil_input" name="foto_profil" style="display: none;" accept="image/*">
                            <button type="button" class="btn btn-image-action btn-ganti-gambar" onclick="document.getElementById('foto_profil_input').click();">GANTI GAMBAR</button>
                            <button type="submit" name="delete_profile_photo" class="btn btn-image-action btn-hapus-gambar" onclick="return confirm('Apakah Anda yakin ingin menghapus gambar profil?');">HAPUS</button>
                        </div>
                    </div>

                    <button type="submit" name="update_profile" class="btn btn-simpan-profile mt-4">SIMPAN</button>
                </form>
            </div>
        </div>
    </main>

    <!-- Modals for "Ganti" actions - These will submit to update_profile.php -->

    <!-- Modal Edit Email -->
    <div class="modal fade" id="editEmailModal" tabindex="-1" aria-labelledby="editEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="update_profile.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEmailModalLabel">Ganti Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="field" value="email">
                        <input type="hidden" name="id_user" value="<?= $user_data['id_user'] ?>">
                        <div class="mb-3">
                            <label for="new_email" class="form-label">Email Baru</label>
                            <input type="email" class="form-control" id="new_email" name="value" value="<?= htmlspecialchars($user_data['email']) ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit No. Telepon -->
    <div class="modal fade" id="editPhoneModal" tabindex="-1" aria-labelledby="editPhoneModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="update_profile.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPhoneModalLabel">Ganti No. Telepon</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="field" value="no_tlp">
                        <input type="hidden" name="id_user" value="<?= $user_data['id_user'] ?>">
                        <div class="mb-3">
                            <label for="new_phone" class="form-label">No. Telepon Baru</label>
                            <input type="text" class="form-control" id="new_phone" name="value" value="<?= htmlspecialchars($user_data['no_tlp']) ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Alamat -->
    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="update_profile.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAddressModalLabel">Ganti Alamat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="field" value="alamat">
                        <input type="hidden" name="id_user" value="<?= $user_data['id_user'] ?>">
                        <div class="mb-3">
                            <label for="new_address" class="form-label">Alamat Baru</label>
                            <textarea class="form-control" id="new_address" name="value" rows="3" required><?= htmlspecialchars($user_data['alamat']) ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script untuk pratinjau gambar upload -->
    <script>
        document.getElementById('foto_profil_input').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-img').src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>


    <!-- Vendor JS Files -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="../assets/vendor/chart.js/chart.umd.js"></script>
    <script src="../assets/vendor/echarts/echarts.min.js"></script>
    <script src="../assets/vendor/quill/quill.min.js"></script>
    <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>
    <!-- Template Main JS File (jika ada) -->
    <script src="../assets/js/main.js"></script>

</body>
</html>

<?php
// Tutup koneksi database
if ($query) $query->close();
if ($kon) $kon->close();
?>
