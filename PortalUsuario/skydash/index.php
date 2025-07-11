<?php
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

include 'db.php';

$nombreUsuario = "Usuario";
$customerName = "";

$today_campaigns = 0;
$total_campaigns = 0;
$total_messages = 0;
$total_users = 0;

try {
    $result_today = $conexion->query("SELECT COUNT(*) AS total FROM campaigns WHERE DATE(process_date) = CURDATE()");
    $today_campaigns = $result_today->fetch_assoc()['total'];

    $result_total = $conexion->query("SELECT COUNT(*) AS total FROM campaigns");
    $total_campaigns = $result_total->fetch_assoc()['total'];

    $result_messages = $conexion->query("SELECT COUNT(*) AS total FROM messages");
    $total_messages = $result_messages->fetch_assoc()['total'];

    $result_users = $conexion->query("SELECT COUNT(*) AS total FROM users WHERE deleted = 0");
    $total_users = $result_users->fetch_assoc()['total'];
} catch (Exception $e) {
    // En caso de error con las consultas, usar valores por defecto
}

if (isset($_SESSION['id_usuario']) && isset($_SESSION['username'])) {
    $id_usuario = $_SESSION['id_usuario']; 
    $nombreUsuario = htmlspecialchars($_SESSION['username']);
    
    if (isset($_SESSION['customer_id'])) {
        $customer_id = $_SESSION['customer_id'];
        
        try {
            $sql = "SELECT name FROM customers WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $customerName = htmlspecialchars($row['name']);
            }
            $stmt->close();
        } catch (Exception $e) {
            // En caso de error, mantener valor por defecto
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Prueba Tecnica</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="images/sinapsis.png" />
</head>

<body>
  <div class="container-scroller">
      <?php include 'layouts/header.php'; ?>
      <?php include 'layouts/configuration.php'; ?>
      <?php include 'layouts/sidebar.php'; ?>

      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <h3 class="font-weight-bold">Bienvenido, <?= $nombreUsuario?></h3>
                <h6 class="font-weight-normal mb-0">A mi<span class="text-primary"> Prueba Tecnica!</span> 
                <?php if($customerName): ?>
                  - Cliente: <span class="text-info"><?= $customerName ?></span>
                <?php endif; ?>
                </h6>
                </div>
                <div class="col-12 col-xl-4">
                 <div class="justify-content-end d-flex">
                  <div class="dropdown flex-md-grow-1 flex-xl-grow-0">

                  </div>
                 </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card tale-bg">
                <div class="card-people mt-auto">
                  <img src="images/dashboard/people.svg" alt="people">
                  <div class="weather-info">
                    <div class="d-flex">
                      <h2 class="mb-0 font-weight-normal">
                        <i class="icon-screen-smartphone mr-2"></i> 
                      </h2>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 grid-margin transparent">
              <div class="row">
                <div class="col-md-6 mb-4 stretch-card transparent">
                  <div class="card card-tale">
                    <div class="card-body">
                      <p class="mb-4">Campañas de Hoy</p>
                      <p class="fs-30 mb-2"><?php echo $today_campaigns; ?></p>
                      <p>Campañas procesadas hoy</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 mb-4 stretch-card transparent">
                  <div class="card card-dark-blue">
                    <div class="card-body">
                      <p class="mb-4">Total Campañas</p>
                      <p class="fs-30 mb-2"><?php echo $total_campaigns; ?></p>
                      <p>Campañas totales del sistema</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                  <div class="card card-light-blue">
                    <div class="card-body">
                      <p class="mb-4">Mensajes Enviados</p>
                      <p class="fs-30 mb-2"><?php echo $total_messages; ?></p>
                      <p>Total de mensajes SMS</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 stretch-card transparent">
                  <div class="card card-light-danger">
                    <div class="card-body">
                      <p class="mb-4">Usuarios Activos</p>
                      <p class="fs-30 mb-2"><?php echo $total_users; ?></p>
                      <p>Usuarios del sistema</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 stretch-card grid-margin">
              <div class="card">
                <div class="card-body">
                  <p class="card-title">Últimas Campañas</p>
                  <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                      <thead>
                        <tr>
                          <th>Campaña</th>
                          <th>Usuario</th>
                          <th>Fecha</th>
                          <th>Mensajes</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        try {
                          $sql = "SELECT c.name, u.username, c.process_date, 
                                  (SELECT COUNT(*) FROM messages m WHERE m.campaign_id = c.id) as total_messages
                                  FROM campaigns c 
                                  INNER JOIN users u ON c.user_id = u.id 
                                  ORDER BY c.process_date DESC 
                                  LIMIT 5";
                          $result = $conexion->query($sql);
                          
                          if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                              echo "<tr>
                                      <td>" . htmlspecialchars($row['name']) . "</td>
                                      <td>" . htmlspecialchars($row['username']) . "</td>
                                      <td>" . htmlspecialchars($row['process_date']) . "</td>
                                      <td>" . htmlspecialchars($row['total_messages']) . "</td>
                                    </tr>";
                            }
                          } else {
                            echo "<tr><td colspan='4'>No hay campañas registradas</td></tr>";
                          }
                        } catch (Exception $e) {
                          echo "<tr><td colspan='4'>Error al cargar datos</td></tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Lista de Usuarios</h4>
                  <div class="list-wrapper pt-2">
                    <ul class="d-flex flex-column-reverse todo-list todo-list-custom">
                      <?php
                      try {
                        $sql = "SELECT u.username, c.name as customer_name, u.deleted 
                                FROM users u 
                                INNER JOIN customers c ON u.customer_id = c.id 
                                ORDER BY u.id DESC 
                                LIMIT 7";
                        $result = $conexion->query($sql);
                        
                        if ($result && $result->num_rows > 0) {
                          while ($row = $result->fetch_assoc()) {
                            $status = $row['deleted'] ? 'Inactivo' : 'Activo';
                            $statusClass = $row['deleted'] ? 'text-danger' : 'text-success';
                            
                            echo '<li>
                                    <div class="form-check form-check-flat">
                                      <label class="form-check-label">
                                        <input class="checkbox" type="checkbox">
                                        ' . htmlspecialchars($row['username']) . ' - ' . htmlspecialchars($row['customer_name']) . '
                                        <br><small class="' . $statusClass . '">Estado: ' . $status . '</small>
                                      </label>
                                    </div>
                                    <i class="remove ti-close"></i>
                                  </li>';
                          }
                        } else {
                          echo '<li><span>No hay usuarios registrados</span></li>';
                        }
                      } catch (Exception $e) {
                        echo '<li><span>Error al cargar usuarios</span></li>';
                      }
                      ?>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!-- content-wrapper ends -->

        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>   
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="vendors/chart.js/Chart.min.js"></script>
  <script src="vendors/datatables.net/jquery.dataTables.js"></script>
  <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <script src="js/dataTables.select.min.js"></script>

  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="js/dashboard.js"></script>
  <script src="js/Chart.roundedBarCharts.js"></script>
  <!-- End custom js for this page-->

  <script>
    console.log('Variables de sesión disponibles:');
    <?php 
    echo "console.log('id_usuario: " . (isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 'NO SET') . "');";
    echo "console.log('username: " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'NO SET') . "');";
    echo "console.log('customer_id: " . (isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 'NO SET') . "');";
    ?>
  </script>
</body>

</html>