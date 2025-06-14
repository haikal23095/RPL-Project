<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="container-fluid d-flex align-items-center justify-content-between">

    <div class="d-flex align-items-center">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="../assets/img/LOGOCASALUXE2.png" alt="logo">
        <span class="d-none d-lg-block">&nbsp; CasaLuxe</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn ms-3"></i>
    </div><!-- End Logo -->

    <nav class="header-nav">
      <ul class="d-flex align-items-center mb-0">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle" href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->
        <!-- Kategori Link -->
        <li class="nav-item pe-4">
          <a class="nav-link nav-profile d-flex align-items-center pe-1 <?php echo (isset($page) && $page == 'kategori') ? 'active' : ''; ?>" href="kategori.php">
            <span class="d-none d-md-block ps-2">
              <i class="bi bi-ui-checks-grid" style="font-size: 20px; color: <?php echo (isset($page) && $page == 'kategori') ? '#EFAA31' : '#2D3A3A'; ?>;"></i>
            </span>
          </a>
        </li>

        <!-- Notifikasi Link -->
        <li class="nav-item pe-4">
          <a class="nav-link nav-profile d-flex align-items-center pe-1 <?php echo (isset($page) && $page == 'notifikasi') ? 'active' : ''; ?>" href="notifikasi.php">
            <span class="d-none d-md-block ps-2">
              <i class="bi bi-bell" style="font-size: 20px; color: <?php echo (isset($page) && $page == 'notifikasi') ? '#EFAA31' : '#2D3A3A'; ?>;"></i>
            </span>
          </a>
        </li>

        <!-- Review Link -->
        <li class="nav-item pe-4">
          <a class="nav-link nav-profile d-flex align-items-center pe-1 <?php echo (isset($page) && $page == 'review') ? 'active' : ''; ?>" href="review.php">
            <span class="d-none d-md-block ps-2">
              <i class="bi bi-chat-quote" style="font-size: 20px; color: <?php echo (isset($page) && $page == 'review') ? '#EFAA31' : '#2D3A3A'; ?>;"></i>
            </span>
          </a>
        </li>

        <!-- Profil Link -->
        <li class="nav-item pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0 <?php echo (isset($page) && $page == 'profil') ? 'active' : ''; ?>" href="profil.php">
            <span class="d-none d-md-block ps-2" style="color: <?php echo (isset($page) && $page == 'profil') ? '#EFAA31' : '#2D3A3A'; ?>">
              <i class="bi bi-person" style="font-size: 20px; color: <?php echo (isset($page) && $page == 'profil') ? '#EFAA31' : '#2D3A3A'; ?>;"></i>&nbsp; Halo, <?= isset($_SESSION["admin"]) ? htmlspecialchars($_SESSION["admin"]) : 'Guest'; ?>!
            </span>
          </a><!-- End Profile Image Icon -->
        </li><!-- End Profile Nav --> 

      </ul>
    </nav><!-- End Icons Navigation -->

  </div><!-- End Container -->
</header><!-- End Header -->
