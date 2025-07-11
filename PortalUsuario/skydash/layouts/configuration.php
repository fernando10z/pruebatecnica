  <div id="right-sidebar" class="settings-panel">
    <i class="settings-close ti-close"></i>
    <ul class="nav nav-tabs border-top" id="setting-panel" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab" aria-controls="todo-section" aria-expanded="true">Nuestros Blogs</a>
      </li>
    </ul>
    <div class="tab-content" id="setting-content">
      <!-- 📌 Sección de Notificaciones -->
      <div class="tab-pane fade show active scroll-wrapper" id="todo-section" role="tabpanel" aria-labelledby="todo-section">
          <div class="list-wrapper px-3" style="overflow-x: hidden; overflow-y: auto; max-height: 250px;">
              <ul class="d-flex flex-column-reverse todo-list" id="lista-notificaciones">
              </ul>
          </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* Estilo personalizado para el checkbox en morado */
    .form-check-input {
        display: inline-block !important;
        width: 18px;
        height: 18px;
        border: 2px solid #6a0dad !important; /* Morado */
        background-color: white !important;
    }

    .form-check-input:checked {
        background-color: #6a0dad !important;
        border-color: #6a0dad !important;
    }
    .form-check-label {
        color:rgb(0, 0, 0) !important;
        font-weight: bold;
    }
  </style>

