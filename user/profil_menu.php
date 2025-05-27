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
        <a class="nav-link <?php if($page == "history_pembayaran"){ echo "collapsed"; } ?>" href="history_pembayaran.php">
          <i class="bi bi-cash-stack"></i>
          <span>RIWAYAT PEMBAYARAN</span>
        </a>
      </li> 

      <li class="nav-item">
        <a class="nav-link <?php if($page == "pesanan_selesai"){ echo "collapsed"; } ?>" href="pesanan_selesai.php">
          <i class="bi bi-bag-check"></i>
          <span>PESANAN SELESAI</span>
        </a>
      </li>
      

      <li class="nav-item">
        <a class="nav-link <?php if($page == "pesanan_diproses"){ echo "collapsed"; } ?>" href="pesanan_diproses.php">
          <i class="bi bi-clock-history"></i>
          <span>PESANAN DIPROSES</span>
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link <?php if($page == "pesanan_dikirim"){ echo "collapsed"; } ?>" href="pesanan_dikirim.php">
          <i class="bi bi-truck"></i>
          <span>PESANAN DIKIRIM</span>
        </a>
      </li> 

      <li class="nav-item">
        <a class="nav-link <?php if($page == "pesanan_dibatalkan"){ echo "collapsed"; } ?>" href="pesanan_dibatalkan.php">
          <i class="bi bi-x-lg"></i>
          <span>PESANAN DIBATALKAN</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php if($page == "history_review"){ echo "collapsed"; } ?>" href="history_review.php">
          <i class="bi bi-chat-quote"></i>
          <span>RIWAYAT ULASAN</span>
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link <?php if($page == "wishlist"){ echo "collapsed"; } ?>" href="wishlist.php">
          <i class="bi bi-heart-fill"></i>
          <span>DAFTAR KEINGINAN</span>
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link <?php if($page == "history"){ echo "collapsed"; } ?>" href="pengeluaran.php">
          <i class="bi bi-wallet2"></i>
          <span>PENGELUARAN</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>KELUAR</span>
        </a>
      </li>
    </ul>

</aside>