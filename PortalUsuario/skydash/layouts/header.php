<?php
// Iniciar sesión antes de cualquier salida
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

require_once 'db.php';

// Verificar si el usuario ha iniciado sesión y tiene un rol asignado
$id_rol = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;
?>

<style>
  .notification-icon {
    position: relative;
    display: inline-block;
  }

  .notification-icon i {
      font-size: 24px;
      color: #333;
  }

  .notification-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background: red;
      color: white;
      font-size: 12px;
      font-weight: bold;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
  }
</style>

<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo" href="index.php" style="font-size: 1.5rem; font-weight: bold; color: #333;">Sinapsis</a>
        <a class="navbar-brand brand-logo-mini" href="index.php" style="font-size: 1rem; font-weight: bold; color: #333;">Sinap</a>

      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>

        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <img src="images/sinapsis.png" alt="profile"/>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
           <!-- <?php if ($id_rol == 1) : // Solo muestra lo siguiente si es Administrador ?>
              <a class="dropdown-item" href="configuracion.php">
                <i class="ti-settings text-primary"></i>
                Configuración
              </a>
              <?php endif; ?>


              <?php if ($id_rol == 2) : // Solo muestra lo siguiente si es Administrador ?>
                <a class="dropdown-item" href="configuracionempresa.php">
                <i class="ti-settings text-primary"></i>
                Configuración Empresa
              </a>
              <?php endif; ?> -->

              <a class="dropdown-item" href="layouts/logout.php">
                <i class="ti-power-off text-primary"></i>
                Cerra Sesión
              </a>
            </div>
          </li>

        </ul>
        <!-- <ul class="navbar-nav mr-lg-2">
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
                    <i class="mdi mdi-bell-outline"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="notificationDropdown">
                    <h6 class="p-3 mb-0">Notificaciones</h6>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-divider"></div>
                    <a href="stock.php" class="dropdown-item text-center text-primary">Ver todos</a>
                </div>
            </li>
        </ul> -->
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>
    <!-- partial -->
    

    <!-- acciones -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
      <div class="theme-setting-wrapper">
        <div id="settings-trigger"><i class="ti-settings"></i></div>
        <div id="theme-settings" class="settings-panel">
          <i class="settings-close ti-close"></i>
          <p class="settings-heading">Diseño de Pagina</p>
          <div class="sidebar-bg-options selected" id="sidebar-light-theme"><div class="img-ss rounded-circle bg-light border mr-3"></div>Light</div>
          <div class="sidebar-bg-options" id="sidebar-dark-theme"><div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark</div>
          <p class="settings-heading mt-2">HEADER SKINS</p>
          <div class="color-tiles mx-0 px-4">
            <div class="tiles success"></div>
            <div class="tiles warning"></div>
            <div class="tiles danger"></div>
            <div class="tiles info"></div>
            <div class="tiles dark"></div>
            <div class="tiles default"></div>
          </div>
        </div>
      </div>