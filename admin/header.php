<?php
// Only start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require("config.php");

// Redirect to login if session variable is not set
if (!isset($_SESSION['auser'])) {
    header("Location: index.php");
    exit();
}
?>
<style>
/* Sidebar Container */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 240px;
  height: 100vh;
  background-color: #1e1e1e;
  color: white;
  overflow-y: auto;
  z-index: 1000;
  transition: width 0.3s;
}

.sidebar-inner {
  padding: 20px 0;
}

/* Scrollbar Customization */
.sidebar-inner::-webkit-scrollbar {
  width: 6px;
}
.sidebar-inner::-webkit-scrollbar-thumb {
  background: #a8a432;
  border-radius: 6px;
}

/* Menu Items */
.sidebar-menu ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-menu .menu-title {
  padding: 10px 20px;
  color: #a8a432;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: bold;
}

/* Links */
.sidebar-menu li a {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  color: #e0e0e0;
  font-size: 15px;
  text-decoration: none;
  transition: background 0.3s, transform 0.1s;
  border-radius: 8px;
}

.sidebar-menu li a i {
  margin-right: 12px;
  font-size: 18px;
  color: #a8a432;
}

/* Hover and Click Effect */
.sidebar-menu li a:hover {
  background-color: #2d2d2d;
  transform: translateX(5px);
  color: #ffffff;
}
.sidebar-menu li a:active {
  transform: translateX(10px);
}

/* Submenu */
.submenu ul {
  display: none;
  padding-left: 20px;
}

.submenu:hover ul {
  display: block;
}

.submenu ul li a {
  padding: 10px 20px;
  font-size: 14px;
  color: #cccccc;
}

.submenu ul li a:hover {
  background-color: #333;
  color: #fff;
  transform: translateX(5px);
}

/* Active Link Highlight */
.sidebar-menu li a.active,
.sidebar-menu li.active > a {
  background-color: #a8a432;
  color: #111;
  font-weight: bold;
}

.sidebar-menu li a.active i,
.sidebar-menu li.active > a i {
  color: #111;
}

</style>

<!-- Header -->
<div class="header">
  <ul class="nav user-menu">
    <li class="nav-item dropdown app-dropdown">
      <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
        <span class="user-img"><img class="rounded-circle" src="assets/img/profiles/avatar-01.png" width="31"></span>
      </a>
      <div class="dropdown-menu">
        <div class="user-header">
          <div class="avatar avatar-sm">
            <img src="assets/img/profiles/avatar-01.png" alt="User Image" class="avatar-img rounded-circle">
          </div>
          <div class="user-text">
            <h6><?php echo $_SESSION['auser']; ?></h6>
            <p class="text-muted mb-0">Administrator</p>
          </div>
        </div>
        <a class="dropdown-item" href="profile.php">Profile</a>
        <a class="dropdown-item" href="logout.php">Logout</a>
      </div>
    </li>
  </ul>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-inner slimscroll">
    <div id="sidebar-menu" class="sidebar-menu">
      <ul>
        <li class="menu-title"><span>Main</span></li>
        <li><a href="dashboard.php"><i class="fe fe-home"></i> <span>Dashboard</span></a></li>

        <li class="menu-title"><span>All Users</span></li>
        <li class="submenu">
          <a href="#"><i class="fe fe-user"></i> <span> All Users </span></a>
          <ul>
            <li><a href="adminlist.php">Admin</a></li>
            <li><a href="userlist.php">Users</a></li>
            <li><a href="useragent.php">Agent</a></li>
          </ul>
        </li>

        <li class="menu-title"><span>State & City</span></li>
        <li class="submenu">
          <a href="#"><i class="fe fe-location"></i> <span>State & City</span></a>
          <ul>
            <li><a href="stateadd.php">State</a></li>
            <li><a href="cityadd.php">City</a></li>
          </ul>
        </li>

        <li class="menu-title"><span>Property Management</span></li>
        <li class="submenu">
          <a href="#"><i class="fe fe-map"></i> <span>Property</span></a>
          <ul>
            <li><a href="propertyview.php">Property Details</a></li>
          </ul>
        </li>

        <li class="menu-title"><span>Query</span></li>
        <li class="submenu">
          <a href="#"><i class="fe fe-comment"></i> <span>Contact & Feedback</span></a>
          <ul>
            <li><a href="contactview.php">Contact</a></li>
            <li><a href="feedbackview.php">Feedback</a></li>
          </ul>
        </li>

        <li class="menu-title"><span>About</span></li>
        <li class="submenu">
          <a href="#"><i class="fe fe-browser"></i> <span>About Page</span></a>
          <ul>
            <li><a href="aboutadd.php">Add About Content</a></li>
            <li><a href="aboutview.php">View About</a></li>
          </ul>
        </li>
        
         <li class="menu-title"><span>Reports</span></li>
        <li class="submenu">
          <a href="#"><i class="fe fe-browser"></i> <span>Report Page</span></a>
          <ul>
            <li><a href="reports.php">Reports</a></li>

          </ul>
        </li>
          
      </ul>
    </div>
  </div>
</div>
