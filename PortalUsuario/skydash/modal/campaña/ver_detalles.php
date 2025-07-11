    <!-- Modal para ver detalles de campaña -->
    <div class="modal fade" id="modalDetallesCampaign" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de Campaña</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> <span id="detail_id"></span></p>
                            <p><strong>Nombre:</strong> <span id="detail_name"></span></p>
                            <p><strong>Cliente:</strong> <span id="detail_customer"></span></p>
                            <p><strong>Usuario:</strong> <span id="detail_user"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fecha:</strong> <span id="detail_date"></span></p>
                            <p><strong>Hora:</strong> <span id="detail_hour"></span></p>
                            <p><strong>Total Mensajes:</strong> <span id="detail_total"></span></p>
                            <p><strong>Enviados:</strong> <span id="detail_sent"></span></p>
                            <p><strong>Errores:</strong> <span id="detail_error"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>