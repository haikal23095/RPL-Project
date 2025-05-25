<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

    <h5 class="text-center">MAIN MENU</h5>
      <li class="nav-item">
        <a class="nav-link <?php if($page == "dashboard"){ echo "collapsed"; } ?>" href="index.php">
          <i class="bi bi-grid"></i>
          <span>DASHBOARD</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php if($page == "info"){ echo "collapsed"; } ?>" href="informasipromo.php">
          <i class="bi bi-megaphone"></i>
          <span>INFORMASI PROMO</span>
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link <?php if($page == "fav"){ echo "collapsed"; } ?>" href="most_favorite.php">
          <i class="bi bi-star-fill"></i>
          <span>FAVORITE</span>
        </a>
      </li>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      
      <li class="nav-item">
        <a class="nav-link <?php if($page == "chat"){ echo "collapsed"; } ?>" href="chat.php">
          <i class="bi bi-headset"></i>
          <span>CUSTOMER SERVICE</span>
        </a>
      </li> 

      <li class="nav-item">
        <a class="nav-link" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>LOG-OUT</span>
        </a>
      </li>

    </ul>

</aside>