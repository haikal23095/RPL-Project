<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

    <h5 class="text-center"></h5>

      <li class="nav-item">
        <a class="nav-link <?php if($page == "dashboard"){ echo "collapsed"; } ?>" href="index.php">
          <i class="bi bi-grid"></i>
          <span>BERANDA</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php if($page == "user"){ echo "collapsed"; } ?>" href="user.php">
          <i class="bi bi-person"></i>
          <span>DATA USER</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php if($page == "produk"){ echo "collapsed"; } ?>" href="produk.php">
          <i class="bi bi-box-seam-fill"></i>
          <span>DATA PRODUK</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php if($page == "info"){ echo "collapsed"; } ?>" href="informasipromo.php">
          <i class="bi bi-megaphone"></i>
          <span>INFORMASI PROMO</span>
        </a>
      </li>
      

      <li class="nav-item">
        <a class="nav-link <?php if($page == "order"){ echo "collapsed"; } ?>" href="order.php">
          <i class="bi bi-currency-dollar"></i>
          <span>PESANAN MASUK</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php if($page == "penjualan"){ echo "collapsed"; } ?>" href="penjualan.php">
          <i class="bi bi-graph-up"></i>
          <span>PENJUALAN</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php if($page == "promo"){ echo "collapsed"; } ?>" href="promo.php">
          <i class="bi bi-tags"></i>
          <span>KODE PROMO</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php if($page == "statistik"){ echo "collapsed"; } ?>" href="stok_produk.php">
          <i class="bi bi-clipboard-data"></i>
          <span>STOK & STATISTIK</span>
        </a>
      </li>
      <br>
      <br>
      <br>
      <br>
      <li class="nav-item">
        <a class="nav-link" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>KELUAR</span>
        </a>
      </li>

    </ul>

</aside>