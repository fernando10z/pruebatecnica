<?php
require_once '../vendor/autoload.php';
require_once '../db.php'; // Conexión a la base de datos
use Dompdf\Dompdf;
use Dompdf\Options;

// Configurar zona horaria para Perú
date_default_timezone_set('America/Lima');


    $emisor = [
        'ruc' => '20123456789',
        'razon_social' => 'Sinapsis Technologies S.A.C.',
        'direccion' => 'Av. Principal 123, Lima, Perú',
        'telefono' => '+51 1 234-5678'
    ];


// Obtener el rol del usuario desde la sesión
session_start();
$rolUsuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : "1";

// Mapear el rol numérico a su nombre
$roles = [1 => "Administrador", 2 => "Analista", 3 => "Usuario"];
$nombreRol = isset($roles[$rolUsuario]) ? $roles[$rolUsuario] : "Analista de Plataformas";

// Obtener los datos filtrados del reporte
$reportData = isset($_POST['reportData']) ? json_decode($_POST['reportData'], true) : [];
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';

// Verificar si hay datos
if (empty($reportData)) {
    die("<script>alert('No hay registros disponibles para generar el reporte PDF.'); window.close();</script>");
}

// Estilos CSS del reporte en PDF
$html = '<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
    }
    
    #tabla-cabecera, #tabla-mensajes {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    #tabla-cabecera {
        text-align: center;
        color: #333;
    }

    #tabla-cabecera h3 {
        font-size: 18px;
        margin-bottom: 2px;
        color: #444;
    }

    .ruc-emisor {
        border: 2px solid #667eea;
        border-radius: 20px;
        text-align: center;
        padding: 15px;
        display: inline-block;
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    }

    .direccion {
        font-size: 13px;
        color: #666;
        line-height: 1.4;
    }

    #tabla-mensajes td, #tabla-mensajes th {
        border: 1px solid #667eea;
        padding: 12px;
        font-size: 12px;
        text-align: center;
    }

    #tabla-mensajes th {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        font-weight: bold;
        font-size: 13px;
    }

    #tabla-mensajes tbody tr:nth-child(even) {
        background-color: #f8fafc;
    }

    #tabla-mensajes tbody tr:hover {
        background-color: #e2e8f0;
    }

    .seccion-titulo {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 15px;
        margin: 25px 0 15px 0;
        font-weight: bold;
        font-size: 16px;
        border-radius: 10px;
        text-align: center;
    }

    .pie-pagina {
        margin-top: 30px;
        padding: 15px;
        font-size: 12px;
        border: 2px solid #667eea;
        border-radius: 10px;
        text-align: center;
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    }

    .marco-logo {
        border: 2px solid #667eea;
        border-radius: 15px;
        text-align: center;
        padding: 15px;
        display: inline-block;
        background: white;
    }

    .estadisticas {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 10px;
        border-radius: 8px;
        margin: 10px 0;
        text-align: center;
        font-weight: bold;
    }

    .ranking-gold { color: #f59e0b; font-weight: bold; }
    .ranking-silver { color: #6b7280; font-weight: bold; }
    .ranking-bronze { color: #cd7c2f; font-weight: bold; }

    .v45 { width: 45%; }
    .v30 { width: 30%; }
    .v25 { width: 25%; }
</style>';

// Calcular estadísticas
$totalClientes = count($reportData);
$totalMensajes = array_sum(array_column($reportData, 'total_successful_messages'));

// Cabecera con datos de la empresa
$html .= '<table id="tabla-cabecera">
    <tr>
        <td class="v25">
            <div class="marco-logo">
                <img src="https://reqlut2.s3.amazonaws.com/uploads/logos/3cdb24273653187ed92db39ff2fe144f5c6732cf-5242880.jpg" alt="Logo Sinapsis" style="height: 40px; margin: 0;">
                <p style="margin: 5px 0; font-size: 10px;">SINAPSIS</p>
            </div>
        </td>
        <td class="v45">
            <h3>' . htmlspecialchars($emisor['razon_social']) . '</h3>
            <div class="direccion">' . htmlspecialchars($emisor['direccion']) . '</div>
            <div class="direccion">RUC: ' . htmlspecialchars($emisor['ruc']) . '</div>
            <div class="direccion">Telf. ' . htmlspecialchars($emisor['telefono']) . '</div>
        </td>
        <td class="v30">
            <div class="ruc-emisor">
                <h4 style="margin: 0; color: #667eea;">REPORTE DE MENSAJES EXITOSOS</h4>
                <hr style="border: 1px solid #667eea; margin: 10px 0;">
                <div style="font-size: 12px;">
                    <strong>Período:</strong><br>
                    ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)) . '
                </div>
                <div style="font-size: 11px; margin-top: 5px;">
                    Generado: ' . date('d/m/Y H:i') . '
                </div>
            </div>
        </td>
    </tr>
</table>';

// Estadísticas generales
$html .= '<div class="estadisticas" style="color: black;">
RESUMEN EJECUTIVO: ' . $totalClientes . ' clientes procesaron ' . number_format($totalMensajes) . ' mensajes exitosos
</div>';

// Sección de Listado de Mensajes Exitosos
$html .= '<div class="seccion-titulo" style="color: black;">
RANKING DE CLIENTES POR MENSAJES EXITOSOS
</div>';

$html .= '<table id="tabla-mensajes">
    <thead>
        <tr>
            <th style="width: 10%;" style="color: black;">Ranking</th>
            <th style="width: 15%;" style="color: black;">ID Cliente</th>
            <th style="width: 45%;" style="color: black;">Nombre del Cliente</th>
            <th style="width: 20%;" style="color: black;">Mensajes Exitosos</th>
            <th style="width: 10%;" style="color: black;">% del Total</th>
        </tr>
    </thead>
    <tbody>';

// Recorrer los datos del reporte y agregar filas a la tabla
foreach ($reportData as $index => $client) {
    $ranking = $index + 1;
    $porcentaje = $totalMensajes > 0 ? round(($client['total_successful_messages'] / $totalMensajes) * 100, 2) : 0;
    
    // formato de numeros para el pdf
    $rankingClass = '';
    $rankingIcon = '';
    if ($ranking == 1) {
        $rankingClass = 'ranking-gold';
        $rankingIcon = '1';
    } elseif ($ranking == 2) {
        $rankingClass = 'ranking-silver';
        $rankingIcon = '2';
    } elseif ($ranking == 3) {
        $rankingClass = 'ranking-bronze';
        $rankingIcon = '3';
    } else {
        $rankingIcon = '#' . $ranking;
    }
    
    $html .= "<tr>
        <td class='{$rankingClass}'>{$rankingIcon}</td>
        <td>" . htmlspecialchars($client['customer_id']) . "</td>
        <td style='text-align: left; padding-left: 15px;'>" . htmlspecialchars($client['customer_name']) . "</td>
        <td style='font-weight: bold;'>" . number_format($client['total_successful_messages']) . "</td>
        <td>{$porcentaje}%</td>
    </tr>";
}

$html .= '</tbody></table>';

// Análisis adicional
if ($totalClientes > 0) {
    $promedioMensajes = round($totalMensajes / $totalClientes, 2);
    $clienteTop = $reportData[0];
    
    $html .= '<div style="background: #f8fafc; padding: 15px; border-radius: 10px; margin: 20px 0; border-left: 5px solid #667eea;">
        <h4 style="color: #667eea; margin: 0 0 10px 0;">ANÁLISIS ESTADÍSTICO</h4>
        <div style="font-size: 12px; line-height: 1.6;">
            <strong>• Cliente líder:</strong> ' . htmlspecialchars($clienteTop['customer_name']) . ' con ' . number_format($clienteTop['total_successful_messages']) . ' mensajes<br>
            <strong>• Promedio por cliente:</strong> ' . number_format($promedioMensajes) . ' mensajes exitosos<br>
            <strong>• Período analizado:</strong> ' . $totalClientes . ' clientes activos entre ' . date('d/m/Y', strtotime($startDate)) . ' y ' . date('d/m/Y', strtotime($endDate)) . '
        </div>
    </div>';
}

// Pie de página
$html .= '<div class="pie-pagina">
    <strong>Sistema de Gestión de Campañas SMS - Sinapsis</strong><br>
    Reporte generado por: <strong>' . htmlspecialchars($nombreRol) . '</strong> | 
    Fecha y hora: ' . date('d/m/Y H:i:s') . ' (Lima, Perú)<br>
    <div style="font-size: 10px; margin-top: 8px; color: #666;">
        Este reporte contiene información confidencial de campañas SMS. Uso exclusivo interno.
    </div>
</div>';

// Configuración y generación del PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Nombre del archivo PDF
$filename = "reporte_mensajes_exitosos_" . date('Y-m-d_H-i-s') . ".pdf";

// Mostrar el PDF en el navegador
$dompdf->stream($filename, ["Attachment" => false]);
?>