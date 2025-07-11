<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

include 'db.php'; // Conectar a la base de datos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Gestión de Campañas</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/feather/feather.css">
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css">
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <link rel="shortcut icon" href="images/sinapsis.png" />
    <!-- SweetAlert2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.27/sweetalert2.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .btn-api {
            margin: 2px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-api:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
        }
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
        }
        .loading-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
        }
        .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <h5>Procesando...</h5>
            <p>Por favor espere</p>
        </div>
    </div>

    <div class="container-scroller">
        <?php include 'layouts/header.php'; ?>
        <?php include 'layouts/configuration.php'; ?>
        <?php include 'layouts/sidebar.php'; ?>

        <div class="main-panel">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-md-12 grid-margin">
                        <div class="row">
                            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                                <h3 class="font-weight-bold" style= "padding-left: 20px;">Campaña</h3>
                                <h6 class="font-weight-normal mb-0" style= "padding-left: 20px;">Prueba Técnica - Analista de Plataformas</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Lista de Campañas SMS</h4>
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="refreshData()">
                                        <i class="fas fa-sync-alt"></i> Actualizar
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive pt-3">
                                <table id="campaignsTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <?php
                                            $columns = [
                                                ['title' => 'ID', 'type' => 'text'],
                                                ['title' => 'Nombre', 'type' => 'text'],
                                                ['title' => 'Cliente', 'type' => 'text'],
                                                ['title' => 'Usuario', 'type' => 'text'],
                                                ['title' => 'Estado', 'type' => 'select', 'options' => ['', 'Finalizada', 'Pendiente']],
                                                ['title' => 'Total Mensajes', 'type' => 'text'],
                                                ['title' => 'Enviados', 'type' => 'text'],
                                                ['title' => 'Errores', 'type' => 'text'],
                                                ['title' => 'Fecha', 'type' => 'date'],
                                                ['title' => 'Acciones API', 'type' => 'none']
                                            ];

                                            foreach ($columns as $column) {
                                                echo "<th>{$column['title']}<br>";
                                                if ($column['type'] !== 'none') {
                                                    if ($column['type'] === 'select') {
                                                        echo '<select class="form-control form-control-sm">';
                                                        foreach ($column['options'] as $option) {
                                                            $label = $option ?: 'Todos';
                                                            echo "<option value='$option'>$label</option>";
                                                        }
                                                        echo '</select>';
                                                    } else {
                                                        echo "<input type='{$column['type']}' class='form-control form-control-sm' placeholder='Buscar'>";
                                                    }
                                                }
                                                echo "</th>";
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT 
                                                c.id,
                                                c.name as campaign_name,
                                                cu.name as customer_name,
                                                u.username,
                                                c.process_date,
                                                c.process_hour,
                                                c.total_records,
                                                c.total_sent,
                                                c.total_error,
                                                c.process_status,
                                                c.final_hour,
                                                (SELECT COUNT(*) FROM messages m WHERE m.campaign_id = c.id) as total_messages
                                            FROM campaigns c
                                            INNER JOIN users u ON c.user_id = u.id
                                            INNER JOIN customers cu ON u.customer_id = cu.id
                                            ORDER BY c.process_date DESC, c.id DESC";
                                        
                                        $result = $conexion->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                // Determinar el estado de la campaña
                                                $status = 'Pendiente';
                                                $statusClass = 'warning';
                                                
                                                if ($row['process_status'] == 2) {
                                                    $status = 'Finalizada';
                                                    $statusClass = 'success';
                                                } elseif ($row['process_status'] == 1) {
                                                    $status = 'Pendiente';
                                                    $statusClass = 'warning';
                                                }

                                                // Calcular mensajes
                                                $total_messages = $row['total_messages'] ?: 0;
                                                $total_sent = $row['total_sent'] ?: 0;
                                                $total_error = $row['total_error'] ?: 0;
                                                
                                                echo "<tr>
                                                        <td>{$row['id']}</td>
                                                        <td>{$row['campaign_name']}</td>
                                                        <td>{$row['customer_name']}</td>
                                                        <td>{$row['username']}</td>
                                                        <td><span class='badge badge-{$statusClass}'>{$status}</span></td>
                                                        <td>{$total_messages}</td>
                                                        <td>{$total_sent}</td>
                                                        <td>{$total_error}</td>
                                                        <td>{$row['process_date']}</td>
                                                        <td>
                                                            <div role='group'>
                                                                <button type='button' class='btn btn-outline-success btn-sm btn-api' 
                                                                    onclick='calculateTotals({$row['id']})' title='Calcular Totales'>
                                                                    <i class='fas fa-calculator'></i>
                                                                </button>
                                                                <button type='button' class='btn btn-outline-warning btn-sm btn-api' 
                                                                    onclick='updateStatus({$row['id']})' title='Actualizar Estado'>
                                                                    <i class='fas fa-sync-alt'></i>
                                                                </button>
                                                                <button type='button' class='btn btn-outline-info btn-sm btn-api' 
                                                                    onclick='viewDetails({$row['id']})' title='Ver Detalles'>
                                                                    <i class='fas fa-eye'></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='10' class='text-center'>No hay campañas registradas</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <?php include 'modal/campaña/ver_detalles.php'; ?>
    <?php include 'layouts/plugins.php'; ?>
    <!-- SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.27/sweetalert2.min.js"></script>

    <script>
        // Configuration
        const API_BASE_URL = 'http://localhost:3000/api';
        let reportsTable;

        $(document).ready(function() {
            const table = $('#campaignsTable').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 5,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            columnDefs: [
                { targets: 0, type: 'num' } 
            ],
            order: [[0, 'asc']] 
            });

            // Filtrado por columnas
            $('#campaignsTable thead th input').on('keyup change', function() {
                const columnIndex = $(this).closest('th').index();
                table.column(columnIndex).search(this.value).draw();
            });

            // Filtrado exacto para el estado
            $('#campaignsTable thead th select').on('change', function() {
                const columnIndex = $(this).closest('th').index();
                const filterValue = this.value;

                if (filterValue) {
                    table.column(columnIndex).search('^' + filterValue + '$', true, false).draw();
                } else {
                    table.column(columnIndex).search('').draw();
                }
            });

            // Cargar estadísticas iniciales
            loadStats();
        });

        function showLoading() {
            $('#loadingOverlay').show();
        }

        function hideLoading() {
            $('#loadingOverlay').hide();
        }

        function showAlert(type, title, message) {
            Swal.fire({
                icon: type,
                title: title,
                text: message,
                confirmButtonText: 'OK'
            });
        }

        // ===============================
        // FUNCIONES API - PRUEBA TÉCNICA
        // ===============================

        async function calculateTotals(campaignId) {
            try {
                showLoading();
                const response = await fetch(`${API_BASE_URL}/campaigns/${campaignId}/calculate-totals`, {
                    method: 'PUT'
                });
                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', '¡Éxito!', data.message);
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showAlert('error', 'Error', data.message);
                }
            } catch (error) {
                console.error('Error calculating totals:', error);
                showAlert('error', 'Error', 'No se pudo calcular los totales. Verifica que el servidor Node.js esté corriendo.');
            } finally {
                hideLoading();
            }
        }

        async function updateStatus(campaignId) {
            try {
                showLoading();
                const response = await fetch(`${API_BASE_URL}/campaigns/${campaignId}/update-status`, {
                    method: 'PUT'
                });
                const data = await response.json();
                
                if (data.success) {
                    const statusDesc = data.data.status_description;
                    const finalHour = data.data.campaign_info?.final_hour || 'N/A';
                    showAlert('success', '¡Éxito!', 
                        `Estado actualizado: ${statusDesc}\nHora final: ${finalHour}`);
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showAlert('error', 'Error', data.message);
                }
            } catch (error) {
                console.error('Error updating status:', error);
                showAlert('error', 'Error', 'No se pudo actualizar el estado. Verifica que el servidor Node.js esté corriendo.');
            } finally {
                hideLoading();
            }
        }

        async function viewDetails(campaignId) {
            try {
                showLoading();
                const response = await fetch(`${API_BASE_URL}/campaigns/${campaignId}`);
                const data = await response.json();
                
                if (data.success) {
                    const campaign = data.data;
                    const statusText = campaign.process_status == 2 ? 'Finalizada' : 
                                     campaign.process_status == 1 ? 'Pendiente' : 'Sin procesar';
                    
                    Swal.fire({
                        title: `Campaña #${campaign.id}`,
                        html: `
                            <div class="text-left">
                                <p><strong>Nombre:</strong> ${campaign.name || 'Sin nombre'}</p>
                                <p><strong>Cliente:</strong> ${campaign.customer_name}</p>
                                <p><strong>Usuario:</strong> ${campaign.username}</p>
                                <p><strong>Estado:</strong> ${statusText}</p>
                                <p><strong>Fecha de Proceso:</strong> ${campaign.process_date || 'N/A'}</p>
                                <p><strong>Hora de Proceso:</strong> ${campaign.process_hour || 'N/A'}</p>
                                <p><strong>Hora Final:</strong> ${campaign.final_hour || 'N/A'}</p>
                                <hr>
                                <p><strong>Total Mensajes:</strong> ${campaign.total_records || 0}</p>
                                <p><strong>Mensajes Enviados:</strong> ${campaign.total_sent || 0}</p>
                                <p><strong>Mensajes con Error:</strong> ${campaign.total_error || 0}</p>
                            </div>
                        `,
                        icon: 'info',
                        confirmButtonText: 'Cerrar',
                        width: '600px'
                    });
                } else {
                    showAlert('error', 'Error', data.message);
                }
            } catch (error) {
                console.error('Error loading campaign details:', error);
                showAlert('error', 'Error', 'No se pudo cargar los detalles');
            } finally {
                hideLoading();
            }
        }

        async function loadStats() {
            try {
                const response = await fetch(`${API_BASE_URL}/campaigns`);
                const data = await response.json();
                
                if (data.success) {
                    const campaigns = data.data;
                    const totalCampaigns = campaigns.length;
                    const completedCampaigns = campaigns.filter(c => c.process_status == 2).length;
                    const pendingCampaigns = campaigns.filter(c => c.process_status == 1).length;
                    const totalMessages = campaigns.reduce((sum, c) => sum + (c.total_records || 0), 0);
                    
                    $('#totalCampaigns').text(totalCampaigns);
                    $('#completedCampaigns').text(completedCampaigns);
                    $('#pendingCampaigns').text(pendingCampaigns);
                    $('#totalMessages').text(totalMessages.toLocaleString());
                }
            } catch (error) {
                console.error('Error loading stats:', error);
                // Si falla la conexión, mantener los valores por defecto
            }
        }

        function refreshData() {
            showLoading();
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    </script>
</body>
</html>