<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="container-fluid d-flex align-items-center justify-content-between">

    <div class="d-flex align-items-center">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="../assets/img/LOGOCASALUXE2.png" alt="logo">
        <span class="d-none d-lg-block"></i>&nbsp; Casaluxe</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn ms-3"></i>
    </div><!-- End Logo -->

    <nav class="header-nav">
      <ul class="d-flex align-items-center mb-0">
        <li class="nav-item pe-4">
          <a class="nav-link nav-profile d-flex align-items-center pe-1 <?php echo (isset($page) && $page == 'notifikasi') ? 'active' : ''; ?>" href="notifikasi.php">
            <span class="d-none d-md-block ps-2" style="color: <?php echo (isset($page) && $page == 'notifikasi') ? '#EFAA31' : '#2D3A3A'; ?>">
              <i style="font-size: 20px ; color: <?php echo (isset($page) && $page == 'notifikasi') ? '#EFAA31' : '#2D3A3A'; ?>" class="bi bi-bell"></i></span>
          </a>
        </li>
        <li class="nav-item pe-4">
          <a class="nav-link nav-profile d-flex align-items-center pe-1  <?php echo (isset($page) && $page == 'keranjang') ? 'active' : ''; ?>" href="add_to_cart.php">
            <span class="d-none d-md-block ps-2" style="color: <?php echo (isset($page) && $page == 'keranjang') ? '#EFAA31' : '#2D3A3A'; ?>">
              <i style="font-size: 20px; color: <?php echo (isset($page) && $page == 'keranjang') ? '#EFAA31' : '#2D3A3A'; ?>" class="bi bi-cart"></i></span>
          </a>
        </li>

        <li class="nav-item pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0 <?php echo (isset($page) && $page == 'profil') ? 'active' : ''; ?>" href="profil.php">
            <span class="d-none d-md-block ps-2" style="color: <?php echo (isset($page) && $page == 'profil') ? '#EFAA31' : '#2D3A3A'; ?>">
              <i style="font-size: 20px; color: <?php echo (isset($page) && $page == 'profil') ? '#EFAA31' : '#2D3A3A'; ?>" class="bi bi-person"></i>&nbsp; Halo, <?= $_SESSION["user"] ?>!
            </span>
          </a><!-- End Profile Image Icon -->
        </li><!-- End Profile Nav -->
        
      </ul>
    </nav><!-- End Icons Navigation -->

  </div><!-- End Container -->
</header><!-- End Header -->
