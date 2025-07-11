<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
      <li class="nav-item">
        <a class="nav-link" href="index.php">
          <i class="icon-grid menu-icon mr-2"></i>
          <span class="menu-title">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#form-clientes" aria-expanded="false" aria-controls="form-clientes">
          <i class="mdi mdi-database mr-2"></i>
          <span class="menu-title">Endpoints</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="form-clientes">
          <ul class="nav flex-column sub-menu">
        <li class="nav-item"><a class="nav-link" href="campaña.php">Campañas</a></li>
        <li class="nav-item"><a class="nav-link" href="reporte.php">Reportes</a></li>
          </ul>
        </div>
      </li>
  </ul>
</nav>
