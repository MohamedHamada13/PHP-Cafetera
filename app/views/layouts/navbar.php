<style>

/* ===== Coffee Navbar Style ===== */

.navbar-coffee {
    background-color: #6f4e37;
    padding: 10px 24px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* NAV LINKS */
.navbar-coffee .nav-link {
    color: #FFF8E1 !important;
    font-weight: 500;
    margin: 0 10px;
    position: relative;
    transition: all 0.3s ease;
}

/* Hover underline animation */
.navbar-coffee .nav-link::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -5px;
    width: 0%;
    height: 2px;
    background-color: #FFD54F;
    transition: width 0.3s ease;
}

.navbar-coffee .nav-link:hover {
    color: #FFD54F !important;
}

.navbar-coffee .nav-link:hover::after {
    width: 100%;
}

/* USER NAME BADGE */
.navbar-coffee .user-name {
    background: rgba(255,255,255,0.12);
    padding: 6px 14px;
    border-radius: 20px;
    margin-right: 10px;
}

/* ACTION BUTTONS */
.navbar-coffee .action-btn {
    border: 1px solid #FFD54F;
    border-radius: 20px;
    padding: 6px 14px;
    color: #FFF8E1 !important;
    transition: all 0.3s ease;
}

.navbar-coffee .action-btn:hover {
    background-color: #FFD54F;
    color: #4E342E !important;
}

/* MOBILE TOGGLER */
.navbar-toggler {
    border-color: rgba(255,255,255,0.3);
}

.navbar-toggler-icon {
    filter: invert(1);
}

/* FIX BUTTON COLOR */
.btn {
    color: #FFF8E1 !important;
    font-weight: 500;
    margin: 0 10px;
}
.navbar-brand img {
      width: 80px !important;
    height: 80px !important;
    transition:all 0.4s;
    border-radius:50%;
    border:2px solid #6f4e37 !important;
}
.navbar-brand img:hover{
  transform:scale3d(1.3,1.3,1.3) rotate(9deg);
  border-color:#ddd !important;
}
</style>


<nav class="navbar navbar-expand-lg navbar-coffee">
  <div class="container-fluid">

    <!-- LOGO -->
    <a class="navbar-brand" href="/">
      <img src="/assets/images/logo.png" alt="Logo" height="40">
    </a>

    <!-- MOBILE BUTTON -->
    <button class="navbar-toggler" type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">

      <!-- LEFT MENU -->
      <ul class="navbar-nav me-auto align-items-center">

        <li class="nav-item">
          <a class="nav-link" href="/">Home</a>
        </li>

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>

          <li class="nav-item">
            <a class="nav-link" href="/admin/products">Products</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="/admin/orders">Orders</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="/admin/checks">Checks</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="/admin/users">Users</a>
          </li>

        <?php else: ?>

          <li class="nav-item">
            <a class="nav-link" href="/my-orders">My Orders</a>
          </li>

        <?php endif; ?>

      </ul>

      <!-- RIGHT MENU -->
      <ul class="navbar-nav ms-auto align-items-center">

        <?php if (isset($_SESSION['user_id'])): ?>

          <li class="nav-item">
            <span class="nav-link user-name">
              👋 <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>
            </span>
          </li>

          <li class="nav-item">
            <a class="btn action-btn" href="/logout">
              Logout
            </a>
          </li>

        <?php else: ?>

          <li class="nav-item">
            <a class="nav-link action-btn" href="/login">
              Login
            </a>
          </li>

        <?php endif; ?>

      </ul>

    </div>
  </div>
</nav>