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
    <title>Reportes de Mensajes Exitosos</title>
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
        .pdf-section {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            text-align: center;
            display: none;
        }
        .btn-pdf {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-pdf:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
            color: white;
        }
        .ranking-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 12px;
        }
        .ranking-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; }
        .ranking-2 { background: linear-gradient(135deg, #9ca3af, #6b7280); color: white; }
        .ranking-3 { background: linear-gradient(135deg, #cd7c2f, #92400e); color: white; }
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
                                <h3 class="font-weight-bold" style="padding-left: 20px;">
                                    Reportes de Mensajes Exitosos
                                </h3>
                                <h6 class="font-weight-normal mb-0" style="padding-left: 20px;">
                                    Prueba Técnica - Analista de Plataformas
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Reportes -->
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                Generador de Reportes
                            </h4>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="startDate" class="form-label">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="startDate" value="2025-01-01">
                                </div>
                                <div class="col-md-4">
                                    <label for="endDate" class="form-label">Fecha Fin</label>
                                    <input type="date" class="form-control" id="endDate" value="2025-12-31">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-primary w-100" onclick="generateReport()">
                                        <i class="fas fa-search"></i> Generar Reporte
                                    </button>
                                </div>
                            </div>

                            <!-- Sección PDF (aparece después de generar reporte) -->
                            <div class="pdf-section" id="pdfSection">
                                <h5><i class="fas fa-file-pdf"></i> ¡Reporte Generado Exitosamente!</h5>
                                <p>Ahora puedes descargar el reporte en formato PDF</p>
                                <button type="button" class="btn btn-pdf" onclick="generatePDF()">
                                    <i class="fas fa-download"></i> Descargar PDF
                                </button>
                            </div>
                            
                            <div class="table-responsive">
                                <table id="reportsTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Ranking</th>
                                            <th>ID Cliente</th>
                                            <th>Nombre Cliente</th>
                                            <th>Mensajes Exitosos</th>
                                            <th>% del Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <!-- Formulario oculto para enviar datos al PDF -->
    <form id="pdfForm" method="post" action="reportes/generar_pdf_reporte.php" target="_blank" style="display: none;">
        <input type="hidden" name="reportData" id="reportDataInput">
        <input type="hidden" name="startDate" id="startDateInput">
        <input type="hidden" name="endDate" id="endDateInput">
    </form>
    
    <?php include 'modal/campaña/ver_detalles.php'; ?>
    <?php include 'layouts/plugins.php'; ?>
    <!-- SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.27/sweetalert2.min.js"></script>

    <script>
        // Configuration
        const API_BASE_URL = 'http://localhost:3000/api';
        let reportsTable;
        let currentReportData = []; // Para almacenar los datos del reporte actual

        $(document).ready(function() {
            // Inicializar tabla de reportes
            reportsTable = $('#reportsTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                order: [[3, 'desc']], // Ordenar por mensajes exitosos
                pageLength: 10
            });
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
        // FUNCIÓN PRINCIPAL - GENERAR REPORTE
        // ===============================
        async function generateReport() {
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            
            if (!startDate || !endDate) {
                showAlert('warning', 'Atención', 'Por favor seleccione ambas fechas');
                return;
            }
            
            try {
                showLoading();
                const response = await fetch(`${API_BASE_URL}/reports/successful-messages?start_date=${startDate}&end_date=${endDate}`);
                const data = await response.json();
                
                if (data.success) {
                    reportsTable.clear();
                    currentReportData = data.data.clients; // Guardar datos para PDF
                    
                    // Calcular total de mensajes para porcentajes
                    const totalMessages = currentReportData.reduce((sum, client) => sum + client.total_successful_messages, 0);
                    
                    currentReportData.forEach((client, index) => {
                        const ranking = index + 1;
                        const percentage = totalMessages > 0 ? ((client.total_successful_messages / totalMessages) * 100).toFixed(2) : 0;
                        
                        // Crear badge de ranking
                        let rankingBadge = '';
                        if (ranking === 1) {
                            rankingBadge = `<span class="ranking-badge ranking-1">🥇 #${ranking}</span>`;
                        } else if (ranking === 2) {
                            rankingBadge = `<span class="ranking-badge ranking-2">🥈 #${ranking}</span>`;
                        } else if (ranking === 3) {
                            rankingBadge = `<span class="ranking-badge ranking-3">🥉 #${ranking}</span>`;
                        } else {
                            rankingBadge = `<span class="badge badge-secondary">#${ranking}</span>`;
                        }
                        
                        reportsTable.row.add([
                            rankingBadge,
                            client.customer_id,
                            client.customer_name,
                            client.total_successful_messages.toLocaleString(),
                            `${percentage}%`
                        ]);
                    });
                    
                    reportsTable.draw();
                    
                    // Mostrar sección PDF y mensaje de éxito
                    $('#pdfSection').slideDown();
                    showAlert('success', '¡Éxito!', 
                        `Reporte generado con ${data.data.total_clients} clientes\n` +
                        `Total mensajes exitosos: ${totalMessages.toLocaleString()}`
                    );
                } else {
                    showAlert('error', 'Error', data.message);
                }
            } catch (error) {
                console.error('Error generating report:', error);
                showAlert('error', 'Error', 'No se pudo generar el reporte. Verifica que el servidor Node.js esté corriendo.');
            } finally {
                hideLoading();
            }
        }

        // ===============================
        // FUNCIÓN GENERAR PDF
        // ===============================
        function generatePDF() {
            if (currentReportData.length === 0) {
                showAlert('warning', 'Atención', 'Primero debes generar un reporte');
                return;
            }

            try {
                showLoading();
                
                // Preparar datos para el PDF
                const reportDataForPDF = JSON.stringify(currentReportData);
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();
                
                // Llenar formulario oculto
                $('#reportDataInput').val(reportDataForPDF);
                $('#startDateInput').val(startDate);
                $('#endDateInput').val(endDate);
                
                // Enviar formulario para generar PDF
                $('#pdfForm').submit();
                
                setTimeout(() => {
                    hideLoading();
                    showAlert('info', 'PDF Generado', 'El archivo PDF se abrirá en una nueva pestaña');
                }, 1500);
                
            } catch (error) {
                console.error('Error generating PDF:', error);
                hideLoading();
                showAlert('error', 'Error', 'No se pudo generar el PDF');
            }
        }

        // ===============================
        // FUNCIÓN REFRESCAR DATOS
        // ===============================
        function refreshData() {
            showLoading();
            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        // ===============================
        // VALIDACIONES DE FECHA
        // ===============================
        $('#startDate, #endDate').on('change', function() {
            const startDate = new Date($('#startDate').val());
            const endDate = new Date($('#endDate').val());
            
            if (startDate > endDate) {
                showAlert('warning', 'Fechas Inválidas', 'La fecha de inicio no puede ser mayor a la fecha fin');
                $('#startDate').val('2025-01-01');
                $('#endDate').val('2025-12-31');
            }
        });
    </script>
</body>
</html>