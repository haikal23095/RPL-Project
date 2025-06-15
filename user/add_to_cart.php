<?php
session_start();
include('../db.php');
$page = "keranjang";
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$userName = $_SESSION['user'];

$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$userName'");
$row_user = mysqli_fetch_array($kue_user);
$user_id = $row_user['id_user'];

// Backend Logic (Tidak ada perubahan)

// Add product to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $jumlah = 1;

    $query_check = "SELECT * FROM keranjang WHERE user_id = '$user_id' AND id_produk = '$product_id'";
    $result_check = mysqli_query($kon, $query_check);

    if (mysqli_num_rows($result_check) > 0) {
        $query_update = "UPDATE keranjang SET jumlah = jumlah + $jumlah WHERE user_id = '$user_id' AND id_produk = '$product_id'";
        mysqli_query($kon, $query_update);
    } else {
        $query_insert = "INSERT INTO keranjang (user_id, id_produk, jumlah) VALUES ('$user_id', '$product_id', '$jumlah')";
        mysqli_query($kon, $query_insert);
    }

    header("Location: produk.php?cart_success=1");
    exit();
}

// Update cart quantity via AJAX
if (isset($_POST['update_quantity'])) {
    $cart_id = $_POST['cart_id'];
    $new_quantity = $_POST['quantity'];

    $response = [];

    $stock_query = mysqli_query($kon, "SELECT p.stok FROM produk p JOIN keranjang k ON p.id_produk = k.id_produk WHERE k.id_keranjang = '$cart_id'");
    $product_data = mysqli_fetch_assoc($stock_query);
    $stok_produk = $product_data['stok'];

    if ($new_quantity > $stok_produk) {
        $response = ['error' => 'Stok tidak mencukupi. Sisa stok: ' . $stok_produk, 'current_quantity' => $stok_produk];
        $sql = "UPDATE keranjang SET jumlah = '$stok_produk' WHERE id_keranjang = '$cart_id'";
        mysqli_query($kon, $sql);
    } elseif ($new_quantity <= 0) {
        $sql = "DELETE FROM keranjang WHERE id_keranjang = '$cart_id'";
        mysqli_query($kon, $sql);
        $response = ['action' => 'removed'];
    } else {
        $sql = "UPDATE keranjang SET jumlah = '$new_quantity' WHERE id_keranjang = '$cart_id'";
        mysqli_query($kon, $sql);
        
        $query_total = "SELECT k.jumlah, p.harga FROM keranjang k JOIN produk p ON k.id_produk = p.id_produk WHERE k.id_keranjang = '$cart_id'";
        $result_total = mysqli_query($kon, $query_total);
        $item = mysqli_fetch_assoc($result_total);
        $updated_total = $item ? $item['jumlah'] * $item['harga'] : 0;
        $response = ['item_total' => $updated_total];
    }
    
    echo json_encode($response);
    exit();
}


// Remove item from cart
if (isset($_POST['remove_item'])) {
    $cart_id = $_POST['cart_id'];
    $sql = "DELETE FROM keranjang WHERE id_keranjang = '$cart_id'";
    mysqli_query($kon, $sql);
    echo json_encode(['action' => 'removed']);
    exit();
}

// Get the products in the cart
$cartItems = mysqli_query($kon, "SELECT k.*, p.nama_produk, p.harga, p.gambar, p.stok FROM keranjang k JOIN produk p ON k.id_produk = p.id_produk WHERE k.user_id = $user_id");

// Check if cart has items
$has_items = ($cartItems && mysqli_num_rows($cartItems) > 0);

if (isset($_POST['checkout']) && isset($_POST['selected_items'])) {
    $_SESSION['checkout_cart_ids'] = $_POST['selected_items'];
    header("Location: checkout.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Keranjang Belanja</title>
    
    <?php include "aset.php"; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
        }
        .main {
            padding-bottom: 2rem;
        }
        .sidebar {
            width: auto !important; /* Equivalent to w-64 in Tailwind */
            background-color: #F8F7F1 !important;
            padding: 1rem !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            height: 100vh !important;
        }
        .cart-item-card {
            display: flex;
            align-items: center;
            padding: 1rem;
            background-color: #fff;
        }
        .cart-item-card .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin: 0 1rem;
        }
        .cart-item-card .product-details {
            flex-grow: 1;
        }
        .quantity-control {
            display: flex;
            align-items: center;
        }
        .quantity-control .btn {
            width: 30px;
            height: 30px;
            padding: 0;
        }
        .quantity-control .quantity-input {
            width: 40px;
            text-align: center;
            border: none;
            background: transparent;
            font-weight: bold;
        }
        #summary-card {
            position: sticky;
            top: 80px; /* Adjust this value based on your header's height */
        }
        .subtotal-line {
            font-size: 1.1rem;
        }
        .subtotal-price {
            font-size: 1.2rem;
            color: #dc3545;
            font-weight: bold;
        }
        
        .btn-primary{
            color: #1A877E !important;
            background-color: transparent !important;
            border: 1px solid #1A877E !important;
            border-radius: 0.375rem; /* Bootstrap default for btn-sm */
            padding: 10px 15px; /* Bootstrap default for btn-sm */
            font-size: 0.875rem; /* Bootstrap default for btn-sm */
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #1A877E !important;
            color: #fff !important;
            border: 1px solid transparent !important;
        }
    </style>
</head>

<body>
    <?php require "atas.php"; ?>
    <?php require "menu.php"; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-cart"></i>&nbsp;KERANJANG</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                    <li class="breadcrumb-item active">KERANJANG</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <?php if ($has_items): ?>
                <form method="POST" action="checkout.php" id="cart-form">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body d-flex align-items-center p-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                        <label class="form-check-label" for="select-all">
                                            Pilih Semua
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <?php while ($item = mysqli_fetch_assoc($cartItems)): ?>
                                <div class="card cart-item mb-3"
                                     data-price="<?= $item['harga']; ?>"
                                     data-stock="<?= $item['stok']; ?>"
                                     data-cart-id="<?= $item['id_keranjang']; ?>">
                                    <div class="card-body cart-item-card">
                                        <input class="form-check-input item-checkbox" type="checkbox" name="selected_items[]" value="<?= $item['id_keranjang']; ?>">
                                        <img src="../uploads/<?= htmlspecialchars($item['gambar']); ?>" alt="<?= htmlspecialchars($item['nama_produk']); ?>" class="product-img">
                                        
                                        <div class="product-details">
                                            <p class="mb-1"><?= htmlspecialchars($item['nama_produk']); ?></p>
                                            <strong class="text-danger">Rp <?= number_format($item['harga'], 0, ',', '.'); ?></strong>
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <button type="button" class="btn btn-link text-danger remove-item p-2" data-cart-id="<?= $item['id_keranjang']; ?>">
                                                <i class="bi bi-trash fs-5"></i>
                                            </button>
                                            <div class="quantity-control input-group ms-2 border rounded p-1">
                                                <button class="btn btn-sm btn-outline-secondary quantity-minus" type="button">-</button>
                                                <input type="text" class="form-control form-control-sm border-0 text-center quantity-input" value="<?= $item['jumlah']; ?>" readonly>
                                                <button class="btn btn-sm btn-outline-secondary quantity-plus" type="button">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <div class="col-lg-4">
                            <div class="card" id="summary-card">
                                <div class="card-body">
                                    <h5 class="card-title pb-3">Ringkasan Order</h5>
                                    <div class="d-flex justify-content-between subtotal-line mb-3">
                                        <span>Subtotal (<span id="selected-items-count">0</span> barang)</span>
                                        <strong id="subtotal-price" class="subtotal-price">Rp 0</strong>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" name="checkout" class="btn btn-danger btn-lg" id="checkout-button" disabled>
                                            Checkout
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-cart-x" style="font-size: 5rem; color: #ccc;"></i>
                        <h4 class="mt-3">Keranjang Anda Kosong</h4>
                        <p>Yuk, isi dengan barang-barang impianmu!</p>
                        <a href="produk.php" class="btn btn-primary mt-3">Mulai Belanja</a>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        if (!document.getElementById('cart-form')) {
            return; // Exit if there's no cart form on the page
        }

        function formatCurrency(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }

        function updateTotal() {
            let subtotal = 0;
            let selectedCount = 0;
            const checkoutButton = document.getElementById('checkout-button');

            document.querySelectorAll('.cart-item').forEach(itemCard => {
                const checkbox = itemCard.querySelector('.item-checkbox');
                if (checkbox.checked) {
                    const price = parseFloat(itemCard.dataset.price);
                    const quantity = parseInt(itemCard.querySelector('.quantity-input').value, 10);
                    subtotal += price * quantity;
                    selectedCount++;
                }
            });

            document.getElementById('subtotal-price').textContent = formatCurrency(subtotal);
            document.getElementById('selected-items-count').textContent = selectedCount;

            checkoutButton.disabled = selectedCount === 0;

            const allCheckboxes = document.querySelectorAll('.item-checkbox');
            const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
            document.getElementById('select-all').checked = allCheckboxes.length > 0 && allChecked;
        }
        
        function updateQuantity(cartId, newQuantity, quantityInput) {
            const itemCard = quantityInput.closest('.cart-item');
            const maxStock = parseInt(itemCard.dataset.stock, 10);

            if (newQuantity > maxStock) {
                alert('Stok tidak mencukupi. Sisa stok: ' + maxStock);
                quantityInput.value = maxStock;
                newQuantity = maxStock;
            }
            
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `update_quantity=1&cart_id=${cartId}&quantity=${newQuantity}`
            })
            .then(response => response.json())
            .then(data => {
                 if (data.error) {
                    alert(data.error);
                    quantityInput.value = data.current_quantity;
                } else if (data.action === 'removed') {
                    itemCard.remove();
                     if (document.querySelectorAll('.cart-item').length === 0) {
                        location.reload(); 
                    }
                }
                updateTotal();
            });
        }

        document.querySelectorAll('.cart-item').forEach(itemCard => {
            const cartId = itemCard.dataset.cartId;
            const quantityInput = itemCard.querySelector('.quantity-input');

            itemCard.querySelector('.quantity-plus').addEventListener('click', function() {
                let currentQuantity = parseInt(quantityInput.value, 10);
                quantityInput.value = currentQuantity + 1;
                updateQuantity(cartId, quantityInput.value, quantityInput);
            });

            itemCard.querySelector('.quantity-minus').addEventListener('click', function() {
                let currentQuantity = parseInt(quantityInput.value, 10);
                if (currentQuantity > 0) {
                    quantityInput.value = currentQuantity - 1;
                    updateQuantity(cartId, quantityInput.value, quantityInput);
                }
            });
        });

        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                if (!confirm('Anda yakin ingin menghapus item ini dari keranjang?')) {
                    return;
                }
                const cartId = this.dataset.cartId;
                const itemCard = this.closest('.cart-item');
                
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `remove_item=1&cart_id=${cartId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.action === 'removed') {
                        itemCard.remove();
                        updateTotal();
                        if (document.querySelectorAll('.cart-item').length === 0) {
                           location.reload(); 
                        }
                    }
                });
            });
        });

        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateTotal);
        });

        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateTotal();
        });

        updateTotal();
    });
    </script>
</body>
</html>