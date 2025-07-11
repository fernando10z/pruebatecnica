//Creado por Fernando Jose Carbajal Carbajal para la prueba técnica de Sinapsis

const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middlewares
app.use(cors());
app.use(express.json());

// Configuración de base de datos
const dbConfig = {
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'test_db',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
};

const pool = mysql.createPool(dbConfig);

// ===============================
// ENDPOINT 1: Desarrollar un endpoint que calcule los totales de una campaña y que actualice sus respec vas columnas. 
// ===============================
app.put('/api/campaigns/:id/calculate-totals', async (req, res) => {
    const campaignId = req.params.id;
    
    try {
        // Calcular totales basados en los mensajes
        const [totalsResult] = await pool.execute(`
            SELECT 
                COUNT(*) as total_records,
                SUM(CASE WHEN shipping_status = 2 THEN 1 ELSE 0 END) as total_sent,
                SUM(CASE WHEN shipping_status = 3 THEN 1 ELSE 0 END) as total_error
            FROM messages 
            WHERE campaign_id = ?
        `, [campaignId]);
        
        const totals = totalsResult[0];
        
        // Actualizar la campaña con los totales calculados
        await pool.execute(`
            UPDATE campaigns 
            SET 
                total_records = ?,
                total_sent = ?,
                total_error = ?
            WHERE id = ?
        `, [totals.total_records, totals.total_sent, totals.total_error, campaignId]);
        
        res.json({
            success: true,
            message: 'Totales calculados y actualizados correctamente',
            data: {
                campaign_id: campaignId,
                total_records: totals.total_records,
                total_sent: totals.total_sent,
                total_error: totals.total_error
            }
        });
        
    } catch (error) {
        console.error('Error calculando totales:', error);
        res.status(500).json({
            success: false,
            message: 'Error interno del servidor',
            error: error.message
        });
    }
});

// ===============================
// ENDPOINT 2: Desarrollar un endpoint que permita identificar el estado de una campaña y que actualice su respectiva columna. Si la campaña está finalizada, también debe actualizar la columna final_hour. 
// ===============================
app.put('/api/campaigns/:id/update-status', async (req, res) => {
    const campaignId = req.params.id;
    
    try {
        // Verificar si hay mensajes pendientes (estado 1)
        const [pendingResult] = await pool.execute(`
            SELECT COUNT(*) as pending_count
            FROM messages 
            WHERE campaign_id = ? AND shipping_status = 1
        `, [campaignId]);
        
        const hasPendingMessages = pendingResult[0].pending_count > 0;
        const newStatus = hasPendingMessages ? 1 : 2; // 1: pendiente, 2: finalizada
        
        let updateQuery = `UPDATE campaigns SET process_status = ?`;
        let updateParams = [newStatus];
        
        // Solo actualizar final_hour si la campaña está finalizada (según requerimiento)
        if (newStatus === 2) {
            const [finalHourResult] = await pool.execute(`
                SELECT MAX(shipping_hour) as max_hour
                FROM messages 
                WHERE campaign_id = ? AND shipping_status IN (2, 3) AND shipping_hour IS NOT NULL
            `, [campaignId]);
            
            const finalHour = finalHourResult[0].max_hour;
            
            if (finalHour) {
                updateQuery += `, final_hour = ?`;
                updateParams.push(finalHour);
            }
        }
        
        updateQuery += ` WHERE id = ?`;
        updateParams.push(campaignId);
        
        await pool.execute(updateQuery, updateParams);
        
        // Obtener información actualizada de la campaña
        const [campaignResult] = await pool.execute(`
            SELECT id, name, process_status, final_hour
            FROM campaigns 
            WHERE id = ?
        `, [campaignId]);
        
        res.json({
            success: true,
            message: 'Estado de campaña actualizado correctamente',
            data: {
                campaign_id: campaignId,
                process_status: newStatus,
                status_description: newStatus === 1 ? 'Pendiente' : 'Finalizada',
                pending_messages: pendingResult[0].pending_count,
                campaign_info: campaignResult[0]
            }
        });
        
    } catch (error) {
        console.error('Error actualizando estado:', error);
        res.status(500).json({
            success: false,
            message: 'Error interno del servidor',
            error: error.message
        });
    }
});

// ===============================
// ENDPOINT 3: Desarrollar un endpoint que dado una inicial y final, retorne la lista de clientes con su respec vo total de mensajes exitosos en el rango de fechas
// ===============================
app.get('/api/reports/successful-messages', async (req, res) => {
    const { start_date, end_date } = req.query;
    
    if (!start_date || !end_date) {
        return res.status(400).json({
            success: false,
            message: 'Se requieren los parámetros start_date y end_date (formato: YYYY-MM-DD)'
        });
    }
    
    try {
        const [results] = await pool.execute(`
            SELECT 
                c.id as customer_id,
                c.name as customer_name,
                COUNT(m.id) as total_successful_messages
            FROM customers c
            INNER JOIN users u ON c.id = u.customer_id
            INNER JOIN campaigns cam ON u.id = cam.user_id
            INNER JOIN messages m ON cam.id = m.campaign_id
            WHERE 
                c.deleted = FALSE
                AND u.deleted = FALSE
                AND m.shipping_status = 2
                AND cam.process_date BETWEEN ? AND ?
            GROUP BY c.id, c.name
            ORDER BY total_successful_messages DESC, c.name ASC
        `, [start_date, end_date]);
        
        res.json({
            success: true,
            message: 'Reporte generado correctamente',
            data: {
                date_range: {
                    start_date,
                    end_date
                },
                total_clients: results.length,
                clients: results
            }
        });
        
    } catch (error) {
        console.error('Error generando reporte:', error);
        res.status(500).json({
            success: false,
            message: 'Error interno del servidor',
            error: error.message
        });
    }
});

// ===============================
// ENDPOINTS AGREGADOS POR MI
// ===============================

// Sirve para obtener información de una campaña específica
app.get('/api/campaigns/:id', async (req, res) => {
    const campaignId = req.params.id;
    
    try {
        const [campaignResult] = await pool.execute(`
            SELECT 
                c.*,
                u.username,
                cu.name as customer_name
            FROM campaigns c
            INNER JOIN users u ON c.user_id = u.id
            INNER JOIN customers cu ON u.customer_id = cu.id
            WHERE c.id = ?
        `, [campaignId]);
        
        if (campaignResult.length === 0) {
            return res.status(404).json({
                success: false,
                message: 'Campaña no encontrada'
            });
        }
        
        res.json({
            success: true,
            data: campaignResult[0]
        });
        
    } catch (error) {
        console.error('Error obteniendo campaña:', error);
        res.status(500).json({
            success: false,
            message: 'Error interno del servidor',
            error: error.message
        });
    }
});

//Hace una lista de todas las campañas
app.get('/api/campaigns', async (req, res) => {
    try {
        const [campaigns] = await pool.execute(`
            SELECT 
                c.*,
                u.username,
                cu.name as customer_name,
                CASE 
                    WHEN c.process_status = 1 THEN 'Pendiente'
                    WHEN c.process_status = 2 THEN 'Finalizada'
                    ELSE 'Desconocido'
                END as status_description
            FROM campaigns c
            INNER JOIN users u ON c.user_id = u.id
            INNER JOIN customers cu ON u.customer_id = cu.id
            ORDER BY c.process_date DESC, c.id DESC
        `);
        
        res.json({
            success: true,
            total: campaigns.length,
            data: campaigns
        });
        
    } catch (error) {
        console.error('Error listando campañas:', error);
        res.status(500).json({
            success: false,
            message: 'Error interno del servidor',
            error: error.message
        });
    }
});

//Hace una verificación de el estado de la API
app.get('/api/health', async (req, res) => {
    try {
        await pool.execute('SELECT 1');
        res.json({
            success: true,
            message: 'API funcionando correctamente',
            timestamp: new Date().toISOString()
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            message: 'Error de conexión a base de datos',
            error: error.message
        });
    }
});

//Un middleware para rutas no encontradas
app.use('*', (req, res) => {
    res.status(404).json({
        success: false,
        message: 'Endpoint no encontrado'
    });
});

// Iniciar servidor
app.listen(PORT, () => {
    console.log(`
Creado por Fernando Jose Carbajal Carbajal para la prueba técnica de Sinapsis.
Servidor iniciado correctamente.
Puerto: ${PORT}
URL: http://localhost:${PORT}
Base de datos: ${dbConfig.database}

Endpoints disponibles:
- PUT  /api/campaigns/:id/calculate-totals
- PUT  /api/campaigns/:id/update-status  
- GET  /api/reports/successful-messages?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD
- GET  /api/campaigns/:id
- GET  /api/campaigns
- GET  /api/health
    `);
});